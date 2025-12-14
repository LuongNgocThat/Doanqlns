<?php
require_once __DIR__ . '/../includes/check_login.php';
include(__DIR__ . '/../includes/header.php');

// Set current page for sidebar
$current_page = 'lich_su_diem_danh_my.php';
$is_admin = false; // Nhân viên không phải admin

// Get current employee ID from session
$current_user_id = $_SESSION['user_id'] ?? null;
if (!$current_user_id) {
    header('Location: /doanqlns/views/login.php');
    exit;
}

// Get employee ID from database
$database = new Database();
$conn = $database->getConnection();
$query = "SELECT id_nhan_vien FROM nguoi_dung WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$current_user_id]);
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

$current_employee_id = $user_data['id_nhan_vien'] ?? null;
if (!$current_employee_id) {
    header('Location: /doanqlns/views/login.php');
    exit;
}

// Get employee name
$employee_query = "SELECT ho_ten FROM nhan_vien WHERE id_nhan_vien = ?";
$employee_stmt = $conn->prepare($employee_query);
$employee_stmt->execute([$current_employee_id]);
$employee_data = $employee_stmt->fetch(PDO::FETCH_ASSOC);
$employee_name = $employee_data['ho_ten'] ?? 'Nhân viên';
?>

<!DOCTYPE html>
<html lang="vi" class="light-style layout-navbar-fixed layout-menu-fixed">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRM Pro - Lịch sử điểm danh của tôi</title>

    <!-- Font Awesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS riêng -->
    <style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'Roboto', sans-serif;
        margin: 0;
        padding: 0;
        background: linear-gradient(135deg,rgb(251, 252, 252) 0%,rgb(146, 201, 235) 100%);

        min-height: 100vh;
    }

    .attendance-container {
        max-width: 1500px;
        margin: 20px auto;
        padding: 20px;
        padding-left:250px;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .attendance-header {
        text-align: center;
        margin-bottom: 30px;
        padding: 20px 0;
        background: linear-gradient(135deg, #667eea 0%,rgb(205, 236, 247) 100%);
        color: white;
        margin: -20px -20px 30px -20px;
        border-radius: 20px 20px 0 0;
    }

    .attendance-header h3 {
        margin: 0;
        font-size: 2.5rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
    }

    .attendance-header i {
        font-size: 2.2rem;
    }

    .employee-info {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 20px;
        border-radius: 15px;
        margin: 0 auto 25px auto;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        max-width: 100%;
    }

    .employee-info h4 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .employee-info p {
        margin: 5px 0 0 0;
        opacity: 0.9;
        font-size: 1rem;
    }

    .filter-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 15px;
        margin: 0 auto 25px auto;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        max-width: 100%;
    }

    .filter-section h4 {
        color: #2c3e50;
        margin-bottom: 15px;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .filter-container {
        display: flex;
        gap: 15px;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        width: 100%;
    }

    .filter-container select,
    .filter-container button {
        padding: 12px 20px;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: white;
    }

    .filter-container select:focus,
    .filter-container button:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .filter-container button {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        cursor: pointer;
        font-weight: 600;
        transition: transform 0.3s ease;
    }

    .filter-container button:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }

    .attendance-table {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        margin: 25px auto 0 auto;
        max-width: 100%;
    }

    .attendance-table table {
        width: 100%;
        border-collapse: collapse;
        margin: 0 auto;
    }

    .attendance-table th {
        background: linear-gradient(135deg,rgb(230, 233, 233) 0%,rgb(235, 237, 237) 100%);
        color: #4a5568;
        padding: 15px;
        text-align: left;
        font-weight: 600;
    }

    .attendance-table td {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
    }

    .attendance-table tr:hover {
        background: #f8f9fa;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-sang {
        background: #d4edda;
        color: #155724;
    }
    .status-trua {
        background: #fff3cd;
        color: #856404;
    }
    .status-chieu {
        background: #d1ecf1;
        color: #0c5460;
    }
    .status-success {
        background: #d4edda;
        color: #155724;
    }
    .status-warning {
        background: #fff3cd;
        color: #856404;
    }
    .status-info {
        background: #d1ecf1;
        color: #0c5460;
    }
    .status-secondary {
        background: #e2e3e5;
        color: #383d41;
    }

    .confidence-high {
        color: #28a745;
        font-weight: 600;
    }

    .confidence-medium {
        color: #ffc107;
        font-weight: 600;
    }

    .confidence-low {
        color: #dc3545;
        font-weight: 600;
    }

    .method-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.8rem;
        background: #e9ecef;
        color: #495057;
    }

    .attendance-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .attendance-image:hover {
        transform: scale(1.1);
    }

    .no-data {
        text-align: center;
        padding: 40px;
        color: #6c757d;
        font-style: italic;
    }

    .pagination {
        display: flex;
        justify-content: center;
        margin: 20px auto 0 auto;
        gap: 5px;
        max-width: 100%;
    }

    .pagination button {
        padding: 10px 15px;
        border: 2px solid #e9ecef;
        background: white;
        cursor: pointer;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .pagination button:hover {
        background: #f8f9fa;
        border-color: #667eea;
    }

    .pagination button.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: #667eea;
    }

    .pagination button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .image-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.8);
    }

    .image-modal-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        max-width: 90%;
        max-height: 90%;
    }

    .image-modal img {
        width: 100%;
        height: auto;
        border-radius: 8px;
    }

    .close-modal {
        position: absolute;
        top: 10px;
        right: 20px;
        color: white;
        font-size: 30px;
        cursor: pointer;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .attendance-container {
            margin: 10px;
            padding: 15px;
            border-radius: 15px;
        }

        .attendance-header {
            margin: -15px -15px 20px -15px;
            padding: 15px;
            border-radius: 15px 15px 0 0;
        }

        .attendance-header h3 {
            font-size: 1.8rem;
            flex-direction: column;
            gap: 10px;
        }

        .employee-info,
        .filter-section,
        .attendance-table,
        .pagination {
            max-width: 100%;
        }

        .filter-container {
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
        }

        .filter-container select,
        .filter-container button {
            padding: 8px 12px;
            font-size: 0.9rem;
        }

        .attendance-table {
            overflow-x: auto;
        }

        .attendance-table table {
            min-width: 600px;
        }

        .attendance-table th,
        .attendance-table td {
            padding: 8px 4px;
            font-size: 0.8rem;
        }
    }

    /* Tablet Responsive */
    @media (min-width: 769px) and (max-width: 1024px) {
        .filter-container {
            flex-wrap: wrap;
            gap: 12px;
        }
    }

    /* Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .attendance-table {
        animation: fadeInUp 0.6s ease-out;
    }
    </style>
</head>

<body>
    <div class="layout-wrapper">
        <?php include(__DIR__ . '/../includes/sidebar.php'); ?>
        
        <div class="layout-page">
            <div class="content-wrapper">
                <div class="attendance-container">
                    
                    
                  
                    
                    <div class="filter-section">
                        <h4><i class="fas fa-filter"></i> Lọc dữ liệu</h4>
                        <div class="filter-container">
                            <select id="typeFilter">
                                <option value="">Tất cả loại</option>
                                <option value="sang">Điểm danh sáng</option>
                                <option value="trua">Điểm danh trưa</option>
                                <option value="chieu">Điểm danh chiều</option>
                            </select>
                            
                            <select id="methodFilter">
                                <option value="">Tất cả phương thức</option>
                                <option value="guong_mat">Gương mặt</option>
                                <option value="thu_cong">Thủ công</option>
                            </select>
                            
                            <select id="monthFilter" aria-label="Chọn tháng"></select>
                            <select id="yearFilter" aria-label="Chọn năm"></select>
                            
                            <button onclick="filterData()">
                                <i class="fas fa-search"></i> Lọc
                            </button>
                        </div>
                    </div>
                    
                    <div class="attendance-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Ngày</th>
                                    <th>Loại điểm danh</th>
                                    <th>Giờ vào</th>
                                    <th>Giờ trưa</th>
                                    <th>Giờ ra</th>
                                    <th>Trạng thái</th>
                                    <th>Ảnh</th>
                                    <!-- <th>Độ tin cậy</th>
                                    <th>Phương thức</th> -->
                                </tr>
                            </thead>
                            <tbody id="attendanceTableBody">
                                <tr>
                                    <td colspan="9" class="no-data">
                                        <i class="fas fa-spinner fa-spin"></i> Đang tải dữ liệu...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="pagination" id="pagination">
                        <!-- Pagination buttons will be generated here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="image-modal" onclick="closeImageModal()">
        <span class="close-modal">&times;</span>
        <div class="image-modal-content">
            <img id="modalImage" src="" alt="Ảnh điểm danh">
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    let currentPage = 1;
    let totalPages = 1;
    let allData = [];
    let filteredData = [];
    const currentEmployeeId = '<?= $current_employee_id ?>';

    // Load data on page load
    document.addEventListener('DOMContentLoaded', function() {
        initMonthYearFilters();
        loadAttendanceData();
    });

    function initMonthYearFilters() {
        const monthSelect = document.getElementById('monthFilter');
        const yearSelect = document.getElementById('yearFilter');
        
        // Tháng
        const monthPlaceholder = document.createElement('option');
        monthPlaceholder.value = '';
        monthPlaceholder.textContent = 'Tháng';
        monthSelect.appendChild(monthPlaceholder);
        for (let m = 1; m <= 12; m++) {
            const opt = document.createElement('option');
            opt.value = String(m).padStart(2, '0');
            opt.textContent = `Tháng ${m}`;
            monthSelect.appendChild(opt);
        }
        
        // Năm (từ currentYear-5 đến currentYear+1)
        const currentYear = new Date().getFullYear();
        const yearPlaceholder = document.createElement('option');
        yearPlaceholder.value = '';
        yearPlaceholder.textContent = 'Năm';
        yearSelect.appendChild(yearPlaceholder);
        for (let y = currentYear + 1; y >= currentYear - 5; y--) {
            const opt = document.createElement('option');
            opt.value = String(y);
            opt.textContent = String(y);
            yearSelect.appendChild(opt);
        }
    }

    function loadAttendanceData() {
        console.log('Loading data for employee ID:', currentEmployeeId);
        fetch(`../api/get_attendance_history_my.php?employee_id=${currentEmployeeId}`)
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received data:', data);
                if (data.error) {
                    throw new Error(data.message || 'API returned error');
                }
                
                // Chuyển đổi dữ liệu từ lich_su_diem_danh sang format hiển thị
                const mapped = data.map(record => {
                    // Xác định loại điểm danh dựa trên thời gian
                    const time = new Date(record.thoi_gian_diem_danh);
                    const hour = time.getHours();
                    const minute = time.getMinutes();
                    const totalMinutes = hour * 60 + minute;
                    
                    let loaiDiemDanh = record.loai_diem_danh; // 'vao' hoặc 'ra'
                    let loaiThoiGian = '';
                    
                    // Phân loại thời gian dựa trên giờ điểm danh (theo logic chamcong.php)
                    if (totalMinutes >= 450 && totalMinutes <= 510) {
                        // 7:30 - 8:30: Điểm danh sáng
                        loaiThoiGian = 'sang';
                    } else if (totalMinutes >= 690 && totalMinutes <= 780) {
                        // 11:30 - 13:00: Điểm danh trưa
                        loaiThoiGian = 'trua';
                    } else if (totalMinutes >= 781) {
                        // 13:01 trở đi: Điểm danh chiều
                        loaiThoiGian = 'chieu';
                    } else {
                        // 8:30 - 11:30: coi như điểm danh sáng
                        loaiThoiGian = 'sang';
                    }
                    
                    // Debug logging
                    console.log(`Time: ${record.thoi_gian_diem_danh}, TotalMinutes: ${totalMinutes}, LoaiThoiGian: ${loaiThoiGian}`);
                    
                    return {
                        id: record.id,
                        ma_nhan_vien: record.ma_nhan_vien,
                        ngay: record.thoi_gian_diem_danh.split(' ')[0],
                        loai_diem_danh: loaiThoiGian,
                        thoi_gian: record.thoi_gian_diem_danh.split(' ')[1],
                        trang_thai: getAttendanceStatus(loaiThoiGian, totalMinutes),
                        duong_dan_anh: record.duong_dan_anh,
                        do_tin_cay: record.do_tin_cay,
                        phuong_thuc: record.phuong_thuc || 'thu_cong',
                        dia_chi_ip: record.dia_chi_ip,
                        wifi: record.wifi
                    };
                });

                // Gộp dữ liệu theo Ngày + Loại điểm danh (mỗi ca chỉ giữ 1 bản ghi, ưu tiên lần điểm danh đầu tiên)
                const uniqueMap = new Map();
                mapped.forEach(item => {
                    const key = `${item.ngay}-${item.loai_diem_danh}`;
                    if (!uniqueMap.has(key)) {
                        uniqueMap.set(key, item);
                    } else {
                        const existing = uniqueMap.get(key);
                        const existingTime = new Date(`${existing.ngay} ${existing.thoi_gian}`);
                        const currentTime = new Date(`${item.ngay} ${item.thoi_gian}`);
                        // Giữ lại lần điểm danh sớm hơn trong cùng ca
                        if (currentTime < existingTime) {
                            uniqueMap.set(key, item);
                        }
                    }
                });

                allData = Array.from(uniqueMap.values()).sort((a, b) => {
                    const da = new Date(`${a.ngay} ${a.thoi_gian}`);
                    const db = new Date(`${b.ngay} ${b.thoi_gian}`);
                    return db - da; // mới nhất lên trên
                });
                
                filteredData = [...allData];
                displayData();
                updatePagination();
            })
            .catch(error => {
                console.error('Error loading attendance data:', error);
                document.getElementById('attendanceTableBody').innerHTML = 
                    '<tr><td colspan="9" class="no-data">Lỗi tải dữ liệu: ' + error.message + '</td></tr>';
            });
    }

    function displayData() {
        const tbody = document.getElementById('attendanceTableBody');
        const startIndex = (currentPage - 1) * 20;
        const endIndex = startIndex + 20;
        const pageData = filteredData.slice(startIndex, endIndex);

        if (pageData.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" class="no-data">Không có dữ liệu</td></tr>';
            return;
        }

        tbody.innerHTML = pageData.map(record => `
            <tr>
                <td>${record.ngay}</td>
                <td><span class="status-badge status-${record.loai_diem_danh}">${getTypeText(record.loai_diem_danh)}</span></td>
                <td>${record.loai_diem_danh === 'sang' ? record.thoi_gian : '-'}</td>
                <td>${record.loai_diem_danh === 'trua' ? record.thoi_gian : '-'}</td>
                <td>${record.loai_diem_danh === 'chieu' ? record.thoi_gian : '-'}</td>
                <td><span class="status-badge status-${getStatusClass(record.trang_thai)}">${record.trang_thai}</span></td>
                <td>
                    ${record.duong_dan_anh ? 
                        `<img src="${record.duong_dan_anh}" class="attendance-image" onclick="showImageModal('${record.duong_dan_anh}')" alt="Ảnh điểm danh">` : 
                        '<span class="text-muted">Không có</span>'
                    }
                </td>
                <td><span class="confidence-${getConfidenceClass(record.do_tin_cay)}">${record.do_tin_cay ? record.do_tin_cay + '%' : 'N/A'}</span></td>
                <td><span class="method-badge">${getMethodText(record.phuong_thuc)}</span></td>
            </tr>
        `).join('');
    }

    function getConfidenceClass(confidence) {
        if (!confidence) return 'low';
        if (confidence >= 90) return 'high';
        if (confidence >= 70) return 'medium';
        return 'low';
    }

    function getMethodText(method) {
        const methods = {
            'guong_mat': 'Gương mặt',
            'van_tay': 'Vân tay',
            'thu_cong': 'Thủ công'
        };
        return methods[method] || method;
    }

    function getTypeText(type) {
        const types = {
            'sang': 'Điểm danh sáng',
            'trua': 'Điểm danh trưa',
            'chieu': 'Điểm danh chiều'
        };
        return types[type] || type;
    }

    function getStatusClass(status) {
        if (status === 'Đúng giờ') return 'success';
        if (status === 'Đi trễ') return 'warning';
        if (status === 'Ra sớm') return 'info';
        if (status === 'Chưa điểm danh') return 'secondary';
        return 'default';
    }

    function getAttendanceStatus(loaiThoiGian, totalMinutes) {
        switch(loaiThoiGian) {
            case 'sang':
                if (totalMinutes <= 480) return 'Đúng giờ'; // Trước 8:00
                if (totalMinutes <= 510) return 'Đi trễ'; // 8:00-8:30
                return 'Đi trễ';
            case 'trua':
                if (totalMinutes >= 690 && totalMinutes <= 720) return 'Đúng giờ'; // 11:30-12:00
                return 'Đúng giờ';
            case 'chieu':
                // Logic từ chamcong.php:
                // 13:01 - 15:59 (781-959 phút): Điểm danh chiều ĐI TRỄ
                // 16:00 - 17:29 (960-1049 phút): Điểm danh chiều RA SỚM
                if (totalMinutes >= 781 && totalMinutes <= 959) return 'Đi trễ'; // 13:01-15:59
                if (totalMinutes >= 960 && totalMinutes <= 1049) return 'Ra sớm'; // 16:00-17:29
                return 'Đúng giờ';
            default:
                return 'Đúng giờ';
        }
    }

    function formatDateTime(dateTimeStr) {
        const date = new Date(dateTimeStr);
        return date.toLocaleString('vi-VN');
    }

    function filterData() {
        const typeFilter = document.getElementById('typeFilter').value;
        const methodFilter = document.getElementById('methodFilter').value;
        const monthFilter = document.getElementById('monthFilter').value;
        const yearFilter = document.getElementById('yearFilter').value;

        filteredData = allData.filter(record => {
            if (typeFilter && record.loai_diem_danh !== typeFilter) return false;
            if (methodFilter && record.phuong_thuc !== methodFilter) return false;
            
            if (monthFilter || yearFilter) {
                const d = new Date(record.ngay);
                const rMonth = String(d.getMonth() + 1).padStart(2, '0');
                const rYear = String(d.getFullYear());
                if (monthFilter && rMonth !== monthFilter) return false;
                if (yearFilter && rYear !== yearFilter) return false;
            }
            
            return true;
        });

        currentPage = 1;
        displayData();
        updatePagination();
    }

    function updatePagination() {
        const pagination = document.getElementById('pagination');
        totalPages = Math.ceil(filteredData.length / 20);
        
        if (totalPages <= 1) {
            pagination.innerHTML = '';
            return;
        }

        let paginationHTML = '';
        
        // Previous button
        paginationHTML += `<button ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1})">Trước</button>`;
        
        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                paginationHTML += `<button class="${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                paginationHTML += '<span>...</span>';
            }
        }
        
        // Next button
        paginationHTML += `<button ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${currentPage + 1})">Sau</button>`;
        
        pagination.innerHTML = paginationHTML;
    }

    function changePage(page) {
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        displayData();
        updatePagination();
    }

    function showImageModal(imageSrc) {
        document.getElementById('modalImage').src = imageSrc;
        document.getElementById('imageModal').style.display = 'block';
    }

    function closeImageModal() {
        document.getElementById('imageModal').style.display = 'none';
    }

    function exportData() {
        const csvContent = generateCSV(filteredData);
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', `lich_su_diem_danh_${currentEmployeeId}_${new Date().toISOString().split('T')[0]}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function generateCSV(data) {
        const headers = ['Ngày', 'Loại điểm danh', 'Giờ vào', 'Giờ trưa', 'Giờ ra', 'Trạng thái', 'Độ tin cậy', 'Phương thức'];
        const csvRows = [headers.join(',')];
        
        data.forEach(record => {
            const row = [
                record.ngay,
                getTypeText(record.loai_diem_danh),
                record.loai_diem_danh === 'sang' ? record.thoi_gian : '-',
                record.loai_diem_danh === 'trua' ? record.thoi_gian : '-',
                record.loai_diem_danh === 'chieu' ? record.thoi_gian : '-',
                record.trang_thai,
                record.do_tin_cay ? record.do_tin_cay + '%' : 'N/A',
                getMethodText(record.phuong_thuc)
            ];
            csvRows.push(row.map(field => `"${field}"`).join(','));
        });
        
        return csvRows.join('\n');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('imageModal');
        if (event.target === modal) {
            closeImageModal();
        }
    }
    </script>
</body>
</html>
