<?php
require_once __DIR__ . '/../includes/check_login.php';

// Lấy userId từ URL hoặc từ tham số AJAX
$userId = isset($_GET['id']) ? intval($_GET['id']) : 0;
?>

<style>
    /* Modal container */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.6);
        justify-content: center;
        align-items: center;
        overflow: auto;
        animation: fadeIn 0.3s ease-in-out;
    }

    /* Modal content */
    .modal-content {
        background: #ffffff;
        padding: 30px;
        width: 100%;
        max-width: 1000px;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        position: relative;
        transform: translateY(-20px);
        animation: slideIn 0.3s ease-in-out forwards;
        border: 1px solid #e0e0e0;
    }

    /* Modal header */
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 1px solid #e0e0e0;
        padding-bottom: 10px;
    }

    .modal-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1a3c6d;
    }

    .modal-close {
        font-size: 32px;
        cursor: pointer;
        color: #666;
        background: none;
        border: none;
        transition: color 0.2s, transform 0.2s;
    }

    .modal-close:hover {
        color: #000;
        transform: scale(1.1);
    }

    /* Modal section */
    .modal-section {
        margin-bottom: 25px;
    }

    .modal-section h3 {
        font-size: 1.4rem;
        margin-bottom: 15px;
        color: #2c3e50;
        border-left: 4px solid #007bff;
        padding-left: 10px;
    }

    /* Modal field */
    .modal-field {
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .modal-field label {
        font-weight: 600;
        width: 180px;
        color: #34495e;
    }

    .modal-field span {
        flex: 1;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 6px;
        border: 1px solid #e0e0e0;
        font-size: 1rem;
        color: #333;
    }

    /* Avatar */
    .avatar-container {
        text-align: center;
        margin-bottom: 20px;
    }

    .avatar {
        width: 200px !important;
        height: 120px !important;
        object-fit: cover;
        border: 2px solid #ddd;
        border-radius: 0 !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.3s;
    }

    .avatar:hover {
        transform: scale(1.05);
    }

    .avatar-error {
        font-size: 0.9rem;
        color: #dc3545;
        margin-top: 5px;
        display: none;
    }

    /* Modal actions */
    .modal-actions {
        text-align: center;
        margin-top: 30px;
        display: flex;
        justify-content: center;
        gap: 15px;
    }

    .modal-btn {
        padding: 12px 30px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 1.1rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background-color 0.2s, transform 0.2s;
    }

    .modal-btn-save {
        background-color: #007bff;
        color: white;
    }

    .modal-btn-save:hover {
        background-color: #0056b3;
        transform: translateY(-2px);
    }

    .modal-btn-delete {
        background-color: #dc3545;
        color: white;
    }

    .modal-btn-delete:hover {
        background-color: #b02a37;
        transform: translateY(-2px);
    }

    .modal-btn i {
        font-size: 1.2rem;
    }

    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideIn {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .modal-content {
            width: 95%;
            padding: 20px;
        }
        .modal-title {
            font-size: 1.5rem;
        }
        .modal-section h3 {
            font-size: 1.2rem;
        }
        .modal-field {
            flex-direction: column;
            align-items: flex-start;
        }
        .modal-field label {
            width: auto;
            margin-bottom: 5px;
        }
        .modal-btn {
            padding: 10px 20px;
            font-size: 1rem;
        }
        .avatar {
            width: 120px !important;
            height: 100px !important;
        }
    }

    @media (max-width: 480px) {
        .modal-content {
            padding: 15px;
        }
        .modal-title {
            font-size: 1.3rem;
        }
        .modal-close {
            font-size: 28px;
        }


        
    }
</style>

<!-- Modal chi tiết nhân viên -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Thông Tin Chi Tiết Nhân Viên</h2>
            <button class="modal-close" aria-label="Đóng modal">×</button>
        </div>
        <div class="modal-body" id="modalBody">
            <p>Đang tải dữ liệu...</p>
        </div>
    </div>
</div>

<script>
    function loadUserDetails(userId) {
        if (!userId) {
            alert('Không tìm thấy ID nhân viên');
            return;
        }

        fetch(`http://localhost/doanqlns/index.php/api/user?id=${userId}`)
            .then(response => response.json())
            .then(user => {
                if (user.message) {
                    alert(user.message);
                    closeUserModal();
                    return;
                }
                const modalBody = document.getElementById('modalBody');
                const avatarSrc = user.hinh_anh ? '/doanqlns/' + user.hinh_anh : 'https://via.placeholder.com/150x150';
                modalBody.innerHTML = `
                    <div class="modal-section">
                        <h3>Thông Tin Cá Nhân</h3>
                        <div class="avatar-container">
                            <img src="${avatarSrc}" 
                                 onerror="this.src='https://via.placeholder.com/150x150'; document.getElementById('avatarError').style.display='block';" 
                                 alt="Avatar" class="avatar">
                            <div id="avatarError" class="avatar-error">Không thể tải ảnh</div>
                        </div>
                        <div class="modal-field">
                            <label>Mã Nhân Viên</label>
                            <span>${user.id_nhan_vien}</span>
                        </div>
                        <div class="modal-field">
                            <label>Họ Tên</label>
                            <span>${user.ho_ten}</span>
                        </div>
                        <div class="modal-field">
                            <label>Giới Tính</label>
                            <span>${user.gioi_tinh || 'N/A'}</span>
                        </div>
                        <div class="modal-field">
                            <label>Ngày Sinh</label>
                            <span>${user.ngay_sinh || 'N/A'}</span>
                        </div>
                        <div class="modal-field">
                            <label>Căn Cước Công Dân</label>
                            <span>${user.can_cuoc_cong_dan || 'N/A'}</span>
                        </div>
                        <div class="modal-field">
                            <label>Ngày Cấp</label>
                            <span>${user.ngay_cap || 'N/A'}</span>
                        </div>
                        <div class="modal-field">
                            <label>Nơi Cấp</label>
                            <span>${user.noi_cap || 'N/A'}</span>
                        </div>
                        <div class="modal-field">
                            <label>Quê Quán</label>
                            <span>${user.que_quan || 'N/A'}</span>
                        </div>
                    </div>
                    <div class="modal-section">
                        <h3>Liên Hệ</h3>
                        <div class="modal-field">
                            <label>Email</label>
                            <span>${user.email || 'N/A'}</span>
                        </div>
                        <div class="modal-field">
                            <label>Số Điện Thoại</label>
                            <span>${user.so_dien_thoai || 'N/A'}</span>
                        </div>
                        <div class="modal-field">
                            <label>Địa Chỉ</label>
                            <span>${user.dia_chi || 'N/A'}</span>
                        </div>
                    </div>
                    <div class="modal-section">
                        <h3>Công Việc</h3>
                        <div class="modal-field">
                            <label>Phòng Ban</label>
                            <span>${user.ten_phong_ban || 'N/A'}</span>
                        </div>
                        <div class="modal-field">
                            <label>Chức Vụ</label>
                            <span>${user.ten_chuc_vu || 'N/A'}</span>
                        </div>
                        <div class="modal-field">
                            <label>Loại Hợp Đồng</label>
                            <span>${user.loai_hop_dong || 'N/A'}</span>
                        </div>
                        <div class="modal-field">
                            <label>Ngày Vào Làm</label>
                            <span>${user.ngay_vao_lam || 'N/A'}</span>
                        </div>
                        <div class="modal-field">
                            <label>Ngày Nghỉ Việc</label>
                            <span>${user.ngay_nghi_viec || 'N/A'}</span>
                        </div>
                        <div class="modal-field">
                            <label>Trạng Thái</label>
                            <span>${user.trang_thai || 'N/A'}</span>
                        </div>
                    </div>
                    <div class="modal-section">
                        <h3>Tài Chính</h3>
                        <div class="modal-field">
                            <label>Lương Cơ Bản</label>
                            <span>${user.luong_co_ban ? new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(user.luong_co_ban) : 'N/A'}</span>
                        </div>
                    </div>
                    
                `;
                document.getElementById('userModal').style.display = 'flex';
            })
            .catch(error => {
                console.error("Lỗi khi tải chi tiết nhân viên:", error);
                document.getElementById('modalBody').innerHTML = '<p>Lỗi khi tải thông tin nhân viên</p>';
            });
    }

    function closeUserModal() {
        const modal = document.getElementById('userModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    function confirmDelete(userId) {
        if (confirm('Bạn có chắc chắn muốn xóa nhân viên này?')) {
            fetch(`http://localhost/doanqlns/index.php/api/user?id=${userId}`, {
                method: 'DELETE'
            })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert('Xóa nhân viên thành công!');
                        closeUserModal();
                        window.location.reload();
                    } else {
                        alert('Lỗi khi xóa nhân viên: ' + (result.message || 'Không rõ nguyên nhân'));
                    }
                })
                .catch(error => {
                    console.error("Lỗi khi xóa nhân viên:", error);
                    alert("Lỗi khi xóa nhân viên");
                });
        }
    }

    // Đóng modal khi nhấp nút đóng
    document.querySelector('#userModal .modal-close')?.addEventListener('click', () => {
        closeUserModal();
    });

    // Đóng modal khi nhấp ra ngoài
    document.getElementById('userModal')?.addEventListener('click', (e) => {
        if (e.target === document.getElementById('userModal')) {
            closeUserModal();
        }
    });

    // Nếu có userId (khi gọi trực tiếp), tự động hiển thị
    <?php if ($userId): ?>
        loadUserDetails(<?php echo $userId; ?>);
    <?php endif; ?>
</script>