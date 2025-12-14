<?php
require_once __DIR__ . '/../includes/check_login.php';
include(__DIR__ . '/../includes/header.php');

// Set current page for sidebar
$current_page = 'lich_su_diem_danh.php';
?>

<!DOCTYPE html>
<html lang="vi" class="light-style layout-navbar-fixed layout-menu-fixed">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRM Pro - Lịch sử điểm danh</title>

    <!-- Font Awesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS riêng -->
    <style>
    body {
        font-family: 'Roboto', sans-serif;
        background: #f4f6f9;
        margin: 0;
        padding: 0;
    }
    .main-content {
        margin-left: 240px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    h3 {
        font-size: 26px;
        margin-bottom: 20px;
        color: #333;
        text-align: center;
    }
    .filter-container {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        width: 100%;
        max-width: 1200px;
    }
    .filter-container select,
    .filter-container input,
    .filter-container button {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
        transition: border-color 0.3s;
    }
    .filter-container select:focus,
    .filter-container input:focus,
    .filter-container button:focus {
        border-color: #007bff;
        outline: none;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.2);
    }
    .filter-container button {
        background: #007bff;
        color: #fff;
        border: none;
        cursor: pointer;
    }
    .filter-container button:hover {
        background: #0056b3;
    }
    .filter-container button#exportBtn {
        background: #28a745;
    }
    .filter-container button#exportBtn:hover {
        background: #218838;
    }
    table {
        border-collapse: collapse;
        width: 100%;
        max-width: 1200px;
        margin: 0 auto 20px;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    th, td {
        padding: 14px 16px;
        border-bottom: 1px solid #ddd;
        text-align: left;
        word-wrap: break-word;
    }
    th {
        background: #007bff;
        color: #fff;
        font-weight: 500;
    }
    tr:nth-child(even) {
        background: #f9f9f9;
    }
    tr:hover {
        background: #eef3f7;
    }
    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
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
        padding: 2px 6px;
        border-radius: 8px;
        font-size: 0.7rem;
        background: #e9ecef;
        color: #495057;
    }
     #attendanceTable {
         width: 100%;
         max-width: 1200px;
         margin: 0 auto;
     }
     .attendance-image {
         width: 50px;
         height: 50px;
         object-fit: cover;
         border-radius: 4px;
         cursor: pointer;
         transition: transform 0.2s;
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
        margin-top: 20px;
        width: 100%;
        max-width: 1200px;
    }
    .pagination button {
        margin: 0 5px;
        padding: 8px 12px;
        border: 1px solid #ddd;
        background: white;
        cursor: pointer;
        border-radius: 4px;
    }
    .pagination button:hover {
        background: #f8f9fa;
    }
    .pagination button.active {
        background: #007bff;
        color: white;
        border-color: #007bff;
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
    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
            padding: 10px;
        }
        .filter-container {
            flex-direction: column;
            align-items: stretch;
        }
        table {
            font-size: 0.8rem;
        }
        th, td {
            padding: 8px 4px;
        }
    }
    </style>
</head>

<body>
    <div class="layout-wrapper">
        <?php include(__DIR__ . '/../includes/sidebar.php'); ?>
        
        <div class="layout-page">
            <div class="content-wrapper">
                <div class="main-content">
                    <h3><i class="fas fa-history"></i> Lịch sử điểm danh</h3>
                    
                    <div class="filter-container">
                        <select id="employeeFilter">
                            <option value="">Tất cả nhân viên</option>
                        </select>
                        
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
                        
                        <button id="exportBtn" onclick="exportData()">
                            <i class="fas fa-download"></i> Xuất Excel
                        </button>
                    </div>
                    
                    <div id="attendanceTable">
                        <table>
                            <thead>
                                <tr>
                                    <th>Mã NV</th>
                                    <th>Tên NV</th>
                                    <th>Ngày</th>
                                    <th>Loại điểm danh</th>
                                    <th>Giờ vào</th>
                                    <th>Giờ trưa</th>
                                    <th>Giờ ra</th>
                                    <th>Trạng thái</th>
                                    <th>Ảnh</th>
                                    <th>Độ tin cậy</th>
                                    <th>Phương thức</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTableBody">
                                <tr>
                                    <td colspan="11" class="no-data">
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
    let cauHinhGioLamViec = null; // Lưu cấu hình giờ làm việc

    // Load data on page load
    document.addEventListener('DOMContentLoaded', async function() {
        initMonthYearFilters();
        // Tải cấu hình giờ làm việc trước để sử dụng cho tính trạng thái
        await loadCauHinhGioLamViec();
        loadEmployees();
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

    function loadEmployees() {
        fetch('/doanqlns/index.php/api/nhanvien')
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('employeeFilter');
                data.forEach(emp => {
                    // Lưu tên nhân viên vào cache
                    employeeNames[emp.id_nhan_vien] = emp.ho_ten;
                    
                    const option = document.createElement('option');
                    option.value = emp.id_nhan_vien;
                    option.textContent = `${emp.id_nhan_vien} - ${emp.ho_ten}`;
                    select.appendChild(option);
                });
            })
            .catch(error => console.error('Error loading employees:', error));
    }

    function loadAttendanceData() {
        fetch('/doanqlns/api/get_attendance_history.php')
            .then(response => response.json())
            .then(data => {
                console.log('API Response:', data);
                console.log('Data count:', Array.isArray(data) ? data.length : 'Not array');
                
                if (Array.isArray(data)) {
                    // Chuyển đổi dữ liệu từ lich_su_diem_danh sang format hiển thị
                    const mapped = data.map(record => {
                        // Xác định loại điểm danh dựa trên thời gian - sử dụng logic giống chamcong.php
                        const time = new Date(record.thoi_gian_diem_danh);
                        const hour = time.getHours();
                        const minute = time.getMinutes();
                        const totalMinutes = hour * 60 + minute;
                        
                        // Sử dụng hàm determineAttendanceStatus giống chamcong.php để xác định loại và trạng thái
                        const attendanceInfo = determineAttendanceStatusFromConfig(totalMinutes);
                        const loaiThoiGian = attendanceInfo.type; // 'sang', 'trua', hoặc 'chieu'
                        const trangThai = attendanceInfo.status; // 'Đúng giờ', 'Đi trễ', 'Ra sớm'
                        const gioField = attendanceInfo.gio; // 'gio_vao', 'gio_trua', hoặc 'gio_ra'
                        
                        // Lấy thời gian từ record
                        const thoiGianStr = record.thoi_gian_diem_danh.split(' ')[1] || '';
                        
                        // Xác định giá trị cho các cột Giờ vào, Giờ trưa, Giờ ra
                        let gioVao = '-';
                        let gioTrua = '-';
                        let gioRa = '-';
                        
                        if (gioField === 'gio_vao') {
                            gioVao = thoiGianStr;
                        } else if (gioField === 'gio_trua') {
                            gioTrua = thoiGianStr;
                        } else if (gioField === 'gio_ra') {
                            gioRa = thoiGianStr;
                        }
                        
                        // Debug logging
                        console.log(`Time: ${record.thoi_gian_diem_danh}, TotalMinutes: ${totalMinutes}, LoaiThoiGian: ${loaiThoiGian}, TrangThai: ${trangThai}, GioField: ${gioField}`);
                        
                        return {
                            id: record.id,
                            ma_nhan_vien: record.ma_nhan_vien,
                            ho_ten: getEmployeeName(record.ma_nhan_vien),
                            ngay: record.thoi_gian_diem_danh.split(' ')[0],
                            loai_diem_danh: loaiThoiGian,
                            thoi_gian: thoiGianStr,
                            gio_vao: gioVao,
                            gio_trua: gioTrua,
                            gio_ra: gioRa,
                            trang_thai: trangThai,
                            duong_dan_anh: record.duong_dan_anh,
                            do_tin_cay: record.do_tin_cay,
                            phuong_thuc: record.phuong_thuc || 'thu_cong',
                            dia_chi_ip: record.dia_chi_ip,
                            wifi: record.wifi
                        };
                    });

                    // Gộp dữ liệu theo Mã NV + Ngày + Loại điểm danh (mỗi ca của mỗi nhân viên chỉ giữ 1 bản ghi),
                    // ưu tiên lần điểm danh đầu tiên (thời gian sớm hơn)
                    const uniqueMap = new Map();
                    mapped.forEach(item => {
                        const key = `${item.ma_nhan_vien}-${item.ngay}-${item.loai_diem_danh}`;
                        if (!uniqueMap.has(key)) {
                            uniqueMap.set(key, item);
                        } else {
                            const existing = uniqueMap.get(key);
                            const existingTime = new Date(`${existing.ngay} ${existing.thoi_gian}`);
                            const currentTime = new Date(`${item.ngay} ${item.thoi_gian}`);
                            if (currentTime < existingTime) {
                                uniqueMap.set(key, item);
                            }
                        }
                    });

                    allData = Array.from(uniqueMap.values()).sort((a, b) => {
                        const da = new Date(`${a.ngay} ${a.thoi_gian}`);
                        const db = new Date(`${b.ngay} ${b.thoi_gian}`);
                        return db - da; // Bản ghi mới hơn lên trên
                    });
                    
                    filteredData = [...allData];
                    displayData();
                    updatePagination();
                } else {
                    console.error('Dữ liệu không đúng format:', data);
                    document.getElementById('attendanceTableBody').innerHTML = 
                        '<tr><td colspan="11" class="no-data">Dữ liệu không đúng format</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error loading attendance data:', error);
                document.getElementById('attendanceTableBody').innerHTML = 
                    '<tr><td colspan="11" class="no-data">Lỗi tải dữ liệu</td></tr>';
            });
    }

    function displayData() {
        const tbody = document.getElementById('attendanceTableBody');
        const startIndex = (currentPage - 1) * 20;
        const endIndex = startIndex + 20;
        const pageData = filteredData.slice(startIndex, endIndex);

        if (pageData.length === 0) {
            tbody.innerHTML = '<tr><td colspan="11" class="no-data">Không có dữ liệu</td></tr>';
            return;
        }

        tbody.innerHTML = pageData.map(record => `
            <tr>
                <td>${record.ma_nhan_vien}</td>
                <td>${record.ho_ten}</td>
                <td>${record.ngay}</td>
                <td><span class="status-badge status-${record.loai_diem_danh}">${getTypeText(record.loai_diem_danh)}</span></td>
                <td>${record.gio_vao || '-'}</td>
                <td>${record.gio_trua || '-'}</td>
                <td>${record.gio_ra || '-'}</td>
                <td><span class="status-badge status-${getStatusClass(record.trang_thai)}">${record.trang_thai}</span></td>
                <td>
                    ${record.duong_dan_anh ? 
                        `<img src="${record.duong_dan_anh}" class="attendance-image" onclick="showImageModal('${record.duong_dan_anh}')" alt="Ảnh điểm danh" onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'50\' height=\'50\'%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\'%3EẢnh%3C/text%3E%3C/svg%3E'; this.style.cursor='default'; this.onclick=null;">` : 
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

    // Cache tên nhân viên
    let employeeNames = {};
    
    function getEmployeeName(maNhanVien) {
        if (employeeNames[maNhanVien]) {
            return employeeNames[maNhanVien];
        }
        return `NV${maNhanVien}`; // Fallback nếu chưa load được tên
    }
    
    // Hàm chuyển đổi thời gian từ "HH:MM:SS" hoặc "HH:MM" sang phút
    function timeToMinutes(timeStr) {
        if (!timeStr) return null;
        const parts = timeStr.split(':');
        if (parts.length < 2) return null;
        const hours = parseInt(parts[0], 10);
        const minutes = parseInt(parts[1], 10);
        return hours * 60 + minutes;
    }

    // Hàm lấy cấu hình giờ làm việc từ API
    async function loadCauHinhGioLamViec() {
        try {
            const response = await fetch('/doanqlns/index.php/api/cau-hinh-gio-lam-viec/hien-tai');
            if (!response.ok) {
                console.warn('Không thể tải cấu hình giờ làm việc, sử dụng giá trị mặc định');
                return null;
            }
            const result = await response.json();
            if (result.success && result.data) {
                cauHinhGioLamViec = result.data;
                console.log('Đã tải cấu hình giờ làm việc:', cauHinhGioLamViec);
                return cauHinhGioLamViec;
            } else {
                console.warn('Không tìm thấy cấu hình giờ làm việc, sử dụng giá trị mặc định');
                return null;
            }
        } catch (error) {
            console.error('Lỗi khi tải cấu hình giờ làm việc:', error);
            return null;
        }
    }

    // Hàm xác định loại điểm danh và trạng thái dựa trên cấu hình giờ làm việc (giống chamcong.php)
    function determineAttendanceStatusFromConfig(totalMinutes) {
        // Nếu chưa có cấu hình, sử dụng giá trị mặc định
        if (!cauHinhGioLamViec) {
            return determineAttendanceStatusDefault(totalMinutes);
        }

        // Chuyển đổi thời gian từ cấu hình sang phút
        const gioSangBatDau = timeToMinutes(cauHinhGioLamViec.gio_sang_bat_dau);
        const gioSangKetThuc = timeToMinutes(cauHinhGioLamViec.gio_sang_ket_thuc);
        const gioSangTreBatDau = timeToMinutes(cauHinhGioLamViec.gio_sang_tre_bat_dau);
        const gioSangTreKetThuc = timeToMinutes(cauHinhGioLamViec.gio_sang_tre_ket_thuc);

        const gioTruaBatDau = timeToMinutes(cauHinhGioLamViec.gio_trua_bat_dau);
        const gioTruaKetThuc = timeToMinutes(cauHinhGioLamViec.gio_trua_ket_thuc);
        const gioTruaTreBatDau = timeToMinutes(cauHinhGioLamViec.gio_trua_tre_bat_dau);
        const gioTruaTreKetThuc = timeToMinutes(cauHinhGioLamViec.gio_trua_tre_ket_thuc);

        const gioChieuRaSomBatDau = timeToMinutes(cauHinhGioLamViec.gio_chieu_ra_som_bat_dau);
        const gioChieuRaSomKetThuc = timeToMinutes(cauHinhGioLamViec.gio_chieu_ra_som_ket_thuc);
        const gioChieuBatDau = timeToMinutes(cauHinhGioLamViec.gio_chieu_bat_dau);
        const gioChieuKetThuc = timeToMinutes(cauHinhGioLamViec.gio_chieu_ket_thuc);

        // Kiểm tra điểm danh sáng - Đúng giờ
        if (gioSangBatDau !== null && gioSangKetThuc !== null && 
            totalMinutes >= gioSangBatDau && totalMinutes <= gioSangKetThuc) {
            return { type: 'sang', status: 'Đúng giờ', gio: 'gio_vao' };
        }
        // Kiểm tra điểm danh sáng - Đi trễ
        if (gioSangTreBatDau !== null && gioSangTreKetThuc !== null && 
            totalMinutes >= gioSangTreBatDau && totalMinutes <= gioSangTreKetThuc) {
            return { type: 'sang', status: 'Đi trễ', gio: 'gio_vao' };
        }

        // Kiểm tra điểm danh trưa - Đúng giờ
        if (gioTruaBatDau !== null && gioTruaKetThuc !== null && 
            totalMinutes >= gioTruaBatDau && totalMinutes <= gioTruaKetThuc) {
            return { type: 'trua', status: 'Đúng giờ', gio: 'gio_trua' };
        }
        // Kiểm tra điểm danh trưa - Đi trễ
        if (gioTruaTreBatDau !== null && gioTruaTreKetThuc !== null && 
            totalMinutes >= gioTruaTreBatDau && totalMinutes <= gioTruaTreKetThuc) {
            return { type: 'trua', status: 'Đi trễ', gio: 'gio_trua' };
        }

        // Kiểm tra điểm danh chiều - Ra sớm
        if (gioChieuRaSomBatDau !== null && gioChieuRaSomKetThuc !== null && 
            totalMinutes >= gioChieuRaSomBatDau && totalMinutes <= gioChieuRaSomKetThuc) {
            return { type: 'chieu', status: 'Ra sớm', gio: 'gio_ra' };
        }
        // Kiểm tra điểm danh chiều - Đúng giờ
        if (gioChieuBatDau !== null && gioChieuKetThuc !== null && 
            totalMinutes >= gioChieuBatDau && totalMinutes <= gioChieuKetThuc) {
            return { type: 'chieu', status: 'Đúng giờ', gio: 'gio_ra' };
        }

        // Nếu không khớp với bất kỳ khoảng thời gian nào, mặc định là điểm danh sáng
        return { type: 'sang', status: 'Đúng giờ', gio: 'gio_vao' };
    }

    // Hàm xác định trạng thái mặc định (fallback khi không có cấu hình) - giống chamcong.php
    function determineAttendanceStatusDefault(totalMinutes) {
        // Điểm danh sáng (7:30 - 8:15) = 450 - 495 phút
        if (totalMinutes >= 450 && totalMinutes <= 495) {
            return { type: 'sang', status: 'Đúng giờ', gio: 'gio_vao' };
        }
        // Điểm danh sáng đi trễ (8:16 - 11:29) = 496 - 689 phút
        if (totalMinutes >= 496 && totalMinutes <= 689) {
            return { type: 'sang', status: 'Đi trễ', gio: 'gio_vao' };
        }
        // Điểm danh trưa đúng giờ (11:30 - 13:00) = 690 - 780 phút
        if (totalMinutes >= 690 && totalMinutes <= 780) {
            return { type: 'trua', status: 'Đúng giờ', gio: 'gio_trua' };
        }
        // Điểm danh chiều đi trễ (13:01 - 15:59) = 781 - 959 phút
        if (totalMinutes >= 781 && totalMinutes <= 959) {
            return { type: 'chieu', status: 'Đi trễ', gio: 'gio_ra' };
        }
        // Điểm danh chiều ra sớm (16:00 - 17:29) = 960 - 1049 phút
        if (totalMinutes >= 960 && totalMinutes <= 1049) {
            return { type: 'chieu', status: 'Ra sớm', gio: 'gio_ra' };
        }
        // Điểm danh chiều đúng giờ (17:30 - 21:00) = 1050 - 1260 phút
        if (totalMinutes >= 1050 && totalMinutes <= 1260) {
            return { type: 'chieu', status: 'Đúng giờ', gio: 'gio_ra' };
        }
        // Điểm danh ngoài giờ - coi như điểm danh sáng
        return { type: 'sang', status: 'Đúng giờ', gio: 'gio_vao' };
    }

    function formatDateTime(dateTimeStr) {
        const date = new Date(dateTimeStr);
        return date.toLocaleString('vi-VN');
    }

    function filterData() {
        const employeeFilter = document.getElementById('employeeFilter').value;
        const typeFilter = document.getElementById('typeFilter').value;
        const methodFilter = document.getElementById('methodFilter').value;
        const monthFilter = document.getElementById('monthFilter').value;
        const yearFilter = document.getElementById('yearFilter').value;

        filteredData = allData.filter(record => {
            if (employeeFilter && record.ma_nhan_vien !== employeeFilter) return false;
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
        link.setAttribute('download', `lich_su_diem_danh_${new Date().toISOString().split('T')[0]}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function generateCSV(data) {
        const headers = ['Mã NV', 'Thời gian', 'Loại', 'Độ tin cậy', 'Phương thức', 'IP', 'WiFi'];
        const csvRows = [headers.join(',')];
        
        data.forEach(record => {
            const row = [
                record.ma_nhan_vien,
                formatDateTime(record.thoi_gian_diem_danh),
                record.loai_diem_danh === 'vao' ? 'Vào' : 'Ra',
                record.do_tin_cay ? record.do_tin_cay + '%' : 'N/A',
                getMethodText(record.phuong_thuc),
                record.dia_chi_ip || 'N/A',
                record.wifi || 'N/A'
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
