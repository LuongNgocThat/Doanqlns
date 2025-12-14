<?php
require_once __DIR__ . '/../includes/check_login.php';
include(__DIR__ . '/../includes/header.php');
?>

<!DOCTYPE html>
<html lang="vi" class="light-style layout-navbar-fixed layout-menu-fixed">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRM Pro - Bảng Chấm Công</title>

    <!-- Font Awesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- CSS chính -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/stylechamcong.css">
   <style>
    body {
        background: var(--bs-body-bg);
    }

    .layout-wrapper {
        display: flex;
        min-height: 100vh;
    }

    .layout-page {
        padding-left: 260px;
        width: 100%;
        padding-top: 1rem;
    }

    .content-wrapper {
        padding: 0 1.5rem 1.5rem;
    }

    @media (max-width: 1199.98px) {
        .layout-page {
            padding-left: 0;
        }
    }

    /* Các kiểu hiện có (giữ nguyên, chỉ thêm hoặc chỉnh sửa phần cần thiết) */
    .status.nghile {
        background-color: rgb(66, 157, 227); /* Màu xanh dương cho Nghỉ Lễ */
    }
    .status.phepnam {
        background-color: #8e44ad; /* Màu tím nhạt cho Phép Năm */
    }

    /* CSS cho cột Kết Luận */
    .conclusion {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        background-color: #e3f2fd;
        color: #1976d2;
        border: 1px solid #bbdefb;
    }

    .name-link,
    .name-link:hover {
        text-decoration: none;
        color: #007bff;
    }

    .btn-diemdanh, .btn-edit, .btn-delete, .btn-export {
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        color: white;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: opacity 0.2s, transform 0.2s;
    }

    .btn-diemdanh {
        background-color: #4CAF50;
    }

    .btn-diemdanh.btn-holiday {
        margin-left: 10px; /* Tạo khoảng cách giữa nút Điểm Danh và Điểm Danh Nghỉ Lễ */
        background-color: #007bff; /* Màu xanh dương để phân biệt */
    }

    .btn-diemdanh:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    .btn-edit {
        background-color: #007bff;
        margin-right: 5px;
    }

    .btn-delete {
        background-color: #dc3545;
    }

    .btn-export {
        background: #007bff;
    }

    .btn-export:hover {
        opacity: 0.9;
    }

    /* Dropdown styles */
    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-toggle {
        position: relative;
    }

    .dropdown-toggle .fa-chevron-down {
        margin-left: 5px;
        transition: transform 0.3s ease;
    }

    .dropdown.active .fa-chevron-down {
        transform: rotate(180deg);
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        z-index: 1000;
        min-width: 180px;
    }

    .dropdown-menu a {
        display: block;
        padding: 10px 15px;
        text-decoration: none;
        color: #333;
        border-bottom: 1px solid #eee;
        transition: background-color 0.2s;
    }

    .dropdown-menu a:last-child {
        border-bottom: none;
    }

    .dropdown-menu a:hover {
        background-color: #f8f9fa;
        color: #007bff;
    }

    .dropdown-menu a i {
        margin-right: 8px;
        width: 16px;
    }

    .status {
        padding: 4px 8px;
        border-radius: 4px;
        color: white;
        font-size: 12px;
    }

    .status.dadiemdanh { background-color: #4CAF50; }
    .status.ditre { background-color: #FF9800; }
    .status.cophep { background-color: #2196F3; }
    .status.khongphep { background-color: #f44336; }
    .status.nghiviec { background-color: #9E9E9E; }
    .status.chuadiemdanh { background-color: #B0BEC5; }

    .modal-content {
        max-width: 40%;
        overflow-x: auto;
    }

    .attendance-table th, .attendance-table td {
        min-width: 60px;
    }

    .current-day-column, .current-day-cell {
        background-color: #e0f7fa;
    }

    .day-header {
        font-weight: bold;
    }

    .weekday-header {
        font-size: 12px;
        color: #555;
    }

    @media (max-width: 768px) {
        th, td {
            font-size: 12px;
            padding: 6px;
        }

        .btn-diemdanh, .btn-edit, .btn-delete, .btn-export {
            padding: 4px 8px;
            font-size: 12px;
        }
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

    /* Modal Container */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        overflow: auto;
        animation: fadeIn 0.3s;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Modal Content */
    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 0;
        border: none;
        border-radius: 8px;
        width: 90%;
        max-width: 1300px; /* Giảm max-width để modal nhỏ gọn hơn */
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        overflow: hidden;
    }

    /* Modal Header */
    .modal-header {
        background: linear-gradient(90deg, #007bff, #0056b3);
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-title {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .modal-close {
        color: white;
        font-size: 1.8rem;
        font-weight: bold;
        background: none;
        border: none;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .modal-close:hover {
        transform: scale(1.2);
        color: #f1f1f1;
    }

    /* Modal Body */
    .modal-body {
        padding: 20px;
        max-height: 60vh;
        overflow-y: auto;
    }

    /* Holiday Form Styles */
    .holiday-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .holiday-form .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .holiday-form label {
        font-weight: 500;
        color: #333;
        font-size: 14px;
    }

    .holiday-form input[type="date"] {
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
        transition: border-color 0.3s;
        background-color: #f9f9f9;
    }

    .holiday-form input[type="date"]:focus {
        border-color: #007bff;
        outline: none;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.2);
    }

    /* Modal Footer */
    .modal-footer {
        padding: 15px 20px;
        border-top: 1px solid #e9ecef;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        background: #f8f9fa;
    }

    .btn-close {
        padding: 10px 20px;
        background-color: #dc3545;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        transition: background-color 0.2s;
    }

    .btn-close:hover {
        background-color: #c82333;
    }

    .holiday-form .btn-diemdanh {
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        transition: background-color 0.2s;
    }

    .holiday-form .btn-diemdanh:hover {
        background-color: #0056b3;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .modal-content {
            width: 95%;
            max-width: 400px;
        }

        .holiday-form .form-group {
            gap: 5px;
        }

        .holiday-form input[type="date"] {
            font-size: 14px;
            padding: 8px 10px;
        }

        .modal-footer {
            flex-direction: column;
            gap: 8px;
        }

        .btn-close, .holiday-form .btn-diemdanh {
            width: 100%;
            padding: 10px;
        }

        .action-container {
            flex-direction: column;
            gap: 10px;
        }

        .btn-diemdanh.btn-holiday {
            margin-left: 0; /* Bỏ margin-left trên mobile */
        }
    }

    /* Action Container */
    .action-container {
        display: flex;
        gap: 10px; /* Tạo khoảng cách giữa các nút */
        margin-bottom: 20px;
        align-items: center;
    }
 
    /* CSS cho modal Chỉnh Sửa Chấm Công */
    .form-section {
        margin-bottom: 25px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid #007bff;
    }

    .form-section h3 {
        margin: 0 0 15px 0;
        color: #007bff;
        font-size: 1.1rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .time-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-top: 15px;
    }

    .time-column {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .time-column:hover {
        border-color: #007bff;
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.1);
    }

    .time-header {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
    }

    .time-header i {
        font-size: 1.5rem;
        color: #007bff;
    }

    .time-header span {
        font-weight: 600;
        color: #495057;
        font-size: 0.9rem;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
        color: #495057;
        font-size: 0.9rem;
    }

    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 0.9rem;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-control:focus {
        border-color: #007bff;
        outline: none;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
    }

    .form-control.readonly {
        background-color: #f8f9fa;
        color: #6c757d;
        cursor: not-allowed;
    }

    .status-summary {
        background: linear-gradient(135deg, #28a745, #20c997) !important;
        color: white !important;
        font-weight: bold;
        text-align: center;
        font-size: 1rem;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid #e9ecef;
    }

    .btn-cancel, .btn-save {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .btn-cancel {
        background-color: #6c757d;
        color: white;
    }

    .btn-cancel:hover {
        background-color: #5a6268;
        transform: translateY(-1px);
    }

    .btn-save {
        background-color: #007bff;
        color: white;
    }

    .btn-save:hover {
        background-color: #0056b3;
        transform: translateY(-1px);
    }

    /* Responsive cho modal */
    @media (max-width: 768px) {
        .time-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }
        
        .form-row {
            grid-template-columns: 1fr;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn-cancel, .btn-save {
            width: 100%;
            justify-content: center;
        }
    }
</style>
</head>

<body>
    <div class="layout-wrapper">
        <?php include('../includes/sidebar.php'); ?>
        <div class="layout-page">
            <div class="content-wrapper">
        <h3>Bảng Chấm Công</h3>

        <!-- Nút Điểm Danh -->
 <!-- Trong action-container -->
<div class="action-container">
    <?php if (isset($_SESSION['quyen_them']) && $_SESSION['quyen_them']): ?>
        <div class="dropdown">
            <button class="btn-diemdanh dropdown-toggle" onclick="toggleDropdown()">
                <i class="fas fa-check-circle"></i> Điểm Danh <i class="fas fa-chevron-down"></i>
        </button>
            <div class="dropdown-menu" id="diemDanhDropdown">
                <a href="#" onclick="showDiemDanhModal(); closeDropdown();">
                    <i class="fas fa-sun"></i> Điểm Danh Sáng
                </a>
                <a href="#" onclick="showDiemDanhTruaModal(); closeDropdown();">
                    <i class="fas fa-sun"></i> Điểm Danh Trưa
                </a>
                <a href="#" onclick="showDiemDanhChieuModal(); closeDropdown();">
                    <i class="fas fa-moon"></i> Điểm Danh Chiều
                </a>
            </div>
        </div>
        <button class="btn-diemdanh btn-holiday" onclick="showHolidayModal()">
            <i class="fas fa-calendar-alt"></i> Điểm Danh Nghỉ Lễ
        </button>
        <button class="btn-diemdanh" style="background-color:#6f42c1" onclick="showFaceModal()">
            <i class="fas fa-camera"></i> Điểm Danh Bằng Gương Mặt
        </button>
    <?php endif; ?>
</div>

<!-- Modal Điểm Danh Nghỉ Lễ -->
<div id="holidayModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Điểm Danh Nghỉ Lễ</h2>
            <button class="modal-close" onclick="closeHolidayModal()" aria-label="Đóng modal">×</button>
        </div>
        <div class="modal-body">
            <form id="holidayForm" class="holiday-form">
                <div class="form-group">
                    <label for="holidayStartDate">Ngày Bắt Đầu</label>
                    <input type="date" id="holidayStartDate" required>
                </div>
                <div class="form-group">
                    <label for="holidayEndDate">Ngày Kết Thúc</label>
                    <input type="date" id="holidayEndDate" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-close" onclick="closeHolidayModal()">Hủy</button>
                    <button type="submit" class="btn-diemdanh">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>
        
        <!-- Modal Điểm Danh Bằng Gương Mặt -->
        <div id="faceModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Điểm Danh Bằng Gương Mặt</h2>
                    <button class="modal-close" onclick="closeFaceModal()" aria-label="Đóng modal">×</button>
                </div>
                <div class="modal-body" style="padding:0; height:80vh;">
                    <iframe id="faceIframe" src="http://localhost:5001/" style="width:100%; height:100%; border:0;" allow="camera; microphone"></iframe>
                </div>
                <div class="modal-footer">
                    <button class="btn-close" onclick="closeFaceModal()">Đóng</button>
                </div>
            </div>
        </div>
        <!-- Điều hướng ngày -->
        <div class="date-navigation">
            <button onclick="changeDate(-1)" aria-label="Ngày trước"><i class="fas fa-chevron-left"></i></button>
            <span id="selectedDate"></span>
            <button onclick="changeDate(1)" aria-label="Ngày sau"><i class="fas fa-chevron-right"></i></button>
        </div>

        <!-- Bảng chấm công chính -->
        <table>
            <thead>
                <tr>
                    <th>ID Chấm Công</th>
                    <th>Nhân Viên</th>
                    <th>Ngày Công</th>
                    <th>Giờ Vào</th>
                    <th>Giờ Trưa</th>
                    <th>Giờ Ra</th>
                    <th>Trạng Thái</th>
                    <th>Kết Luận</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody id="chamCongTableBody">
                <tr><td colspan="9">Chưa có dữ liệu. Vui lòng điểm danh để hiển thị.</td></tr>
            </tbody>
        </table>

        <!-- Modal Điểm Danh Sáng -->
        <div id="diemDanhModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Điểm Danh Sáng</h2>
                    <button class="modal-close" onclick="closeDiemDanhModal()" aria-label="Đóng modal">×</button>
                </div>
                <div class="modal-body">
                    <div class="filter-container">
                        <select id="selectMonth" onchange="updateAttendanceTable()" aria-label="Chọn tháng">
                            <option value="1">Tháng 1</option>
                            <option value="2">Tháng 2</option>
                            <option value="3">Tháng 3</option>
                            <option value="4">Tháng 4</option>
                            <option value="5" selected>Tháng 5</option>
                            <option value="6">Tháng 6</option>
                            <option value="7">Tháng 7</option>
                            <option value="8">Tháng 8</option>
                            <option value="9">Tháng 9</option>
                            <option value="10">Tháng 10</option>
                            <option value="11">Tháng 11</option>
                            <option value="12">Tháng 12</option>
                        </select>
                        <input type="number" id="selectYear" min="2000" max="2100" onchange="updateAttendanceTable()" aria-label="Nhập năm"/>
                        <select id="selectPhongBan" class="filter-select" onchange="updateAttendanceTable()" aria-label="Chọn phòng ban">
                            <option value="">Tất cả phòng ban</option>
                        </select>
                        <input type="text" id="searchName" class="search-input" placeholder="Tìm kiếm theo tên..." oninput="updateAttendanceTable()" aria-label="Tìm kiếm theo tên"/>
                        <button class="btn-export" id="exportExcelBtn">
                            <i class="fas fa-file-excel"></i> Xuất Excel
                        </button>
                    </div>
                    <div class="attendance-table-container">
                        <table class="attendance-table" id="attendanceTable">
                            <thead id="attendanceTableHead"></thead>
                            <tbody id="attendanceTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

<!-- Modal Điểm Danh Chiều -->
        <div id="diemDanhChieuModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Điểm Danh Chiều</h2>
                    <button class="modal-close" onclick="closeDiemDanhChieuModal()" aria-label="Đóng modal">×</button>
                </div>
                <div class="modal-body">
                    <div class="filter-container">
                        <select id="selectMonthChieu" onchange="updateAttendanceTableChieu()" aria-label="Chọn tháng">
                            <option value="1">Tháng 1</option>
                            <option value="2">Tháng 2</option>
                            <option value="3">Tháng 3</option>
                            <option value="4">Tháng 4</option>
                            <option value="5" selected>Tháng 5</option>
                            <option value="6">Tháng 6</option>
                            <option value="7">Tháng 7</option>
                            <option value="8">Tháng 8</option>
                            <option value="9">Tháng 9</option>
                            <option value="10">Tháng 10</option>
                            <option value="11">Tháng 11</option>
                            <option value="12">Tháng 12</option>
                        </select>
                        <input type="number" id="selectYearChieu" min="2000" max="2100" onchange="updateAttendanceTableChieu()" aria-label="Nhập năm"/>
                        <select id="selectPhongBanChieu" class="filter-select" onchange="updateAttendanceTableChieu()" aria-label="Chọn phòng ban">
                            <option value="">Tất cả phòng ban</option>
                        </select>
                        <input type="text" id="searchNameChieu" class="search-input" placeholder="Tìm kiếm theo tên..." oninput="updateAttendanceTableChieu()" aria-label="Tìm kiếm theo tên"/>
                        <button class="btn-export" id="exportExcelBtnChieu">
                            <i class="fas fa-file-excel"></i> Xuất Excel
                        </button>
                    </div>
                    <div class="attendance-table-container">
                        <table class="attendance-table" id="attendanceTableChieu">
                            <thead id="attendanceTableHeadChieu"></thead>
                            <tbody id="attendanceTableBodyChieu"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Điểm Danh Trưa -->
        <div id="diemDanhTruaModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Điểm Danh Trưa</h2>
                    <button class="modal-close" onclick="closeDiemDanhTruaModal()" aria-label="Đóng modal">×</button>
                </div>
                <div class="modal-body">
                    <div class="filter-container">
                        <select id="selectMonthTrua" onchange="updateAttendanceTableTrua()" aria-label="Chọn tháng">
                            <option value="1">Tháng 1</option>
                            <option value="2">Tháng 2</option>
                            <option value="3">Tháng 3</option>
                            <option value="4">Tháng 4</option>
                            <option value="5" selected>Tháng 5</option>
                            <option value="6">Tháng 6</option>
                            <option value="7">Tháng 7</option>
                            <option value="8">Tháng 8</option>
                            <option value="9">Tháng 9</option>
                            <option value="10">Tháng 10</option>
                            <option value="11">Tháng 11</option>
                            <option value="12">Tháng 12</option>
                        </select>
                        <input type="number" id="selectYearTrua" min="2000" max="2100" onchange="updateAttendanceTableTrua()" aria-label="Nhập năm"/>
                        <select id="selectPhongBanTrua" class="filter-select" onchange="updateAttendanceTableTrua()" aria-label="Chọn phòng ban">
                            <option value="">Tất cả phòng ban</option>
                        </select>
                        <input type="text" id="searchNameTrua" class="search-input" placeholder="Tìm kiếm theo tên..." oninput="updateAttendanceTableTrua()" aria-label="Tìm kiếm theo tên"/>
                        <button class="btn-export" id="exportExcelBtnTrua">
                            <i class="fas fa-file-excel"></i> Xuất Excel
                        </button>
                    </div>
                    <div class="table-container">
                        <table class="attendance-table" id="attendanceTableTrua">
                            <thead id="attendanceTableHeadTrua"></thead>
                            <tbody id="attendanceTableBodyTrua"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Chỉnh Sửa Chấm Công -->
        <div id="editChamCongModal" class="modal">
            <div class="modal-content" style="max-width: 800px;">
                <div class="modal-header">
                    <h2 class="modal-title">Chỉnh Sửa Chấm Công</h2>
                    <button class="modal-close" onclick="closeEditChamCongModal()" aria-label="Đóng modal">×</button>
                </div>
                <div class="modal-body">
                    <form id="editChamCongForm">
                        <input type="hidden" id="editIdChamCong">
                        <input type="hidden" id="editIdNhanVien">
                        
                        <!-- Thông tin cơ bản -->
                        <div class="form-section">
                            <h3><i class="fas fa-user"></i> Thông Tin Nhân Viên</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="editHoTen">Nhân Viên</label>
                                    <input type="text" id="editHoTen" readonly class="form-control readonly">
                                </div>
                                <div class="form-group">
                                    <label for="editNgayLamViec">Ngày Làm Việc</label>
                                    <input type="date" id="editNgayLamViec" required class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- Thời gian điểm danh -->
                        <div class="form-section">
                            <h3><i class="fas fa-clock"></i> Thời Gian Điểm Danh</h3>
                            <div class="time-grid">
                                <div class="time-column">
                                    <div class="time-header">
                                        <i class="fas fa-sun"></i>
                                        <span>Giờ Vào</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="editGioVao">Thời gian</label>
                                        <input type="time" id="editGioVao" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="editTrangThaiSang">Trạng thái</label>
                                        <select id="editTrangThaiSang" class="form-control">
                                            <option value="">Chưa điểm danh</option>
                                            <option value="Đúng giờ">Đúng giờ</option>
                                            <option value="Đi trễ">Đi trễ</option>
                                            <option value="Ra sớm">Ra sớm</option>
                                            <option value="Có phép">Có phép</option>
                                            <option value="Phép Năm">Phép Năm</option>
                                            <option value="Nghỉ Lễ">Nghỉ Lễ</option>
                                            <option value="Nghỉ nữa buổi">Nghỉ nữa buổi</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="time-column">
                                    <div class="time-header">
                                        <i class="fas fa-sun"></i>
                                        <span>Giờ Trưa</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="editGioTrua">Thời gian</label>
                                        <input type="time" id="editGioTrua" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="editTrangThaiTrua">Trạng thái</label>
                                        <select id="editTrangThaiTrua" class="form-control">
                                            <option value="">Chưa điểm danh</option>
                                            <option value="Đúng giờ">Đúng giờ</option>
                                            <option value="Đi trễ">Đi trễ</option>
                                            <option value="Ra sớm">Ra sớm</option>
                                            <option value="Có phép">Có phép</option>
                                            <option value="Phép Năm">Phép Năm</option>
                                            <option value="Nghỉ Lễ">Nghỉ Lễ</option>
                                            <option value="Nghỉ nữa buổi">Nghỉ nữa buổi</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="time-column">
                                    <div class="time-header">
                                        <i class="fas fa-moon"></i>
                                        <span>Giờ Ra</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="editGioRa">Thời gian</label>
                                        <input type="time" id="editGioRa" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="editTrangThaiChieu">Trạng thái</label>
                                        <select id="editTrangThaiChieu" class="form-control">
                                            <option value="">Chưa điểm danh</option>
                                            <option value="Đúng giờ">Đúng giờ</option>
                                            <option value="Đi trễ">Đi trễ</option>
                                            <option value="Ra sớm">Ra sớm</option>
                                            <option value="Có phép">Có phép</option>
                                            <option value="Phép Năm">Phép Năm</option>
                                            <option value="Nghỉ Lễ">Nghỉ Lễ</option>
                                            <option value="Nghỉ nữa buổi">Nghỉ nữa buổi</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Trạng thái tổng hợp -->
                        <div class="form-section">
                            <h3><i class="fas fa-chart-line"></i> Tổng Kết</h3>
                            <div class="form-group">
                                <label for="editTrangThai">Trạng Thái Tổng Hợp</label>
                                <input type="text" id="editTrangThai" readonly class="form-control readonly status-summary">
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn-cancel" onclick="closeEditChamCongModal()">
                                <i class="fas fa-times"></i> Hủy
                            </button>
                            <button type="submit" class="btn-save">
                                <i class="fas fa-save"></i> Lưu Thay Đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Chi Tiết Chấm Công -->
        <div id="detailChamCongModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Chi Tiết Nhân Viên</h2>
                    <button class="modal-close" onclick="closeDetailChamCongModal()">×</button>
                </div>
                <div class="modal-body">
                    <div class="section-title">Thông Tin Nhân Viên</div>
                    <div class="info-group">
                        <label>Họ và Tên:</label>
                        <span id="detailHoTen" class="info-value"></span>
                    </div>
                    <div class="info-group">
                        <label>Giới Tính:</label>
                        <span id="detailGioiTinh" class="info-value"></span>
                    </div>
                    <div class="info-group">
                        <label>Ngày Sinh:</label>
                        <span id="detailNgaySinh" class="info-value"></span>
                    </div>
                    <div class="info-group">
                        <label>Email:</label>
                        <span id="detailEmail" class="info-value"></span>
                    </div>
                    <div class="info-group">
                        <label>Số Điện Thoại:</label>
                        <span id="detailSoDienThoai" class="info-value"></span>
                    </div>
                    <div class="info-group">
                        <label>Địa Chỉ:</label>
                        <span id="detailDiaChi" class="info-value"></span>
                    </div>
                    <div class="info-group">
                        <label>Phòng Ban:</label>
                        <span id="detailPhongBan" class="info-value"></span>
                    </div>
                    <div class="info-group">
                        <label>Chức Vụ:</label>
                        <span id="detailChucVu" class="info-value"></span>
                    </div>
                    <div class="info-group">
                        <label>Lương Cơ Bản:</label>
                        <span id="detailLuongCoBanNhanVien" class="info-value"></span>
                    </div>
                    <div class="section-title">Thông Tin Chấm Công</div>
                    <div class="info-group">
                        <label>Số Ngày Công:</label>
                        <span id="detailSoNgayCong" class="info-value"></span>
                    </div>
                    <div class="info-group">
                        <label>Nghỉ Phép:</label>
                        <span id="detailNghiPhep" class="info-value"></span>
                    </div>
                    <div class="info-group">
                        <label>Tổng Công:</label>
                        <span id="detailTongCong" class="info-value"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-close" onclick="closeDetailChamCongModal()">Đóng</button>
                </div>
            </div>
        </div>

        <!-- Loading indicator -->
        <div class="loading" id="loadingIndicator"></div>
    </div>

    <script>
        // Biến toàn cục
        let usersData = [];
        let attendanceData = [];
        let phongBanList = [];
        let selectedDate = new Date();
        let cauHinhGioLamViec = null; // Lưu cấu hình giờ làm việc
        const userPermissions = {
            quyen_them: <?php echo isset($_SESSION['quyen_them']) && $_SESSION['quyen_them'] ? 'true' : 'false'; ?>,
            quyen_sua: <?php echo isset($_SESSION['quyen_sua']) && $_SESSION['quyen_sua'] ? 'true' : 'false'; ?>,
            quyen_xoa: <?php echo isset($_SESSION['quyen_xoa']) && $_SESSION['quyen_xoa'] ? 'true' : 'false'; ?>
        };

        // Tham chiếu đến các phần tử DOM
        const chamCongTableBody = document.getElementById("chamCongTableBody");
        const selectedDateElement = document.getElementById("selectedDate");
        const diemDanhModal = document.getElementById("diemDanhModal");
        const editChamCongModal = document.getElementById("editChamCongModal");
        const loadingIndicator = document.getElementById("loadingIndicator");

        // Hàm hiển thị loading
        function showLoading() {
            loadingIndicator.style.display = "flex";
        }

        // Hàm ẩn loading
        function hideLoading() {
            loadingIndicator.style.display = "none";
        }

        // Hàm định dạng số
        function formatNumber(number) {
            if (Number.isInteger(number)) {
                return number.toString();
            }
            return number.toFixed(2);
        }

        // Hàm định dạng tiền tệ
        function formatCurrency(value) {
            if (value == null || value === undefined) return '0';
            return Number(value).toLocaleString('vi-VN', { style: 'currency', currency: 'VND' }).replace('₫', '');
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

        // Hàm xác định trạng thái điểm danh dựa trên cấu hình giờ làm việc
        function determineAttendanceStatus(currentTimeInMinutes) {
            // Nếu chưa có cấu hình, sử dụng giá trị mặc định
            if (!cauHinhGioLamViec) {
                return determineAttendanceStatusDefault(currentTimeInMinutes);
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
                currentTimeInMinutes >= gioSangBatDau && currentTimeInMinutes <= gioSangKetThuc) {
                return { type: 'sang', status: 'Đúng giờ', gio: 'gio_vao' };
            }
            // Kiểm tra điểm danh sáng - Đi trễ
            if (gioSangTreBatDau !== null && gioSangTreKetThuc !== null && 
                currentTimeInMinutes >= gioSangTreBatDau && currentTimeInMinutes <= gioSangTreKetThuc) {
                return { type: 'sang', status: 'Đi trễ', gio: 'gio_vao' };
            }

            // Kiểm tra điểm danh trưa - Đúng giờ
            if (gioTruaBatDau !== null && gioTruaKetThuc !== null && 
                currentTimeInMinutes >= gioTruaBatDau && currentTimeInMinutes <= gioTruaKetThuc) {
                return { type: 'trua', status: 'Đúng giờ', gio: 'gio_trua' };
            }
            // Kiểm tra điểm danh trưa - Đi trễ
            if (gioTruaTreBatDau !== null && gioTruaTreKetThuc !== null && 
                currentTimeInMinutes >= gioTruaTreBatDau && currentTimeInMinutes <= gioTruaTreKetThuc) {
                return { type: 'trua', status: 'Đi trễ', gio: 'gio_trua' };
            }

            // Kiểm tra điểm danh chiều - Ra sớm
            if (gioChieuRaSomBatDau !== null && gioChieuRaSomKetThuc !== null && 
                currentTimeInMinutes >= gioChieuRaSomBatDau && currentTimeInMinutes <= gioChieuRaSomKetThuc) {
                return { type: 'chieu', status: 'Ra sớm', gio: 'gio_ra' };
            }
            // Kiểm tra điểm danh chiều - Đúng giờ
            if (gioChieuBatDau !== null && gioChieuKetThuc !== null && 
                currentTimeInMinutes >= gioChieuBatDau && currentTimeInMinutes <= gioChieuKetThuc) {
                return { type: 'chieu', status: 'Đúng giờ', gio: 'gio_ra' };
            }

            // Nếu không khớp với bất kỳ khoảng thời gian nào, mặc định là điểm danh sáng
            return { type: 'sang', status: 'Đúng giờ', gio: 'gio_vao' };
        }

        // Hàm xác định trạng thái mặc định (fallback khi không có cấu hình)
        function determineAttendanceStatusDefault(currentTimeInMinutes) {
            // Điểm danh sáng (7:30 - 8:15) = 450 - 495 phút
            if (currentTimeInMinutes >= 450 && currentTimeInMinutes <= 495) {
                return { type: 'sang', status: 'Đúng giờ', gio: 'gio_vao' };
            }
            // Điểm danh sáng đi trễ (8:16 - 11:29) = 496 - 689 phút
            if (currentTimeInMinutes >= 496 && currentTimeInMinutes <= 689) {
                return { type: 'sang', status: 'Đi trễ', gio: 'gio_vao' };
            }
            // Điểm danh trưa đúng giờ (11:30 - 13:00) = 690 - 780 phút
            if (currentTimeInMinutes >= 690 && currentTimeInMinutes <= 780) {
                return { type: 'trua', status: 'Đúng giờ', gio: 'gio_trua' };
            }
            // Điểm danh chiều đi trễ (13:01 - 15:59) = 781 - 959 phút
            if (currentTimeInMinutes >= 781 && currentTimeInMinutes <= 959) {
                return { type: 'chieu', status: 'Đi trễ', gio: 'gio_ra' };
            }
            // Điểm danh chiều ra sớm (16:00 - 17:29) = 960 - 1049 phút
            if (currentTimeInMinutes >= 960 && currentTimeInMinutes <= 1049) {
                return { type: 'chieu', status: 'Ra sớm', gio: 'gio_ra' };
            }
            // Điểm danh chiều đúng giờ (17:30 - 21:00) = 1050 - 1260 phút
            if (currentTimeInMinutes >= 1050 && currentTimeInMinutes <= 1260) {
                return { type: 'chieu', status: 'Đúng giờ', gio: 'gio_ra' };
            }
            // Điểm danh ngoài giờ - coi như điểm danh sáng
            return { type: 'sang', status: 'Đúng giờ', gio: 'gio_vao' };
        }

        // Hàm tính lại trạng thái dựa trên thời gian thực tế và cấu hình
        function recalculateStatusFromTime(gioVao, gioTrua, gioRa) {
            let trangThaiSang = null;
            let trangThaiTrua = null;
            let trangThaiChieu = null;

            // Tính trạng thái sáng nếu có giờ vào
            if (gioVao && gioVao !== '-') {
                const gioVaoMinutes = timeToMinutes(gioVao);
                if (gioVaoMinutes !== null) {
                    const sangInfo = determineAttendanceStatus(gioVaoMinutes);
                    if (sangInfo.type === 'sang') {
                        trangThaiSang = sangInfo.status;
                    }
                }
            }

            // Tính trạng thái trưa nếu có giờ trưa
            if (gioTrua && gioTrua !== '-') {
                const gioTruaMinutes = timeToMinutes(gioTrua);
                if (gioTruaMinutes !== null) {
                    const truaInfo = determineAttendanceStatus(gioTruaMinutes);
                    if (truaInfo.type === 'trua') {
                        trangThaiTrua = truaInfo.status;
                    }
                }
            }

            // Tính trạng thái chiều nếu có giờ ra
            if (gioRa && gioRa !== '-') {
                const gioRaMinutes = timeToMinutes(gioRa);
                if (gioRaMinutes !== null) {
                    const chieuInfo = determineAttendanceStatus(gioRaMinutes);
                    if (chieuInfo.type === 'chieu') {
                        trangThaiChieu = chieuInfo.status;
                    }
                }
            }

            // Tính trạng thái tổng hợp từ các trạng thái chi tiết
            return calculateOverallStatus(trangThaiSang, trangThaiTrua, trangThaiChieu);
        }

        // Hàm lấy danh sách phòng ban từ API
        async function loadPhongBanData() {
            try {
                const response = await fetch("http://localhost/doanqlns/index.php/api/phongban");
                if (!response.ok) throw new Error("Lỗi khi tải danh sách phòng ban");
                const data = await response.json();
                phongBanList = data;
                populatePhongBanSelect();
            } catch (error) {
                console.error("Lỗi khi tải danh sách phòng ban:", error);
            }
        }

        // Hàm điền danh sách phòng ban vào select
        function populatePhongBanSelect() {
            // Điền cho modal sáng
            const select = document.getElementById('selectPhongBan');
            select.innerHTML = '<option value="">Tất cả phòng ban</option>';
            phongBanList.forEach(pb => {
                const option = document.createElement('option');
                option.value = pb.ten_phong_ban;
                option.textContent = pb.ten_phong_ban;
                select.appendChild(option);
            });

            // Điền cho modal trưa
            const selectTrua = document.getElementById('selectPhongBanTrua');
            if (selectTrua) {
                selectTrua.innerHTML = '<option value="">Tất cả phòng ban</option>';
                phongBanList.forEach(pb => {
                    const option = document.createElement('option');
                    option.value = pb.ten_phong_ban;
                    option.textContent = pb.ten_phong_ban;
                    selectTrua.appendChild(option);
                });
            }

            // Điền cho modal chiều
            const selectChieu = document.getElementById('selectPhongBanChieu');
            if (selectChieu) {
                selectChieu.innerHTML = '<option value="">Tất cả phòng ban</option>';
                phongBanList.forEach(pb => {
                    const option = document.createElement('option');
                    option.value = pb.ten_phong_ban;
                    option.textContent = pb.ten_phong_ban;
                    selectChieu.appendChild(option);
                });
            }
        }

        // Hàm lấy dữ liệu chấm công từ API
        async function loadAttendanceData() {
            showLoading();
            try {
                const response = await fetch("http://localhost/doanqlns/index.php/api/chamcong");
                if (!response.ok) throw new Error("Lỗi khi tải dữ liệu chấm công: " + response.status);
                const data = await response.json();
                if (!Array.isArray(data)) throw new Error("Dữ liệu chấm công không hợp lệ");
                attendanceData = data;
                console.log("Dữ liệu chấm công:", attendanceData);
                renderChamCongTable(attendanceData);
            } catch (error) {
                console.error("Lỗi khi tải dữ liệu:", error);
                chamCongTableBody.innerHTML = '<tr><td colspan="7">Lỗi khi tải dữ liệu</td></tr>';
            } finally {
                hideLoading();
            }
        }

        // Hàm lấy danh sách nhân viên từ API
        async function loadUsersData() {
            showLoading();
            try {
                const response = await fetch("http://localhost/doanqlns/index.php/api/users");
                if (!response.ok) throw new Error("Lỗi khi tải danh sách nhân viên: " + response.status);
                const data = await response.json();
                if (!Array.isArray(data)) throw new Error("Danh sách nhân viên không hợp lệ");
                usersData = data;
                console.log("Danh sách nhân viên:", usersData);
            } catch (error) {
                console.error("Lỗi khi tải danh sách nhân viên:", error);
            } finally {
                hideLoading();
            }
        }

        // Hàm tính kết luận dựa trên giờ vào, trưa, ra
        function calculateConclusion(gioVao, gioTrua, gioRa) {
            const hasGioVao = gioVao && gioVao !== '-';
            const hasGioTrua = gioTrua && gioTrua !== '-';
            const hasGioRa = gioRa && gioRa !== '-';
            
            // Trường hợp 1: Có đủ 3 giờ - Đi làm đủ
            if (hasGioVao && hasGioTrua && hasGioRa) {
                return 'Đi làm đủ';
            }
            
            // Trường hợp 2: Có giờ vào và trưa, không có giờ ra - Làm sáng, vắng chiều
            if (hasGioVao && hasGioTrua && !hasGioRa) {
                return 'Làm sáng, vắng chiều';
            }
            
            // Trường hợp 3: Không có giờ vào, có trưa và ra - Làm chiều, vắng sáng
            if (!hasGioVao && hasGioTrua && hasGioRa) {
                return 'Làm chiều, vắng sáng';
            }
            
            // Trường hợp 4: Không có giờ nào - Nghỉ cả ngày
            if (!hasGioVao && !hasGioTrua && !hasGioRa) {
                return 'Nghỉ cả ngày';
            }
            
            // Trường hợp 5: Có giờ vào và ra, không có trưa - Nghỉ sớm buổi sáng
            if (hasGioVao && !hasGioTrua && hasGioRa) {
                return 'Nghỉ sớm buổi sáng';
            }
            
            // Trường hợp 6: Có giờ vào, trưa và ra muộn - Có thể tăng ca
            if (hasGioVao && hasGioTrua && hasGioRa) {
                const gioRaTime = new Date(`2000-01-01 ${gioRa}`);
                const gioRaMuon = new Date(`2000-01-01 18:00:00`);
                if (gioRaTime > gioRaMuon) {
                    return 'Có thể tăng ca';
                }
            }
            
            // Trường hợp 7: Chỉ có giờ vào - Chưa hoàn thành
            if (hasGioVao && !hasGioTrua && !hasGioRa) {
                return 'Chưa hoàn thành';
            }
            
            // Trường hợp 8: Chỉ có giờ trưa - Chưa hoàn thành
            if (!hasGioVao && hasGioTrua && !hasGioRa) {
                return 'Chưa hoàn thành';
            }
            
            // Trường hợp 9: Chỉ có giờ ra - Chưa hoàn thành
            if (!hasGioVao && !hasGioTrua && hasGioRa) {
                return 'Chưa hoàn thành';
            }
            
            return 'Chưa xác định';
        }

        // Hàm hiển thị bảng chấm công chính
        function renderChamCongTable(data) {
            const selectedDateStr = `${selectedDate.getFullYear()}-${(selectedDate.getMonth() + 1).toString().padStart(2, '0')}-${selectedDate.getDate().toString().padStart(2, '0')}`;
            selectedDateElement.textContent = `${selectedDate.getDate().toString().padStart(2, '0')}/${(selectedDate.getMonth() + 1).toString().padStart(2, '0')}/${selectedDate.getFullYear()}`;

            const filteredData = data.filter(record => record.ngay_lam_viec === selectedDateStr);
            chamCongTableBody.innerHTML = "";

            // Kiểm tra xem ngày được chọn có phải Chủ nhật không
            const isSunday = selectedDate.getDay() === 0;

            if (isSunday) {
                // Nếu là Chủ nhật, hiển thị tất cả nhân viên với trạng thái "Đúng giờ" và kết luận "Đi làm đủ"
                if (usersData && usersData.length > 0) {
                    usersData.forEach(user => {
                        // Tìm bản ghi chấm công nếu có
                        const existingRecord = filteredData.find(record => record.id_nhan_vien == user.id_nhan_vien);
                        
                        // Sử dụng dữ liệu từ database nếu có, nếu không dùng giá trị mặc định cho Chủ nhật
                        const record = existingRecord || {
                            id_cham_cong: user.id_nhan_vien * 1000 + 1,
                            id_nhan_vien: user.id_nhan_vien,
                            ho_ten: user.ho_ten,
                            ngay_lam_viec: selectedDateStr,
                            gio_vao: '08:30:00',
                            gio_trua: '12:00:00',
                            gio_ra: '17:30:00',
                            trang_thai: 'Đúng giờ',
                            trang_thai_sang: 'Đúng giờ',
                            trang_thai_trua: 'Đúng giờ',
                            trang_thai_chieu: 'Đúng giờ'
                        };

                        const conclusion = 'Đi làm đủ';
                        const calculatedStatus = 'Đúng giờ';
                        const statusClass = "dadiemdanh";

                        const row = document.createElement("tr");
                        row.innerHTML = `
                            <td>${record.id_cham_cong}</td>
                            <td><a href="#" class="name-link" data-id="${record.id_nhan_vien}">${record.ho_ten}</a></td>
                            <td>${record.ngay_lam_viec}</td>
                            <td>${record.gio_vao || '08:30:00'}</td>
                            <td>${record.gio_trua || '12:00:00'}</td>
                            <td>${record.gio_ra || '17:30:00'}</td>
                            <td><span class="status ${statusClass}">${calculatedStatus}</span></td>
                            <td><span class="conclusion">${conclusion}</span></td>
                            <td>
                                ${userPermissions.quyen_sua ? `
                                    <button class="btn-edit" onclick="editChamCong(${record.id_cham_cong}, ${record.id_nhan_vien}, '${record.ho_ten.replace(/'/g, "\\'")}', '${record.ngay_lam_viec}', '${record.gio_vao || '08:30:00'}', '${record.gio_trua || '12:00:00'}', '${record.gio_ra || '17:30:00'}', '${record.trang_thai}', '${record.trang_thai_sang || 'Đúng giờ'}', '${record.trang_thai_trua || 'Đúng giờ'}', '${record.trang_thai_chieu || 'Đúng giờ'}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                ` : ''}
                                ${userPermissions.quyen_xoa ? `
                                    <button class="btn-delete" onclick="deleteChamCong(${record.id_cham_cong})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                ` : ''}
                            </td>
                        `;
                        chamCongTableBody.appendChild(row);
                    });

                    document.querySelectorAll('.name-link').forEach(link => {
                        link.addEventListener('click', function(e) {
                            e.preventDefault();
                            const userId = this.getAttribute('data-id');
                            showDetailChamCong(userId);
                        });
                    });
                } else {
                    chamCongTableBody.innerHTML = `
                        <tr>
                            <td colspan="9">Chưa có dữ liệu nhân viên. Vui lòng tải lại trang.</td>
                        </tr>
                    `;
                }
            } else if (filteredData.length > 0) {
                // Nếu không phải Chủ nhật, hiển thị như bình thường
                filteredData.forEach(record => {
                    // Tính kết luận dựa trên giờ vào, trưa, ra
                    const conclusion = calculateConclusion(record.gio_vao, record.gio_trua, record.gio_ra);
                    
                    // Tính lại trạng thái dựa trên thời gian thực tế và cấu hình hiện tại
                    // Thay vì chỉ dùng trạng thái đã lưu trong database
                    let calculatedStatus = recalculateStatusFromTime(record.gio_vao, record.gio_trua, record.gio_ra);
                    console.log('renderChamCongTable - Trạng thái:', { 
                        gio_vao: record.gio_vao,
                        gio_trua: record.gio_trua,
                        gio_ra: record.gio_ra,
                        sang: record.trang_thai_sang, 
                        trua: record.trang_thai_trua, 
                        chieu: record.trang_thai_chieu,
                        calculated: calculatedStatus 
                    });
                    
                   const statusClass = {
    "đúng giờ": "dadiemdanh",
    "đi trễ": "ditre",
    "có phép": "cophep",
    "không phép": "khongphep",
    "nghỉ việc": "nghiviec",
    "phép năm": "phepnam",
    "nghỉ lễ": "nghile",
    "nghỉ nữa buổi": "nghiviec"
}[calculatedStatus.toLowerCase()] || "chuadiemdanh";

                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${record.id_cham_cong}</td>
                        <td><a href="#" class="name-link" data-id="${record.id_nhan_vien}">${record.ho_ten}</a></td>
                        <td>${record.ngay_lam_viec}</td>
                        <td>${record.gio_vao || '-'}</td>
                        <td>${record.gio_trua || '-'}</td>
                        <td>${record.gio_ra || '-'}</td>
                        <td><span class="status ${statusClass}">${calculatedStatus}</span></td>
                        <td><span class="conclusion">${conclusion}</span></td>
                        <td>
                            ${userPermissions.quyen_sua ? `
                                <button class="btn-edit" onclick="editChamCong(${record.id_cham_cong}, ${record.id_nhan_vien}, '${record.ho_ten.replace(/'/g, "\\'")}', '${record.ngay_lam_viec}', '${record.gio_vao || ''}', '${record.gio_trua || ''}', '${record.gio_ra || ''}', '${record.trang_thai}', '${record.trang_thai_sang || ''}', '${record.trang_thai_trua || ''}', '${record.trang_thai_chieu || ''}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                            ` : ''}
                            ${userPermissions.quyen_xoa ? `
                                <button class="btn-delete" onclick="deleteChamCong(${record.id_cham_cong})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            ` : ''}
                        </td>
                    `;
                    chamCongTableBody.appendChild(row);
                });

                document.querySelectorAll('.name-link').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const userId = this.getAttribute('data-id');
                        showDetailChamCong(userId);
                    });
                });
            } else {
                chamCongTableBody.innerHTML = `
                    <tr>
                        <td colspan="9">Chưa có dữ liệu cho ngày ${selectedDateElement.textContent}. Vui lòng điểm danh để hiển thị.</td>
                    </tr>
                `;
            }
        }

        // Hàm hiển thị modal chi tiết chấm công
        async function showDetailChamCong(userId) {
            showLoading();
            try {
                let user = usersData.find(u => u.id_nhan_vien == userId);
                if (!user) {
                    const response = await fetch(`http://localhost/doanqlns/index.php/api/users?id=${userId}`);
                    if (!response.ok) throw new Error("Lỗi khi tải thông tin nhân viên: " + response.status);
                    const data = await response.json();
                    user = Array.isArray(data) ? data[0] : data;
                    if (!user) throw new Error("Không tìm thấy thông tin nhân viên");
                }

                const month = parseInt(document.getElementById('selectMonth').value);
                const year = parseInt(document.getElementById('selectYear').value) || new Date().getFullYear();
                const { diemDanhDays, nghiDays, totalWorkDays } = calculateAttendanceStats(userId, month, year);

                document.getElementById('detailHoTen').textContent = user.ho_ten || 'Không có dữ liệu';
                document.getElementById('detailGioiTinh').textContent = user.gioi_tinh || 'Không có dữ liệu';
                document.getElementById('detailNgaySinh').textContent = user.ngay_sinh || 'Không có dữ liệu';
                document.getElementById('detailEmail').textContent = user.email || 'Không có dữ liệu';
                document.getElementById('detailSoDienThoai').textContent = user.so_dien_thoai || 'Không có dữ liệu';
                document.getElementById('detailDiaChi').textContent = user.dia_chi || 'Không có dữ liệu';
                document.getElementById('detailPhongBan').textContent = user.ten_phong_ban || 'Không có dữ liệu';
                document.getElementById('detailChucVu').textContent = user.ten_chuc_vu || 'Không có dữ liệu';
                document.getElementById('detailLuongCoBanNhanVien').textContent = formatCurrency(user.luong_co_ban) || 'Không có dữ liệu';

                document.getElementById('detailSoNgayCong').textContent = formatNumber(diemDanhDays) || '0';
                document.getElementById('detailNghiPhep').textContent = formatNumber(nghiDays) || '0';
                document.getElementById('detailTongCong').textContent = formatNumber(totalWorkDays) || '0';

                document.getElementById('detailChamCongModal').style.display = 'flex';
            } catch (error) {
                console.error("Lỗi khi hiển thị chi tiết chấm công:", error);
                alert("Lỗi khi hiển thị chi tiết chấm công: " + error.message);
            } finally {
                hideLoading();
            }
        }

        // Hàm đóng modal chi tiết chấm công
        function closeDetailChamCongModal() {
            document.getElementById('detailChamCongModal').style.display = 'none';
        }

        // Hàm thay đổi ngày
        function changeDate(direction) {
            selectedDate.setDate(selectedDate.getDate() + direction);
            renderChamCongTable(attendanceData);
        }

        // Hàm hiển thị modal điểm danh sáng
        async function showDiemDanhModal() {
            if (!userPermissions.quyen_them) {
                alert("Bạn không có quyền điểm danh!");
                return;
            }
            const currentDate = new Date();
            document.getElementById('selectMonth').value = currentDate.getMonth() + 1;
            document.getElementById('selectYear').value = currentDate.getFullYear();
            diemDanhModal.style.display = 'flex';
            await loadPhongBanData();
            await loadUsersData();
            await loadAttendanceData();
            updateAttendanceTable();
        }

        // Hàm hiển thị modal điểm danh chiều
        async function showDiemDanhTruaModal() {
            if (!userPermissions.quyen_them) {
                alert("Bạn không có quyền điểm danh!");
                return;
            }
            const currentDate = new Date();
            document.getElementById('selectMonthTrua').value = currentDate.getMonth() + 1;
            document.getElementById('selectYearTrua').value = currentDate.getFullYear();
            document.getElementById('diemDanhTruaModal').style.display = 'flex';
            await loadPhongBanData();
            await loadUsersData();
            await loadAttendanceData();
            updateAttendanceTableTrua();
        }

        async function showDiemDanhChieuModal() {
            if (!userPermissions.quyen_them) {
                alert("Bạn không có quyền điểm danh!");
                return;
            }
            const currentDate = new Date();
            document.getElementById('selectMonthChieu').value = currentDate.getMonth() + 1;
            document.getElementById('selectYearChieu').value = currentDate.getFullYear();
            document.getElementById('diemDanhChieuModal').style.display = 'flex';
            await loadPhongBanData();
            await loadUsersData();
            await loadAttendanceData();
            updateAttendanceTableChieu();
        }

        // Hàm xử lý dropdown menu
        function toggleDropdown() {
            const dropdown = document.querySelector('.dropdown');
            const dropdownMenu = document.getElementById('diemDanhDropdown');
            
            if (dropdownMenu.style.display === 'block') {
                closeDropdown();
            } else {
                openDropdown();
            }
        }

        function openDropdown() {
            const dropdown = document.querySelector('.dropdown');
            const dropdownMenu = document.getElementById('diemDanhDropdown');
            
            dropdown.classList.add('active');
            dropdownMenu.style.display = 'block';
        }

        function closeDropdown() {
            const dropdown = document.querySelector('.dropdown');
            const dropdownMenu = document.getElementById('diemDanhDropdown');
            
            dropdown.classList.remove('active');
            dropdownMenu.style.display = 'none';
        }

        // Hàm đóng modal điểm danh sáng
        function closeDiemDanhModal() {
            diemDanhModal.style.display = 'none';
        }

        // Hàm đóng modal điểm danh chiều
        function closeDiemDanhTruaModal() {
            document.getElementById('diemDanhTruaModal').style.display = 'none';
        }

        function closeDiemDanhChieuModal() {
            diemDanhChieuModal.style.display = 'none';
        }

        // Hiển thị modal điểm danh gương mặt
        function showFaceModal() {
            if (!userPermissions.quyen_them) {
                alert("Bạn không có quyền điểm danh!");
                return;
            }
            document.getElementById('faceModal').style.display = 'flex';
        }

        function closeFaceModal() {
            const modal = document.getElementById('faceModal');
            modal.style.display = 'none';
        }

        // Nhận thông điệp từ iframe để cập nhật bảng sau khi điểm danh thành công
        window.addEventListener('message', async function(event) {
            try {
                const data = event.data || {};
                if (data.type === 'faceAttendanceSuccess') {
                    // Nếu student_id là số, tự động ghi nhận vào hệ thống PHP
                    const numericId = parseInt(data.student_id, 10);
                    if (!isNaN(numericId)) {
                        try {
                            const now = new Date();
                            const yyyy = now.getFullYear();
                            const mm = String(now.getMonth() + 1).padStart(2, '0');
                            const dd = String(now.getDate()).padStart(2, '0');
                            const hh = String(now.getHours()).padStart(2, '0');
                            const mi = String(now.getMinutes()).padStart(2, '0');
                            const ss = String(now.getSeconds()).padStart(2, '0');
                            const dateStr = `${yyyy}-${mm}-${dd}`;
                            const timeStr = `${hh}:${mi}:${ss}`;
                            
                            // Phân biệt thời gian điểm danh dựa trên giờ và phút
                            const currentHour = now.getHours();
                            const currentMinute = now.getMinutes();
                            const currentTime = currentHour * 60 + currentMinute; // Chuyển thành phút để so sánh dễ dàng
                            
                            console.log(`Debug thời gian: ${timeStr}, Giờ: ${currentHour}, Phút: ${currentMinute}, Tổng phút: ${currentTime}`);
                            
                            // Sử dụng hàm xác định trạng thái dựa trên cấu hình giờ làm việc
                            const attendanceInfo = determineAttendanceStatus(currentTime);
                            
                            let gioVao = null;
                            let gioTrua = null;
                            let gioRa = null;
                            let trangThaiSang = null;
                            let trangThaiTrua = null;
                            let trangThaiChieu = null;
                            
                            // Gán giá trị dựa trên kết quả từ hàm determineAttendanceStatus
                            if (attendanceInfo.type === 'sang') {
                                gioVao = timeStr;
                                trangThaiSang = attendanceInfo.status;
                                console.log(`Phân loại: Điểm danh SÁNG ${attendanceInfo.status} - lưu vào gio_vao`);
                            } else if (attendanceInfo.type === 'trua') {
                                gioTrua = timeStr;
                                trangThaiTrua = attendanceInfo.status;
                                console.log(`Phân loại: Điểm danh TRƯA ${attendanceInfo.status} - lưu vào gio_trua`);
                            } else if (attendanceInfo.type === 'chieu') {
                                gioRa = timeStr;
                                trangThaiChieu = attendanceInfo.status;
                                console.log(`Phân loại: Điểm danh CHIỀU ${attendanceInfo.status} - lưu vào gio_ra`);
                            }

                            // Tính trạng thái tổng hợp cho bản ghi mới
                            let finalTrangThai = 'Chưa điểm danh';
                            const allStatuses = [trangThaiSang, trangThaiTrua, trangThaiChieu].filter(s => s);
                            
                            if (allStatuses.length > 0) {
                                // Ưu tiên: Đi trễ > Ra sớm > Đúng giờ
                                if (allStatuses.includes('Đi trễ')) {
                                    finalTrangThai = 'Đi trễ';
                                } else if (allStatuses.includes('Ra sớm')) {
                                    finalTrangThai = 'Ra sớm';
                                } else if (allStatuses.includes('Đúng giờ')) {
                                    finalTrangThai = 'Đúng giờ';
                                }
                            }

                            const payload = {
                                id_nhan_vien: numericId,
                                ngay_lam_viec: dateStr,
                                gio_vao: gioVao,
                                gio_trua: gioTrua,
                                gio_ra: gioRa,
                                trang_thai_sang: trangThaiSang,
                                trang_thai_trua: trangThaiTrua,
                                trang_thai_chieu: trangThaiChieu,
                                trang_thai: finalTrangThai,
                                month: now.getMonth() + 1,
                                year: now.getFullYear()
                            };

                            // Kiểm tra bản ghi tồn tại để PUT/POST
                            const checkResp = await fetch(`http://localhost/doanqlns/index.php/api/chamcong?id_nhan_vien=${numericId}&ngay_cham_cong=${dateStr}`);
                            const existing = await checkResp.json();
                            let resp;

                            if (existing && existing.length > 0) {
                                const existingRecord = existing[0];
                                const shiftRecorded = (
                                    (attendanceInfo.type === 'sang' && existingRecord.gio_vao) ||
                                    (attendanceInfo.type === 'trua' && existingRecord.gio_trua) ||
                                    (attendanceInfo.type === 'chieu' && existingRecord.gio_ra)
                                );
                                if (shiftRecorded) {
                                    alert('Bạn đã điểm danh ca này rồi!');
                                    return;
                                }
                            }
                            
                            if (existing && existing.length > 0) {
                                // Cập nhật bản ghi hiện có - chỉ cập nhật cột tương ứng với thời gian điểm danh
                                const existingRecord = existing[0];
                                
                                console.log(`Bản ghi hiện có: gio_vao=${existingRecord.gio_vao}, gio_trua=${existingRecord.gio_trua}, gio_ra=${existingRecord.gio_ra}`);
                                
                                // Tính trạng thái tổng hợp dựa trên tất cả các trạng thái
                                const finalTrangThaiSang = trangThaiSang !== null ? trangThaiSang : existingRecord.trang_thai_sang;
                                const finalTrangThaiTrua = trangThaiTrua !== null ? trangThaiTrua : existingRecord.trang_thai_trua;
                                const finalTrangThaiChieu = trangThaiChieu !== null ? trangThaiChieu : existingRecord.trang_thai_chieu;
                                
                                // Tính trạng thái tổng hợp - ưu tiên trạng thái xấu nhất
                                let finalTrangThai = 'Chưa điểm danh';
                                const allStatuses = [finalTrangThaiSang, finalTrangThaiTrua, finalTrangThaiChieu].filter(s => s);
                                
                                if (allStatuses.length > 0) {
                                    // Ưu tiên: Đi trễ > Ra sớm > Đúng giờ
                                    if (allStatuses.includes('Đi trễ')) {
                                        finalTrangThai = 'Đi trễ';
                                    } else if (allStatuses.includes('Ra sớm')) {
                                        finalTrangThai = 'Ra sớm';
                                    } else if (allStatuses.includes('Đúng giờ')) {
                                        finalTrangThai = 'Đúng giờ';
                                    }
                                }
                                
                                const updatePayload = {
                                    ...payload,
                                    // Chỉ cập nhật cột tương ứng với thời gian điểm danh hiện tại
                                    gio_vao: gioVao !== null ? gioVao : existingRecord.gio_vao,
                                    gio_trua: gioTrua !== null ? gioTrua : existingRecord.gio_trua,
                                    gio_ra: gioRa !== null ? gioRa : existingRecord.gio_ra,
                                    trang_thai_sang: finalTrangThaiSang,
                                    trang_thai_trua: finalTrangThaiTrua,
                                    trang_thai_chieu: finalTrangThaiChieu,
                                    trang_thai: finalTrangThai
                                };
                                
                                // Debug: Kiểm tra giá trị trước khi gửi
                                console.log(`Trước khi cập nhật: gioVao=${gioVao}, gioTrua=${gioTrua}, gioRa=${gioRa}`);
                                console.log(`Dữ liệu cũ: gio_vao=${existingRecord.gio_vao}, gio_trua=${existingRecord.gio_trua}, gio_ra=${existingRecord.gio_ra}`);
                                console.log(`Sau khi cập nhật: gio_vao=${updatePayload.gio_vao}, gio_trua=${updatePayload.gio_trua}, gio_ra=${updatePayload.gio_ra}`);
                                
                                console.log(`Payload cập nhật: gio_vao=${updatePayload.gio_vao}, gio_trua=${updatePayload.gio_trua}, gio_ra=${updatePayload.gio_ra}`);
                                
                                resp = await fetch(`http://localhost/doanqlns/index.php/api/chamcong?id_nhan_vien=${numericId}&ngay_cham_cong=${dateStr}`, {
                                    method: 'PUT',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify(updatePayload)
                                });
                            } else {
                                // Tạo bản ghi mới
                                const newPayload = {
                                    ...payload,
                                    // Tính trạng thái tổng hợp cho bản ghi mới
                                    trang_thai: (trangThaiSang || trangThaiTrua || trangThaiChieu) ? 'Đúng giờ' : 'Chưa điểm danh'
                                };
                                
                                resp = await fetch('http://localhost/doanqlns/index.php/api/chamcong', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify(newPayload)
                                });
                            }
                            const resJson = await resp.json();
                            if (!resJson.success) {
                                console.warn('Ghi nhận điểm danh từ nhận diện khuôn mặt thất bại:', resJson.message);
                            } else {
                                // Thông báo thời gian điểm danh đã được lưu vào cột nào
                                let timeType = '';
                                if (gioVao) timeType = 'Giờ Vào';
                                else if (gioTrua) timeType = 'Giờ Trưa';
                                else if (gioRa) timeType = 'Giờ Ra';
                                
                                console.log(`Điểm danh thành công! Thời gian ${timeStr} (${currentTime} phút) đã được lưu vào cột ${timeType}`);
                                console.log(`Chi tiết: gioVao=${gioVao}, gioTrua=${gioTrua}, gioRa=${gioRa}`);
                            }
                        } catch (e) {
                            console.warn('Không thể ghi nhận điểm danh vào hệ thống PHP:', e);
                        }
                    }

                    await loadAttendanceData();
                    updateAttendanceTable();
                    renderChamCongTable(attendanceData);
                }
            } catch (e) {
                console.warn('Không thể xử lý message từ iframe:', e);
            }
        });

        // Hàm hiển thị modal chỉnh sửa chấm công
        function editChamCong(id, idNhanVien, hoTen, ngayLamViec, gioVao, gioTrua, gioRa, trangThai, trangThaiSang, trangThaiTrua, trangThaiChieu) {
            if (!userPermissions.quyen_sua) {
                alert("Bạn không có quyền chỉnh sửa chấm công!");
                return;
            }
            document.getElementById('editIdChamCong').value = id;
            document.getElementById('editIdNhanVien').value = idNhanVien;
            document.getElementById('editHoTen').value = hoTen;
            document.getElementById('editNgayLamViec').value = ngayLamViec;
            document.getElementById('editGioVao').value = gioVao || '';
            document.getElementById('editGioTrua').value = gioTrua || '';
            document.getElementById('editGioRa').value = gioRa || '';
            document.getElementById('editTrangThaiSang').value = trangThaiSang || '';
            document.getElementById('editTrangThaiTrua').value = trangThaiTrua || '';
            document.getElementById('editTrangThaiChieu').value = trangThaiChieu || '';
            document.getElementById('editTrangThai').value = trangThai;
            
            // Thêm event listeners để tự động tính trạng thái tổng hợp
            document.getElementById('editTrangThaiSang').addEventListener('change', updateEditTrangThaiTongHop);
            document.getElementById('editTrangThaiTrua').addEventListener('change', updateEditTrangThaiTongHop);
            document.getElementById('editTrangThaiChieu').addEventListener('change', updateEditTrangThaiTongHop);
            
            editChamCongModal.style.display = 'flex';
        }

        // Hàm cập nhật trạng thái tổng hợp trong modal chỉnh sửa
        function updateEditTrangThaiTongHop() {
            const trangThaiSang = document.getElementById('editTrangThaiSang').value;
            const trangThaiTrua = document.getElementById('editTrangThaiTrua').value;
            const trangThaiChieu = document.getElementById('editTrangThaiChieu').value;
            const trangThaiTongHop = calculateOverallStatus(trangThaiSang, trangThaiTrua, trangThaiChieu);
            document.getElementById('editTrangThai').value = trangThaiTongHop;
        }

        // Hàm đóng modal chỉnh sửa chấm công
        function closeEditChamCongModal() {
            editChamCongModal.style.display = 'none';
        }

        // Hàm tính thống kê chấm công
       function calculateAttendanceStats(userId, month, year) {
    const startDate = new Date(year, month - 1, 1);
    const endDate = new Date(year, month, 0);
    const records = attendanceData.filter(record => {
        const recordDate = new Date(record.ngay_lam_viec);
        return record.id_nhan_vien == userId &&
               recordDate >= startDate &&
               recordDate <= endDate;
    });

    let diemDanhDays = 0;
    let nghiDays = 0;
    let khongPhepCount = 0;

    for (let day = 1; day <= endDate.getDate(); day++) {
        const date = new Date(year, month - 1, day);
        const dateStr = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
        const isSunday = date.getDay() === 0;
        const record = records.find(r => r.ngay_lam_viec === dateStr);

        if (isSunday) {
            diemDanhDays += 1;
        } else if (record) {
            if (record.trang_thai === 'Đúng giờ' || record.trang_thai === 'Phép Năm' || record.trang_thai === 'Nghỉ Lễ') {
                diemDanhDays += 1;
            } else if (record.trang_thai === 'Đi trễ') {
                diemDanhDays += 0.75;
            } else if (record.trang_thai === 'Ra sớm') {
                diemDanhDays += 0.75;
            } else if (record.trang_thai === 'Có phép') {
                diemDanhDays += 1;
                nghiDays -= 0.5;
                khongPhepCount += 0.5;
            } else if (record.trang_thai === 'Nghỉ nữa buổi') {
                diemDanhDays += 0.5;
                nghiDays -= 0.5;
            }
        }
    }

    const totalWorkDays = diemDanhDays - khongPhepCount;
    return { diemDanhDays, nghiDays, totalWorkDays };
}

        // Hàm cập nhật bảng điểm danh trong modal sáng
        async function updateAttendanceTable() {
            const month = parseInt(document.getElementById('selectMonth').value);
            const yearInput = document.getElementById('selectYear');
            const year = parseInt(yearInput.value) || new Date().getFullYear();
            const phongBan = document.getElementById('selectPhongBan').value;
            const searchName = document.getElementById('searchName').value.toLowerCase();
            const daysInMonth = new Date(year, month, 0).getDate();

            const tableHead = document.getElementById('attendanceTableHead');
            const tableBody = document.getElementById('attendanceTableBody');

            if (!yearInput.value) {
                yearInput.value = new Date().getFullYear();
            }

            const currentDate = new Date();
            const currentYear = currentDate.getFullYear();
            const currentMonth = currentDate.getMonth() + 1;
            const currentDay = currentDate.getDate();

            // Lọc dữ liệu nhân viên dựa trên phòng ban và tên
            let filteredUsers = usersData;
            if (phongBan) {
                filteredUsers = filteredUsers.filter(user => user.ten_phong_ban === phongBan);
            }
            if (searchName) {
                filteredUsers = filteredUsers.filter(user => user.ho_ten.toLowerCase().includes(searchName));
            }

            let headerRow = `
                <tr>
                    <th>ID Chấm Công</th>
                    <th>ID Nhân Viên</th>
                    <th>Họ Tên</th>
            `;
            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month - 1, day);
                const weekday = date.toLocaleDateString('vi-VN', { weekday: 'short' });
                const isCurrentDay = (year === currentYear && month === currentMonth && day === currentDay);
                headerRow += `
                    <th class="${isCurrentDay ? 'current-day-column' : ''}">
                        <div class="day-header">${day}</div>
                        <div class="weekday-header">${weekday}</div>
                    </th>
                `;
            }
            headerRow += `
                    <th>Điểm danh</th>
                    <th>Số ngày nghỉ</th>
                    <th>Tổng công</th>
                </tr>`;
            tableHead.innerHTML = headerRow;

            tableBody.innerHTML = '';

            if (!filteredUsers || filteredUsers.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="${daysInMonth + 6}">Không có dữ liệu nhân viên</td></tr>`;
                return;
            }

            filteredUsers.forEach(user => {
                const row = document.createElement('tr');
                let rowContent = `
                    <td>${user.id_nhan_vien * 1000 + 1}</td>
                    <td>${user.id_nhan_vien}</td>
                    <td>${user.ho_ten}</td>
                `;

                for (let day = 1; day <= daysInMonth; day++) {
                    const date = new Date(year, month - 1, day);
                    const dateStr = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
                    const isSunday = date.getDay() === 0;
                    const isFutureDate = year > currentYear ||
                        (year === currentYear && month > currentMonth) ||
                        (year === currentYear && month === currentMonth && day > currentDay);

                    const attendanceRecord = attendanceData.find(record =>
                        record.id_nhan_vien == user.id_nhan_vien &&
                        record.ngay_lam_viec === dateStr
                    );

                    let currentStatus = attendanceRecord ? (attendanceRecord.trang_thai_chieu || 'Chưa điểm danh') : 'Chưa điểm danh';
                    let isDisabled = isFutureDate || !userPermissions.quyen_sua;

                    if (isSunday) {
                        currentStatus = 'Đúng giờ';
                        isDisabled = true;

                        if (!attendanceRecord) {
                            const data = {
                                id_nhan_vien: user.id_nhan_vien,
                                ngay_lam_viec: dateStr,
                                gio_vao: '08:30:00',
                                gio_ra: '17:30:00',
                                trang_thai: 'Đúng giờ',
                                month: month,
                                year: year
                            };

                            fetch("http://localhost/doanqlns/index.php/api/chamcong", {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify(data)
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result.success) {
                                    console.log(`Tự động thêm điểm danh "Đúng giờ" cho Chủ nhật ${dateStr}, nhân viên ${user.id_nhan_vien}`);
                                } else {
                                    console.error("Thêm điểm danh thất bại:", result.message);
                                }
                            })
                            .catch(error => console.error("Lỗi khi tự động thêm điểm danh:", error));
                        }
                    }

                    const isCurrentDay = (year === currentYear && month === currentMonth && day === currentDay);
                   rowContent += `
                        <td class="${isCurrentDay ? 'current-day-cell' : ''}">
                            <select class="status-select" 
                                    data-id="${user.id_nhan_vien}" 
                                    data-date="${dateStr}" 
                                    onchange="updateAttendanceSang(this)"
                                    ${isDisabled ? 'disabled' : ''}>
                                <option value="Chưa điểm danh" ${currentStatus === 'Chưa điểm danh' ? 'selected' : ''}>Chưa điểm danh</option>
                                <option value="Đúng giờ" ${currentStatus === 'Đúng giờ' ? 'selected' : ''}>Đúng giờ</option>
                                <option value="Đi trễ" ${currentStatus === 'Đi trễ' ? 'selected' : ''}>Đi trễ</option>
                                <option value="Ra sớm" ${currentStatus === 'Ra sớm' ? 'selected' : ''}>Ra sớm</option>
                                <option value="Có phép" ${currentStatus === 'Có phép' ? 'selected' : ''}>Có phép</option>
                                <option value="Phép Năm" ${currentStatus === 'Phép Năm' ? 'selected' : ''}>Phép Năm</option>
                                <option value="Nghỉ Lễ" ${currentStatus === 'Nghỉ Lễ' ? 'selected' : ''}>Nghỉ Lễ</option>
                                <option value="Nghỉ nữa buổi" ${currentStatus === 'Nghỉ nữa buổi' ? 'selected' : ''}>Nghỉ nữa buổi</option>
                            </select>
                        </td>
                    `;
                }

                const { diemDanhDays, nghiDays, totalWorkDays } = calculateAttendanceStats(user.id_nhan_vien, month, year);
                rowContent += `
                    <td class="diem-danh-days" data-id="${user.id_nhan_vien}">${formatNumber(diemDanhDays)}</td>
                    <td class="nghi-days" data-id="${user.id_nhan_vien}">${formatNumber(nghiDays)}</td>
                    <td class="total-work-days" data-id="${user.id_nhan_vien}">${formatNumber(totalWorkDays)}</td>
                `;

                row.innerHTML = rowContent;
                tableBody.appendChild(row);
            });

            await loadAttendanceData();
        }

        // Hàm cập nhật bảng điểm danh trong modal chiều
        async function updateAttendanceTableChieu() {
            const month = parseInt(document.getElementById('selectMonthChieu').value);
            const yearInput = document.getElementById('selectYearChieu');
            const year = parseInt(yearInput.value) || new Date().getFullYear();
            const phongBan = document.getElementById('selectPhongBanChieu').value;
            const searchName = document.getElementById('searchNameChieu').value.toLowerCase();
            const daysInMonth = new Date(year, month, 0).getDate();

            const tableHead = document.getElementById('attendanceTableHeadChieu');
            const tableBody = document.getElementById('attendanceTableBodyChieu');

            if (!yearInput.value) {
                yearInput.value = new Date().getFullYear();
            }

            const currentDate = new Date();
            const currentYear = currentDate.getFullYear();
            const currentMonth = currentDate.getMonth() + 1;
            const currentDay = currentDate.getDate();

            // Lọc dữ liệu nhân viên dựa trên phòng ban và tên
            let filteredUsers = usersData;
            if (phongBan) {
                filteredUsers = filteredUsers.filter(user => user.ten_phong_ban === phongBan);
            }
            if (searchName) {
                filteredUsers = filteredUsers.filter(user => user.ho_ten.toLowerCase().includes(searchName));
            }

            let headerRow = `
                <tr>
                    <th>ID Chấm Công</th>
                    <th>ID Nhân Viên</th>
                    <th>Họ Tên</th>
            `;
            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month - 1, day);
                const weekday = date.toLocaleDateString('vi-VN', { weekday: 'short' });
                const isCurrentDay = (year === currentYear && month === currentMonth && day === currentDay);
                headerRow += `
                    <th class="${isCurrentDay ? 'current-day-column' : ''}">
                        <div class="day-header">${day}</div>
                        <div class="weekday-header">${weekday}</div>
                    </th>
                `;
            }
            headerRow += `
                    <th>Điểm danh</th>
                    <th>Số ngày nghỉ</th>
                    <th>Tổng công</th>
                </tr>`;
            tableHead.innerHTML = headerRow;

            tableBody.innerHTML = '';

            if (!filteredUsers || filteredUsers.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="${daysInMonth + 6}">Không có dữ liệu nhân viên</td></tr>`;
                return;
            }

            filteredUsers.forEach(user => {
                const row = document.createElement('tr');
                let rowContent = `
                    <td>${user.id_nhan_vien * 1000 + 1}</td>
                    <td>${user.id_nhan_vien}</td>
                    <td>${user.ho_ten}</td>
                `;

                for (let day = 1; day <= daysInMonth; day++) {
                    const date = new Date(year, month - 1, day);
                    const dateStr = `${year}-${month.toString().padStart(2, 0)}-${day.toString().padStart(2, 0)}`;
                    const isSunday = date.getDay() === 0;
                    const isFutureDate = year > currentYear ||
                        (year === currentYear && month > currentMonth) ||
                        (year === currentYear && month === currentMonth && day > currentDay);

                    const attendanceRecord = attendanceData.find(record =>
                        record.id_nhan_vien == user.id_nhan_vien &&
                        record.ngay_lam_viec === dateStr
                    );

                    let currentStatus = attendanceRecord ? (attendanceRecord.trang_thai_chieu || 'Chưa điểm danh') : 'Chưa điểm danh';
                    let isDisabled = isFutureDate || !userPermissions.quyen_sua;

                    if (isSunday) {
                        currentStatus = 'Đúng giờ';
                        isDisabled = true;

                        if (!attendanceRecord) {
                            const data = {
                                id_nhan_vien: user.id_nhan_vien,
                                ngay_lam_viec: dateStr,
                                gio_vao: '08:30:00',
                                gio_ra: '17:30:00',
                                trang_thai: 'Đúng giờ',
                                month: month,
                                year: year
                            };

                            fetch("http://localhost/doanqlns/index.php/api/chamcong", {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify(data)
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result.success) {
                                    console.log(`Tự động thêm điểm danh "Đúng giờ" cho Chủ nhật ${dateStr}, nhân viên ${user.id_nhan_vien}`);
                                } else {
                                    console.error("Thêm điểm danh thất bại:", result.message);
                                }
                            })
                            .catch(error => console.error("Lỗi khi tự động thêm điểm danh:", error));
                        }
                    }

                    const isCurrentDay = (year === currentYear && month === currentMonth && day === currentDay);
                   rowContent += `
                        <td class="${isCurrentDay ? 'current-day-cell' : ''}">
                            <select class="status-select" 
                                    data-id="${user.id_nhan_vien}" 
                                    data-date="${dateStr}" 
                                    onchange="updateAttendanceChieu(this)"
                                    ${isDisabled ? 'disabled' : ''}>
                                <option value="Chưa điểm danh" ${currentStatus === 'Chưa điểm danh' ? 'selected' : ''}>Chưa điểm danh</option>
                                <option value="Đúng giờ" ${currentStatus === 'Đúng giờ' ? 'selected' : ''}>Đúng giờ</option>
                                <option value="Đi trễ" ${currentStatus === 'Đi trễ' ? 'selected' : ''}>Đi trễ</option>
                                <option value="Ra sớm" ${currentStatus === 'Ra sớm' ? 'selected' : ''}>Ra sớm</option>
                                <option value="Có phép" ${currentStatus === 'Có phép' ? 'selected' : ''}>Có phép</option>
                                <option value="Phép Năm" ${currentStatus === 'Phép Năm' ? 'selected' : ''}>Phép Năm</option>
                                <option value="Nghỉ Lễ" ${currentStatus === 'Nghỉ Lễ' ? 'selected' : ''}>Nghỉ Lễ</option>
                                <option value="Nghỉ nữa buổi" ${currentStatus === 'Nghỉ nữa buổi' ? 'selected' : ''}>Nghỉ nữa buổi</option>
                            </select>
                        </td>
                    `;
                }

                const { diemDanhDays, nghiDays, totalWorkDays } = calculateAttendanceStats(user.id_nhan_vien, month, year);
                rowContent += `
                    <td class="diem-danh-days" data-id="${user.id_nhan_vien}">${formatNumber(diemDanhDays)}</td>
                    <td class="nghi-days" data-id="${user.id_nhan_vien}">${formatNumber(nghiDays)}</td>
                    <td class="total-work-days" data-id="${user.id_nhan_vien}">${formatNumber(totalWorkDays)}</td>
                `;

                row.innerHTML = rowContent;
                tableBody.appendChild(row);
            });

            await loadAttendanceData();
        }

        // Hàm cập nhật bảng điểm danh trưa
        async function updateAttendanceTableTrua() {
            const month = parseInt(document.getElementById('selectMonthTrua').value);
            const yearInput = document.getElementById('selectYearTrua');
            const year = parseInt(yearInput.value) || new Date().getFullYear();
            const phongBan = document.getElementById('selectPhongBanTrua').value;
            const searchName = document.getElementById('searchNameTrua').value.toLowerCase();
            const daysInMonth = new Date(year, month, 0).getDate();

            const tableHead = document.getElementById('attendanceTableHeadTrua');
            const tableBody = document.getElementById('attendanceTableBodyTrua');

            if (!yearInput.value) {
                yearInput.value = new Date().getFullYear();
            }

            const currentDate = new Date();
            const currentYear = currentDate.getFullYear();
            const currentMonth = currentDate.getMonth() + 1;
            const currentDay = currentDate.getDate();

            let headerRow = `
                <tr>
                    <th>ID Chấm Công</th>
                    <th>ID Nhân Viên</th>
                    <th>Họ Tên</th>
            `;
            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month - 1, day);
                const weekday = date.toLocaleDateString('vi-VN', { weekday: 'short' });
                const isCurrentDay = (year === currentYear && month === currentMonth && day === currentDay);
                headerRow += `
                    <th class="${isCurrentDay ? 'current-day-column' : ''}">
                        <div class="day-header">${day}</div>
                        <div class="weekday-header">${weekday}</div>
                    </th>
                `;
            }
            headerRow += `
                    <th>Điểm danh</th>
                    <th>Số ngày nghỉ</th>
                    <th>Tổng công</th>
                </tr>`;
            tableHead.innerHTML = headerRow;

            // Lọc dữ liệu nhân viên dựa trên phòng ban và tên
            let filteredUsers = usersData;
            if (phongBan) {
                filteredUsers = filteredUsers.filter(user => user.ten_phong_ban === phongBan);
            }
            if (searchName) {
                filteredUsers = filteredUsers.filter(user => user.ho_ten.toLowerCase().includes(searchName));
            }

            tableBody.innerHTML = '';

            if (!filteredUsers || filteredUsers.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="${daysInMonth + 6}">Không có dữ liệu nhân viên</td></tr>`;
                return;
            }

            filteredUsers.forEach(user => {
                const row = document.createElement('tr');
                let rowContent = `
                    <td>${user.id_nhan_vien * 1000 + 1}</td>
                    <td>${user.id_nhan_vien}</td>
                    <td>${user.ho_ten}</td>
                `;

                for (let day = 1; day <= daysInMonth; day++) {
                    const date = new Date(year, month - 1, day);
                    const dateStr = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
                    const isSunday = date.getDay() === 0;
                    const isFutureDate = year > currentYear ||
                        (year === currentYear && month > currentMonth) ||
                        (year === currentYear && month === currentMonth && day > currentDay);

                    const attendanceRecord = attendanceData.find(record =>
                        record.id_nhan_vien == user.id_nhan_vien &&
                        record.ngay_lam_viec === dateStr
                    );

                    let currentStatus = attendanceRecord ? (attendanceRecord.trang_thai_trua || 'Chưa điểm danh') : 'Chưa điểm danh';
                    let isDisabled = isFutureDate || !userPermissions.quyen_sua;

                    if (isSunday) {
                        currentStatus = 'Đúng giờ';
                        isDisabled = true;

                        if (!attendanceRecord) {
                            const data = {
                                id_nhan_vien: user.id_nhan_vien,
                                ngay_lam_viec: dateStr,
                                gio_vao: '08:30:00',
                                gio_trua: '12:00:00',
                                gio_ra: '17:30:00',
                                trang_thai: 'Đúng giờ',
                                trang_thai_sang: 'Đúng giờ',
                                trang_thai_trua: 'Đúng giờ',
                                trang_thai_chieu: 'Đúng giờ',
                                month: month,
                                year: year
                            };

                            fetch("http://localhost/doanqlns/index.php/api/chamcong", {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify(data)
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result.success) {
                                    console.log(`Tự động thêm điểm danh "Đúng giờ" cho Chủ nhật ${dateStr}, nhân viên ${user.id_nhan_vien}`);
                                } else {
                                    console.error("Thêm điểm danh thất bại:", result.message);
                                }
                            })
                            .catch(error => console.error("Lỗi khi tự động thêm điểm danh:", error));
                        }
                    }

                    const isCurrentDay = (year === currentYear && month === currentMonth && day === currentDay);
                   rowContent += `
                        <td class="${isCurrentDay ? 'current-day-cell' : ''}">
                            <select class="status-select" 
                                    data-id="${user.id_nhan_vien}" 
                                    data-date="${dateStr}" 
                                    onchange="updateAttendanceTrua(this)"
                                    ${isDisabled ? 'disabled' : ''}>
                                <option value="Chưa điểm danh" ${currentStatus === 'Chưa điểm danh' ? 'selected' : ''}>Chưa điểm danh</option>
                                <option value="Đúng giờ" ${currentStatus === 'Đúng giờ' ? 'selected' : ''}>Đúng giờ</option>
                                <option value="Đi trễ" ${currentStatus === 'Đi trễ' ? 'selected' : ''}>Đi trễ</option>
                                <option value="Ra sớm" ${currentStatus === 'Ra sớm' ? 'selected' : ''}>Ra sớm</option>
                                <option value="Có phép" ${currentStatus === 'Có phép' ? 'selected' : ''}>Có phép</option>
                                <option value="Phép Năm" ${currentStatus === 'Phép Năm' ? 'selected' : ''}>Phép Năm</option>
                                <option value="Nghỉ Lễ" ${currentStatus === 'Nghỉ Lễ' ? 'selected' : ''}>Nghỉ Lễ</option>
                                <option value="Nghỉ nữa buổi" ${currentStatus === 'Nghỉ nữa buổi' ? 'selected' : ''}>Nghỉ nữa buổi</option>
                            </select>
                        </td>
                    `;
                }

                const { diemDanhDays, nghiDays, totalWorkDays } = calculateAttendanceStats(user.id_nhan_vien, month, year);
                rowContent += `
                    <td class="diem-danh-days" data-id="${user.id_nhan_vien}">${formatNumber(diemDanhDays)}</td>
                    <td class="nghi-days" data-id="${user.id_nhan_vien}">${formatNumber(nghiDays)}</td>
                    <td class="total-work-days" data-id="${user.id_nhan_vien}">${formatNumber(totalWorkDays)}</td>
                `;

                row.innerHTML = rowContent;
                tableBody.appendChild(row);
            });
        }

        // Hàm tính toán trạng thái tổng hợp từ sáng, trưa và chiều
        function calculateOverallStatus(trangThaiSang, trangThaiTrua, trangThaiChieu) {
            console.log('calculateOverallStatus - Input:', { trangThaiSang, trangThaiTrua, trangThaiChieu });
            
            // Nếu cả ba đều null hoặc chưa điểm danh
            if (!trangThaiSang && !trangThaiTrua && !trangThaiChieu) {
                console.log('calculateOverallStatus - Kết quả: Chưa điểm danh');
                return 'Chưa điểm danh';
            }
            
            // Kiểm tra trường hợp "Làm chiều, vắng sáng" (có trưa và chiều, không có sáng)
            if (!trangThaiSang && trangThaiTrua && trangThaiChieu) {
                console.log('calculateOverallStatus - Kết quả: Nghỉ nữa buổi (Làm chiều, vắng sáng)');
                return 'Nghỉ nữa buổi';
            }
            
            // Kiểm tra trường hợp "Làm sáng, vắng chiều" (có sáng và trưa, không có chiều)
            if (trangThaiSang && trangThaiTrua && !trangThaiChieu) {
                console.log('calculateOverallStatus - Kết quả: Nghỉ nữa buổi (Làm sáng, vắng chiều)');
                return 'Nghỉ nữa buổi';
            }
            
            // Nếu chỉ có một trạng thái
            if (trangThaiSang && !trangThaiTrua && !trangThaiChieu) {
                return trangThaiSang;
            }
            if (!trangThaiSang && trangThaiTrua && !trangThaiChieu) {
                return trangThaiTrua;
            }
            if (!trangThaiSang && !trangThaiTrua && trangThaiChieu) {
                return trangThaiChieu;
            }
            
            // Nếu có nhiều trạng thái, ưu tiên trạng thái xấu hơn
            const statusPriority = {
                'Nghỉ nữa buổi': 1,
                'Đi trễ': 2,
                'Ra sớm': 3,
                'Có phép': 4,
                'Phép Năm': 5,
                'Nghỉ Lễ': 6,
                'Đúng giờ': 7
            };
            
            const sangPriority = statusPriority[trangThaiSang] || 8;
            const truaPriority = statusPriority[trangThaiTrua] || 8;
            const chieuPriority = statusPriority[trangThaiChieu] || 8;
            
            // Tìm trạng thái có priority thấp nhất (xấu nhất)
            const priorities = [
                { status: trangThaiSang, priority: sangPriority },
                { status: trangThaiTrua, priority: truaPriority },
                { status: trangThaiChieu, priority: chieuPriority }
            ].filter(item => item.status); // Chỉ lấy những trạng thái có giá trị
            
            if (priorities.length === 0) return 'Chưa điểm danh';
            
            const worstStatus = priorities.reduce((min, current) => 
                current.priority < min.priority ? current : min
            );
            
            return worstStatus.status;
        }

        // Hàm cập nhật trạng thái điểm danh sáng (chỉ cập nhật giờ vào)
        async function updateAttendanceSang(select) {
            if (!userPermissions.quyen_sua) {
                alert("Bạn không có quyền chỉnh sửa chấm công!");
                select.value = attendanceData.find(record => 
                    record.id_nhan_vien == select.getAttribute('data-id') && 
                    record.ngay_lam_viec === select.getAttribute('data-date')
                )?.trang_thai || 'Chưa điểm danh';
                return;
            }

            const userId = select.getAttribute('data-id');
            const date = select.getAttribute('data-date');
            const status = select.value;
            const month = parseInt(document.getElementById('selectMonth').value);
            const year = parseInt(document.getElementById('selectYear').value);

            showLoading();

            const data = {
                id_nhan_vien: userId,
                ngay_lam_viec: date,
                gio_vao: status === 'Đúng giờ' ? '08:30:00' : 
                         (status === 'Đi trễ' ? '09:30:00' : 
                          (status === 'Ra sớm' ? '07:30:00' : 
                           (status === 'Có phép' ? '08:30:00' :
                            (status === 'Phép Năm' ? '08:30:00' :
                             (status === 'Nghỉ Lễ' ? '08:30:00' :
                              (status === 'Nghỉ nữa buổi' ? '08:30:00' : null)))))),
                gio_ra: null, // Không cập nhật giờ ra khi điểm danh sáng
                trang_thai: status, // Trạng thái chung
                month: month,
                year: year
            };

            try {
                // Kiểm tra xem bản ghi đã tồn tại chưa
                const checkResponse = await fetch(`http://localhost/doanqlns/index.php/api/chamcong?id_nhan_vien=${userId}&ngay_cham_cong=${date}`);
                const existingRecord = await checkResponse.json();

                let response;
                if (existingRecord && existingRecord.length > 0) {
                    // Cập nhật bản ghi hiện có (chỉ cập nhật giờ vào và trạng thái sáng)
                    const trangThaiTrua = existingRecord[0].trang_thai_trua || null;
                    const trangThaiChieu = existingRecord[0].trang_thai_chieu || null;
                    console.log('Debug Sáng - Trạng thái hiện tại:', { status, trangThaiTrua, trangThaiChieu });
                    const trangThaiTongHop = calculateOverallStatus(status, trangThaiTrua, trangThaiChieu);
                    console.log('Debug Sáng - Trạng thái tổng hợp:', trangThaiTongHop);
                    
                    const updateData = {
                        ...data,
                        gio_vao: data.gio_vao, // Cập nhật giờ vào
                        gio_trua: existingRecord[0].gio_trua, // Giữ nguyên giờ trưa hiện tại
                        gio_ra: existingRecord[0].gio_ra, // Giữ nguyên giờ ra hiện tại
                        trang_thai_sang: status, // Cập nhật trạng thái sáng
                        trang_thai_trua: trangThaiTrua, // Giữ nguyên trạng thái trưa
                        trang_thai_chieu: trangThaiChieu, // Giữ nguyên trạng thái chiều
                        trang_thai: trangThaiTongHop // Cập nhật trạng thái tổng hợp
                    };
                    response = await fetch(`http://localhost/doanqlns/index.php/api/chamcong?id_nhan_vien=${userId}&ngay_cham_cong=${date}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(updateData)
                    });
                } else {
                    // Thêm bản ghi mới
                    const trangThaiTongHop = calculateOverallStatus(status, null, null);
                    const newData = {
                        ...data,
                        gio_trua: null, // Chưa điểm danh trưa
                        gio_ra: null, // Chưa điểm danh chiều
                        trang_thai_sang: status, // Trạng thái sáng
                        trang_thai_trua: null, // Chưa điểm danh trưa
                        trang_thai_chieu: null, // Chưa điểm danh chiều
                        trang_thai: trangThaiTongHop // Trạng thái tổng hợp
                    };
                    response = await fetch("http://localhost/doanqlns/index.php/api/chamcong", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(newData)
                    });
                }

                const result = await response.json();

                if (result.success) {
                    await loadAttendanceData();
                    updateAttendanceTable();
                    updateAttendanceTableChieu(); // Cập nhật cả bảng chiều
                } else {
                    throw new Error('Lỗi từ API: ' + (result.message || 'Không rõ nguyên nhân'));
                }
            } catch (error) {
                console.error("Lỗi khi cập nhật điểm danh sáng:", error);
                alert(error.message || "Lỗi khi cập nhật điểm danh sáng");
                select.value = attendanceData.find(record => 
                    record.id_nhan_vien == userId && 
                    record.ngay_lam_viec === date
                )?.trang_thai || 'Chưa điểm danh';
            } finally {
                hideLoading();
            }
        }

        // Hàm cập nhật trạng thái điểm danh chiều (chỉ cập nhật giờ ra)
        async function updateAttendanceChieu(select) {
            if (!userPermissions.quyen_sua) {
                alert("Bạn không có quyền chỉnh sửa chấm công!");
                select.value = attendanceData.find(record => 
                    record.id_nhan_vien == select.getAttribute('data-id') && 
                    record.ngay_lam_viec === select.getAttribute('data-date')
                )?.trang_thai || 'Chưa điểm danh';
                return;
            }

            const userId = select.getAttribute('data-id');
            const date = select.getAttribute('data-date');
            const status = select.value;
            const month = parseInt(document.getElementById('selectMonthChieu').value);
            const year = parseInt(document.getElementById('selectYearChieu').value);

            showLoading();

            const data = {
                id_nhan_vien: userId,
                ngay_lam_viec: date,
                gio_vao: null, // Không cập nhật giờ vào khi điểm danh chiều
                gio_ra: status === 'Đúng giờ' ? '17:30:00' : 
                        (status === 'Đi trễ' ? '18:30:00' : 
                         (status === 'Ra sớm' ? '16:30:00' : 
                          (status === 'Có phép' ? '17:30:00' :
                           (status === 'Phép Năm' ? '17:30:00' :
                            (status === 'Nghỉ Lễ' ? '17:30:00' :
                             (status === 'Nghỉ nữa buổi' ? '17:30:00' : null)))))),
                trang_thai: status, // Trạng thái chung
                month: month,
                year: year
            };

            try {
                // Kiểm tra xem bản ghi đã tồn tại chưa
                const checkResponse = await fetch(`http://localhost/doanqlns/index.php/api/chamcong?id_nhan_vien=${userId}&ngay_cham_cong=${date}`);
                const existingRecord = await checkResponse.json();

                let response;
                if (existingRecord && existingRecord.length > 0) {
                    // Cập nhật bản ghi hiện có (chỉ cập nhật giờ ra và trạng thái chiều)
                    const trangThaiSang = existingRecord[0].trang_thai_sang || null;
                    const trangThaiTrua = existingRecord[0].trang_thai_trua || null;
                    console.log('Debug - Trạng thái hiện tại:', { trangThaiSang, trangThaiTrua, status });
                    const trangThaiTongHop = calculateOverallStatus(trangThaiSang, trangThaiTrua, status);
                    console.log('Debug - Trạng thái tổng hợp:', trangThaiTongHop);
                    
                    const updateData = {
                        ...data,
                        gio_vao: existingRecord[0].gio_vao, // Giữ nguyên giờ vào hiện tại
                        gio_trua: existingRecord[0].gio_trua, // Giữ nguyên giờ trưa hiện tại
                        gio_ra: data.gio_ra, // Cập nhật giờ ra
                        trang_thai_sang: trangThaiSang, // Giữ nguyên trạng thái sáng
                        trang_thai_trua: trangThaiTrua, // Giữ nguyên trạng thái trưa
                        trang_thai_chieu: status, // Cập nhật trạng thái chiều
                        trang_thai: trangThaiTongHop // Cập nhật trạng thái tổng hợp
                    };
                    response = await fetch(`http://localhost/doanqlns/index.php/api/chamcong?id_nhan_vien=${userId}&ngay_cham_cong=${date}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(updateData)
                    });
                } else {
                    // Thêm bản ghi mới
                    const trangThaiTongHop = calculateOverallStatus(status, null, null);
                    const newData = {
                        ...data,
                        gio_vao: null, // Chưa điểm danh sáng
                        gio_trua: null, // Chưa điểm danh trưa
                        trang_thai_sang: null, // Chưa điểm danh sáng
                        trang_thai_trua: null, // Chưa điểm danh trưa
                        trang_thai_chieu: status, // Trạng thái chiều
                        trang_thai: trangThaiTongHop // Trạng thái tổng hợp
                    };
                    response = await fetch("http://localhost/doanqlns/index.php/api/chamcong", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(newData)
                    });
                }

                const result = await response.json();

                if (result.success) {
                    await loadAttendanceData();
                    updateAttendanceTable(); // Cập nhật cả bảng sáng
                    updateAttendanceTableChieu();
                } else {
                    throw new Error('Lỗi từ API: ' + (result.message || 'Không rõ nguyên nhân'));
                }
            } catch (error) {
                console.error("Lỗi khi cập nhật điểm danh chiều:", error);
                alert(error.message || "Lỗi khi cập nhật điểm danh chiều");
                select.value = attendanceData.find(record => 
                    record.id_nhan_vien == userId && 
                    record.ngay_lam_viec === date
                )?.trang_thai || 'Chưa điểm danh';
            } finally {
                hideLoading();
            }
        }

        // Hàm cập nhật trạng thái điểm danh trưa
        async function updateAttendanceTrua(select) {
            if (!userPermissions.quyen_sua) {
                alert("Bạn không có quyền chỉnh sửa chấm công!");
                select.value = attendanceData.find(record => 
                    record.id_nhan_vien == select.getAttribute('data-id') && 
                    record.ngay_lam_viec === select.getAttribute('data-date')
                )?.trang_thai_trua || 'Chưa điểm danh';
                return;
            }

            const userId = select.getAttribute('data-id');
            const date = select.getAttribute('data-date');
            const status = select.value;
            const month = parseInt(document.getElementById('selectMonthTrua').value);
            const year = parseInt(document.getElementById('selectYearTrua').value);

            showLoading();

            const data = {
                id_nhan_vien: userId,
                ngay_lam_viec: date,
                gio_trua: status === 'Đúng giờ' ? '12:00:00' : 
                          (status === 'Đi trễ' ? '13:00:00' : 
                           (status === 'Ra sớm' ? '11:30:00' : 
                            (status === 'Có phép' ? '12:00:00' :
                             (status === 'Phép Năm' ? '12:00:00' :
                              (status === 'Nghỉ Lễ' ? '12:00:00' :
                               (status === 'Nghỉ nữa buổi' ? '12:00:00' : null)))))),
                trang_thai_trua: status,
                month: month,
                year: year
            };

            try {
                // Kiểm tra xem bản ghi đã tồn tại chưa
                const existingResponse = await fetch(`http://localhost/doanqlns/index.php/api/chamcong?id_nhan_vien=${userId}&ngay_cham_cong=${date}`);
                const existingRecord = await existingResponse.json();

                let response;
                if (existingRecord && existingRecord.length > 0) {
                    // Cập nhật bản ghi hiện có
                    const trangThaiSang = existingRecord[0].trang_thai_sang;
                    const trangThaiChieu = existingRecord[0].trang_thai_chieu;
                    console.log('Debug Trưa - Trạng thái hiện tại:', { trangThaiSang, status, trangThaiChieu });
                    const trangThaiTongHop = calculateOverallStatus(trangThaiSang, status, trangThaiChieu);
                    console.log('Debug Trưa - Trạng thái tổng hợp:', trangThaiTongHop);
                    
                    const updateData = {
                        ...data,
                        gio_vao: existingRecord[0].gio_vao, // Giữ nguyên giờ vào hiện tại
                        gio_ra: existingRecord[0].gio_ra, // Giữ nguyên giờ ra hiện tại
                        gio_trua: data.gio_trua, // Cập nhật giờ trưa
                        trang_thai_sang: trangThaiSang, // Giữ nguyên trạng thái sáng
                        trang_thai_chieu: trangThaiChieu, // Giữ nguyên trạng thái chiều
                        trang_thai_trua: status, // Cập nhật trạng thái trưa
                        trang_thai: trangThaiTongHop // Cập nhật trạng thái tổng hợp
                    };
                    response = await fetch(`http://localhost/doanqlns/index.php/api/chamcong?id_nhan_vien=${userId}&ngay_cham_cong=${date}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(updateData)
                    });
                } else {
                    // Thêm bản ghi mới
                    const trangThaiTongHop = calculateOverallStatus(null, status, null);
                    const newData = {
                        ...data,
                        gio_vao: null, // Chưa điểm danh sáng
                        gio_ra: null, // Chưa điểm danh chiều
                        trang_thai_sang: null, // Chưa điểm danh sáng
                        trang_thai_chieu: null, // Chưa điểm danh chiều
                        trang_thai_trua: status, // Trạng thái trưa
                        trang_thai: trangThaiTongHop // Trạng thái tổng hợp
                    };
                    response = await fetch("http://localhost/doanqlns/index.php/api/chamcong", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(newData)
                    });
                }

                const result = await response.json();

                if (result.success) {
                    await loadAttendanceData();
                    updateAttendanceTableTrua();
                    alert('Cập nhật điểm danh trưa thành công!');
                } else {
                    throw new Error(result.message || 'Lỗi khi cập nhật điểm danh trưa');
                }
            } catch (error) {
                console.error('Lỗi khi cập nhật điểm danh trưa:', error);
                alert('Lỗi khi cập nhật điểm danh trưa: ' + error.message);
                // Khôi phục giá trị cũ
                select.value = attendanceData.find(record => 
                    record.id_nhan_vien == userId && 
                    record.ngay_lam_viec === date
                )?.trang_thai_trua || 'Chưa điểm danh';
            } finally {
                hideLoading();
            }
        }

        // Hàm cập nhật trạng thái điểm danh (giữ lại để tương thích)
        async function updateAttendance(select) {
            if (!userPermissions.quyen_sua) {
                alert("Bạn không có quyền chỉnh sửa chấm công!");
                select.value = attendanceData.find(record => 
                    record.id_nhan_vien == select.getAttribute('data-id') && 
                    record.ngay_lam_viec === select.getAttribute('data-date')
                )?.trang_thai || 'Chưa điểm danh';
                return;
            }

            const userId = select.getAttribute('data-id');
            const date = select.getAttribute('data-date');
            const status = select.value;
            const month = parseInt(document.getElementById('selectMonth').value);
            const year = parseInt(document.getElementById('selectYear').value);

            showLoading();

            const data = {
                id_nhan_vien: userId,
                ngay_lam_viec: date,
                gio_vao: status === 'Đúng giờ' ? '08:30:00' : (status === 'Đi trễ' ? '09:30:00' : null),
                gio_ra: (status === 'Đúng giờ' || status === 'Đi trễ') ? '17:30:00' : null,
                trang_thai: status,
                month: month,
                year: year
            };

            try {
                // Kiểm tra xem bản ghi đã tồn tại chưa
                const checkResponse = await fetch(`http://localhost/doanqlns/index.php/api/chamcong?id_nhan_vien=${userId}&ngay_cham_cong=${date}`);
                const existingRecord = await checkResponse.json();

                let response;
                if (existingRecord && existingRecord.length > 0) {
                    // Cập nhật bản ghi hiện có
                    response = await fetch(`http://localhost/doanqlns/index.php/api/chamcong?id_nhan_vien=${userId}&ngay_cham_cong=${date}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(data)
                    });
                } else {
                    // Thêm bản ghi mới
                    response = await fetch("http://localhost/doanqlns/index.php/api/chamcong", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(data)
                    });
                }

                const result = await response.json();

                if (result.success) {
                    await loadAttendanceData();
                    updateAttendanceTable();
                } else {
                    throw new Error('Lỗi từ API: ' + (result.message || 'Không rõ nguyên nhân'));
                }
            } catch (error) {
                console.error("Lỗi khi cập nhật điểm danh:", error);
                alert(error.message || "Lỗi khi cập nhật điểm danh");
                select.value = attendanceData.find(record => 
                    record.id_nhan_vien == userId && 
                    record.ngay_lam_viec === date
                )?.trang_thai || 'Chưa điểm danh';
            } finally {
                hideLoading();
            }
        }

        // Hàm xử lý chỉnh sửa chấm công
        async function handleEditChamCong(event) {
            event.preventDefault();
            if (!userPermissions.quyen_sua) {
                alert("Bạn không có quyền chỉnh sửa chấm công!");
                return;
            }

            const id = document.getElementById('editIdChamCong').value;
            const idNhanVien = document.getElementById('editIdNhanVien').value;
            const ngayLamViec = document.getElementById('editNgayLamViec').value;
            const gioVao = document.getElementById('editGioVao').value || null;
            const gioTrua = document.getElementById('editGioTrua').value || null;
            const gioRa = document.getElementById('editGioRa').value || null;
            const trangThaiSang = document.getElementById('editTrangThaiSang').value || null;
            const trangThaiTrua = document.getElementById('editTrangThaiTrua').value || null;
            const trangThaiChieu = document.getElementById('editTrangThaiChieu').value || null;
            const trangThai = document.getElementById('editTrangThai').value;

            // Validation dữ liệu bắt buộc
            if (!idNhanVien || !ngayLamViec || !trangThai) {
                alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
                return;
            }

            // Lấy tháng và năm từ ngày làm việc
            const ngayLamViecDate = new Date(ngayLamViec);
            const month = ngayLamViecDate.getMonth() + 1;
            const year = ngayLamViecDate.getFullYear();

            const data = {
                id_nhan_vien: idNhanVien,
                ngay_lam_viec: ngayLamViec,
                gio_vao: gioVao,
                gio_trua: gioTrua,
                gio_ra: gioRa,
                trang_thai_sang: trangThaiSang,
                trang_thai_trua: trangThaiTrua,
                trang_thai_chieu: trangThaiChieu,
                trang_thai: trangThai,
                month: month,
                year: year
            };

            // Debug: Log dữ liệu trước khi gửi
            console.log('Dữ liệu gửi lên server:', data);
            
            showLoading();
            try {
                const response = await fetch(`http://localhost/doanqlns/index.php/api/chamcong?id_nhan_vien=${idNhanVien}&ngay_cham_cong=${ngayLamViec}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();

                if (result.success) {
                    closeEditChamCongModal();
                    await loadAttendanceData();
                    updateAttendanceTable();
                    renderChamCongTable(attendanceData);
                    alert("Chỉnh sửa chấm công thành công!");
                } else {
                    throw new Error(result.message || "Lỗi khi chỉnh sửa chấm công");
                }
            } catch (error) {
                console.error("Lỗi khi chỉnh sửa chấm công:", error);
                alert(error.message || "Lỗi khi chỉnh sửa chấm công");
            } finally {
                hideLoading();
            }
        }

        // Hàm xóa chấm công
        async function deleteChamCong(id) {
            if (!userPermissions.quyen_xoa) {
                alert("Bạn không có quyền xóa chấm công!");
                return;
            }
            if (!confirm(`Bạn có chắc chắn muốn xóa chấm công ID ${id} không?`)) return;

            showLoading();
            try {
                const response = await fetch(`http://localhost/doanqlns/index.php/api/chamcong?id=${id}`, {
                    method: 'DELETE'
                });
                const result = await response.json();

                if (result.success) {
                    await loadAttendanceData();
                    updateAttendanceTable();
                    renderChamCongTable(attendanceData);
                    alert(`Đã xóa chấm công với ID: ${id}`);
                } else {
                    throw new Error("Lỗi khi xóa chấm công: " + (result.message || "Không rõ nguyên nhân"));
                }
            } catch (error) {
                console.error("Lỗi khi xóa chấm công:", error);
                alert(error.message || "Lỗi khi xóa chấm công");
            } finally {
                hideLoading();
            }
        }

        // Hàm xuất Excel
        async function exportToExcel() {
            const month = parseInt(document.getElementById('selectMonth').value);
            const year = parseInt(document.getElementById('selectYear').value) || new Date().getFullYear();
            const phongBan = document.getElementById('selectPhongBan').value;
            const searchName = document.getElementById('searchName').value.toLowerCase();
            const daysInMonth = new Date(year, month, 0).getDate();

            showLoading();
            try {
                let filteredUsers = usersData;
                if (phongBan) {
                    filteredUsers = filteredUsers.filter(user => user.ten_phong_ban === phongBan);
                }
                if (searchName) {
                    filteredUsers = filteredUsers.filter(user => user.ho_ten.toLowerCase().includes(searchName));
                }

                if (!filteredUsers || filteredUsers.length === 0) {
                    throw new Error("Không có dữ liệu nhân viên để xuất.");
                }

                const headers = [
                    'ID Chấm Công',
                    'ID Nhân Viên',
                    'Họ Tên',
                    ...Array.from({ length: daysInMonth }, (_, i) => `Ngày ${i + 1}`),
                    'Điểm danh',
                    'Số ngày nghỉ',
                    'Tổng công'
                ];

                const csvRows = [headers.map(header => `"${header}"`).join(',')];

                filteredUsers.forEach(user => {
                    const row = [
                        user.id_nhan_vien * 1000 + 1,
                        user.id_nhan_vien,
                        user.ho_ten
                    ];

                    for (let day = 1; day <= daysInMonth; day++) {
                        const dateStr = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
                        const attendanceRecord = attendanceData.find(record =>
                            record.id_nhan_vien == user.id_nhan_vien &&
                            record.ngay_lam_viec === dateStr
                        );
                        const status = attendanceRecord ? attendanceRecord.trang_thai : 'Chưa điểm danh';
                        row.push(status);
                    }

                    const { diemDanhDays, nghiDays, totalWorkDays } = calculateAttendanceStats(user.id_nhan_vien, month, year);
                    row.push(formatNumber(diemDanhDays));
                    row.push(formatNumber(nghiDays));
                    row.push(formatNumber(totalWorkDays));

                    csvRows.push(row.map(value => `"${value.toString().replace(/"/g, '""')}"`).join(','));
                });

                const csvContent = '\uFEFF' + csvRows.join('\n');
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.setAttribute('href', url);
                link.setAttribute('download', `BangChamCong_Thang${month}_${year}.csv`);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
            } catch (error) {
                console.error('Lỗi khi xuất CSV:', error);
                alert('Lỗi khi xuất file CSV: ' + error.message);
            } finally {
                hideLoading();
            }
        }

        // Hiển thị modal điểm danh nghỉ lễ
function showHolidayModal() {
    if (!userPermissions.quyen_them) {
        alert("Bạn không có quyền điểm danh!");
        return;
    }
    document.getElementById('holidayModal').style.display = 'flex';
}

// Đóng modal điểm danh nghỉ lễ
function closeHolidayModal() {
    document.getElementById('holidayModal').style.display = 'none';
}

// Xử lý submit form điểm danh nghỉ lễ
document.getElementById('holidayForm').addEventListener('submit', async (event) => {
    event.preventDefault();
    const startDate = document.getElementById('holidayStartDate').value;
    const endDate = document.getElementById('holidayEndDate').value;

    if (!startDate || !endDate) {
        alert("Vui lòng chọn cả ngày bắt đầu và ngày kết thúc!");
        return;
    }

    if (new Date(endDate) < new Date(startDate)) {
        alert("Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu!");
        return;
    }

    const data = {
        start_date: startDate,
        end_date: endDate
    };

    showLoading();
    try {
        const response = await fetch('http://localhost/doanqlns/index.php/api/chamcong?action=markHoliday', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await response.json();

        if (result.success) {
            closeHolidayModal();
            await loadAttendanceData();
            updateAttendanceTable();
            renderChamCongTable(attendanceData);
            alert("Điểm danh nghỉ lễ thành công!");
        } else {
            throw new Error(result.message || "Lỗi khi điểm danh nghỉ lễ");
        }
    } catch (error) {
        console.error("Lỗi khi điểm danh nghỉ lễ:", error);
        alert(error.message || "Lỗi khi điểm danh nghỉ lễ");
    } finally {
        hideLoading();
    }
});

// Cập nhật sự kiện đóng modal
document.getElementById('holidayModal').addEventListener('click', (e) => {
    if (e.target === document.getElementById('holidayModal')) {
        closeHolidayModal();
    }
});

        // Sự kiện đóng modal
        diemDanhModal.addEventListener('click', (e) => {
            if (e.target === diemDanhModal) {
                closeDiemDanhModal();
            }
        });

        // Sự kiện đóng modal điểm danh chiều
        document.getElementById('diemDanhChieuModal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('diemDanhChieuModal')) {
                closeDiemDanhChieuModal();
            }
        });

        // Sự kiện đóng dropdown khi click ra ngoài
        document.addEventListener('click', (e) => {
            const dropdown = document.querySelector('.dropdown');
            if (!dropdown.contains(e.target)) {
                closeDropdown();
            }
        });

        editChamCongModal.addEventListener('click', (e) => {
            if (e.target === editChamCongModal) {
                closeEditChamCongModal();
            }
        });

        document.getElementById('detailChamCongModal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('detailChamCongModal')) {
                closeDetailChamCongModal();
            }
        });

        // Đóng modal gương mặt khi click ra ngoài
        document.getElementById('faceModal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('faceModal')) {
                closeFaceModal();
            }
        });

        // Sự kiện submit form chỉnh sửa
        document.getElementById('editChamCongForm').addEventListener('submit', handleEditChamCong);

        // Sự kiện xuất Excel
        document.getElementById('exportExcelBtn').addEventListener('click', exportToExcel);
        document.getElementById('exportExcelBtnChieu').addEventListener('click', exportToExcel);

        // Khởi tạo khi trang được tải
        document.addEventListener('DOMContentLoaded', async () => {
            selectedDateElement.textContent = `${selectedDate.getDate().toString().padStart(2, '0')}/${(selectedDate.getMonth() + 1).toString().padStart(2, '0')}/${selectedDate.getFullYear()}`;
            // Tải cấu hình giờ làm việc trước để sử dụng cho điểm danh
            await loadCauHinhGioLamViec();
            await loadPhongBanData();
            await loadUsersData();
            await loadAttendanceData();
            
            // Khởi tạo dữ liệu cho modal chiều
            const currentDate = new Date();
            const currentMonth = currentDate.getMonth() + 1;
            const currentYear = currentDate.getFullYear();
            
            document.getElementById('selectMonthChieu').value = currentMonth;
            document.getElementById('selectYearChieu').value = currentYear;
        });

        // Lắng nghe sự kiện cập nhật từ sidebar
        window.addEventListener('attendanceUpdated', async (event) => {
            console.log('Nhận được sự kiện cập nhật từ sidebar:', event.detail);
            if (event.detail.source === 'sidebar') {
                // Cập nhật dữ liệu chấm công
                await loadAttendanceData();
                // Cập nhật bảng điểm danh trong modal sáng
                updateAttendanceTable();
                // Cập nhật bảng điểm danh trong modal chiều
                updateAttendanceTableChieu();
                // Cập nhật bảng chấm công chính
                renderChamCongTable(attendanceData);
                console.log('Đã cập nhật bảng chấm công sau khi điểm danh từ sidebar');
            }
        });
    </script>
            </div>
        </div>
    </div>
</body>
</html>