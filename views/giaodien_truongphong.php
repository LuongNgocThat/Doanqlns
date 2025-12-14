<?php
require_once __DIR__ . '/../includes/check_login.php';
require_once __DIR__ . '/../config/Database.php';

// Only allow managers (id_chuc_vu = 4)
$database = new Database();
$conn = $database->getConnection();

// Resolve current employee and department
$currentUserId = $_SESSION['user_id'] ?? null;
if (!$currentUserId) {
    header('Location: /doanqlns/views/login.php');
    exit;
}

// Map user -> employee and department
$stmt = $conn->prepare("SELECT nv.id_nhan_vien, nv.id_phong_ban, nv.id_chuc_vu, pb.ten_phong_ban
                        FROM nguoi_dung u
                        JOIN nhan_vien nv ON nv.id_nhan_vien = u.id_nhan_vien
                        LEFT JOIN phong_ban pb ON nv.id_phong_ban = pb.id_phong_ban
                        WHERE u.id = ? LIMIT 1");
$stmt->execute([$currentUserId]);
$me = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$me) {
    header('Location: /doanqlns/views/login.php');
    exit;
}

// Guard: only head of department
if ((int)$me['id_chuc_vu'] !== 4) {
    http_response_code(403);
    echo 'Chỉ Trưởng phòng mới được truy cập trang này.';
    exit;
}

// Kiểm tra xem có phải trưởng phòng Kinh doanh không
$isKinhDoanh = false;
if (isset($me['ten_phong_ban']) && stripos($me['ten_phong_ban'], 'kinh doanh') !== false) {
    $isKinhDoanh = true;
}

// Fetch department employees
$stmt = $conn->prepare("SELECT nv.id_nhan_vien, nv.ho_ten
                        FROM nhan_vien nv
                        WHERE nv.id_phong_ban = ? AND nv.id_nhan_vien <> ?");
$stmt->execute([$me['id_phong_ban'], $me['id_nhan_vien']]);
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

$current_page = 'giaodien_truongphong.php';
include(__DIR__ . '/../includes/header.php');
include(__DIR__ . '/../includes/sidebar.php');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRM Pro - Đánh giá nhân viên</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; margin: 0; padding: 0; background: linear-gradient(135deg,rgb(251, 252, 252) 0%,rgb(146, 201, 235) 100%); }
        .container { max-width: 1500px; margin: 20px 20px 20px 300px; padding: 20px; border-radius: 20px; background: #fff; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; margin: -20px -20px 20px -20px; padding: 20px; border-radius: 20px 20px 0 0; text-align: center; }
        .header h1 { margin: 0; display: flex; gap: 10px; align-items: center; justify-content: center; }
        .filter { background: #f8f9fa; padding: 15px; border-radius: 12px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: linear-gradient(135deg,rgb(230,233,233) 0%,rgb(235,237,237) 100%); color: #4a5568; padding: 12px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #f0f0f0; }
        .actions { display: flex; gap: 8px; }
        .btn { padding: 8px 12px; border-radius: 8px; border: 1px solid #e9ecef; background: #fff; cursor: pointer; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; border: none; }
        
        /* Hiệu ứng highlight khi lưu thành công */
        .saved-highlight {
            background-color: #d4edda !important;
            border: 2px solid #28a745 !important;
            transition: all 0.3s ease !important;
            animation: pulse 0.5s ease-in-out;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
        
        /* Style cho các cột đã lưu */
        .saved-score {
            font-weight: bold;
            color: #28a745;
        }
        
        /* Toast notification */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            font-weight: 500;
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @media (max-width: 768px) { .container { margin: 10px; } }
    </style>
    <script>
        // Columns per danh_gia_hieu_qua_cong_viec.csv
        // ID nhân viên, Tên nhân viên, Câu hỏi 1..5, Tổng điểm (0-50), Điểm cuối (thang 10), Hiệu quả công việc (40%)
        document.addEventListener('DOMContentLoaded', function() {
            // Set current quarter automatically
            const now = new Date();
            const currentQuarter = Math.ceil((now.getMonth() + 1) / 3);
            const quarterFilter = document.getElementById('quarterFilter');
            if (quarterFilter) {
                quarterFilter.value = currentQuarter;
            }
            
            // Update quarter info display
            updateQuarterInfo();
            
            // Add event listeners for filter changes
            if (quarterFilter) {
                quarterFilter.addEventListener('change', function() {
                updateQuarterInfo();
                loadEvaluationData();
            });
            }
            const yearFilter = document.getElementById('yearFilter');
            if (yearFilter) {
                yearFilter.addEventListener('change', function() {
                updateQuarterInfo();
                loadEvaluationData();
            });
            }
            
            // Prefill saved scores so they persist after reload
            loadExistingScores();
        });

        async function loadExistingScores() {
            try {
                const quarterFilter = document.getElementById('quarterFilter');
                const yearFilter = document.getElementById('yearFilter');
                if (!quarterFilter || !yearFilter) return;
                
                const quarter = quarterFilter.value;
                const year = yearFilter.value;
                
                console.log('Loading scores for Quarter:', quarter, 'Year:', year);
                
                // Try to load from database first
                try {
                    const resp = await fetch(`/doanqlns/index.php/api/danhgia/all?quy=${quarter}&nam=${year}`);
                    const data = await resp.json();
                    
                    if (data && data.success && Array.isArray(data.data) && data.data.length > 0) {
                        console.log('Loading from database:', data.data);
                        
                        // Build latest-by-employee map
                        const byEmp = new Map();
                        for (const dg of data.data) {
                            const id = dg.id_nhan_vien;
                            const ts = Date.parse(dg.ngay_tao || dg.ngay_cap_nhat || dg.ngay || '') || 0;
                            const curr = byEmp.get(id);
                            if (!curr || ts > curr.ts) {
                                byEmp.set(id, { 
                                    he: parseFloat(dg.diem_hieu_qua) || 0, 
                                    td: parseFloat(dg.diem_thai_do) || 0, 
                                    ts,
                                    q1: parseFloat(dg.cau_hoi_1) || 0,
                                    q2: parseFloat(dg.cau_hoi_2) || 0,
                                    q3: parseFloat(dg.cau_hoi_3) || 0,
                                    q4: parseFloat(dg.cau_hoi_4) || 0,
                                    q5: parseFloat(dg.cau_hoi_5) || 0,
                                    bq1: parseFloat(dg.bcau_hoi_1) || 0,
                                    bq2: parseFloat(dg.bcau_hoi_2) || 0,
                                    bq3: parseFloat(dg.bcau_hoi_3) || 0,
                                    bq4: parseFloat(dg.bcau_hoi_4) || 0,
                                    bq5: parseFloat(dg.bcau_hoi_5) || 0
                                });
                            }
                        }
                        
                        // Fill inputs and recalc rows
                        byEmp.forEach((val, id) => {
                            // Chỉ fill performance scores nếu không phải trưởng phòng Kinh doanh
                            <?php if (!$isKinhDoanh): ?>
                            const q1 = document.querySelector(`.q1[data-id='${id}']`);
                            const q2 = document.querySelector(`.q2[data-id='${id}']`);
                            const q3 = document.querySelector(`.q3[data-id='${id}']`);
                            const q4 = document.querySelector(`.q4[data-id='${id}']`);
                            const q5 = document.querySelector(`.q5[data-id='${id}']`);
                            
                            // Fill performance scores
                            if (q1) q1.value = val.q1 || '0';
                            if (q2) q2.value = val.q2 || '0';
                            if (q3) q3.value = val.q3 || '0';
                            if (q4) q4.value = val.q4 || '0';
                            if (q5) q5.value = val.q5 || '0';
                            
                            // Recalculate totals
                            recalcFor(id);
                            <?php endif; ?>
                            
                            const bq1 = document.querySelector(`.bq1[data-id='${id}']`);
                            const bq2 = document.querySelector(`.bq2[data-id='${id}']`);
                            const bq3 = document.querySelector(`.bq3[data-id='${id}']`);
                            const bq4 = document.querySelector(`.bq4[data-id='${id}']`);
                            const bq5 = document.querySelector(`.bq5[data-id='${id}']`);
                            
                            // Fill behavior scores
                            if (bq1) bq1.value = val.bq1 || '0';
                            if (bq2) bq2.value = val.bq2 || '0';
                            if (bq3) bq3.value = val.bq3 || '0';
                            if (bq4) bq4.value = val.bq4 || '0';
                            if (bq5) bq5.value = val.bq5 || '0';
                            
                            // Recalculate totals
                            recalcBehaviorFor(id);
                        });
                        
                        showToast(`📋 Đã tải ${byEmp.size} đánh giá từ database Quý ${quarter} năm ${year}`, 'success');
                        return;
                    }
                } catch (dbError) {
                    console.log('Database load failed, trying localStorage:', dbError);
                }
                
                // Fallback: Load from localStorage
                const storageKey = `evaluation_${quarter}_${year}`;
                const savedData = localStorage.getItem(storageKey);
                
                if (savedData) {
                    const data = JSON.parse(savedData);
                    console.log('Loading from localStorage:', data);
                    
                    // Fill inputs and recalc rows
                    Object.keys(data).forEach(employeeId => {
                        const empData = data[employeeId];
                        
                        // Chỉ load performance scores nếu không phải trưởng phòng Kinh doanh
                        <?php if (!$isKinhDoanh): ?>
                        // Load performance scores
                        const q1 = document.querySelector(`.q1[data-id='${employeeId}']`);
                        const q2 = document.querySelector(`.q2[data-id='${employeeId}']`);
                        const q3 = document.querySelector(`.q3[data-id='${employeeId}']`);
                        const q4 = document.querySelector(`.q4[data-id='${employeeId}']`);
                        const q5 = document.querySelector(`.q5[data-id='${employeeId}']`);
                        
                        if (empData.performance) {
                            if (q1) q1.value = empData.performance.q1 || '0';
                            if (q2) q2.value = empData.performance.q2 || '0';
                            if (q3) q3.value = empData.performance.q3 || '0';
                            if (q4) q4.value = empData.performance.q4 || '0';
                            if (q5) q5.value = empData.performance.q5 || '0';
                            
                            // Recalculate totals
                            recalcFor(employeeId);
                        }
                        <?php endif; ?>
                        
                        // Load behavior scores
                        const bq1 = document.querySelector(`.bq1[data-id='${employeeId}']`);
                        const bq2 = document.querySelector(`.bq2[data-id='${employeeId}']`);
                        const bq3 = document.querySelector(`.bq3[data-id='${employeeId}']`);
                        const bq4 = document.querySelector(`.bq4[data-id='${employeeId}']`);
                        const bq5 = document.querySelector(`.bq5[data-id='${employeeId}']`);
                        
                        if (empData.behavior) {
                            if (bq1) bq1.value = empData.behavior.q1 || '0';
                            if (bq2) bq2.value = empData.behavior.q2 || '0';
                            if (bq3) bq3.value = empData.behavior.q3 || '0';
                            if (bq4) bq4.value = empData.behavior.q4 || '0';
                            if (bq5) bq5.value = empData.behavior.q5 || '0';
                            
                            // Recalculate totals
                            recalcBehaviorFor(employeeId);
                        }
                    });
                    
                    showToast(`📋 Đã tải ${Object.keys(data).length} đánh giá từ localStorage Quý ${quarter} năm ${year}`, 'info');
                } else {
                    console.log('No saved evaluation data found for Quarter:', quarter, 'Year:', year);
                    showToast(`📋 Chưa có dữ liệu đánh giá cho Quý ${quarter} năm ${year}`, 'info');
                }
            } catch (e) {
                console.warn('Cannot load existing scores:', e);
            }
        }

        // Load evaluation data for selected quarter
        function loadEvaluationData() {
            updateQuarterInfo();
            
            // Reset all inputs to 0 first
            document.querySelectorAll('input[type="number"]').forEach(input => {
                input.value = '0';
            });
            
            // Reset all calculated fields
            document.querySelectorAll('[class*="tong"], [class*="cuoi"], [class*="hieuqua"], [class*="bhoptac"]').forEach(el => {
                if (el.classList.contains('tong') || el.classList.contains('btong')) {
                    el.textContent = '0';
                } else if (el.classList.contains('cuoi') || el.classList.contains('bcuoi')) {
                    el.textContent = '0';
                } else if (el.classList.contains('hieuqua') || el.classList.contains('bhoptac')) {
                    el.textContent = '0%';
                }
            });
            
            // Then load data for the selected quarter
            loadExistingScores();
        }

        // Save evaluation data to localStorage
        function saveEvaluationData(employeeId, type, scores) {
            const quarter = document.getElementById('quarterFilter').value;
            const year = document.getElementById('yearFilter').value;
            const storageKey = `evaluation_${quarter}_${year}`;
            
            // Get existing data
            let data = {};
            const savedData = localStorage.getItem(storageKey);
            if (savedData) {
                data = JSON.parse(savedData);
            }
            
            // Initialize employee data if not exists
            if (!data[employeeId]) {
                data[employeeId] = {};
            }
            
            // Save scores based on type
            if (type === 'performance') {
                data[employeeId].performance = {
                    q1: scores.q1,
                    q2: scores.q2,
                    q3: scores.q3,
                    q4: scores.q4,
                    q5: scores.q5,
                    timestamp: new Date().toISOString()
                };
            } else if (type === 'behavior') {
                data[employeeId].behavior = {
                    q1: scores.q1,
                    q2: scores.q2,
                    q3: scores.q3,
                    q4: scores.q4,
                    q5: scores.q5,
                    timestamp: new Date().toISOString()
                };
            }
            
            // Save to localStorage
            localStorage.setItem(storageKey, JSON.stringify(data));
            console.log('Saved evaluation data:', data);
        }

        // Update quarter info display
        function updateQuarterInfo() {
            const quarterFilter = document.getElementById('quarterFilter');
            const yearFilter = document.getElementById('yearFilter');
            const quarterInfo = document.getElementById('quarterInfo');
            
            if (!quarterFilter || !yearFilter || !quarterInfo) return;
            
            const quarter = quarterFilter.value;
            const year = yearFilter.value;
            const quarterNames = {
                '1': 'Quý 1 (Tháng 1-3)',
                '2': 'Quý 2 (Tháng 4-6)', 
                '3': 'Quý 3 (Tháng 7-9)',
                '4': 'Quý 4 (Tháng 10-12)'
            };
            quarterInfo.textContent = `Đang đánh giá: ${quarterNames[quarter]} năm ${year}`;
        }

        // Cập nhật giao diện sau khi lưu thành công
        function updateUIAfterSave(employeeId, type, score) {
            if (type === 'performance') {
                // Cập nhật bảng hiệu quả công việc
                const cuoiEl = document.querySelector(`.cuoi[data-id='${employeeId}']`);
                const tongEl = document.querySelector(`.tong[data-id='${employeeId}']`);
                const hqEl = document.querySelector(`.hieuqua[data-id='${employeeId}']`);
                
                if (cuoiEl) cuoiEl.textContent = String(score);
                if (tongEl) tongEl.textContent = String(Math.round(score * 5));
                if (hqEl) hqEl.textContent = Math.round((score / 10) * 40) + '%';
                
                // Thêm hiệu ứng visual để người dùng biết đã lưu
                highlightRow(employeeId, 'performance');
            } else if (type === 'behavior') {
                // Cập nhật bảng thái độ & hợp tác
                const bcuoiEl = document.querySelector(`.bcuoi[data-id='${employeeId}']`);
                const btongEl = document.querySelector(`.btong[data-id='${employeeId}']`);
                const bhEl = document.querySelector(`.bhoptac[data-id='${employeeId}']`);
                
                if (bcuoiEl) bcuoiEl.textContent = String(score);
                if (btongEl) btongEl.textContent = String(Math.round(score * 5));
                if (bhEl) bhEl.textContent = Math.round((score / 10) * 20) + '%';
                
                // Thêm hiệu ứng visual để người dùng biết đã lưu
                highlightRow(employeeId, 'behavior');
            }
        }

        // Highlight row để người dùng biết đã lưu thành công
        function highlightRow(employeeId, type) {
            let row;
            if (type === 'performance') {
                row = document.querySelector(`.cuoi[data-id='${employeeId}']`)?.closest('tr');
            } else if (type === 'behavior') {
                row = document.querySelector(`.bcuoi[data-id='${employeeId}']`)?.closest('tr');
            }
            
            if (row) {
                // Thêm class highlight
                row.classList.add('saved-highlight');
                
                // Thêm class saved-score cho các cột điểm
                const scoreElements = row.querySelectorAll('.cuoi, .tong, .hieuqua, .bcuoi, .btong, .bhoptac');
                scoreElements.forEach(el => {
                    el.classList.add('saved-score');
                });
                
                // Xóa highlight sau 3 giây
                setTimeout(() => {
                    row.classList.remove('saved-highlight');
                    scoreElements.forEach(el => {
                        el.classList.remove('saved-score');
                    });
                }, 3000);
            }
        }

        // Hiển thị toast notification
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.style.background = type === 'success' ? '#28a745' : '#dc3545';
            toast.textContent = message;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 3000);
        }

        // Show evaluation status
        function showEvaluationStatus() {
            const quarter = document.getElementById('quarterFilter').value;
            const year = document.getElementById('yearFilter').value;
            const storageKey = `evaluation_${quarter}_${year}`;
            const savedData = localStorage.getItem(storageKey);
            
            if (savedData) {
                const data = JSON.parse(savedData);
                const employeeIds = Object.keys(data);
                
                let statusMessage = `📊 Trạng thái đánh giá Quý ${quarter} năm ${year}:\n\n`;
                
                employeeIds.forEach(empId => {
                    const empData = data[empId];
                    const row = document.querySelector(`tr:has(.q1[data-id='${empId}'])`);
                    const empName = row ? row.querySelector('td:nth-child(2)')?.textContent?.trim() || `NV${empId}` : `NV${empId}`;
                    
                    statusMessage += `👤 ${empName}:\n`;
                    
                    if (empData.performance) {
                        const p = empData.performance;
                        const total = parseInt(p.q1) + parseInt(p.q2) + parseInt(p.q3) + parseInt(p.q4) + parseInt(p.q5);
                        const final = (total / 50 * 10).toFixed(1);
                        statusMessage += `  ✅ Hiệu quả: ${p.q1},${p.q2},${p.q3},${p.q4},${p.q5} (${final}/10)\n`;
                    } else {
                        statusMessage += `  ❌ Hiệu quả: Chưa đánh giá\n`;
                    }
                    
                    if (empData.behavior) {
                        const b = empData.behavior;
                        const total = parseInt(b.q1) + parseInt(b.q2) + parseInt(b.q3) + parseInt(b.q4) + parseInt(b.q5);
                        const final = (total / 50 * 10).toFixed(1);
                        statusMessage += `  ✅ Thái độ: ${b.q1},${b.q2},${b.q3},${b.q4},${b.q5} (${final}/10)\n`;
                    } else {
                        statusMessage += `  ❌ Thái độ: Chưa đánh giá\n`;
                    }
                    
                    statusMessage += `\n`;
                });
                
                alert(statusMessage);
            } else {
                showToast('📋 Chưa có dữ liệu đánh giá nào được lưu', 'info');
            }
        }

        // Clear evaluation data
        function clearEvaluationData() {
            const quarter = document.getElementById('quarterFilter').value;
            const year = document.getElementById('yearFilter').value;
            const storageKey = `evaluation_${quarter}_${year}`;
            
            if (confirm(`Bạn có chắc chắn muốn xóa tất cả dữ liệu đánh giá Quý ${quarter} năm ${year}?`)) {
                localStorage.removeItem(storageKey);
                
                // Clear all input fields
                document.querySelectorAll('input[type="number"]').forEach(input => {
                    input.value = '0';
                });
                
                // Reset all calculated fields
                document.querySelectorAll('[class*="tong"], [class*="cuoi"], [class*="hieuqua"], [class*="bhoptac"]').forEach(el => {
                    if (el.classList.contains('tong') || el.classList.contains('btong')) {
                        el.textContent = '0';
                    } else if (el.classList.contains('cuoi') || el.classList.contains('bcuoi')) {
                        el.textContent = '0';
                    } else if (el.classList.contains('hieuqua') || el.classList.contains('bhoptac')) {
                        el.textContent = '0%';
                    }
                });
                
                showToast('🗑️ Đã xóa tất cả dữ liệu đánh giá', 'success');
            }
        }
    </script>
    </head>
<body>
    <div class="container">
        <?php if (!$isKinhDoanh): ?>
        <div class="header">
            <h1><i class="fas fa-star"></i> Đánh giá hiệu quả công việc</h1>
            <div id="quarterInfo" style="margin-top: 10px; font-size: 16px; opacity: 0.9;">
                <!-- Quý hiện tại sẽ được cập nhật bởi JavaScript -->
            </div>
        </div>
        <?php else: ?>
        <div class="header">
            <h1><i class="fas fa-users"></i> Đánh giá thái độ & hợp tác</h1>
            <div id="quarterInfo" style="margin-top: 10px; font-size: 16px; opacity: 0.9;">
                <!-- Quý hiện tại sẽ được cập nhật bởi JavaScript -->
            </div>
        </div>
        <?php endif; ?>

        <!-- Bộ lọc theo quý -->
        <div class="filter">
            <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                <div>
                    <label for="quarterFilter" style="font-weight: 500; margin-right: 8px;">Quý:</label>
                    <select id="quarterFilter" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                        <option value="1">Quý 1 (T1-T3)</option>
                        <option value="2">Quý 2 (T4-T6)</option>
                        <option value="3">Quý 3 (T7-T9)</option>
                        <option value="4">Quý 4 (T10-T12)</option>
                    </select>
                </div>
                <div>
                    <label for="yearFilter" style="font-weight: 500; margin-right: 8px;">Năm:</label>
                    <select id="yearFilter" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                        <?php 
                        $currentYear = date('Y');
                        for($i = $currentYear; $i >= 2020; $i--): 
                        ?>
                        <option value="<?= $i ?>" <?= $i == $currentYear ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <button onclick="loadEvaluationData()" style="padding: 8px 16px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
                    <i class="fas fa-search"></i> Tải dữ liệu
                </button>
                <!-- <button onclick="showEvaluationStatus()" style="padding: 8px 16px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
                    <i class="fas fa-chart-bar"></i> Trạng thái
                </button> -->
                <button onclick="clearEvaluationData()" style="padding: 8px 16px; background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
                    <i class="fas fa-trash"></i> Xóa dữ liệu
                </button>
                <!-- <div style="margin-left: auto; font-size: 14px; color: #666;">
                    <i class="fas fa-info-circle"></i> 
                    Dữ liệu được lưu tạm thời trong trình duyệt
                </div> -->
            </div>
        </div>

        <?php if (!$isKinhDoanh): ?>
        <!-- Bảng Đánh giá hiệu quả công việc - chỉ hiển thị cho các trưởng phòng khác (không phải Kinh doanh) -->
        <div class="header" style="margin-top: 20px;">
            <h1><i class="fas fa-chart-line"></i> Đánh giá hiệu quả công việc</h1>
        </div>
  
     <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID nhân viên</th>
                        <th>Tên nhân viên</th>
                        <th>Câu hỏi 1</th>
                        <th>Câu hỏi 2</th>
                        <th>Câu hỏi 3</th>
                        <th>Câu hỏi 4</th>
                        <th>Câu hỏi 5</th>
                        <th>Tổng điểm (0-50)</th>
                        <th>Điểm cuối (thang 10)</th>
                        <th>Hiệu quả công việc (40%)</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $emp): ?>
                    <tr>
                        <td><?= htmlspecialchars($emp['id_nhan_vien']) ?></td>
                        <td><?= htmlspecialchars($emp['ho_ten']) ?></td>
                        <td><input type="number" min="0" max="10" step="1" class="score q1" data-id="<?= $emp['id_nhan_vien'] ?>" value="0"></td>
                        <td><input type="number" min="0" max="10" step="1" class="score q2" data-id="<?= $emp['id_nhan_vien'] ?>" value="0"></td>
                        <td><input type="number" min="0" max="10" step="1" class="score q3" data-id="<?= $emp['id_nhan_vien'] ?>" value="0"></td>
                        <td><input type="number" min="0" max="10" step="1" class="score q4" data-id="<?= $emp['id_nhan_vien'] ?>" value="0"></td>
                        <td><input type="number" min="0" max="10" step="1" class="score q5" data-id="<?= $emp['id_nhan_vien'] ?>" value="0"></td>
                        <td class="tong" data-id="<?= $emp['id_nhan_vien'] ?>">0</td>
                        <td class="cuoi" data-id="<?= $emp['id_nhan_vien'] ?>">0</td>
                        <td class="hieuqua" data-id="<?= $emp['id_nhan_vien'] ?>">0%</td>
                        <td class="actions">
                            <button class="btn btn-primary" onclick="saveRow(<?= $emp['id_nhan_vien'] ?>)">Lưu</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <div class="header" style="margin-top: 30px;">
            <h1><i class="fas fa-users"></i> Đánh giá thái độ & hợp tác</h1>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID nhân viên</th>
                        <th>Tên nhân viên</th>
                        <th>Câu hỏi 1</th>
                        <th>Câu hỏi 2</th>
                        <th>Câu hỏi 3</th>
                        <th>Câu hỏi 4</th>
                        <th>Câu hỏi 5</th>
                        <th>Tổng điểm (0-50)</th>
                        <th>Điểm cuối (thang 10)</th>
                        <th>Thái độ & Hợp tác (20%)</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $emp): ?>
                    <tr>
                        <td><?= htmlspecialchars($emp['id_nhan_vien']) ?></td>
                        <td><?= htmlspecialchars($emp['ho_ten']) ?></td>
                        <td><input type="number" min="0" max="10" step="1" class="score-b bq1" data-id="<?= $emp['id_nhan_vien'] ?>" value="0"></td>
                        <td><input type="number" min="0" max="10" step="1" class="score-b bq2" data-id="<?= $emp['id_nhan_vien'] ?>" value="0"></td>
                        <td><input type="number" min="0" max="10" step="1" class="score-b bq3" data-id="<?= $emp['id_nhan_vien'] ?>" value="0"></td>
                        <td><input type="number" min="0" max="10" step="1" class="score-b bq4" data-id="<?= $emp['id_nhan_vien'] ?>" value="0"></td>
                        <td><input type="number" min="0" max="10" step="1" class="score-b bq5" data-id="<?= $emp['id_nhan_vien'] ?>" value="0"></td>
                        <td class="btong" data-id="<?= $emp['id_nhan_vien'] ?>">0</td>
                        <td class="bcuoi" data-id="<?= $emp['id_nhan_vien'] ?>">0</td>
                        <td class="bhoptac" data-id="<?= $emp['id_nhan_vien'] ?>">0%</td>
                        <td class="actions">
                            <button class="btn btn-primary" onclick="saveRowBehavior(<?= $emp['id_nhan_vien'] ?>)">Lưu</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<script>
// Auto-calc totals per row
function recalcFor(id) {
    const getVal = cls => {
        const el = document.querySelector(`.${cls}[data-id='${id}']`);
        const v = parseFloat(el && el.value ? el.value : 0);
        return isNaN(v) ? 0 : v;
    };
    const total50 = getVal('q1') + getVal('q2') + getVal('q3') + getVal('q4') + getVal('q5');
    const final10 = (total50 / 50 * 10).toFixed(1);
    const efficiency = Math.round((final10 / 10) * 40); // percentage of 40%
    const tongEl = document.querySelector(`.tong[data-id='${id}']`);
    const cuoiEl = document.querySelector(`.cuoi[data-id='${id}']`);
    const hqEl = document.querySelector(`.hieuqua[data-id='${id}']`);
    if (tongEl) tongEl.textContent = String(total50);
    if (cuoiEl) cuoiEl.textContent = String(final10);
    if (hqEl) hqEl.textContent = efficiency + '%';
}

document.addEventListener('input', function(e) {
    if (e.target && e.target.classList.contains('score')) {
        // Clamp to [0,10]
        let v = parseFloat(e.target.value);
        if (isNaN(v)) v = 0;
        if (v < 0) v = 0;
        if (v > 10) v = 10;
        e.target.value = v;
        const id = e.target.getAttribute('data-id');
        recalcFor(id);
    }
});

async function saveRow(id) {
    <?php if (!$isKinhDoanh): ?>
    recalcFor(id);
    try {
        const finalEl = document.querySelector(`.cuoi[data-id='${id}']`);
        const final10 = finalEl ? parseFloat(finalEl.textContent) || 0 : 0;
        const row = finalEl ? finalEl.closest('tr') : null;
        const empName = row ? row.querySelector('td:nth-child(2)')?.textContent?.trim() || '' : '';
        const quarterFilter = document.getElementById('quarterFilter');
        const yearFilter = document.getElementById('yearFilter');
        if (!quarterFilter || !yearFilter) return;
        const quarter = quarterFilter.value;
        const year = yearFilter.value;
        
        // Get individual question scores
        const q1 = document.querySelector(`.q1[data-id='${id}']`)?.value || 0;
        const q2 = document.querySelector(`.q2[data-id='${id}']`)?.value || 0;
        const q3 = document.querySelector(`.q3[data-id='${id}']`)?.value || 0;
        const q4 = document.querySelector(`.q4[data-id='${id}']`)?.value || 0;
        const q5 = document.querySelector(`.q5[data-id='${id}']`)?.value || 0;
        
        // Save to localStorage
        const scores = { q1, q2, q3, q4, q5 };
        saveEvaluationData(id, 'performance', scores);
        
        // Also save to database
        const reqBody = {
            type: 'hieu-qua',
            quy: quarter,
            nam: year,
            data: [
                {
                    idNhanVien: String(id),
                    tenNhanVien: empName,
                    cauHoi1: String(q1), 
                    cauHoi2: String(q2), 
                    cauHoi3: String(q3), 
                    cauHoi4: String(q4), 
                    cauHoi5: String(q5),
                    tongDiem: String(Math.round(final10 * 5)),
                    diemCuoi: final10
                }
            ]
        };
        
        const resp = await fetch('/doanqlns/index.php/api/import-evaluation-csv', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(reqBody)
        });
        
        const json = await resp.json();
        if (json && json.success) {
            // Cập nhật giao diện ngay lập tức
            updateUIAfterSave(id, 'performance', final10);
            showToast(`✅ Đã lưu điểm hiệu quả (${final10}/10) cho ${empName || id} - Quý ${quarter}/${year}`);
        } else {
            showToast('❌ Không thể lưu vào database. Dữ liệu chỉ được lưu tạm thời.', 'error');
        }
        
    } catch (e) {
        console.error(e);
        showToast('❌ Lỗi khi lưu điểm hiệu quả.', 'error');
    }
    <?php else: ?>
    // Trưởng phòng Kinh doanh không có chức năng này
    showToast('❌ Trưởng phòng Kinh doanh không thể đánh giá hiệu quả công việc.', 'error');
    <?php endif; ?>
}

// Behavior table calculations
function recalcBehaviorFor(id) {
    const getVal = cls => {
        const el = document.querySelector(`.${cls}[data-id='${id}']`);
        const v = parseFloat(el && el.value ? el.value : 0);
        return isNaN(v) ? 0 : v;
    };
    const total50 = getVal('bq1') + getVal('bq2') + getVal('bq3') + getVal('bq4') + getVal('bq5');
    const final10 = (total50 / 50 * 10).toFixed(1);
    const percent20 = Math.round((final10 / 10) * 20);
    const tongEl = document.querySelector(`.btong[data-id='${id}']`);
    const cuoiEl = document.querySelector(`.bcuoi[data-id='${id}']`);
    const hpEl = document.querySelector(`.bhoptac[data-id='${id}']`);
    if (tongEl) tongEl.textContent = String(total50);
    if (cuoiEl) cuoiEl.textContent = String(final10);
    if (hpEl) hpEl.textContent = percent20 + '%';
}

document.addEventListener('input', function(e) {
    if (e.target && e.target.classList.contains('score-b')) {
        // Clamp to [0,10]
        let v = parseFloat(e.target.value);
        if (isNaN(v)) v = 0;
        if (v < 0) v = 0;
        if (v > 10) v = 10;
        e.target.value = v;
        const id = e.target.getAttribute('data-id');
        recalcBehaviorFor(id);
    }
});

async function saveRowBehavior(id) {
    recalcBehaviorFor(id);
    try {
        const finalEl = document.querySelector(`.bcuoi[data-id='${id}']`);
        const final10 = finalEl ? parseFloat(finalEl.textContent) || 0 : 0;
        const row = finalEl ? finalEl.closest('tr') : null;
        const empName = row ? row.querySelector('td:nth-child(2)')?.textContent?.trim() || '' : '';
        const quarter = document.getElementById('quarterFilter').value;
        const year = document.getElementById('yearFilter').value;
        
        // Get individual question scores for behavior
        const bq1 = document.querySelector(`.bq1[data-id='${id}']`)?.value || 0;
        const bq2 = document.querySelector(`.bq2[data-id='${id}']`)?.value || 0;
        const bq3 = document.querySelector(`.bq3[data-id='${id}']`)?.value || 0;
        const bq4 = document.querySelector(`.bq4[data-id='${id}']`)?.value || 0;
        const bq5 = document.querySelector(`.bq5[data-id='${id}']`)?.value || 0;
        
        // Save to localStorage
        const scores = { q1: bq1, q2: bq2, q3: bq3, q4: bq4, q5: bq5 };
        saveEvaluationData(id, 'behavior', scores);
        
        // Also save to database
        const reqBody = {
            type: 'thai-do',
            quy: quarter,
            nam: year,
            data: [
                {
                    idNhanVien: String(id),
                    tenNhanVien: empName,
                    cauHoi1: String(bq1), 
                    cauHoi2: String(bq2), 
                    cauHoi3: String(bq3), 
                    cauHoi4: String(bq4), 
                    cauHoi5: String(bq5),
                    tongDiem: String(Math.round(final10 * 5)),
                    diemCuoi: final10
                }
            ]
        };
        
        const resp = await fetch('/doanqlns/index.php/api/import-evaluation-csv', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(reqBody)
        });
        
        const json = await resp.json();
        if (json && json.success) {
            // Cập nhật giao diện ngay lập tức
            updateUIAfterSave(id, 'behavior', final10);
            showToast(`✅ Đã lưu điểm thái độ (${final10}/10) cho ${empName || id} - Quý ${quarter}/${year}`);
        } else {
            showToast('❌ Không thể lưu vào database. Dữ liệu chỉ được lưu tạm thời.', 'error');
        }
        
    } catch (e) {
        console.error(e);
        showToast('❌ Lỗi khi lưu điểm thái độ.', 'error');
    }
}
</script>
</body>
</html>


