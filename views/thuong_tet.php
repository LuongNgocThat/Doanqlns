<?php
$current_page = 'thuong_tet.php';
require_once __DIR__ . '/../includes/check_login.php';
include(__DIR__ . '/../includes/header.php');
include(__DIR__ . '/../includes/sidebar.php');
?>
<!DOCTYPE html>
<html lang="vi" class="light-style layout-navbar-fixed layout-menu-fixed">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRM Pro - Thưởng Tết</title>

    <!-- Font Awesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS riêng -->
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

    .name-link,
    .name-link:hover {
        text-decoration: none;
        color: #007bff;
    }
    body {
        font-family: 'Roboto', sans-serif;
        background: #f4f6f9;
        margin: 0;
        padding: 0;
    }
    .main-content {
        margin-left: 240px;
        padding: 20px;
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
    .filter-container button#autoCreateBtn:hover {
        background: #28a745;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #eee;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    th {
        background: #f8f9fa;
        font-weight: 700;
        color: #495057;
        font-size: 14px;
        letter-spacing: 0.3px;
    }
    td {
        font-size: 13px;
        font-weight: 500;
    }
    tr:hover {
        background: #f8f9fa;
    }
    .btn {
        padding: 6px 12px;
        margin: 2px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s;
    }
    .btn-primary {
        background: #007bff;
        color: white;
    }
    .btn-primary:hover {
        background: #0056b3;
    }
    .btn-success {
        background: #28a745;
        color: white;
    }
    .btn-success:hover {
        background: #218838;
    }
    .btn-danger {
        background: #dc3545;
        color: white;
    }
    .btn-danger:hover {
        background: #c82333;
    }
    .btn-warning {
        background: #ffc107;
        color: #212529;
    }
    .btn-warning:hover {
        background: #e0a800;
    }
    .btn-info {
        background: #17a2b8;
        color: white;
    }
    .btn-info:hover {
        background: #138496;
    }
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
    }
    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 20px;
        border: none;
        border-radius: 8px;
        width: 80%;
        max-width: 600px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }
    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
    }
    .form-group {
        margin-bottom: 15px;
    }
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
        color: #333;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.2);
    }
    .currency {
        font-weight: bold;
        color: #28a745;
    }
    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .currency {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-weight: 700;
        font-size: 14px;
        color: #28a745;
        letter-spacing: 0.5px;
    }
    
    /* Cột Phép Năm */
    #thuongTetTable td:nth-child(19) {
        font-weight: 600;
        color: #6f42c1;
        font-size: 13px;
    }
    
    /* Cột Mức Thưởng */
    #thuongTetTable td:nth-child(20) {
        font-weight: 700;
        color: #fd7e14;
        font-size: 14px;
    }
    .status-chua-duyet {
        background: #fff3cd;
        color: #856404;
    }
    .status-da-duyet {
        background: #d4edda;
        color: #155724;
    }
    .status-da-thanh-toan {
        background: #d1ecf1;
        color: #0c5460;
    }
    .status-tu-choi {
        background: #f8d7da;
        color: #721c24;
    }
    .status-dang-xu-ly {
        background: #e2e3e5;
        color: #383d41;
    }
    .clickable-status {
        cursor: pointer;
        user-select: none;
        transition: all 0.3s ease;
    }
    .clickable-status:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .stat-card h4 {
        margin: 0 0 10px 0;
        font-size: 24px;
        font-weight: 600;
    }
    .stat-card p {
        margin: 0;
        font-size: 14px;
        opacity: 0.9;
    }
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
    }
    .alert-success {
        color: #155724;
        background-color: #d4edda;
        border-color: #c3e6cb;
    }
    .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }
    .alert-info {
        color: #0c5460;
        background-color: #d1ecf1;
        border-color: #bee5eb;
    }
    
    /* CSS cho bảng giống luong.php */
    .table-container {
        overflow-x: auto;
        margin: 0 auto 20px;
        max-width: 100%;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .table-with-departments {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        min-width: 2000px;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .table-with-departments th,
    .table-with-departments td {
        padding: 12px 16px;
        border-bottom: 1px solid #ddd;
        text-align: left;
        word-wrap: break-word;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: middle;
    }
    
    .table-with-departments th {
        background: #007bff;
        color: #fff;
        font-weight: 500;
        text-align: center;
        height: 60px;
        font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
    }
    
    .table-with-departments tr:nth-child(even) {
        background: #f9f9f9;
    }
    
    .table-with-departments tr:hover {
        background: #eef3f7;
    }
    
    /* CSS cho header nhóm */
    .header-group-row th {
        background: linear-gradient(135deg, #4a90e2, #357abd) !important;
        color: #fff !important;
        font-weight: bold !important;
        text-align: center !important;
        padding: 16px 18px !important;
        border: 1px solid #5a67d8 !important;
        border-right: 2px solid rgba(255, 255, 255, 0.3) !important;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
        position: relative;
        min-width: 100px;
    }
    
    .header-group-row th:last-child {
        border-right: 1px solid #5a67d8 !important;
    }
    
    /* CSS cho header chi tiết */
    .header-detail-row th {
        background: linear-gradient(135deg,rgb(188, 218, 238),rgb(184, 213, 243)) !important;
        color: #4a5568 !important;
        font-weight: 600 !important;
        text-align: center !important;
        padding: 14px 16px !important;
        border: 1px solid #e2e8f0 !important;
        border-right: 1px solid rgba(74, 85, 104, 0.2) !important;
        font-size: 13px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        position: relative;
        min-width: 100px;
    }
    
    .header-detail-row th:last-child {
        border-right: 1px solid #e2e8f0 !important;
    }
    
    /* CSS cho font header */
    .header-detail-row th {
        font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
        font-weight: 700;
        font-size: 13px;
        letter-spacing: 0.2px;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }
    
    .header-group-row th {
        font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
        font-weight: 800;
        font-size: 14px;
        letter-spacing: 0.5px;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }
    
    /* CSS cho sticky header */
    .header-group-row {
        position: sticky;
        top: 0;
        z-index: 30;
    }
    
    .header-detail-row {
        position: sticky;
        top: 45px;
        z-index: 25;
    }
    
    /* Hiệu ứng hover cho header */
    .header-group-row th:hover {
        background: linear-gradient(135deg, #5a67d8, #6b46c1) !important;
        transform: translateY(-1px);
        transition: all 0.3s ease;
    }
    
    .header-detail-row th:hover {
        background: linear-gradient(135deg, #edf2f7, #e2e8f0) !important;
        color: #2d3748 !important;
        transform: translateY(-1px);
        transition: all 0.3s ease;
    }
    
    /* CSS cho các cột số */
    .table-with-departments td:nth-child(n+4) {
        min-width: 120px;
        text-align: center;
        font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
        font-weight: 600;
        font-size: 14px;
        letter-spacing: 0.3px;
        color: #2d3748;
        line-height: 1.4;
    }
    
    /* CSS cho cột tên nhân viên */
    .table-with-departments td:nth-child(2) {
        min-width: 150px;
        text-align: left;
        padding-left: 20px;
    }
    
    /* CSS cho cột năm */
    .table-with-departments td:nth-child(3) {
        min-width: 80px;
        text-align: center;
    }
    
    /* CSS cho tên nhân viên - đảm bảo nằm ngang */
    .name-link {
        white-space: nowrap !important;
        display: inline-block !important;
        max-width: 100% !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        text-decoration: none !important;
        color: #007bff !important;
    }
    
    .name-link:hover {
        text-decoration: underline !important;
        color: #0056b3 !important;
    }
    
    /* CSS cho số tiền */
    .table-with-departments td {
        font-variant-numeric: tabular-nums;
    }
    
    /* CSS cho các cột lương theo tháng - override cho header detail row */
    .header-detail-row th:nth-child(n+7):nth-child(-n+18) {
        min-width: 90px;
        max-width: 110px;
        font-size: 12px;
        font-weight: 700;
        text-align: center;
        background: linear-gradient(135deg,rgb(188, 218, 238),rgb(184, 213, 243)) !important;
        position: relative;
        cursor: help;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #495057;
        letter-spacing: 0.3px;
    }
    
    .header-detail-row th:nth-child(n+7):nth-child(-n+18):hover::after {
        content: "Lương thực nhận từ bảng luong";
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: #333;
        color: white;
        padding: 5px 8px;
        border-radius: 4px;
        font-size: 10px;
        white-space: nowrap;
        z-index: 1000;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    
    .table-with-departments td:nth-child(n+7):nth-child(-n+18) {
        min-width: 90px;
        max-width: 110px;
        font-size: 13px;
        font-weight: 600;
        text-align: center;
        padding: 8px 4px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    /* Responsive cho mobile */
    @media (max-width: 768px) {
        .table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table-with-departments {
            min-width: 2000px;
        }
        
        .table-with-departments th,
        .table-with-departments td {
            padding: 8px;
        }
        
        .table-with-departments th {
            height: 50px;
            font-size: 11px;
        }
        
        #thuongTetTable th:nth-child(n+7):nth-child(-n+18),
        #thuongTetTable td:nth-child(n+7):nth-child(-n+18) {
            min-width: 70px;
            max-width: 80px;
            font-size: 11px;
        }
    }
    </style>
</head>
<body>
    <div class="layout-wrapper">
        <div class="layout-page">
            <div class="content-wrapper">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <h3><i class="fas fa-gift"></i> Quản Lý Thưởng Tết</h3>
                            
                            <!-- Thống kê -->
                            <div class="stats-container" id="statsContainer">
                                <!-- Stats will be loaded here -->
                            </div>
                            
                            <!-- Bộ lọc -->
                            <div class="filter-container">
                                <select id="yearFilter">
                                    <option value="">Tất cả năm</option>
                                    <?php for($i=date('Y'); $i>=2020; $i--): ?>
                                    <option value="<?= $i ?>" <?= $i == date('Y') ? 'selected' : '' ?>><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select id="statusFilter">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="Chưa duyệt">Chưa duyệt</option>
                                    <option value="Đang xử lý">Đang xử lý</option>
                                    <option value="Đã duyệt">Đã duyệt</option>
                                    <option value="Từ chối">Từ chối</option>
                                    <option value="Đã thanh toán">Đã thanh toán</option>
                                </select>
                                <button onclick="filterData()"><i class="fas fa-search"></i> Lọc</button>
                                <button onclick="reloadData()" class="btn btn-secondary"><i class="fas fa-refresh"></i> Tải Lại</button>
                                <button onclick="showAddModal()" class="btn btn-success"><i class="fas fa-plus"></i> Thêm Thưởng Tết</button>
                                <button onclick="autoCreateThuongTet()" class="btn btn-info" id="autoCreateBtn"><i class="fas fa-magic"></i> Tự Động Tạo</button>
                                <button onclick="exportToExcel()" class="btn btn-warning"><i class="fas fa-file-excel"></i> Xuất Excel</button>
                            </div>

                            <!-- Ghi chú về dữ liệu -->
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle"></i>
                                <strong>Ghi chú:</strong> 
                                <ul class="mb-0">
                                    <li>Các cột "Lương T1" đến "Lương T12" hiển thị lương thực nhận từ bảng <code>luong</code> theo từng tháng.</li>
                                    <li><strong>Hiển thị theo tháng hiện tại:</strong> Chỉ hiển thị dữ liệu lương đến tháng hiện tại. Các tháng chưa đến sẽ hiển thị mờ.</li>
                                    <li>Cột "Phép Năm (VNĐ)" = (Lương cơ bản ÷ 30) × Số ngày phép năm.</li>
                                    <li><strong>Cột "Mức Thưởng" = Lương tháng 13 + Tiền phép năm + Thưởng/trừ phép năm</strong></li>
                                    <li><strong>Lương tháng 13:</strong> Đủ 12 tháng = Lương thực nhận trung bình 12 tháng; Chưa đủ 12 tháng = (Số tháng làm việc/12) × Lương thực nhận trung bình</li>
                                </ul>
                            </div>
                            
                            <!-- Bảng dữ liệu -->
                            <div class="table-container">
                                <table id="thuongTetTable" class="table-with-departments">
                                    <thead>
                                        <!-- Hàng 1: Nhóm các cột -->
                                        <tr class="header-group-row">
                                            <th colspan="3" class="group-header">Thông tin nhân viên</th>
                                            <th colspan="3" class="group-header">Đánh giá</th>
                                            <th colspan="12" class="group-header">Lương theo tháng</th>
                                            <th colspan="5" class="group-header">Thưởng tết</th>
                                            <th colspan="2" class="group-header">Quản lý</th>
                                        </tr>
                                        <!-- Hàng 2: Tên cột chi tiết -->
                                        <tr class="header-detail-row">
                                            <th><span>STT</span></th>
                                            <th><span>Nhân Viên</span></th>
                                            <th><span>Năm</span></th>
                                            <th><span>Lương Cơ Bản</span></th>
                                            <th><span>Tổng Điểm</span></th>
                                            <th><span>Xếp Loại</span></th>
                                            <th><span>Lương T1</span></th>
                                            <th><span>Lương T2</span></th>
                                            <th><span>Lương T3</span></th>
                                            <th><span>Lương T4</span></th>
                                            <th><span>Lương T5</span></th>
                                            <th><span>Lương T6</span></th>
                                            <th><span>Lương T7</span></th>
                                            <th><span>Lương T8</span></th>
                                            <th><span>Lương T9</span></th>
                                            <th><span>Lương T10</span></th>
                                            <th><span>Lương T11</span></th>
                                            <th><span>Lương T12</span></th>
                                            <th><span>Phép Năm (VNĐ)</span></th>
                        <th><span>Mức Thưởng</span></th>
                        <th><span>Thu nhập tính thuế</span></th>
                        <th><span>Thuế TNCN</span></th>
                        <th><span>Lương Thực Nhận</span></th>
                                            <th><span>Trạng Thái</span></th>
                                            <th><span>Thao Tác</span></th>
                                        </tr>
                                    </thead>
                                    <tbody id="thuongTetTableBody">
                                        <!-- Data will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Thêm/Sửa Thưởng Tết -->
    <div id="thuongTetModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h4 id="modalTitle">Thêm Thưởng Tết</h4>
            <form id="thuongTetForm">
                <input type="hidden" id="thuongTetId">
                
                <div class="form-group">
                    <label for="nhanVienSelect">Nhân Viên *</label>
                    <select id="nhanVienSelect" required>
                        <option value="">Chọn nhân viên</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="nam">Năm *</label>
                    <select id="nam" required>
                        <?php for($i=date('Y'); $i>=2020; $i--): ?>
                        <option value="<?= $i ?>" <?= $i == date('Y') ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="tongDiemDanhGia">Tổng Điểm Đánh Giá</label>
                    <input type="number" id="tongDiemDanhGia" step="0.01" placeholder="Tự động lấy từ đánh giá">
                </div>
                
                <div class="form-group">
                    <label for="xepLoai">Xếp Loại</label>
                    <select id="xepLoai">
                        <option value="">Chọn xếp loại</option>
                        <option value="Xuất sắc">Xuất sắc</option>
                        <option value="Tốt">Tốt</option>
                        <option value="Khá">Khá</option>
                        <option value="Trung bình">Trung bình</option>
                        <option value="Yếu">Yếu</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="soNgayNghiPhep">Số Ngày Nghỉ Phép</label>
                    <input type="number" id="soNgayNghiPhep" placeholder="Tự động lấy từ nghỉ phép">
                </div>
                
                <div class="form-group">
                    <label for="mucThuong">Mức Thưởng (VNĐ) *</label>
                    <input type="number" id="mucThuong" required placeholder="Tự động tính theo công thức">
                    <small class="form-text text-muted">
                        <strong>Công thức lương tháng 13:</strong><br>
                        • Đủ 12 tháng: Lương tháng 13 = Lương trung bình 12 tháng<br>
                        • Chưa đủ 12 tháng: Lương tháng 13 = (Số tháng làm việc/12) × Lương trung bình<br>
                        • Không có dữ liệu: Sử dụng lương cơ bản
                    </small>
                </div>
                
                <div class="form-group">
                    <label for="trangThai">Trạng Thái</label>
                    <select id="trangThai">
                        <option value="Chưa duyệt">Chưa duyệt</option>
                        <option value="Đang xử lý">Đang xử lý</option>
                        <option value="Đã duyệt">Đã duyệt</option>
                        <option value="Từ chối">Từ chối</option>
                        <option value="Đã thanh toán">Đã thanh toán</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="ghiChu">Ghi Chú</label>
                    <textarea id="ghiChu" rows="3" placeholder="Nhập ghi chú"></textarea>
                </div>
                
                <div style="text-align: right;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Global variables
    let thuongTetData = [];
    let nhanVienData = [];

    // Load data on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM Content Loaded');
        
        // Read URL params for post-sync redirect
        const params = new URLSearchParams(window.location.search);
        const namParam = params.get('nam');
        const synced = params.get('synced');
        if (namParam) {
            const yearSel = document.getElementById('yearFilter');
            if ([...yearSel.options].some(o => o.value === namParam)) {
                yearSel.value = namParam;
            }
        }
        if (synced === '1') {
            showAlert('Đã đồng bộ dữ liệu từ Đánh Giá sang Thưởng Tết.', 'success');
        }

        console.log('Starting to load data...');
        
        // Load data sequentially to avoid race conditions
        loadNhanVienData().then(() => {
            console.log('Nhan vien data loaded, now loading thuong tet data...');
            return loadThuongTetData();
        }).then(() => {
            console.log('Thuong tet data loaded, now loading stats...');
            return loadStats();
        }).then(() => {
            console.log('All data loaded successfully!');
        }).catch(error => {
            console.error('Error in data loading sequence:', error);
            showAlert('Lỗi tải dữ liệu: ' + error.message, 'danger');
        });
    });

    // Load nhân viên data
    async function loadNhanVienData() {
        try {
            console.log('Loading nhan vien data...');
            const response = await fetch('/doanqlns/simple_thuong_tet_api.php/nhan-vien');
            const result = await response.json();
            
            console.log('Nhan vien response:', result);
            
            if (result.success) {
                nhanVienData = result.data;
                console.log('Loaded nhan vien count:', nhanVienData.length);
                populateNhanVienSelect();
                return Promise.resolve();
            } else {
                console.error('Error loading nhan vien:', result.message);
                return Promise.reject(new Error(result.message));
            }
        } catch (error) {
            console.error('Error loading nhan vien data:', error);
            return Promise.reject(error);
        }
    }

    // Populate nhân viên select
    function populateNhanVienSelect() {
        const select = document.getElementById('nhanVienSelect');
        select.innerHTML = '<option value="">Chọn nhân viên</option>';
        
        nhanVienData.forEach(nv => {
            const option = document.createElement('option');
            option.value = nv.id_nhan_vien;
            option.textContent = nv.ho_ten;
            select.appendChild(option);
        });
    }

    // Load thưởng tết data
    async function loadThuongTetData() {
        try {
            const year = document.getElementById('yearFilter').value;
            const status = document.getElementById('statusFilter').value;
            
            let url = '/doanqlns/simple_thuong_tet_api.php/thuong-tet-with-evaluation';
            const params = new URLSearchParams();
            if (year) params.append('nam', year);
            if (status) params.append('trang_thai', status);
            if (params.toString()) url += '?' + params.toString();
            
            console.log('Loading data from:', url);
            
            const response = await fetch(url);
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            console.log('Response data:', result);
            
            if (result.success && result.data) {
                thuongTetData = result.data;
                console.log('Loaded data count:', thuongTetData.length);
                console.log('First record:', thuongTetData[0]);
                renderTable();
                return Promise.resolve();
            } else {
                console.error('API Error:', result.message);
                showAlert('Lỗi khi tải dữ liệu: ' + (result.message || 'Unknown error'), 'danger');
                return Promise.reject(new Error(result.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error loading thuong tet data:', error);
            showAlert('Lỗi kết nối server: ' + error.message, 'danger');
            return Promise.reject(error);
        }
    }

    // Load stats
    async function loadStats() {
        try {
            const year = document.getElementById('yearFilter').value || new Date().getFullYear();
            const response = await fetch(`/doanqlns/simple_thuong_tet_api.php/thong-ke?nam=${year}`);
            const result = await response.json();
            
            if (result.success) {
                renderStats(result.data);
            }
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    // Render stats
    function renderStats(data) {
        const container = document.getElementById('statsContainer');
        container.innerHTML = `
            <div class="stat-card">
                <h4>${data.tong_nhan_vien || 0}</h4>
                <p>Tổng Nhân Viên</p>
            </div>
            <div class="stat-card">
                <h4>${formatCurrency(data.tong_thuong || 0)}</h4>
                <p>Tổng Thưởng</p>
            </div>
            <div class="stat-card">
                <h4>${formatCurrency(data.trung_binh_thuong || 0)}</h4>
                <p>Trung Bình</p>
            </div>
            <div class="stat-card">
                <h4>${data.da_duyet || 0}</h4>
                <p>Đã Duyệt</p>
            </div>
            <div class="stat-card">
                <h4>${data.da_thanh_toan || 0}</h4>
                <p>Đã Thanh Toán</p>
            </div>
        `;
    }

    // Render table header dynamically based on current month
    function renderTableHeader() {
        const currentMonth = new Date().getMonth() + 1;
        const headerDetailRow = document.querySelector('.header-detail-row');
        const headerGroupRow = document.querySelector('.header-group-row');
        
        if (!headerDetailRow || !headerGroupRow) return;
        
        // Update group header for salary columns
        const salaryGroupHeader = headerGroupRow.querySelector('th:nth-child(4)'); // "Lương theo tháng" group
        if (salaryGroupHeader) {
            salaryGroupHeader.textContent = `Lương theo tháng (T1-T${currentMonth})`;
        }
        
        // Find the position where salary columns start (after "Xếp Loại")
        const salaryStartIndex = 6; // STT, Nhân Viên, Năm, Lương Cơ Bản, Tổng Điểm, Xếp Loại = 6 columns
        
        // Clear existing salary headers
        const existingSalaryHeaders = headerDetailRow.querySelectorAll('th:nth-child(n+7):nth-child(-n+18)');
        existingSalaryHeaders.forEach(th => th.remove());
        
        // Add salary headers for all 12 months but with different opacity
        for (let thang = 1; thang <= 12; thang++) {
            const th = document.createElement('th');
            th.innerHTML = `<span>Lương T${thang}</span>`;
            if (thang > currentMonth) {
                th.style.opacity = '0.3'; // Mờ đi cho tháng chưa đến
            }
            headerDetailRow.insertBefore(th, headerDetailRow.children[salaryStartIndex + thang - 1]);
        }
    }

    // Render table
    function renderTable() {
        console.log('Rendering table with data:', thuongTetData);
        
        // Render header first
        renderTableHeader();
        
        const tbody = document.getElementById('thuongTetTableBody');
        
        if (!tbody) {
            console.error('Table body element not found!');
            return;
        }
        
        tbody.innerHTML = '';
        
        if (!thuongTetData || thuongTetData.length === 0) {
            console.log('No data to render');
            tbody.innerHTML = '<tr><td colspan="10" class="text-center">Không có dữ liệu</td></tr>';
            return;
        }
        
        console.log('About to render', thuongTetData.length, 'records');
        
        console.log('Rendering', thuongTetData.length, 'records');
        
        thuongTetData.forEach((item, index) => {
            try {
                const row = document.createElement('tr');
                // Tạo HTML cho các cột lương theo tháng (lấy từ bảng luong)
                // Chỉ hiển thị đến tháng hiện tại
                const currentMonth = new Date().getMonth() + 1; // Tháng hiện tại (1-12)
                let luongTheoThangHTML = '';
                if (item.luong_theo_thang) {
                    for (let thang = 1; thang <= 12; thang++) {
                        if (thang <= currentMonth) {
                            const luong = item.luong_theo_thang[thang] || 0;
                            luongTheoThangHTML += `<td class="currency">${luong > 0 ? formatCurrency(luong) : '-'}</td>`;
                        } else {
                            // Tháng chưa đến thì hiển thị "-" với style mờ
                            luongTheoThangHTML += '<td style="opacity: 0.3;">-</td>';
                        }
                    }
                } else {
                    // Nếu không có dữ liệu, hiển thị 12 cột trống
                    for (let thang = 1; thang <= 12; thang++) {
                        if (thang <= currentMonth) {
                            luongTheoThangHTML += '<td>-</td>';
                        } else {
                            luongTheoThangHTML += '<td style="opacity: 0.3;">-</td>';
                        }
                    }
                }
                
                // Tính các cột mới: thu nhập tính thuế, thuế TNCN, lương thực nhận
                const soNguoiPhuThuoc = Number(nhanVienData.find(n=>n.id_nhan_vien==item.id_nhan_vien)?.so_nguoi_phu_thuoc) || 0;
                const luongCoBan = Number(item.luong_co_ban) || 0;
                const mucThuong = Number(item.muc_thuong) || 0;
                const tongGiamTru = 11000000 + (soNguoiPhuThuoc * 4400000);
                const thuNhapTinhThue = Math.max(0, (luongCoBan + mucThuong) - tongGiamTru);
                const thueTNCN = calculatePIT(thuNhapTinhThue);
                const luongThucNhan = Math.max(0, mucThuong - thueTNCN);

                row.innerHTML = `
                    <td><strong>${index + 1}</strong></td>
                    <td><a href="#" class="name-link" onclick="viewEmployee(${item.id_nhan_vien})">${item.ho_ten || 'N/A'}</a></td>
                    <td>${item.nam || 'N/A'}</td>
                    <td class="currency">${formatCurrency(item.luong_co_ban || 0)}</td>
                    <td>${item.tong_diem || '-'}</td>
                    <td>${item.xep_loai || '-'}</td>
                    ${luongTheoThangHTML}
                    <td class="currency">${formatCurrency(item.phep_nam_tien || 0)}</td>
                    <td class="currency"><strong style="color: #fd7e14;">${formatCurrency(mucThuong)}</strong></td>
                    <td class="currency">${formatCurrency(thuNhapTinhThue)}</td>
                    <td class="currency">${formatCurrency(thueTNCN)}</td>
                    <td class="currency">${formatCurrency(luongThucNhan)}</td>
                    <td><span class="status-badge status-${getStatusClass(item.trang_thai)} clickable-status" onclick="quickChangeStatus(${item.id_thuong_tet || 'null'}, '${item.trang_thai}')" title="Nhấn để thay đổi trạng thái">${item.trang_thai || 'N/A'}</span></td>
                    <td>
                        <button class="btn btn-primary" onclick="editThuongTet(${item.id_thuong_tet || 'null'})" title="Sửa"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-danger" onclick="deleteThuongTet(${item.id_thuong_tet || 'null'})" title="Xóa"><i class="fas fa-trash"></i></button>
                    </td>
                `;
                tbody.appendChild(row);
            } catch (error) {
                console.error('Error rendering row', index, ':', error);
                console.error('Item data:', item);
            }
        });
        
        console.log('Table rendered successfully');
    }

    // Filter data
    function filterData() {
        loadThuongTetData();
        loadStats();
    }
    
    // Reload all data
    function reloadData() {
        console.log('Reloading all data...');
        loadNhanVienData().then(() => {
            console.log('Nhan vien data reloaded, now loading thuong tet data...');
            return loadThuongTetData();
        }).then(() => {
            console.log('Thuong tet data reloaded, now loading stats...');
            return loadStats();
        }).then(() => {
            console.log('All data reloaded successfully!');
            showAlert('Đã tải lại dữ liệu thành công!', 'success');
        }).catch(error => {
            console.error('Error in data reloading sequence:', error);
            showAlert('Lỗi tải lại dữ liệu: ' + error.message, 'danger');
        });
    }

    // Show add modal
    function showAddModal() {
        document.getElementById('modalTitle').textContent = 'Thêm Thưởng Tết';
        document.getElementById('thuongTetForm').reset();
        document.getElementById('thuongTetId').value = '';
        document.getElementById('thuongTetModal').style.display = 'block';
    }

    // Edit thưởng tết
    function editThuongTet(id) {
        const item = thuongTetData.find(t => t.id_thuong_tet == id);
        if (item) {
            document.getElementById('modalTitle').textContent = 'Sửa Thưởng Tết';
            document.getElementById('thuongTetId').value = item.id_thuong_tet;
            document.getElementById('nhanVienSelect').value = item.id_nhan_vien;
            document.getElementById('nam').value = item.nam;
            document.getElementById('tongDiemDanhGia').value = item.tong_diem || '';
            document.getElementById('xepLoai').value = item.xep_loai || '';
            document.getElementById('soNgayNghiPhep').value = item.so_ngay_nghi_phep || '';
            document.getElementById('mucThuong').value = item.muc_thuong;
            document.getElementById('trangThai').value = item.trang_thai;
            document.getElementById('ghiChu').value = item.ghi_chu || '';
            document.getElementById('thuongTetModal').style.display = 'block';
        }
    }

    // Delete thưởng tết
    async function deleteThuongTet(id) {
        if (confirm('Bạn có chắc chắn muốn xóa thưởng tết này?')) {
            try {
                const response = await fetch(`/doanqlns/simple_thuong_tet_api.php/thuong-tet/${id}`, {
                    method: 'DELETE'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('Xóa thưởng tết thành công!', 'success');
                    loadThuongTetData();
                    loadStats();
                } else {
                    showAlert('Lỗi: ' + result.message, 'danger');
                }
            } catch (error) {
                console.error('Error deleting thuong tet:', error);
                showAlert('Lỗi kết nối server', 'danger');
            }
        }
    }

    // Auto create thưởng tết
    async function autoCreateThuongTet() {
        if (confirm('Bạn có chắc chắn muốn tự động tạo thưởng tết cho tất cả nhân viên?')) {
            try {
                const year = document.getElementById('yearFilter').value || new Date().getFullYear();
                const response = await fetch(`/doanqlns/simple_thuong_tet_api.php/auto-create?nam=${year}`);
                const result = await response.json();
                
                if (result.success) {
                    showAlert(`Đã tạo thành công ${result.created} thưởng tết!`, 'success');
                    loadThuongTetData();
                    loadStats();
                } else {
                    showAlert('Lỗi: ' + result.message, 'danger');
                }
            } catch (error) {
                console.error('Error auto creating thuong tet:', error);
                showAlert('Lỗi kết nối server', 'danger');
            }
        }
    }

    // Load data for employee
    async function loadDataForEmployee() {
        const idNhanVien = document.getElementById('nhanVienSelect').value;
        const nam = document.getElementById('nam').value;
        
        if (!idNhanVien || !nam) return;
        
        try {
            const response = await fetch(`/doanqlns/simple_thuong_tet_api.php/employee-data?id_nhan_vien=${idNhanVien}&nam=${nam}`);
            const result = await response.json();
            
            if (result.success) {
                document.getElementById('tongDiemDanhGia').value = result.data.tong_diem || '';
                document.getElementById('xepLoai').value = result.data.xep_loai || '';
                document.getElementById('soNgayNghiPhep').value = result.data.so_ngay_nghi_phep || '';
                
                // Tự động tính mức thưởng
                calculateMucThuong();
            }
        } catch (error) {
            console.error('Error loading employee data:', error);
        }
    }
    
    // Tính mức thưởng tự động theo lương tháng 13
    async function calculateMucThuong() {
        const idNhanVien = document.getElementById('nhanVienSelect').value;
        
        if (!idNhanVien) return;
        
        // Lấy lương cơ bản từ dữ liệu nhân viên
        const nhanVien = nhanVienData.find(nv => nv.id_nhan_vien == idNhanVien);
        const luongCoBan = nhanVien ? nhanVien.luong_co_ban : 10000000;
        
        try {
            // Gọi API để tính lương tháng 13
            const response = await fetch(`/doanqlns/simple_thuong_tet_api.php/calculate-luong-thang-13?id_nhan_vien=${idNhanVien}&luong_co_ban=${luongCoBan}`);
            const result = await response.json();
            
            if (result.success) {
                const luongThang13 = result.data.luong_thang_13;
                const soThangCoDuLieu = result.data.so_thang_co_du_lieu;
                const luongTrungBinh = result.data.luong_trung_binh;
                
                // Cập nhật giá trị
                document.getElementById('mucThuong').value = Math.round(luongThang13);
                
                // Hiển thị thông tin chi tiết
                showCalculationDetails(luongThang13, soThangCoDuLieu, luongTrungBinh, luongCoBan);
            } else {
                console.error('Error calculating luong thang 13:', result.message);
                // Fallback: sử dụng lương cơ bản
                document.getElementById('mucThuong').value = Math.round(luongCoBan);
                showCalculationDetails(luongCoBan, 12, luongCoBan, luongCoBan);
            }
        } catch (error) {
            console.error('Error calling API:', error);
            // Fallback: sử dụng lương cơ bản
            document.getElementById('mucThuong').value = Math.round(luongCoBan);
            showCalculationDetails(luongCoBan, 12, luongCoBan, luongCoBan);
        }
    }
    
    // Hiển thị chi tiết tính toán lương tháng 13
    function showCalculationDetails(luongThang13, soThangCoDuLieu, luongTrungBinh, luongCoBan) {
        let detailsDiv = document.getElementById('calculationDetails');
        if (!detailsDiv) {
            detailsDiv = document.createElement('div');
            detailsDiv.id = 'calculationDetails';
            detailsDiv.className = 'alert alert-info mt-2';
            document.getElementById('mucThuong').parentNode.appendChild(detailsDiv);
        }
        
        let calculationText = '';
        if (soThangCoDuLieu >= 12) {
            calculationText = `Đã làm đủ 12 tháng: Lương tháng 13 = Lương trung bình 12 tháng`;
        } else if (soThangCoDuLieu > 0) {
            calculationText = `Làm ${soThangCoDuLieu} tháng: Lương tháng 13 = (${soThangCoDuLieu}/12) × Lương trung bình`;
        } else {
            calculationText = `Không có dữ liệu lương: Sử dụng lương cơ bản`;
        }
        
        detailsDiv.innerHTML = `
            <strong>Chi tiết tính lương tháng 13:</strong><br>
            • Lương cơ bản: ${formatCurrency(luongCoBan)}<br>
            • Lương trung bình ${soThangCoDuLieu} tháng: ${formatCurrency(luongTrungBinh)}<br>
            • ${calculationText}<br>
            • <strong>Lương tháng 13: ${formatCurrency(luongThang13)}</strong>
        `;
    }

    // Form submit
    document.getElementById('thuongTetForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = {
            id_nhan_vien: document.getElementById('nhanVienSelect').value,
            nam: document.getElementById('nam').value,
            tong_diem: document.getElementById('tongDiemDanhGia').value || null,
            xep_loai: document.getElementById('xepLoai').value || null,
            so_ngay_nghi_phep: document.getElementById('soNgayNghiPhep').value || 0,
            muc_thuong: document.getElementById('mucThuong').value,
            trang_thai: document.getElementById('trangThai').value,
            ghi_chu: document.getElementById('ghiChu').value
        };
        
        const id = document.getElementById('thuongTetId').value;
        const url = id ? 
            `/doanqlns/simple_thuong_tet_api.php/thuong-tet/${id}` : 
            '/doanqlns/simple_thuong_tet_api.php/thuong-tet';
        const method = id ? 'PUT' : 'POST';
        
        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showAlert(id ? 'Cập nhật thưởng tết thành công!' : 'Thêm thưởng tết thành công!', 'success');
                closeModal();
                loadThuongTetData();
                loadStats();
            } else {
                showAlert('Lỗi: ' + result.message, 'danger');
            }
        } catch (error) {
            console.error('Error saving thuong tet:', error);
            showAlert('Lỗi kết nối server', 'danger');
        }
    });

    // Event listeners
    document.getElementById('nhanVienSelect').addEventListener('change', loadDataForEmployee);
    document.getElementById('nam').addEventListener('change', loadDataForEmployee);
    document.getElementById('xepLoai').addEventListener('change', calculateMucThuong);
    document.getElementById('soNgayNghiPhep').addEventListener('input', calculateMucThuong);
    document.getElementById('tongDiemDanhGia').addEventListener('input', calculateMucThuong);

    // Close modal
    function closeModal() {
        document.getElementById('thuongTetModal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('thuongTetModal');
        if (event.target == modal) {
            closeModal();
        }
    }

    // Utility functions
    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount);
    }

    // Tính thuế TNCN theo biểu thuế lũy tiến
    function calculatePIT(taxableIncome) {
        let tax = 0;
        let remaining = taxableIncome;
        const brackets = [
            { limit: 5000000, rate: 0.05 },
            { limit: 5000000, rate: 0.10 },
            { limit: 8000000, rate: 0.15 },
            { limit: 14000000, rate: 0.20 },
            { limit: 20000000, rate: 0.25 },
            { limit: 28000000, rate: 0.30 },
            { limit: Infinity, rate: 0.35 }
        ];
        for (const b of brackets) {
            const apply = Math.min(remaining, b.limit);
            if (apply <= 0) break;
            tax += apply * b.rate;
            remaining -= apply;
        }
        return Math.max(0, Math.round(tax));
    }
    
    // Tính tiền phép năm
    function calculatePhepNamTien(luongCoBan, soNgayPhep) {
        if (!luongCoBan || !soNgayPhep) return 0;
        return Math.round((luongCoBan / 30) * soNgayPhep);
    }

    function getStatusClass(trangThai) {
        const statusMap = {
            'Chưa duyệt': 'chua-duyet',
            'Đang xử lý': 'dang-xu-ly',
            'Đã duyệt': 'da-duyet',
            'Từ chối': 'tu-choi',
            'Đã thanh toán': 'da-thanh-toan'
        };
        return statusMap[trangThai] || 'chua-duyet';
    }

    // Quick change status function
    async function quickChangeStatus(id, currentStatus) {
        const statuses = ['Chưa duyệt', 'Đang xử lý', 'Đã duyệt', 'Từ chối', 'Đã thanh toán'];
        const currentIndex = statuses.indexOf(currentStatus);
        const nextIndex = (currentIndex + 1) % statuses.length;
        const newStatus = statuses[nextIndex];
        
        if (confirm(`Thay đổi trạng thái từ "${currentStatus}" thành "${newStatus}"?`)) {
            try {
                const response = await fetch(`/doanqlns/simple_thuong_tet_api.php/thuong-tet/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        trang_thai: newStatus
                    })
                });

                const result = await response.json();

                if (result.success) {
                    showAlert(`Đã cập nhật trạng thái thành "${newStatus}"`, 'success');
                    loadThuongTetData(); // Reload data
                } else {
                    showAlert('Lỗi: ' + result.message, 'danger');
                }
            } catch (error) {
                console.error('Error changing status:', error);
                showAlert('Lỗi kết nối server', 'danger');
            }
        }
    }

    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.textContent = message;
        
        document.body.insertBefore(alertDiv, document.body.firstChild);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    function viewEmployee(id) {
        // Implement view employee functionality
        console.log('View employee:', id);
    }

    function exportToExcel() {
        // Implement export to Excel functionality
        console.log('Export to Excel');
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
