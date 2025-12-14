<?php
require_once __DIR__ . '/../includes/check_login.php';
include(__DIR__ . '/../includes/header.php');
?>

<!DOCTYPE html>
<html lang="vi" class="light-style layout-navbar-fixed layout-menu-fixed">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRM Pro - Bảng Nghỉ Phép</title>

    <!-- Font Awesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS chính -->
    <link rel="stylesheet" href="../css/stylenghiphep.css">

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
        .btn-edit, .btn-delete, .btn-add {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .btn-edit {
            background-color: #007bff;
            margin-right: 5px;
        }
        .btn-delete {
            background-color: #f44336;
        }
        .btn-add {
            background-color: #4CAF50;
        }
        .btn-info {
            background-color: #17a2b8;
        }
        .btn-sm {
            padding: 4px 8px;
            font-size: 12px;
        }
        .btn-quan-ly-nghi-phep {
            background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
            margin-left: 10px;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(33, 150, 243, 0.3);
            position: relative;
            overflow: hidden;
        }
        .btn-quan-ly-nghi-phep::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        .btn-quan-ly-nghi-phep:hover::before {
            left: 100%;
        }
        .btn-quan-ly-nghi-phep:hover {
            background: linear-gradient(135deg, #1976D2 0%, #2196F3 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(33, 150, 243, 0.4);
        }
        .btn-quan-ly-nghi-phep i {
            margin-right: 8px;
            font-size: 16px;
        }
        .btn-add:hover, .btn-edit:hover, .btn-delete:hover {
            opacity: 0.9;
        }
        .status {
            padding: 4px 8px;
            border-radius: 4px;
            color: white;
            font-size: 12px;
        }
        .status.daduyet { background-color: #4CAF50; }
        .status.tuchoi { background-color: #f44336; }
        .status.choxetduyet { background-color: #FF9800; }
        .status.warning { background-color: #FF9800; }
        .status.danger { background-color: #f44336; }
        .status.info { background-color: #2196F3; }
        .status.success { background-color: #4CAF50; }
        .status.neutral { background-color: #9E9E9E; }
        .status.primary { background-color: #673AB7; }
        .status.secondary { background-color: #607D8B; }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: #fff;
            border-radius: 12px;
            max-width: 600px;
            width: 90%;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            overflow: hidden;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .modal-header {
            background: linear-gradient(90deg, #007bff, #0056b3);
            color: #fff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 500;
        }
        .modal-close {
            background: none;
            border: none;
            color: #fff;
            font-size: 1.5rem;
            cursor: pointer;
            transition: transform 0.2s, color 0.2s;
        }
        .modal-close:hover {
            transform: scale(1.2);
            color: #e0e0e0;
        }
        .modal-body {
            padding: 25px;
            max-height: 60vh;
            overflow-y: auto;
        }
        .modal-body .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin: 0 0 15px 0;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
        }
        .info-group {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .info-group label {
            font-weight: 500;
            color: #333;
            width: 150px;
            flex-shrink: 0;
            font-size: 14px;
        }
        .info-group .info-value {
            color: #555;
            flex-grow: 1;
            font-size: 14px;
            line-height: 1.5;
        }
        .modal-field {
            margin-bottom: 15px;
        }
        .modal-field label {
            display: block;
            font-weight: 500;
            color: #333;
            margin-bottom: 5px;
        }
        .modal-body select, .modal-body input[type="date"], .modal-body input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.2s;
        }
        .modal-body select:focus, .modal-body input:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 5px rgba(0,123,255,0.3);
        }
        .reason-input {
            display: none;
            margin-top: 10px;
        }
        .reason-input.active {
            display: block;
        }
        .reason-input label {
            font-weight: 500;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }
        .reason-input input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        .modal-footer {
            padding: 15px 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            background: #f8f9fa;
            border-top: 1px solid #ddd;
        }
        .modal-footer .btn-save, .modal-footer .btn-close {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        .modal-footer .btn-save {
            background-color: #4CAF50;
            color: white;
        }
        .modal-footer .btn-close {
            background-color: #dc3545;
            color: white;
        }
        .modal-footer .btn-save:hover, .modal-footer .btn-close:hover {
            opacity: 0.85;
        }
        .loading {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 15px 30px;
            border-radius: 8px;
            z-index: 2000;
            font-size: 14px;
        }
        .filter-container {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            justify-content: center;
            align-items: center;
        }
        .filter-container select,
        .filter-container input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .filter-container select:focus,
        .filter-container input:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.2);
        }
        /* Điều chỉnh chiều rộng cột */
        table th, table td {
            padding: 8px;
            text-align: left;
        }
        table th:nth-child(5), table td:nth-child(5) { /* Lý Do */
            min-width: 150px;
        }
        table th:nth-child(6), table td:nth-child(6) { /* Loại Nghỉ */
            min-width: 100px;
        }
        table th:nth-child(7), table td:nth-child(7) { /* Lý Do Từ Chối */
            min-width: 150px;
        }
        .red-star {
            color: #f44336;
            margin-right: 5px;
        }
        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
                max-width: 400px;
            }
            .info-group {
                flex-direction: column;
                align-items: flex-start;
            }
            .info-group label {
                width: auto;
                margin-bottom: 5px;
            }
            .info-group .info-value {
                width: 100%;
            }
            th, td {
                font-size: 12px;
                padding: 6px;
            }
            .btn-edit, .btn-delete, .btn-add {
                padding: 4px 8px;
                font-size: 12px;
            }
            .modal-body {
                padding: 15px;
            }
            .modal-header h2 {
                font-size: 1.2rem;
            }
            .filter-container {
                flex-direction: column;
                align-items: center;
            }
        }
        .warning {
            color:rgb(19, 41, 236);
            
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .warning i {
            color:rgb(249, 31, 31);
        }
        .warning-phep-nam {
            color: #1329ec;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .warning-phep-nam i {
            color: #f91f1f;
        }
    </style>
</head>

<body>
    <div class="layout-wrapper">
        <?php include('../includes/sidebar.php'); ?>
        <div class="layout-page">
            <div class="content-wrapper">
        <h3>Bảng Nghỉ Phép</h3>
        <div class="filter-container">
            <select id="selectMonth" aria-label="Chọn tháng">
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
            <input type="number" id="selectYear" min="2000" max="2100" aria-label="Nhập năm" placeholder="Năm"/>
            <button class="btn-add" onclick="showAddNghiPhepModal()">
                <i class="fas fa-plus"></i> Thêm Nghỉ Phép
            </button>
            <button class="btn-quan-ly-nghi-phep" onclick="showQuanLyNghiPhepModal()">
                <i class="fas fa-chart-bar"></i> Quản lý Nghỉ Phép
            </button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID Nghỉ Phép</th>
                    <th>Nhân Viên</th>
                    <th>Ngày Bắt Đầu</th>
                    <th>Ngày Kết Thúc</th>
                    <th>Lý Do</th>
                    <th>Loại Nghỉ</th>
                    <th>Minh chứng</th>
                    <th>Lý Do Từ Chối</th>
                    <th>Trạng Thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody id="nghiPhepTableBody">
                <tr><td colspan="10">Đang tải dữ liệu...</td></tr>
            </tbody>
        </table>

        <!-- Modal chi tiết đơn nghỉ phép -->
        <div id="detailNghiPhepModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Chi Tiết Đơn Nghỉ Phép</h2>
            <button class="modal-close" onclick="closeDetailModal()">×</button>
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
            <div class="section-title">Thông Tin Đơn Nghỉ Phép</div>
            <div class="info-group">
                <label>Ngày Bắt Đầu:</label>
                <span id="detailNgayBatDau" class="info-value"></span>
            </div>
            <div class="info-group">
                <label>Ngày Kết Thúc:</label>
                <span id="detailNgayKetThuc" class="info-value"></span>
            </div>
            <div class="info-group">
                <label>Lý Do:</label>
                <span id="detailLyDo" class="info-value"></span>
            </div>
            <div class="info-group">
                <label>Loại Nghỉ:</label>
                <span id="detailLoaiNghi" class="info-value"></span>
            </div>
            <div class="info-group">
                <label>Lý Do Từ Chối:</label>
                <span id="detailLyDoTuChoi" class="info-value"></span>
            </div>
            <div class="info-group">
                <label>Trạng Thái:</label>
                <span id="detailTrangThai" class="info-value"></span>
            </div>
            <div class="info-group">
                <label>Tổng Ngày Nghỉ Trong Tháng:</label>
                <span id="detailTongNgayNghi" class="info-value"></span>
                <span id="warningNgayNghi" class="warning" style="display: none;">
                    <i class="fas fa-exclamation-triangle"></i>
                    Vượt quá số ngày nghỉ cho phép (2 ngày/tháng)
                </span>
            </div>
            <div class="info-group">
                <label>Số Ngày Phép Năm Còn Lại:</label>
                <span id="detailSoNgayPhepNamConLai" class="info-value"></span>
                <span id="warningPhepNam" class="warning-phep-nam" style="display: none;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span id="warningPhepNamText"></span>
                </span>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-close" onclick="closeDetailModal()">Đóng</button>
        </div>
    </div>
</div>

        <!-- Modal Sửa Trạng Thái -->
        <div id="editNghiPhepModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Sửa Trạng Thái Đơn Nghỉ Phép</h2>
                    <button class="modal-close" onclick="closeEditModal()">×</button>
                </div>
                <div class="modal-body">
                    <div class="modal-field">
                        <label for="editStatus">Trạng Thái:</label>
                        <select id="editStatus" onchange="toggleReasonInput()">
                            <option value="Chờ duyệt">Chờ duyệt</option>
                            <option value="Từ chối">Từ chối</option>
                            <option value="Duyệt">Duyệt</option>
                        </select>
                    </div>
                    <input type="hidden" id="editIdNghiPhep">
                    <input type="hidden" id="editIdNhanVien">
                    <input type="hidden" id="editNgayBatDau">
                    <input type="hidden" id="editNgayKetThuc">
                    <div class="reason-input" id="reasonInput">
                        <label for="reason">Lý do từ chối:</label>
                        <input type="text" id="reason" name="reason" placeholder="Nhập lý do từ chối">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-save" onclick="saveNghiPhepStatus()">Lưu</button>
                    <button class="btn-close" onclick="closeEditModal()">Hủy</button>
                </div>
            </div>
        </div>

        <!-- Modal Thêm Nghỉ Phép -->
        <div id="addNghiPhepModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Thêm Đơn Nghỉ Phép</h2>
                    <button class="modal-close" onclick="closeAddNghiPhepModal()">×</button>
                </div>
                <div class="modal-body">
                    <div class="modal-field">
                        <label for="addIdNhanVien">Nhân Viên:</label>
                        <select id="addIdNhanVien" required>
                            <option value="">Chọn nhân viên</option>
                        </select>
                    </div>
                    <div class="modal-field">
                        <label for="addNgayBatDau">Ngày Bắt Đầu:</label>
                        <input type="date" id="addNgayBatDau" required>
                    </div>
                    <div class="modal-field">
                        <label for="addNgayKetThuc">Ngày Kết Thúc:</label>
                        <input type="date" id="addNgayKetThuc" required>
                    </div>
                    <div class="modal-field">
                        <label for="addLyDo">Lý Do:</label>
                        <input type="text" id="addLyDo" placeholder="Nhập lý do nghỉ" required>
                    </div>
                    <div class="modal-field">
                        <label for="addLoaiNghi">Loại Nghỉ:</label>
                        <select id="addLoaiNghi" required onchange="toggleMaternityFields()">
                            <option value="Có phép">Nghỉ có phép</option>
                            <option value="Phép Năm">Phép Năm</option>
                            <option value="Không phép">Nghỉ không phép</option>
                            <option value="Nghỉ thai sản">Nghỉ thai sản</option>
                            <option value="Nghỉ tai nạn">Nghỉ tai nạn</option>
                        </select>
                    </div>
                    <div class="modal-field" id="maternity_fields" style="display: none;">
                        <label>Ngày Bắt Đầu Nghỉ Thai Sản</label>
                        <input type="date" id="add_ngay_bat_dau_thai_san" onchange="calculateMaternityEndDate()">
                    </div>
                    <div class="modal-field" id="maternity_end_field" style="display: none;">
                        <label>Ngày Kết Thúc Nghỉ Thai Sản (Tự động tính)</label>
                        <input type="date" id="add_ngay_ket_thuc_thai_san" readonly style="background-color: #f8f9fa; color: #6c757d;">
                        <small class="text-muted">Tự động tính = Ngày bắt đầu + 6 tháng</small>
                    </div>
                    <div class="modal-field" id="accident_fields" style="display: none;">
                        <label>Loại Tai Nạn:</label>
                        <select id="add_loai_tai_nan">
                            <option value="Tai nạn giao thông">Tai nạn giao thông</option>
                            <option value="Tai nạn lao động">Tai nạn lao động</option>
                            <option value="Tai nạn sinh hoạt">Tai nạn sinh hoạt</option>
                            <option value="Khác">Khác</option>
                        </select>
                    </div>
                    <div class="modal-field" id="accident_severity_field" style="display: none;">
                        <label>Mức Độ Nghiêm Trọng:</label>
                        <select id="add_muc_do_tai_nan">
                            <option value="Nhẹ">Nhẹ (1-3 ngày)</option>
                            <option value="Trung bình">Trung bình (4-14 ngày)</option>
                            <option value="Nặng">Nặng (15-30 ngày)</option>
                            <option value="Rất nặng">Rất nặng (trên 30 ngày)</option>
                        </select>
                    </div>
                    <div class="modal-field">
                        <label for="addMinhChung">Minh chứng (link):</label>
                        <input type="url" id="addMinhChung" placeholder="Dán link file minh chứng (Drive/OneDrive...)" pattern="https?://.*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-save" onclick="saveNewNghiPhep()">Lưu</button>
                    <button class="btn-close" onclick="closeAddNghiPhepModal()">Hủy</button>
                </div>
            </div>
        </div>

        <!-- Modal Quản lý Nghỉ Phép -->
        <div id="quanLyNghiPhepModal" class="modal">
            <div class="modal-content" style="max-width: 1200px; width: 95%;">
                <div class="modal-header">
                    <h2>Quản lý Nghỉ Phép</h2>
                    <button class="modal-close" onclick="closeQuanLyNghiPhepModal()">×</button>
                </div>
                <div class="modal-body">
                    <div class="filter-container" style="margin-bottom: 20px;">
                        <input type="number" id="quanLySelectYear" min="2000" max="2100" aria-label="Nhập năm" placeholder="Năm" value="2025" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 120px;"/>
                        <select id="quanLySelectQuarter" aria-label="Chọn quý" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 160px;">
                            <option value="">Tất cả quý</option>
                            <option value="1">Quý 1 (Tháng 1 – 3)</option>
                            <option value="2">Quý 2 (Tháng 4 – 6)</option>
                            <option value="3">Quý 3 (Tháng 7 – 9)</option>
                            <option value="4">Quý 4 (Tháng 10 – 12)</option>
                        </select>
                        <button class="btn-add" onclick="loadQuanLyNghiPhepData()">
                            <i class="fas fa-search"></i> Tìm kiếm
                        </button>
                    </div>
                    <table style="width: 100%; margin-top: 20px;">
                        <thead>
                            <tr>
                                <th>Tên Nhân Viên</th>
                                <th>Số Ngày Nghỉ (Năm)</th>
                                <th>Nghỉ Nửa Buổi</th>
                                <th>Có Phép</th>
                                <th>Tổng Phép Năm</th>
                                <th>Phép Năm Còn Lại</th>
                                <th>Đi Trễ</th>
                                <th>Ra Sớm</th>
                                <th>Điểm</th>
                            </tr>
                        </thead>
                        <tbody id="quanLyNghiPhepTableBody">
                            <tr><td colspan="9">Chọn năm để xem dữ liệu...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button class="btn-close" onclick="closeQuanLyNghiPhepModal()">Đóng</button>
                </div>
            </div>
        </div>

        <!-- Loading indicator -->
        <div class="loading" id="loadingIndicator">Đang xử lý...</div>
    </div>

    <script>
        // Biến toàn cục
        let nghiPhepData = [];
        let usersData = [];
        const userPermissions = {
            quyen_sua: <?php echo isset($_SESSION['quyen_sua']) && $_SESSION['quyen_sua'] ? 'true' : 'false'; ?>,
            quyen_xoa: <?php echo isset($_SESSION['quyen_xoa']) && $_SESSION['quyen_xoa'] ? 'true' : 'false'; ?>
        };
        const userId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;

        // Tham chiếu đến các phần tử DOM
        const nghiPhepTableBody = document.getElementById("nghiPhepTableBody");
        const detailNghiPhepModal = document.getElementById("detailNghiPhepModal");
        const editNghiPhepModal = document.getElementById("editNghiPhepModal");
        const addNghiPhepModal = document.getElementById("addNghiPhepModal");
        const loadingIndicator = document.getElementById("loadingIndicator");
        const reasonInput = document.getElementById("reasonInput");
        const editStatus = document.getElementById("editStatus");

        // Hàm hiển thị loading
        function showLoading() {
            loadingIndicator.style.display = "block";
        }

        // Hàm ẩn loading
        function hideLoading() {
            loadingIndicator.style.display = "none";
        }

        // Tải danh sách nhân viên
        async function loadUsersData() {
            showLoading();
            try {
                const response = await fetch("http://localhost/doanqlns/index.php/api/users");
                if (!response.ok) throw new Error("Lỗi khi tải danh sách nhân viên: " + response.status);
                const data = await response.json();
                if (!Array.isArray(data)) throw new Error("Danh sách nhân viên không hợp lệ");
                usersData = data;

                // Cập nhật danh sách nhân viên cho modal thêm nghỉ phép
                const select = document.getElementById("addIdNhanVien");
                select.innerHTML = '<option value="">Chọn nhân viên</option>';
                data.forEach(nv => {
                    const option = document.createElement("option");
                    option.value = nv.id_nhan_vien;
                    option.textContent = `${nv.ho_ten} (ID: ${nv.id_nhan_vien})`;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error("Lỗi khi tải danh sách nhân viên:", error);
                alert("Lỗi khi tải danh sách nhân viên: " + error.message);
            } finally {
                hideLoading();
            }
        }

        // Hàm tính số ngày nghỉ trong tháng, trừ Chủ Nhật
        function calculateLeaveDays(records, month, year) {
            const leaveDaysByEmployee = {};

            records.forEach(record => {
                const startDate = new Date(record.ngay_bat_dau);
                const endDate = new Date(record.ngay_ket_thuc);
                const employeeId = record.id_nhan_vien;

                // Chỉ tính các ngày trong tháng và năm được chọn
                const monthStart = new Date(year, month - 1, 1);
                const monthEnd = new Date(year, month, 0);

                let currentDate = new Date(Math.max(startDate, monthStart));
                const end = new Date(Math.min(endDate, monthEnd));

                let days = 0;
                while (currentDate <= end) {
                    // Bỏ qua Chủ Nhật
                    if (currentDate.getDay() !== 0) {
                        days++;
                    }
                    currentDate.setDate(currentDate.getDate() + 1);
                }

                if (!leaveDaysByEmployee[employeeId]) {
                    leaveDaysByEmployee[employeeId] = 0;
                }
                leaveDaysByEmployee[employeeId] += days;
            });

            return leaveDaysByEmployee;
        }

        // Hàm tính số ngày nghỉ trong năm, trừ Chủ Nhật
        function calculateLeaveDaysByYear(records, year) {
            const leaveDaysByEmployee = {};

            records.forEach(record => {
                const startDate = new Date(record.ngay_bat_dau);
                const endDate = new Date(record.ngay_ket_thuc);
                const employeeId = record.id_nhan_vien;

                // Chỉ tính các ngày trong năm được chọn
                const yearStart = new Date(year, 0, 1);
                const yearEnd = new Date(year, 11, 31);

                let currentDate = new Date(Math.max(startDate, yearStart));
                const end = new Date(Math.min(endDate, yearEnd));

                let days = 0;
                while (currentDate <= end) {
                    // Bỏ qua Chủ Nhật
                    if (currentDate.getDay() !== 0) {
                        days++;
                    }
                    currentDate.setDate(currentDate.getDate() + 1);
                }

                if (!leaveDaysByEmployee[employeeId]) {
                    leaveDaysByEmployee[employeeId] = 0;
                }
                leaveDaysByEmployee[employeeId] += days;
            });

            return leaveDaysByEmployee;
        }

        // Hàm tính các loại nghỉ phép từ bảng cham_cong theo năm hoặc theo quý
        function calculateLeaveTypesFromChamCong(chamCongData, year, startMonthOpt, endMonthOpt) {
                const leaveTypesByEmployee = {};

            chamCongData.forEach(record => {
                const employeeId = record.id_nhan_vien;
                const recordDate = new Date(record.ngay_lam_viec); // Sửa từ ngay_cham_cong thành ngay_lam_viec
                
                // Chỉ tính trong năm đã chọn và (nếu có) trong khoảng tháng của quý, không tính Chủ nhật
                if (recordDate.getFullYear() === year && recordDate.getDay() !== 0) {
                    if (startMonthOpt && endMonthOpt) {
                        const m = recordDate.getMonth() + 1;
                        if (m < startMonthOpt || m > endMonthOpt) return;
                    }
                    if (!leaveTypesByEmployee[employeeId]) {
                        leaveTypesByEmployee[employeeId] = {
                            nghiNuaBuoi: 0,
                            coPhep: 0,
                            khongPhep: 0
                        };
                    }

                    // Phân loại theo trạng thái trong bảng cham_cong
                    const trangThai = record.trang_thai ? record.trang_thai : '';
                    
                    if (trangThai === 'Nghỉ nữa buổi') {
                        leaveTypesByEmployee[employeeId].nghiNuaBuoi++;
                    } else if (trangThai === 'Có phép') {
                        leaveTypesByEmployee[employeeId].coPhep++;
                    } else if (trangThai === 'Không phép') {
                        // Không còn hiển thị cột, nhưng vẫn ghi nhận để tính điểm
                        leaveTypesByEmployee[employeeId].khongPhep++;
                    }
                }
            });

            return leaveTypesByEmployee;
        }

        async function loadNghiPhepData() {
            const month = parseInt(document.getElementById('selectMonth').value);
            const yearInput = document.getElementById('selectYear');
            const year = parseInt(yearInput.value) || new Date().getFullYear();

            if (!yearInput.value) {
                yearInput.value = year;
            }

            showLoading();
            try {
                const response = await fetch("http://localhost/doanqlns/index.php/api/nghiphep");
                if (!response.ok) throw new Error("Lỗi khi tải dữ liệu: " + response.status);
                const data = await response.json();
                if (!Array.isArray(data)) throw new Error("Dữ liệu không hợp lệ");

                // Lọc dữ liệu theo tháng/năm
                nghiPhepData = data.filter(record => {
                    const startDate = new Date(record.ngay_bat_dau);
                    const endDate = new Date(record.ngay_ket_thuc);
                    
                    return (
                        (startDate.getFullYear() === year && startDate.getMonth() + 1 === month) ||
                        (endDate.getFullYear() === year && endDate.getMonth() + 1 === month) ||
                        (startDate.getFullYear() <= year && endDate.getFullYear() >= year &&
                         startDate.getMonth() + 1 <= month && endDate.getMonth() + 1 >= month)
                    );
                });

                renderNghiPhepTable(nghiPhepData, month, year);
            } catch (error) {
                console.error("Lỗi khi tải dữ liệu:", error);
                nghiPhepTableBody.innerHTML = '<tr><td colspan="10">Lỗi khi tải dữ liệu</td></tr>';
            } finally {
                hideLoading();
            }
        }

        // Hàm khởi tạo giá trị ban đầu và kiểm tra thay đổi ngày
        function initializeDateFilter() {
            const yearInput = document.getElementById('selectYear');
            const monthInput = document.getElementById('selectMonth');
            let currentDate = new Date();

            // Đặt giá trị mặc định cho năm và tháng
            yearInput.value = currentDate.getFullYear();
            monthInput.value = currentDate.getMonth() + 1;

            // Gọi loadNghiPhepData lần đầu để hiển thị dữ liệu
            loadNghiPhepData();

            // Kiểm tra thay đổi tháng/năm mỗi phút
            setInterval(() => {
                const now = new Date();
                if (now.getMonth() !== currentDate.getMonth() || 
                    now.getFullYear() !== currentDate.getFullYear()) {
                    currentDate = now;
                    yearInput.value = now.getFullYear();
                    monthInput.value = now.getMonth() + 1;
                    loadNghiPhepData();
                }
            }, 60000);
        }

        // Hiển thị bảng nghỉ phép
        function renderNghiPhepTable(data, month, year) {
            nghiPhepTableBody.innerHTML = "";
            if (data && Array.isArray(data) && data.length > 0) {
                // Tính số ngày nghỉ của từng nhân viên trong tháng
                const leaveDaysByEmployee = calculateLeaveDays(data, month, year);

                data.forEach(record => {
                    const row = document.createElement("tr");
                    let statusClass = "";
                    let displayStatus = record.trang_thai1;
                    switch (record.trang_thai1.toLowerCase()) {
                        case "đã duyệt":
                            statusClass = "daduyet";
                            displayStatus = "Duyệt";
                            break;
                        case "từ chối":
                            statusClass = "tuchoi";
                            break;
                        case "chờ duyệt":
                            statusClass = "choxetduyet";
                            displayStatus = "Chờ duyệt";
                            break;
                        default:
                            statusClass = "choxetduyet";
                            displayStatus = "Chờ duyệt";
                            break;
                    }

                    // Kiểm tra số ngày nghỉ của nhân viên
                    const totalLeaveDays = leaveDaysByEmployee[record.id_nhan_vien] || 0;
                    const redStar = totalLeaveDays > 2 ? '<span class="red-star">★</span>' : '';

                   // Trong hàm renderNghiPhepTable
row.innerHTML = `
    <td>${record.id_nghi_phep}</td>
    <td>${redStar}<a href="#" class="name-link" data-id="${record.id_nhan_vien}" data-nghiphep-id="${record.id_nghi_phep}">${record.ho_ten}</a></td>
    <td>${record.ngay_bat_dau}</td>
    <td>${record.ngay_ket_thuc}</td>
    <td>${record.ly_do}</td>
    <td>${record.loai_nghi || 'Không có'}</td>
    <td>${record.minh_chung ? `<a href="${record.minh_chung}" target="_blank" class="btn btn-sm btn-info"><i class="fas fa-link"></i> Xem</a>` : '<span style="color: #999;">-</span>'}</td>
    <td>${record.ly_do_tu_choi || '-'}</td>
    <td><span class="status ${statusClass}">${displayStatus}</span></td>
    <td>
        ${userPermissions.quyen_sua && record.trang_thai1.toLowerCase() !== 'từ chối' ? `
            <button class="btn-edit" onclick="editNghiPhep(${record.id_nghi_phep}, ${record.id_nhan_vien}, '${record.ngay_bat_dau}', '${record.ngay_ket_thuc}', '${record.trang_thai1}')" title="Chỉnh sửa trạng thái">
                <i class="fas fa-edit"></i>
            </button>
        ` : ''}
        ${userPermissions.quyen_xoa ? `
            <button class="btn-delete" onclick="deleteNghiPhep(${record.id_nghi_phep})" title="Xóa đơn nghỉ phép">
                <i class="fas fa-trash-alt"></i>
            </button>
        ` : ''}
    </td>
`;
                    nghiPhepTableBody.appendChild(row);
                });
                document.querySelectorAll('.name-link').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const userId = this.getAttribute('data-id');
                        const nghiPhepId = this.getAttribute('data-nghiphep-id');
                        showDetailNghiPhep(userId, nghiPhepId);
                    });
                });
            } else {
                nghiPhepTableBody.innerHTML = '<tr><td colspan="10">Không có nhân viên nào nghỉ!</td></tr>';
            }
        }   

        // Hiển thị modal chi tiết đơn nghỉ phép
        // Hàm tính số ngày phép năm đã sử dụng trong năm
async function calculateUsedAnnualLeaveDays(userId, year) {
    try {
        const response = await fetch("http://localhost/doanqlns/index.php/api/nghiphep");
        if (!response.ok) throw new Error("Lỗi khi tải dữ liệu nghỉ phép: " + response.status);
        const data = await response.json();
        if (!Array.isArray(data)) throw new Error("Dữ liệu không hợp lệ");

        // Lọc các đơn nghỉ phép loại "Phép Năm" và trạng thái "Đã duyệt" trong năm
        const annualLeaveRecords = data.filter(record => 
            record.id_nhan_vien == userId &&
            record.loai_nghi === 'Phép Năm' &&
            record.trang_thai1.toLowerCase() === 'đã duyệt' &&
            new Date(record.ngay_bat_dau).getFullYear() === year
        );

        let totalUsedDays = 0;
        annualLeaveRecords.forEach(record => {
            const startDate = new Date(record.ngay_bat_dau);
            const endDate = new Date(record.ngay_ket_thuc);
            let currentDate = new Date(startDate);
            
            while (currentDate <= endDate) {
                if (currentDate.getDay() !== 0) { // Bỏ qua Chủ Nhật
                    totalUsedDays++;
                }
                currentDate.setDate(currentDate.getDate() + 1);
            }
        });

        return totalUsedDays; // Trả về số ngày đã sử dụng, không phải số ngày còn lại
    } catch (error) {
        console.error("Lỗi khi tính số ngày phép năm:", error);
        return 0; // Giá trị mặc định nếu có lỗi
    }
}

// Hiển thị modal chi tiết đơn nghỉ phép
async function showDetailNghiPhep(userId, nghiPhepId) {
    showLoading();
    try {
        // Tìm bản ghi nghỉ phép
        const nghiPhepRecord = nghiPhepData.find(record => record.id_nghi_phep == nghiPhepId);
        if (!nghiPhepRecord) {
            throw new Error("Không tìm thấy đơn nghỉ phép");
        }

        // Tìm thông tin nhân viên
        let user = usersData.find(u => u.id_nhan_vien == userId);
        if (!user) {
            const response = await fetch(`http://localhost/doanqlns/index.php/api/users?id=${userId}`);
            if (!response.ok) throw new Error("Lỗi khi tải thông tin nhân viên: " + response.status);
            const data = await response.json();
            user = Array.isArray(data) ? data[0] : data;
            if (!user) throw new Error("Không tìm thấy thông tin nhân viên");
        }

        // Lấy năm hiện tại để tính số ngày phép năm
        const year = parseInt(document.getElementById('selectYear').value) || new Date().getFullYear();

        // Tính tổng số ngày nghỉ trong tháng
        const month = parseInt(document.getElementById('selectMonth').value);
        const leaveDaysByEmployee = calculateLeaveDays(nghiPhepData, month, year);
        const totalLeaveDays = leaveDaysByEmployee[userId] || 0;

        // Tính số ngày phép năm đã sử dụng và còn lại
        const usedAnnualLeaveDays = await calculateUsedAnnualLeaveDays(userId, year);
        const remainingAnnualLeaveDays = 12 - usedAnnualLeaveDays;

        // Điền thông tin nhân viên
        document.getElementById('detailHoTen').textContent = user.ho_ten || 'Không có dữ liệu';
        document.getElementById('detailGioiTinh').textContent = user.gioi_tinh || 'Không có dữ liệu';
        document.getElementById('detailNgaySinh').textContent = user.ngay_sinh || 'Không có dữ liệu';
        document.getElementById('detailEmail').textContent = user.email || 'Không có dữ liệu';
        document.getElementById('detailSoDienThoai').textContent = user.so_dien_thoai || 'Không có dữ liệu';
        document.getElementById('detailDiaChi').textContent = user.dia_chi || 'Không có dữ liệu';
        document.getElementById('detailPhongBan').textContent = user.ten_phong_ban || 'Không có dữ liệu';
        document.getElementById('detailChucVu').textContent = user.ten_chuc_vu || 'Không có dữ liệu';

        // Điền thông tin đơn nghỉ phép
        document.getElementById('detailNgayBatDau').textContent = nghiPhepRecord.ngay_bat_dau || 'Không có dữ liệu';
        document.getElementById('detailNgayKetThuc').textContent = nghiPhepRecord.ngay_ket_thuc || 'Không có dữ liệu';
        document.getElementById('detailLyDo').textContent = nghiPhepRecord.ly_do || 'Không có dữ liệu';
        document.getElementById('detailLoaiNghi').textContent = nghiPhepRecord.loai_nghi || 'Không có dữ liệu';
        document.getElementById('detailLyDoTuChoi').textContent = nghiPhepRecord.ly_do_tu_choi || 'Không có';
        document.getElementById('detailTrangThai').textContent = 
            nghiPhepRecord.trang_thai1.toLowerCase() === 'đã duyệt' ? 'Duyệt' :
            nghiPhepRecord.trang_thai1.toLowerCase() === 'chờ duyệt' ? 'Chờ duyệt' :
            nghiPhepRecord.trang_thai1;

        // Điền tổng số ngày nghỉ trong tháng
        document.getElementById('detailTongNgayNghi').textContent = `${totalLeaveDays} ngày`;

        // Điền số ngày phép năm còn lại
        document.getElementById('detailSoNgayPhepNamConLai').textContent = `${remainingAnnualLeaveDays} ngày`;

        updateTongNgayNghi(totalLeaveDays);

        detailNghiPhepModal.style.display = 'flex';
    } catch (error) {
        console.error("Lỗi khi hiển thị chi tiết đơn nghỉ phép:", error);
        alert("Lỗi khi hiển thị chi tiết đơn nghỉ phép: " + error.message);
    } finally {
        hideLoading();
    }
}

        // Đóng modal chi tiết
        function closeDetailModal() {
            detailNghiPhepModal.style.display = 'none';
        }

        // Hiển thị modal sửa trạng thái
       // Hiển thị modal chỉnh trạng thái
function editNghiPhep(id, idNhanVien, ngayBatDau, ngayKetThuc, trangThai) {
    if (!userPermissions.quyen_sua) {
        alert("Bạn không có quyền chỉnh sửa trạng thái đơn nghỉ phép!");
        return;
    }
    // Kiểm tra nếu trạng thái là "Từ chối"
    if (trangThai.toLowerCase() === 'từ chối') {
        alert("Không thể chỉnh sửa đơn nghỉ phép đã bị từ chối!");
        return;
    }
    document.getElementById('editIdNghiPhep').value = id;
    document.getElementById('editIdNhanVien').value = idNhanVien;
    document.getElementById('editNgayBatDau').value = ngayBatDau;
    document.getElementById('editNgayKetThuc').value = ngayKetThuc;
    const displayStatus = trangThai.toLowerCase() === 'đã duyệt' ? 'Duyệt' : 
                         (trangThai.toLowerCase() === 'chờ duyệt' ? 'Chờ duyệt' : trangThai);
    document.getElementById('editStatus').value = displayStatus;
    toggleReasonInput();
    editNghiPhepModal.style.display = 'flex';
}

        // Đóng modal sửa trạng thái
        function closeEditModal() {
            editNghiPhepModal.style.display = 'none';
        }

        // Hiển thị modal thêm nghỉ phép
        function showAddNghiPhepModal() {
            if (!userPermissions.quyen_sua) {
                alert("Bạn không có quyền thêm đơn nghỉ phép!");
                return;
            }
            document.getElementById('addIdNhanVien').value = '';
            document.getElementById('addNgayBatDau').value = '';
            document.getElementById('addNgayKetThuc').value = '';
            document.getElementById('addLyDo').value = '';
            document.getElementById('addLoaiNghi').value = 'Có phép';
            document.getElementById('addMinhChung').value = '';
            document.getElementById('add_ngay_bat_dau_thai_san').value = '';
            document.getElementById('add_ngay_ket_thuc_thai_san').value = '';
            document.getElementById('add_loai_tai_nan').value = 'Tai nạn giao thông';
            document.getElementById('add_muc_do_tai_nan').value = 'Nhẹ';
            toggleMaternityFields(); // Reset maternity fields
            addNghiPhepModal.style.display = 'flex';
        }

        // Đóng modal thêm nghỉ phép
        function closeAddNghiPhepModal() {
            addNghiPhepModal.style.display = 'none';
        }

        // Toggle hiển thị các trường thai sản và tai nạn
        function toggleMaternityFields() {
            const loaiNghi = document.getElementById('addLoaiNghi').value;
            const maternityFields = document.getElementById('maternity_fields');
            const maternityEndField = document.getElementById('maternity_end_field');
            const accidentFields = document.getElementById('accident_fields');
            const accidentSeverityField = document.getElementById('accident_severity_field');
            
            // Ẩn tất cả các trường đặc biệt
            maternityFields.style.display = 'none';
            maternityEndField.style.display = 'none';
            accidentFields.style.display = 'none';
            accidentSeverityField.style.display = 'none';
            
            if (loaiNghi === 'Nghỉ thai sản') {
                maternityFields.style.display = 'block';
                maternityEndField.style.display = 'block';
            } else if (loaiNghi === 'Nghỉ tai nạn') {
                accidentFields.style.display = 'block';
                accidentSeverityField.style.display = 'block';
            }
        }

        // Tính ngày kết thúc nghỉ thai sản (bắt đầu + 6 tháng)
        function calculateMaternityEndDate() {
            const startDate = document.getElementById('add_ngay_bat_dau_thai_san').value;
            if (startDate) {
                const start = new Date(startDate);
                const end = new Date(start);
                end.setMonth(end.getMonth() + 6);
                
                const endDateStr = end.toISOString().split('T')[0];
                document.getElementById('add_ngay_ket_thuc_thai_san').value = endDateStr;
                
                // Cập nhật ngày bắt đầu và kết thúc cho form chính
                document.getElementById('addNgayBatDau').value = startDate;
                document.getElementById('addNgayKetThuc').value = endDateStr;
            }
        }

        // Lưu đơn nghỉ phép mới
       // Lưu đơn nghỉ phép mới
async function saveNewNghiPhep() {
    if (!userPermissions.quyen_sua) {
        alert("Bạn không có quyền thêm đơn nghỉ phép!");
        return;
    }

    const idNhanVien = document.getElementById('addIdNhanVien').value;
    const loaiNghi = document.getElementById('addLoaiNghi').value;
    
    // Xử lý ngày cho nghỉ thai sản
    let ngayBatDau, ngayKetThuc;
    if (loaiNghi === 'Nghỉ thai sản') {
        ngayBatDau = document.getElementById('add_ngay_bat_dau_thai_san').value;
        ngayKetThuc = document.getElementById('add_ngay_ket_thuc_thai_san').value;
    } else {
        ngayBatDau = document.getElementById('addNgayBatDau').value;
        ngayKetThuc = document.getElementById('addNgayKetThuc').value;
    }
    
    const lyDo = document.getElementById('addLyDo').value.trim();
    const minhChung = (document.getElementById('addMinhChung') && document.getElementById('addMinhChung').value.trim()) || null;

    // Validate
    if (!idNhanVien || !ngayBatDau || !ngayKetThuc || !lyDo) {
        alert("Vui lòng điền đầy đủ thông tin!");
        return;
    }
    
    // Validate nghỉ thai sản
    if (loaiNghi === 'Nghỉ thai sản') {
        const ngayBatDauThaiSan = document.getElementById('add_ngay_bat_dau_thai_san').value;
        if (!ngayBatDauThaiSan) {
            alert("Vui lòng chọn ngày bắt đầu nghỉ thai sản!");
            return;
        }
    }
    if (new Date(ngayKetThuc) < new Date(ngayBatDau)) {
        alert("Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu!");
        return;
    }

    // Kiểm tra số ngày phép năm còn lại nếu loại nghỉ là "Phép Năm"
    if (loaiNghi === 'Phép Năm') {
        const year = new Date(ngayBatDau).getFullYear();
        const usedDays = await calculateUsedAnnualLeaveDays(idNhanVien, year);
        const remainingDays = 12 - usedDays;
        
        // Tính số ngày nghỉ của đơn hiện tại (loại bỏ Chủ Nhật)
        let leaveDays = 0;
        let currentDate = new Date(ngayBatDau);
        const endDate = new Date(ngayKetThuc);
        while (currentDate <= endDate) {
            if (currentDate.getDay() !== 0) {
                leaveDays++;
            }
            currentDate.setDate(currentDate.getDate() + 1);
        }

        if (remainingDays < leaveDays) {
            const user = usersData.find(u => u.id_nhan_vien == idNhanVien);
            const hoTen = user ? user.ho_ten : 'Nhân viên';
            alert(`${hoTen} chỉ còn ${remainingDays} ngày phép năm, không đủ để nghỉ ${leaveDays} ngày!`);
            return;
        }
    }

    const payload = {
        id_nhan_vien: parseInt(idNhanVien),
        ngay_bat_dau: ngayBatDau,
        ngay_ket_thuc: ngayKetThuc,
        ly_do: lyDo,
        loai_nghi: loaiNghi,
        minh_chung: minhChung,
        trang_thai1: "Chờ duyệt",
        ly_do_tu_choi: null
    };

    showLoading();
    try {
        const response = await fetch("http://localhost/doanqlns/index.php/api/nghiphep", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const responseText = await response.text();
        console.log('POST Response:', responseText);
        console.log('Response Status:', response.status);
        console.log('Response OK:', response.ok);

        if (!response.ok) {
            throw new Error(`Lỗi HTTP: ${response.status} ${response.statusText}`);
        }

        let result;
        try {
            result = JSON.parse(responseText);
            console.log('Parsed Result:', result);
        } catch (e) {
            console.error('JSON Parse Error:', e);
            throw new Error("Phản hồi không phải JSON hợp lệ: " + responseText);
        }

        if (result.success) {
            await loadNghiPhepData();
            closeAddNghiPhepModal();
            alert("Thêm đơn nghỉ phép thành công!");
        } else {
            console.error('API Error:', result);
            throw new Error(result.message || "Lỗi khi thêm đơn nghỉ phép");
        }
    } catch (error) {
        console.error("Lỗi khi thêm đơn nghỉ phép:", error);
        alert("Lỗi khi thêm đơn nghỉ phép: " + error.message);
    } finally {
        hideLoading();
    }
}

        // Toggle hiển thị ô lý do từ chối
        function toggleReasonInput() {
            const status = editStatus.value;
            const reasonInputDiv = document.getElementById('reasonInput');
            if (status === 'Từ chối') {
                reasonInputDiv.classList.add('active');
            } else {
                reasonInputDiv.classList.remove('active');
            }
        }

        // Lưu trạng thái đơn nghỉ phép
        async function saveNghiPhepStatus() {
            if (!userPermissions.quyen_sua) {
                alert("Bạn không có quyền chỉnh sửa trạng thái đơn nghỉ phép!");
                return;
            }

            const idNghiPhep = document.getElementById('editIdNghiPhep').value;
            const idNhanVien = document.getElementById('editIdNhanVien').value;
            const ngayBatDau = document.getElementById('editNgayBatDau').value;
            const ngayKetThuc = document.getElementById('editNgayKetThuc').value;
            let trangThai = document.getElementById('editStatus').value;
            const reason = document.getElementById('reason').value;

            if (trangThai === 'Từ chối' && !reason.trim()) {
                alert("Vui lòng nhập lý do từ chối!");
                return;
            }

            const trangThaiForBackend = trangThai === 'Duyệt' ? 'Đã duyệt' : 
                                       (trangThai === 'Chờ duyệt' ? 'Chờ duyệt' : trangThai);

            showLoading();
            try {
                if (!userId) {
                    throw new Error("Không tìm thấy ID người dùng trong session");
                }

                const payload = {
                    trang_thai1: trangThaiForBackend,
                    id_nguoi_duyet: (trangThai === 'Duyệt' || trangThai === 'Từ chối') ? userId : null,
                    ly_do_tu_choi: trangThai === 'Từ chối' ? reason : null
                };
                console.log('PUT Payload:', payload);

                const response = await fetch(`http://localhost/doanqlns/index.php/api/nghiphep?id=${idNghiPhep}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const responseText = await response.text();
                console.log('Response Text:', responseText);

                if (!response.ok) {
                    throw new Error(`Lỗi HTTP: ${response.status} ${response.statusText}`);
                }

                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (e) {
                    throw new Error("Phản hồi không phải JSON hợp lệ: " + responseText);
                }

                if (result.success) {
                    await updateChamCong(idNhanVien, ngayBatDau, ngayKetThuc, trangThai);
                    await loadNghiPhepData();
                    closeEditModal();
                    alert(trangThai === 'Duyệt' ? 
                        "Cập nhật trạng thái thành công! Email thông báo đã được gửi." : 
                        "Cập nhật trạng thái thành công!");
                } else {
                    throw new Error(result.message || "Lỗi khi cập nhật trạng thái");
                }
            } catch (error) {
                console.error("Lỗi khi cập nhật trạng thái:", error);
                alert("Lỗi khi cập nhật trạng thái: " + error.message);
            } finally {
                hideLoading();
            }
        }

        // Cập nhật bảng chấm công
        async function updateChamCong(idNhanVien, ngayBatDau, ngayKetThuc, trangThai) {
            // Load lại dữ liệu nghỉ phép để có thông tin mới nhất
            try {
                const response = await fetch("http://localhost/doanqlns/index.php/api/nghiphep");
                if (response.ok) {
                    const data = await response.json();
                    if (Array.isArray(data)) {
                        nghiPhepData = data;
                    }
                }
            } catch (error) {
                console.error("Lỗi khi load dữ liệu nghỉ phép:", error);
            }
    const startDate = new Date(ngayBatDau);
    const endDate = new Date(ngayKetThuc);
    const dates = [];

    for (let d = new Date(startDate); d <= endDate; d.setDate(d.getDate() + 1)) {
        const dateStr = `${d.getFullYear()}-${(d.getMonth() + 1).toString().padStart(2, '0')}-${d.getDate().toString().padStart(2, '0')}`;
        const isSunday = d.getDay() === 0;
        if (!isSunday) {
            dates.push(dateStr);
        }
    }

    let chamCongStatus;
    switch (trangThai.toLowerCase()) {
        case "duyệt":
            chamCongStatus = "Có phép";
            break;
        case "từ chối":
            chamCongStatus = "Không phép";
            break;
        case "chờ duyệt":
            chamCongStatus = "Chưa điểm danh";
            break;
        default:
            chamCongStatus = "Chưa điểm danh";
            break;
    }

    // Thêm logic để xử lý loai_nghi
    const nghiPhepRecord = nghiPhepData.find(record => record.id_nhan_vien == idNhanVien && record.ngay_bat_dau == ngayBatDau && record.ngay_ket_thuc == ngayKetThuc);
    if (nghiPhepRecord && trangThai.toLowerCase() === "duyệt") {
        // Chuyển đổi loai_nghi thành trạng thái chấm công hợp lệ
        switch (nghiPhepRecord.loai_nghi) {
            case "Có phép":
            case "Phép Năm":
            case "Nghỉ thai sản":
            case "Nghỉ tai nạn":
                chamCongStatus = "Có phép";
                break;
            case "Không phép":
                chamCongStatus = "Không phép";
                break;
            default:
                chamCongStatus = "Có phép"; // Mặc định
                break;
        }
    }

    for (const date of dates) {
        const data = {
            id_nhan_vien: idNhanVien,
            ngay_lam_viec: date,
            gio_vao: chamCongStatus === 'Có phép' || chamCongStatus === 'Không phép' || chamCongStatus === 'Phép Năm' ? null : '00:00:00',
            gio_ra: chamCongStatus === 'Có phép' || chamCongStatus === 'Không phép' || chamCongStatus === 'Phép Năm' ? null : '00:00:00',
            trang_thai: chamCongStatus,
            ghi_chu: '',
            month: parseInt(date.split('-')[1]),
            year: parseInt(date.split('-')[0])
        };

        try {
            const response = await fetch("http://localhost/doanqlns/index.php/api/chamcong", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            console.log(`POST ChamCong Response for ${date}:`, result);
            if (!result.success) {
                throw new Error(result.message || `Lỗi khi cập nhật chấm công cho ngày ${date}`);
            }
        } catch (error) {
            console.error(`Lỗi khi cập nhật chấm công cho ngày ${date}:`, error);
            alert(`Lỗi khi cập nhật chấm công cho ngày ${date}: ${error.message}`);
        }
    }
}

        // Xóa đơn nghỉ phép
        async function deleteNghiPhep(id) {
            if (!userPermissions.quyen_xoa) {
                alert("Bạn không có quyền xóa đơn nghỉ phép!");
                return;
            }
            if (!confirm(`Bạn có chắc chắn muốn xóa đơn nghỉ phép ID ${id} không?`)) return;

            showLoading();
            try {
                const response = await fetch(`http://localhost/doanqlns/index.php/api/nghiphep?id=${id}`, {
                    method: 'DELETE'
                });
                const result = await response.json();

                if (result.success) {
                    await loadNghiPhepData();
                    alert(`Đã xóa đơn nghỉ phép với ID: ${id}`);
                } else {
                    throw new Error(result.message || "Lỗi khi xóa đơn nghỉ phép");
                }
            } catch (error) {
                console.error("Lỗi khi xóa đơn nghỉ phép:", error);
                alert("Lỗi khi xóa đơn nghỉ phép: " + error.message);
            } finally {
                hideLoading();
            }
        }

        // Sự kiện đóng modal
        detailNghiPhepModal.addEventListener('click', (e) => {
            if (e.target === detailNghiPhepModal) {
                closeDetailModal();
            }
        });

        editNghiPhepModal.addEventListener('click', (e) => {
            if (e.target === editNghiPhepModal) {
                closeEditModal();
            }
        });

        addNghiPhepModal.addEventListener('click', (e) => {
            if (e.target === addNghiPhepModal) {
                closeAddNghiPhepModal();
            }
        });

        // Event listener cho modal Quản lý Nghỉ Phép
        document.getElementById('quanLyNghiPhepModal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('quanLyNghiPhepModal')) {
                closeQuanLyNghiPhepModal();
            }
        });

        function startInitialization() {
            if (document.readyState === 'complete' || document.readyState === 'interactive') {
                initializeDateFilter();
            } else {
                document.addEventListener('DOMContentLoaded', initializeDateFilter);
                // Fallback: nếu sau 2 giây mà không chạy, buộc chạy
                setTimeout(() => {
                    if (!document.getElementById('selectDay').value) {
                        console.log("Fallback: Forcing initialization...");
                        initializeDateFilter();
                    }
                }, 2000);
            }
        }

        startInitialization();

        // Khởi tạo khi trang được tải
        document.addEventListener('DOMContentLoaded', () => {
            const currentDate = new Date();
            document.getElementById('selectMonth').value = currentDate.getMonth() + 1;
            document.getElementById('selectYear').value = currentDate.getFullYear();
            loadUsersData();
            loadNghiPhepData();

            // Sự kiện thay đổi tháng/năm
            document.getElementById('selectMonth').addEventListener('change', loadNghiPhepData);
            document.getElementById('selectYear').addEventListener('input', loadNghiPhepData);
        });

        // Hàm hiển thị modal Quản lý Nghỉ Phép
        function showQuanLyNghiPhepModal() {
            const modal = document.getElementById('quanLyNghiPhepModal');
            const currentDate = new Date();
            document.getElementById('quanLySelectYear').value = currentDate.getFullYear();
            modal.style.display = 'flex';
        }

        // Hàm đóng modal Quản lý Nghỉ Phép
        function closeQuanLyNghiPhepModal() {
            document.getElementById('quanLyNghiPhepModal').style.display = 'none';
        }

        // Hàm tải dữ liệu chấm công để lấy thông tin đi trễ, ra sớm
        async function loadChamCongData(month, year) {
            try {
                const response = await fetch(`http://localhost/doanqlns/index.php/api/chamcong?thang=${month}&nam=${year}`);
                if (!response.ok) throw new Error("Lỗi khi tải dữ liệu chấm công: " + response.status);
                const data = await response.json();
                return Array.isArray(data) ? data : [];
            } catch (error) {
                console.error("Lỗi khi tải dữ liệu chấm công:", error);
                return [];
            }
        }

        // Hàm tải dữ liệu chấm công theo năm
        async function loadChamCongDataByYear(year) {
            try {
                const response = await fetch(`http://localhost/doanqlns/index.php/api/chamcong?nam=${year}`);
                if (!response.ok) throw new Error("Lỗi khi tải dữ liệu chấm công: " + response.status);
                const data = await response.json();
                return Array.isArray(data) ? data : [];
            } catch (error) {
                console.error("Lỗi khi tải dữ liệu chấm công:", error);
                return [];
            }
        }

        // Hàm tính số lần đi trễ và ra sớm từ cột trạng thái
        function calculateLateAndEarly(chamCongData, employeeId, year, startMonthOpt, endMonthOpt) {
            let diTre = 0;
            let raSom = 0;

            chamCongData.forEach(record => {
                if (record.id_nhan_vien == employeeId) {
                    const recordDate = new Date(record.ngay_lam_viec); // Sửa từ ngay_cham_cong thành ngay_lam_viec
                    
                    // Chỉ tính trong năm được chọn và (nếu có) trong khoảng tháng của quý; không tính Chủ Nhật
                    if (recordDate.getFullYear() === year && recordDate.getDay() !== 0) {
                        if (startMonthOpt && endMonthOpt) {
                            const m = recordDate.getMonth() + 1;
                            if (m < startMonthOpt || m > endMonthOpt) return;
                        }
                        const trangThai = record.trang_thai ? record.trang_thai : '';
                        
                        // Kiểm tra trạng thái "Đi trễ"
                        if (trangThai === 'Đi trễ') {
                            diTre++;
                        }
                        // Kiểm tra trạng thái "Ra sớm"
                        if (trangThai === 'Ra sớm') {
                            raSom++;
                        }
                    }
                }
            });

            return { diTre, raSom };
        }

        // Hàm tải dữ liệu quản lý nghỉ phép
        async function loadQuanLyNghiPhepData() {
            const year = parseInt(document.getElementById('quanLySelectYear').value) || new Date().getFullYear();
            
            if (!year) {
                alert("Vui lòng nhập năm!");
                return;
            }

            showLoading();
            try {
                // Tải dữ liệu nghỉ phép
                const nghiPhepResponse = await fetch("http://localhost/doanqlns/index.php/api/nghiphep");
                if (!nghiPhepResponse.ok) throw new Error("Lỗi khi tải dữ liệu nghỉ phép: " + nghiPhepResponse.status);
                const nghiPhepData = await nghiPhepResponse.json();
                if (!Array.isArray(nghiPhepData)) throw new Error("Dữ liệu nghỉ phép không hợp lệ");

                // Tải dữ liệu chấm công
                const chamCongData = await loadChamCongDataByYear(year);

                // Tải dữ liệu nhân viên
                const usersResponse = await fetch("http://localhost/doanqlns/index.php/api/users");
                if (!usersResponse.ok) throw new Error("Lỗi khi tải danh sách nhân viên: " + usersResponse.status);
                const usersData = await usersResponse.json();
                if (!Array.isArray(usersData)) throw new Error("Danh sách nhân viên không hợp lệ");

                // Lọc dữ liệu nghỉ phép theo năm/quý (nếu chọn)
                const qSel = document.getElementById('quanLySelectQuarter');
                const quarterRange = { 1: [1,3], 2: [4,6], 3: [7,9], 4: [10,12] };
                const [startM, endM] = qSel && qSel.value ? (quarterRange[parseInt(qSel.value, 10)] || [1,12]) : [1,12];

                const filteredNghiPhepData = nghiPhepData.filter(record => {
                    const startDate = new Date(record.ngay_bat_dau);
                    const endDate = new Date(record.ngay_ket_thuc);

                    // Điều kiện theo năm
                    if (!(startDate.getFullYear() <= year && endDate.getFullYear() >= year)) {
                        return false;
                    }

                    // Nếu không chọn quý => giữ tất cả trong năm được chọn
                    if (!qSel || !qSel.value) return true;

                    // Kiểm tra xem kỳ nghỉ có đè lên khoảng tháng của quý trong cùng năm không
                    const startMonthInYear = startDate.getFullYear() < year ? 1 : (startDate.getMonth() + 1);
                    const endMonthInYear = endDate.getFullYear() > year ? 12 : (endDate.getMonth() + 1);
                    return endMonthInYear >= startM && startMonthInYear <= endM;
                });

                // Tính số ngày nghỉ của từng nhân viên theo năm/quý
                let leaveDaysByEmployee = {};
                if (qSel && qSel.value) {
                    // Tính theo quý: cộng ngày trong phạm vi tháng của quý
                    const startMonth = startM;
                    const endMonth = endM;
                    const records = filteredNghiPhepData;
                    const result = {};
                    records.forEach(record => {
                        const employeeId = record.id_nhan_vien;
                        const startDate = new Date(record.ngay_bat_dau);
                        const endDate = new Date(record.ngay_ket_thuc);

                        // Xác định khoảng thời gian thuộc quý trong năm
                        const rangeStart = new Date(year, startMonth - 1, 1);
                        const rangeEnd = new Date(year, endMonth, 0);
                        let currentDate = new Date(Math.max(startDate, rangeStart));
                        const end = new Date(Math.min(endDate, rangeEnd));

                        let days = 0;
                        while (currentDate <= end) {
                            if (currentDate.getDay() !== 0) days++;
                            currentDate.setDate(currentDate.getDate() + 1);
                        }

                        if (!result[employeeId]) result[employeeId] = 0;
                        result[employeeId] += days;
                    });
                    leaveDaysByEmployee = result;
                } else {
                    // Mặc định theo năm
                    leaveDaysByEmployee = calculateLeaveDaysByYear(filteredNghiPhepData, year);
                }
                
                // Tính các loại nghỉ phép từ bảng cham_cong (theo năm/quý)
                const leaveTypesByEmployee = (qSel && qSel.value)
                    ? calculateLeaveTypesFromChamCong(chamCongData, year, startM, endM)
                    : calculateLeaveTypesFromChamCong(chamCongData, year);

                // Tạo bảng dữ liệu
                const tableData = [];
                for (const user of usersData) {
                    const employeeId = user.id_nhan_vien;
                    const soNgayNghi = leaveDaysByEmployee[employeeId] || 0;
                    const leaveTypes = leaveTypesByEmployee[employeeId] || { nghiNuaBuoi: 0, coPhep: 0, khongPhep: 0 };
                    
                    // Tính số ngày phép năm đã sử dụng trong năm
                    const phepNamDaSuDung = await calculateUsedAnnualLeaveDays(employeeId, year);
                    const phepNamConLai = 12 - phepNamDaSuDung;
                    
                    // Tính đi trễ và ra sớm từ trạng thái chấm công
                    const { diTre, raSom } = (qSel && qSel.value)
                        ? calculateLateAndEarly(chamCongData, employeeId, year, startM, endM)
                        : calculateLateAndEarly(chamCongData, employeeId, year);

                    // Tính điểm theo thang 10
                    // 10 điểm gốc - các khoản trừ theo quy tắc đã nêu
                    const truNghiQuaPhep = Math.max(0, (soNgayNghi - 12)) * 0.5; // >12 ngày/năm trừ 0.5/ngày
                    // Nghỉ nửa buổi: cứ 2 lần trừ 0.5 điểm
                    const soLanNuaBuoi = leaveTypes.nghiNuaBuoi || 0;
                    const truNghiNuaBuoi = Math.floor(soLanNuaBuoi / 2) * 0.5;
                    // Đi trễ/Ra sớm: cứ 4 lần trừ 0.5 điểm
                    const truDiTre = Math.floor(diTre / 4) * 0.5;
                    const truRaSom = Math.floor(raSom / 4) * 0.5;
                    const truKhongPhep = (leaveTypes.khongPhep || 0) * 1.0;         // 1.0/ngày
                    let diemCuoi = 10 - (truNghiQuaPhep + truNghiNuaBuoi + truDiTre + truRaSom + truKhongPhep);
                    if (diemCuoi < 0) diemCuoi = 0;
                    if (diemCuoi > 10) diemCuoi = 10;

                    tableData.push({
                        id: employeeId,
                        ten: user.ho_ten,
                        soNgayNghi: soNgayNghi,
                        nghiNuaBuoi: leaveTypes.nghiNuaBuoi,
                        coPhep: leaveTypes.coPhep,
                        khongPhep: leaveTypes.khongPhep,
                        phepNam: 12, // Tổng phép năm luôn là 12
                        phepNamConLai: phepNamConLai,
                        diTre: diTre,
                        raSom: raSom,
                        diem: diemCuoi
                    });
                }

                // Đồng bộ điểm chuyên cần theo quý vào danh_gia_nhan_vien (nếu đang lọc theo quý)
                try {
                    const qSel = document.getElementById('quanLySelectQuarter');
                    const quy = qSel && qSel.value ? parseInt(qSel.value, 10) : null;
                    if (quy) {
                        const payload = tableData.map(r => ({ id: r.id, diem: Number(r.diem || 0) }));
                        const res = await fetch(`/doanqlns/index.php/api/danhgia/sync-quarter?nam=${year}&quy=${quy}`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(payload)
                        });
                        try { const txt = await res.text(); console.log('Sync-quarter response:', txt); } catch(e) {}
                    }
                } catch (e) { console.warn('Không thể đồng bộ điểm quý sang bảng đánh giá:', e); }

                // Hiển thị bảng
                renderQuanLyNghiPhepTable(tableData);

            } catch (error) {
                console.error("Lỗi khi tải dữ liệu quản lý nghỉ phép:", error);
                document.getElementById('quanLyNghiPhepTableBody').innerHTML = 
                    '<tr><td colspan="6">Lỗi khi tải dữ liệu: ' + error.message + '</td></tr>';
            } finally {
                hideLoading();
            }
        }

        // Hàm hiển thị bảng quản lý nghỉ phép
        function renderQuanLyNghiPhepTable(data) {
            const tbody = document.getElementById('quanLyNghiPhepTableBody');
            tbody.innerHTML = '';

            if (data && data.length > 0) {
                data.forEach(record => {
                    const row = document.createElement('tr');
                    
                    // Hàm xác định màu sắc cho số ngày nghỉ
                    function getLeaveDaysColor(days) {
                        if (days === 0) return 'neutral';
                        if (days <= 3) return 'success';
                        if (days <= 7) return 'info';
                        if (days <= 10) return 'warning';
                        return 'danger';
                    }
                    
                    // Hàm xác định màu sắc cho phép năm còn lại
                    function getRemainingLeaveColor(remaining) {
                        if (remaining >= 10) return 'success';
                        if (remaining >= 7) return 'info';
                        if (remaining >= 4) return 'warning';
                        return 'danger';
                    }
                    
                    // Hàm xác định màu sắc cho đi trễ/ra sớm
                    function getAttendanceColor(count) {
                        if (count === 0) return 'success';
                        if (count <= 2) return 'info';
                        if (count <= 5) return 'warning';
                        return 'danger';
                    }
                    
                    row.innerHTML = `
                        <td><strong>${record.ten}</strong></td>
                        <td><span class="status ${getLeaveDaysColor(record.soNgayNghi)}">${record.soNgayNghi} ngày</span></td>
                        <td><span class="status ${getLeaveDaysColor(record.nghiNuaBuoi)}">${record.nghiNuaBuoi} lần</span></td>
                        <td><span class="status ${getLeaveDaysColor(record.coPhep)}">${record.coPhep} ngày</span></td>
                        <td><strong style="color: #673AB7;">12 ngày</strong></td>
                        <td><span class="status ${getRemainingLeaveColor(record.phepNamConLai)}">${record.phepNamConLai} ngày</span></td>
                        <td><span class="status ${getAttendanceColor(record.diTre)}">${record.diTre} lần</span></td>
                        <td><span class="status ${getAttendanceColor(record.raSom)}">${record.raSom} lần</span></td>
                        <td><strong>${record.diem.toFixed(1)}/10</strong></td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="9">Không có dữ liệu cho năm đã chọn</td></tr>';
            }
        }

        function updateTongNgayNghi(tongNgayNghi) {
            const tongNgayNghiElement = document.getElementById('detailTongNgayNghi');
            const warningElement = document.getElementById('warningNgayNghi');
            
            tongNgayNghiElement.textContent = tongNgayNghi.toFixed(1);
            
            if (tongNgayNghi > 2) {
                warningElement.style.display = 'inline-flex';
            } else {
                warningElement.style.display = 'none';
            }
        }
    </script>
    <?php include(__DIR__ . '/../includes/footer.php'); ?>
            </div>
        </div>
    </div>
</body>
</html>