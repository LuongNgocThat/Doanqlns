// script.js - Xử lý giao diện điểm danh nhân viên bằng khuôn mặt
$(document).ready(function() {
    // Khởi tạo voice tiếng Việt cho Speech Synthesis
    let viSelectedVoice = null;
    function initVoices() {
        if (!('speechSynthesis' in window)) return;
        const voices = window.speechSynthesis.getVoices();
        if (!voices || voices.length === 0) return;
        viSelectedVoice = voices.find(v => {
            const lang = (v.lang || '').toLowerCase();
            const name = (v.name || '').toLowerCase();
            return lang.startsWith('vi') || name.includes('vietnam');
        }) || null;
    }
    try {
        window.speechSynthesis.onvoiceschanged = initVoices;
        initVoices();
    } catch (_) {}

    // Phát âm thanh qua loa: đọc nội dung tiếng Việt
    function speak(text) {
        try {
            if (!('speechSynthesis' in window)) return;
            // Đảm bảo đã cố gắng lấy voices
            if (!viSelectedVoice) initVoices();
            const utter = new SpeechSynthesisUtterance(text);
            if (viSelectedVoice) {
                utter.voice = viSelectedVoice;
                utter.lang = viSelectedVoice.lang || 'vi-VN';
            } else {
                // Fallback nếu máy không có voice tiếng Việt
                utter.lang = 'vi-VN';
            }
            utter.rate = 1.25; // nói nhanh hơn một chút
            utter.pitch = 1.0;
            utter.volume = 1.0;
            window.speechSynthesis.cancel();
            window.speechSynthesis.speak(utter);
        } catch (e) {
            console.warn('Speech synthesis error:', e);
        }
    }
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const canvasOutput = document.getElementById('canvasOutput');
    const capture = document.getElementById('capture');
    const result = document.getElementById('result');
    const successOverlay = document.getElementById('success-overlay');
    const faceOverlay = document.getElementById('face-overlay');

    // Hàm khởi động webcam
    function initializeWebcam() {
        if (video.srcObject) {
            video.srcObject.getTracks().forEach(track => track.stop());
        }
        navigator.mediaDevices.getUserMedia({ 
            video: { 
                width: { ideal: 400 },
                height: { ideal: 400 },
                facingMode: 'user'
            } 
        })
        .then(stream => {
            video.srcObject = stream;
            video.play();
            
            // Hiển thị canvas và ẩn video
            video.style.display = 'none';
            canvasOutput.style.display = 'block';
            
            setTimeout(() => {
                console.log("Webcam initialized successfully, resolution:", video.videoWidth, "x", video.videoHeight);
                if (video.videoWidth === 0 || video.videoHeight === 0) {
                    result.innerHTML = '<div class="result-error"><i class="fas fa-exclamation-triangle"></i> Lỗi: Webcam không cung cấp dữ liệu hình ảnh!</div>';
                    console.error("Webcam resolution is zero");
                } else {
                    // Ẩn loading overlay khi webcam khởi tạo thành công
                    const loadingOverlay = document.getElementById('loading-overlay');
                    if (loadingOverlay) {
                        loadingOverlay.style.display = 'none';
                    }
                    
                    // Bắt đầu vẽ video lên canvas để hiển thị
                    drawVideoToCanvas();
                }
            }, 1000);
        })
        .catch(err => {
            result.innerHTML = `<div class="result-error"><i class="fas fa-exclamation-triangle"></i> Lỗi truy cập webcam: ${err.message}. Vui lòng cấp quyền và làm mới trang!</div>`;
            console.error('Webcam error:', err);
            // Ẩn loading overlay ngay cả khi có lỗi
            const loadingOverlay = document.getElementById('loading-overlay');
            if (loadingOverlay) {
                loadingOverlay.style.display = 'none';
            }
        });
    }

    // Hàm vẽ video lên canvas
    function drawVideoToCanvas() {
        if (video.videoWidth === 0 || video.videoHeight === 0) {
            return;
        }
        
        const ctx = canvasOutput.getContext('2d');
        canvasOutput.width = video.videoWidth;
        canvasOutput.height = video.videoHeight;
        
        function draw() {
            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                ctx.drawImage(video, 0, 0, canvasOutput.width, canvasOutput.height);
            }
            requestAnimationFrame(draw);
        }
        draw();
    }

    // Hiển thị canvas ngay từ đầu
    canvasOutput.style.display = 'block';
    
    // Khởi động webcam khi tải trang
    initializeWebcam();
    
    // Đảm bảo ẩn loading overlay sau 5 giây (fallback)
    setTimeout(() => {
        const loadingOverlay = document.getElementById('loading-overlay');
        if (loadingOverlay && loadingOverlay.style.display !== 'none') {
            loadingOverlay.style.display = 'none';
            console.log("Loading overlay hidden by timeout fallback");
        }
    }, 5000);

    // Hiển thị overlay thành công
    function showSuccessOverlay() {
        successOverlay.style.display = 'flex';
        setTimeout(() => {
            successOverlay.style.display = 'none';
        }, 3000);
    }

    // Ẩn overlay thành công
    function hideSuccessOverlay() {
        successOverlay.style.display = 'none';
    }

    // Xử lý điểm danh
    $('#capture').click(function() {
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const context = canvas.getContext('2d');
        
        // Thiết lập kích thước canvas
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        // Vẽ frame từ video lên canvas
        context.drawImage(video, 0, 0);
        
        // Chuyển đổi canvas thành blob
        canvas.toBlob(function(blob) {
            const formData = new FormData();
            formData.append('image', blob, 'capture.jpg');
            
            // Hiển thị loading
            $('#result').html('<div class="result-info"><i class="fas fa-spinner fa-spin"></i> Đang xử lý nhận diện...</div>');
            
            $.ajax({
                url: '/attendance',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === 'success') {
                        // Hiển thị overlay thành công
                        showSuccessOverlay();
                        // Nói thông báo thành công
                        speak('Điểm danh thành công');
                        
                        $('#result').html(`
                            <div class="result-success">
                                <i class="fas fa-check-circle"></i>
                                <strong>✅ Điểm danh thành công!</strong><br>
                                <strong>Nhân viên:</strong> ${response.student_name}<br>
                                <strong>Mã số:</strong> ${response.student_id}<br>
                                <strong>Thời gian:</strong> ${new Date().toLocaleString('vi-VN')}
                            </div>
                        `);
                        
                        // Gửi sự kiện sang parent (nếu đang được nhúng trong iframe)
                        try {
                            if (window.parent && window.parent !== window) {
                                window.parent.postMessage({
                                    type: 'faceAttendanceSuccess',
                                    student_id: response.student_id,
                                    student_name: response.student_name,
                                    timestamp: new Date().toISOString()
                                }, '*');
                            }
                        } catch (e) {
                            console.warn('postMessage to parent failed:', e);
                        }
                    } else {
                        $('#result').html(`
                            <div class="result-error">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>⚠️ ${response.message}</strong><br>
                                Vui lòng đảm bảo khuôn mặt rõ ràng và ánh sáng tốt.
                            </div>
                        `);
                        // Nói "Xin vui lòng thử lại" khi nhận diện không thành công
                        speak('Xin vui lòng thử lại');
                        
                        // Báo lỗi sang parent nếu có
                        try {
                            if (window.parent && window.parent !== window) {
                                window.parent.postMessage({
                                    type: 'faceAttendanceError',
                                    message: response.message || 'Không nhận diện được khuôn mặt'
                                }, '*');
                            }
                        } catch (e) {
                            console.warn('postMessage to parent failed:', e);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    $('#result').html(`
                        <div class="result-error">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>❌ Lỗi:</strong> ${error}<br>
                            Vui lòng thử lại sau.
                        </div>
                    `);
                    // Nói "Xin vui lòng thử lại" khi có lỗi API
                    speak('Xin vui lòng thử lại');
                    
                    // Báo lỗi sang parent nếu có
                    try {
                        if (window.parent && window.parent !== window) {
                            window.parent.postMessage({
                                type: 'faceAttendanceError',
                                message: error || 'Lỗi khi gọi API điểm danh'
                            }, '*');
                        }
                    } catch (e) {
                        console.warn('postMessage to parent failed:', e);
                    }
                }
            });
        }, 'image/jpeg');
    });

    // Tự động ẩn overlay thành công sau 3 giây
    setInterval(hideSuccessOverlay, 3000);
});