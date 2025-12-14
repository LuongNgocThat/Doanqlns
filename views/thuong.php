<?php
require_once __DIR__ . '/../includes/check_login.php';
include(__DIR__ . '/../includes/header.php');
?>
<!DOCTYPE html>
<html lang="vi" class="light-style layout-navbar-fixed layout-menu-fixed">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRM Pro - Bảng Thưởng</title>

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
    .filter-container button#ungLuongBtn:hover {
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
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.6);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }
    #bonusModal {
        align-items: center;
        justify-content: center;
    }
    .modal-content {
        background: #fff;
        width: 85%;
        max-width: 500px;
        border-radius: 16px;
        border: 1px solid #e0e0e0;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        overflow: hidden;
        animation: slideIn 0.3s ease;
        position: relative;
        transition: transform 0.2s ease-out;
    }
    #bonusModal .modal-content {
        position: relative;
    }
    #detailThuongModal .modal-content {
        position: relative;
        transition: none;
    }
    @keyframes slideIn {
        from { transform: translateY(-50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .modal-header {
        background: linear-gradient(90deg, #0056b3, #003f87);
        color: #fff;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: move;
        user-select: none;
    }
    #bonusModal .modal-header {
        cursor: default;
    }
    #detailThuongModal .modal-header {
        cursor: default;
    }
    .modal-header h4, .modal-header h2 {
        margin: 0;
        font-size: 1.6rem;
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
        padding: 20px;
        max-height: 60vh;
        overflow-y: auto;
    }
    .modal-field {
        margin-bottom: 15px;
    }
    .modal-field label {
        display: block;
        font-weight: 500;
        color: #333;
        margin-bottom: 6px;
        font-size: 13px;
    }
    .modal-field input,
    .modal-field select,
    .modal-field textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 13px;
        transition: border-color 0.3s, box-shadow 0.3s;
    }
    .modal-field input:focus,
    .modal-field select:focus,
    .modal-field textarea:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.2);
        outline: none;
    }
    .modal-field textarea {
        resize: vertical;
        min-height: 80px;
    }
    .modal-field select:disabled {
        background: #f0f0f0;
        cursor: not-allowed;
    }
    .modal-footer {
        padding: 15px 20px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        background: #fafafa;
        border-top: 1px solid #ddd;
    }
    .modal-footer button {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .modal-footer .btn-save {
        background: #007bff;
        color: #fff;
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
    }
    .modal-footer .btn-save:hover {
        background: #0056b3;
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.4);
    }
    .modal-footer .btn-cancel {
        background: #6c757d;
        color: #fff;
        box-shadow: 0 2px 8px rgba(108, 117, 125, 0.3);
    }
    .modal-footer .btn-cancel:hover {
        background: #5a6268;
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.4);
    }
    .section-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #003f87;
        margin: 0 0 12px 0;
        border-bottom: 2px solid transparent;
        border-image: linear-gradient(90deg, #007bff, #00c4ff) 1;
        padding-bottom: 6px;
        text-transform: uppercase;
    }
    .info-group {
        display: flex;
        align-items: flex-start;
        margin-bottom: 12px;
        padding: 8px 0;
        gap: 8px;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s ease;
    }
    .info-group:hover {
        background: #f8fbff;
    }
    .info-group label {
        font-weight: 600;
        color: #222;
        width: 120px;
        flex-shrink: 0;
        font-size: 13px;
    }
    .info-group .info-value {
        color: #444;
        flex-grow: 1;
        font-size: 13px;
        line-height: 1.4;
    }
    .modal-footer .btn-close {
        background-color: #ff4d4f;
        color: white;
        box-shadow: 0 2px 8px rgba(255, 77, 79, 0.3);
    }
    .modal-footer .btn-close:hover {
        background-color: #d9363e;
        box-shadow: 0 4px 12px rgba(255, 77, 79, 0.4);
    }
    .action-buttons {
        display: flex;
        gap: 6px;
    }
    .action-buttons button {
        padding: 5px 8px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .action-buttons .btn-edit {
        background: #28a745;
        color: #fff;
    }
    .action-buttons .btn-edit:hover {
        background: #218838;
    }
    .action-buttons .btn-delete {
        background: #dc3545;
        color: #fff;
    }
    .action-buttons .btn-delete:hover {
        background: #c82333;
    }
    .action-buttons i {
        font-size: 12px;
    }
    @media (max-width: 768px) {
        .main-content {
            width: 95%;
            max-width: 400px;
        }
        .filter-container {
            flex-direction: column;
            align-items: center;
        }
        table {
            width: 100%;
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }
        th, td {
            padding: 10px;
        }
        h3 {
            font-size: 22px;
        }
        .modal-content {
            width: 95%;
            max-width: 320px;
        }
        .info-group {
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
        }
        .info-group label {
            width: auto;
            margin-bottom: 4px;
        }
        .info-group .info-value {
            width: 100%;
        }
        .modal-body {
            padding: 12px;
        }
        .modal-footer {
            padding: 8px 12px;
        }
        .modal-header h4, .modal-header h2 {
            font-size: 1.2rem;
        }
        .section-title {
            font-size: 1.0rem;
        }
        .info-group label, .info-group .info-value {
            font-size: 12px;
        }
    }
</style>
</head>

<body>
<div class="layout-wrapper">
    <?php include('../includes/sidebar.php'); ?>
    <div class="layout-page">
        <div class="content-wrapper">
    <h3>Bảng Thưởng</h3>

    <!-- Bộ lọc tháng và năm -->
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
        <button id="addBonusBtn"><i class="fas fa-plus"></i> Thêm Thưởng</button>
        <button id="ungLuongBtn" style="background: #28a745;"><i class="fas fa-money-bill-wave"></i> Ứng Lương</button>
    </div>

    <!-- Bảng thưởng/phạt -->
    <table>
        <thead>
            <tr>
                <th>Mã Thưởng</th>
                <th>Tên Nhân Viên</th>
                <th>Nội Dung</th>
                <th>Ngày</th>
                <th>Loại</th>
                <th>Số Tiền</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody id="thuongTableBody">
            <tr><td colspan="7">Đang tải dữ liệu...</td></tr>
        </tbody>
    </table>

    <!-- Loading indicator -->
    <div class="loading" id="loadingIndicator">Đang xử lý...</div>

    <!-- Modal thêm/sửa thưởng/phạt -->
    <div id="bonusModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h4 id="modalTitle">Thêm Thưởng</h4>
                <button class="modal-close" onclick="closeBonusModal()">×</button>
            </div>
            <div class="modal-body">
                <form id="bonusForm">
                    <input type="hidden" id="thuongId" name="thuongId">
                    <div class="modal-field">
                        <label for="employeeSelect">Nhân Viên</label>
                        <select id="employeeSelect" name="id_nhan_vien" required>
                            <option value="">Chọn nhân viên</option>
                        </select>
                    </div>
                    <div class="modal-field">
                        <label for="bonusContent">Nội Dung</label>
                        <textarea id="bonusContent" name="noi_dung_thuong" placeholder="Nhập nội dung thưởng/phạt"></textarea>
                    </div>
                    <div class="modal-field">
                        <label for="bonusDate">Ngày</label>
                        <input type="date" id="bonusDate" name="ngay" required>
                    </div>
                    <div class="modal-field">
                        <label for="bonusType">Loại</label>
                        <select id="bonusType" name="loai" required onchange="updateDefaultAmount()">
                            <option value="">Chọn loại thưởng/phạt</option>
                        </select>
                    </div>
                    <div class="modal-field">
                        <label for="bonusAmount">Số Tiền (VNĐ)</label>
                        <input type="number" id="bonusAmount" name="tien_thuong" placeholder="Nhập số tiền hoặc để trống để dùng mặc định">
                        <small style="color: #666; font-size: 11px;">Để trống để sử dụng số tiền mặc định theo loại</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-save" form="bonusForm" id="submitButton">Lưu</button>
                <button type="button" class="btn-cancel" onclick="closeBonusModal()">Hủy</button>
            </div>
        </div>
    </div>

    <!-- Modal chi tiết thưởng/phạt -->
    <div id="detailThuongModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Chi Tiết Thưởng</h2>
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
                <div class="section-title">Thông Tin Thưởng</div>
                <div class="info-group">
                    <label>Mã Thưởng:</label>
                    <span id="detailMaThuong" class="info-value"></span>
                </div>
                <div class="info-group">
                    <label>Nội Dung:</label>
                    <span id="detailNoiDungThuong" class="info-value"></span>
                </div>
                <div class="info-group">
                    <label>Ngày:</label>
                    <span id="detailNgayThuong" class="info-value"></span>
                </div>
                <div class="info-group">
                    <label>Loại:</label>
                    <span id="detailLoaiThuong" class="info-value"></span>
                </div>
                <div class="info-group">
                    <label>Số Tiền:</label>
                    <span id="detailTienThuong" class="info-value"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-close" onclick="closeDetailModal()">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
// Biến toàn cục
let thuongData = [];
let bonusTypeConfigs = null; // cache cấu hình từ quan_ly_thuong
const userPermissions = {
    quyen_sua: <?php echo isset($_SESSION['quyen_sua']) && $_SESSION['quyen_sua'] ? 'true' : 'false'; ?>,
    quyen_xoa: <?php echo isset($_SESSION['quyen_xoa']) && $_SESSION['quyen_xoa'] ? 'true' : 'false'; ?>
};

// Tham chiếu đến các phần tử DOM
const bonusModal = document.getElementById('bonusModal');
const detailThuongModal = document.getElementById('detailThuongModal');
const addBonusBtn = document.getElementById('addBonusBtn');
const ungLuongBtn = document.getElementById('ungLuongBtn');
const closeModal = document.querySelector('#bonusModal .modal-close');
const cancelBtn = document.querySelector('#bonusModal .btn-cancel');
const bonusForm = document.getElementById('bonusForm');
const loadingIndicator = document.getElementById('loadingIndicator');
const thuongTableBody = document.getElementById('thuongTableBody');

// Suy luận loại thưởng/phạt từ nội dung khi cột "loai" rỗng/không hợp lệ
function inferLoaiFromNoiDung(noiDung) {
    if (!noiDung) return '';
    const nd = (noiDung || '').toLowerCase();
    // Các biến thể không dấu/ lỗi encoding thường gặp (t?t, xu?t s?c)
    if (nd.includes('xuất sắc') || nd.includes('xuat sac') || nd.includes('xu?t s?c')) {
        return 'thành tích cá nhân - xuất sắc';
    }
    if (nd.includes('thưởng thành tích - tốt') || nd.includes('thuong thanh tich - tot') || nd.includes('tt') || nd.includes('t?t')) {
        return 'thành tích cá nhân - tốt';
    }
    if (nd.includes('khá') || nd.includes('kha')) {
        return 'thành tích cá nhân - khá';
    }
    if (nd.includes('cần cải thiện') || nd.includes('can cai thien')) {
        return 'phạt kỷ luật';
    }
    // Mặc định: thành tích cá nhân
    if (nd.includes('thưởng thành tích') || nd.includes('thuong thanh tich')) {
        return 'thành tích cá nhân';
    }
    return '';
}

// Hàm định dạng tiền tệ
function formatCurrency(value) {
    if (value == null || value == undefined) return '0';
    return Number(value).toLocaleString('vi-VN', { style: 'currency', currency: 'VND' });
}

// Hàm xử lý ứng lương
function handleUngLuong() {
    // Chuyển hướng đến trang quản lý ứng lương
    window.location.href = '/doanqlns/views/ungluong.php';
}

// Hàm cập nhật tiền thưởng trong bảng lương
async function updateLuongTienThuong(idNhanVien, ngay) {
    try {
        console.log('🔄 Đang cập nhật tiền thưởng cho nhân viên:', idNhanVien, 'ngày:', ngay);
        
        // Lấy tháng/năm từ ngày
        const date = new Date(ngay);
        const thang = `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}`;
        
        // Tính tổng tiền thưởng cho nhân viên trong tháng
        const response = await fetch(`http://localhost/doanqlns/index.php/api/thuong`);
        if (!response.ok) {
            throw new Error(`Lỗi khi tải dữ liệu thưởng: ${response.status}`);
        }
        
        const thuongData = await response.json();
        const thuongTrongThang = thuongData.filter(record => {
            const recordDate = new Date(record.ngay);
            const recordThang = `${recordDate.getFullYear()}-${(recordDate.getMonth() + 1).toString().padStart(2, '0')}`;
            return record.id_nhan_vien == idNhanVien && recordThang === thang;
        });
        
        // Tính tổng tiền thưởng (chỉ tính thưởng, không tính phạt)
        const tongTienThuong = thuongTrongThang
            .filter(record => record.loai === 'Thưởng Khen Thưởng' || record.loai === 'Thưởng Hiệu Suất' || record.loai === 'Thưởng Dự Án' || record.loai === 'Thưởng Khác')
            .reduce((sum, record) => sum + (parseFloat(record.tien_thuong) || 0), 0);
        
        console.log('💰 Tổng tiền thưởng trong tháng:', tongTienThuong);
        
        // Cập nhật bảng lương
        const updateResponse = await fetch(`http://localhost/doanqlns/index.php/api/update-luong-tien-thuong`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id_nhan_vien: idNhanVien,
                thang: thang,
                tien_thuong: tongTienThuong
            })
        });
        
        if (!updateResponse.ok) {
            const errorData = await updateResponse.json();
            throw new Error(errorData.message || 'Lỗi khi cập nhật bảng lương');
        }
        
        const updateResult = await updateResponse.json();
        console.log('✅ Cập nhật bảng lương thành công:', updateResult);
        
    } catch (error) {
        console.error('❌ Lỗi khi cập nhật tiền thưởng trong bảng lương:', error);
        // Không hiển thị lỗi cho user vì thưởng đã được thêm thành công
    }
}

// Hàm hiển thị loading
function showLoading() {
    loadingIndicator.style.display = 'block';
}

// Hàm ẩn loading
function hideLoading() {
    loadingIndicator.style.display = 'none';
}

// Hàm tải danh sách nhân viên
async function loadEmployees() {
    try {
        const response = await fetch('http://localhost/doanqlns/index.php/api/users');
        if (!response.ok) throw new Error('Lỗi khi tải danh sách nhân viên');
        const employees = await response.json();
        const employeeSelect = document.getElementById('employeeSelect');
        employeeSelect.innerHTML = '<option value="">Chọn nhân viên</option>';
        employees.forEach(emp => {
            const option = document.createElement('option');
            option.value = emp.id_nhan_vien;
            option.textContent = emp.ho_ten;
            employeeSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Lỗi khi tải danh sách nhân viên:', error);
        alert('Không thể tải danh sách nhân viên');
    }
}

// Hàm tải danh sách loại thưởng/phạt
async function loadBonusTypes() {
    try {
        const response = await fetch('http://localhost/doanqlns/index.php/api/thuong/types');
        if (!response.ok) throw new Error('Lỗi khi tải danh sách loại thưởng/phạt');
        const types = await response.json();
        const bonusTypeSelect = document.getElementById('bonusType');
        bonusTypeSelect.innerHTML = '<option value="">Chọn loại thưởng/phạt</option>';
        types.forEach(type => {
            const option = document.createElement('option');
            option.value = type;
            option.textContent = type === 'nghỉ lễ' ? 'Nghỉ Lễ' :
                                 type === 'thăng chức' ? 'Thăng Chức' :
                                 type === 'thành tích cá nhân' ? 'Thành Tích Cá Nhân' :
                                 type === 'thành tích cá nhân - xuất sắc' ? 'Thành Tích Cá Nhân - Xuất Sắc' :
                                 type === 'thành tích cá nhân - tốt' ? 'Thành Tích Cá Nhân - Tốt' :
                                 type === 'thành tích cá nhân - khá' ? 'Thành Tích Cá Nhân - Khá' :
                                 type === 'phạt kỷ luật' ? 'Phạt Kỷ Luật' :
                                 'Phạt Trách Nhiệm Công Việc';
            bonusTypeSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Lỗi khi tải danh sách loại thưởng/phạt:', error);
        alert('Không thể tải danh sách loại thưởng/phạt');
    }
}

// Hàm tải và hiển thị bảng thưởng/phạt
async function loadBonusData() {
    const month = parseInt(document.getElementById('selectMonth').value);
    const yearInput = document.getElementById('selectYear');
    const year = parseInt(yearInput.value) || new Date().getFullYear();
    
    if (!yearInput.value) {
        yearInput.value = new Date().getFullYear();
    }

    showLoading();
    try {
        const thuongResponse = await fetch('http://localhost/doanqlns/index.php/api/thuong');
        if (!thuongResponse.ok) throw new Error('Lỗi khi tải dữ liệu thưởng/phạt: ' + thuongResponse.status);
        thuongData = await thuongResponse.json();
        if (!Array.isArray(thuongData)) throw new Error('Dữ liệu thưởng/phạt không hợp lệ');

        thuongData = thuongData.filter(record => {
            const recordDate = new Date(record.ngay);
            return recordDate.getMonth() + 1 === month && recordDate.getFullYear() === year;
        });

        thuongTableBody.innerHTML = '';

        if (thuongData.length > 0) {
            thuongData.forEach(record => {
                const row = document.createElement('tr');
                const loaiHienTai = record.loai && record.loai.trim() !== '' ? record.loai : inferLoaiFromNoiDung(record.noi_dung_thuong);
                const displayLoai = loaiHienTai === 'nghỉ lễ' ? 'Nghỉ Lễ' :
                                    loaiHienTai === 'thăng chức' ? 'Thăng Chức' :
                                    loaiHienTai === 'thành tích cá nhân' ? 'Thành Tích Cá Nhân' :
                                    loaiHienTai === 'thành tích cá nhân - xuất sắc' ? 'Thành Tích Cá Nhân - Xuất Sắc' :
                                    loaiHienTai === 'thành tích cá nhân - tốt' ? 'Thành Tích Cá Nhân - Tốt' :
                                    loaiHienTai === 'thành tích cá nhân - khá' ? 'Thành Tích Cá Nhân - Khá' :
                                    loaiHienTai === 'phạt kỷ luật' ? 'Phạt Kỷ Luật' :
                                    'Phạt Trách Nhiệm Công Việc';
                row.innerHTML = `
                    <td>${record.id_thuong}</td>
                    <td><a href="#" class="name-link" data-id="${record.id_nhan_vien}" data-thuong-id="${record.id_thuong}">${record.ho_ten}</a></td>
                    <td>${record.noi_dung_thuong || 'Không có'}</td>
                    <td>${record.ngay}</td>
                    <td>${displayLoai}</td>
                    <td>${formatCurrency(record.tien_thuong)}</td>
                    <td class="action-buttons">
                        ${userPermissions.quyen_sua ? `
                            <button class="btn-edit" onclick="editBonus(${record.id_thuong}, '${record.id_nhan_vien}', '${record.noi_dung_thuong || ''}', '${record.ngay}', '${record.loai}', ${record.tien_thuong})"><i class="fas fa-edit"></i> Sửa</button>
                        ` : ''}
                        ${userPermissions.quyen_xoa ? `
                            <button class="btn-delete" onclick="deleteBonus(${record.id_thuong})"><i class="fas fa-trash"></i> Xóa</button>
                        ` : ''}
                    </td>
                `;
                thuongTableBody.appendChild(row);
            });

            document.querySelectorAll('.name-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const userId = this.getAttribute('data-id');
                    const thuongId = this.getAttribute('data-thuong-id');
                    showUserDetails(userId, thuongId);
                });
            });
        } else {
            thuongTableBody.innerHTML = `<tr><td colspan="7">Không có dữ liệu thưởng/phạt cho tháng ${month}/${year}</td></tr>`;
        }
    } catch (error) {
        console.error('Lỗi khi tải dữ liệu:', error);
        thuongTableBody.innerHTML = '<tr><td colspan="7">Lỗi khi tải dữ liệu</td></tr>';
    } finally {
        hideLoading();
    }
}

// Hàm hiển thị chi tiết nhân viên và thưởng/phạt
async function showUserDetails(userId, thuongId) {
    showLoading();
    try {
        // Tìm bản ghi thưởng/phạt
        const thuongRecord = thuongData.find(record => record.id_thuong == thuongId);
        if (!thuongRecord) {
            throw new Error("Không tìm thấy bản ghi thưởng/phạt");
        }

        // Tải thông tin nhân viên
        const response = await fetch(`http://localhost/doanqlns/index.php/api/user?id=${userId}`);
        if (!response.ok) throw new Error("Lỗi khi tải thông tin nhân viên: " + response.status);
        const data = await response.json();
        const user = Array.isArray(data) ? data[0] : data;
        if (!user) throw new Error("Không tìm thấy thông tin nhân viên");

        // Điền thông tin nhân viên
        document.getElementById('detailHoTen').textContent = user.ho_ten || 'Không có dữ liệu';
        document.getElementById('detailGioiTinh').textContent = user.gioi_tinh || 'Không có dữ liệu';
        document.getElementById('detailNgaySinh').textContent = user.ngay_sinh || 'Không có dữ liệu';
        document.getElementById('detailEmail').textContent = user.email || 'Không có dữ liệu';
        document.getElementById('detailSoDienThoai').textContent = user.so_dien_thoai || 'Không có dữ liệu';
        document.getElementById('detailDiaChi').textContent = user.dia_chi || 'Không có dữ liệu';
        document.getElementById('detailPhongBan').textContent = user.ten_phong_ban || 'Không có dữ liệu';
        document.getElementById('detailChucVu').textContent = user.ten_chuc_vu || 'Không có dữ liệu';

        // Điền thông tin thưởng/phạt
        document.getElementById('detailMaThuong').textContent = thuongRecord.id_thuong || 'Không có dữ liệu';
        document.getElementById('detailNoiDungThuong').textContent = thuongRecord.noi_dung_thuong || 'Không có dữ liệu';
        document.getElementById('detailNgayThuong').textContent = thuongRecord.ngay || 'Không có dữ liệu';
        const loaiChiTiet = (thuongRecord.loai && thuongRecord.loai.trim() !== '' ? thuongRecord.loai : inferLoaiFromNoiDung(thuongRecord.noi_dung_thuong));
        document.getElementById('detailLoaiThuong').textContent = 
            loaiChiTiet === 'nghỉ lễ' ? 'Nghỉ Lễ' :
            loaiChiTiet === 'thăng chức' ? 'Thăng Chức' :
            loaiChiTiet === 'thành tích cá nhân' ? 'Thành Tích Cá Nhân' :
            loaiChiTiet === 'thành tích cá nhân - xuất sắc' ? 'Thành Tích Cá Nhân - Xuất Sắc' :
            loaiChiTiet === 'thành tích cá nhân - tốt' ? 'Thành Tích Cá Nhân - Tốt' :
            loaiChiTiet === 'thành tích cá nhân - khá' ? 'Thành Tích Cá Nhân - Khá' :
            loaiChiTiet === 'phạt kỷ luật' ? 'Phạt Kỷ Luật' :
            'Phạt Trách Nhiệm Công Việc';
        document.getElementById('detailTienThuong').textContent = formatCurrency(thuongRecord.tien_thuong);

        detailThuongModal.style.display = 'flex';
    } catch (error) {
        console.error("Lỗi khi hiển thị chi tiết thưởng/phạt:", error);
        alert("Lỗi khi hiển thị chi tiết thưởng/phạt: " + error.message);
    } finally {
        hideLoading();
    }
}

// Hàm đóng modal chi tiết
function closeDetailModal() {
    detailThuongModal.style.display = 'none';
}

// Hàm cập nhật số tiền mặc định khi thay đổi loại
async function updateDefaultAmount() {
    const loai = document.getElementById('bonusType').value;
    const amountInput = document.getElementById('bonusAmount');

    // Nạp cấu hình nếu chưa có
    if (!bonusTypeConfigs) {
        try {
            const res = await fetch('/doanqlns/simple_quan_ly_thuong_api.php/quan-ly-thuong');
            const json = await res.json();
            if (json && json.success) bonusTypeConfigs = json.data || [];
        } catch (e) { bonusTypeConfigs = []; }
    }

    const cfg = (bonusTypeConfigs || []).find(x => x.loai === loai);
    if (cfg) {
        amountInput.placeholder = `Mặc định: ${Number(cfg.so_tien_mac_dinh).toLocaleString('vi-VN')} VNĐ`;
    } else {
        amountInput.placeholder = 'Nhập số tiền hoặc để trống để dùng mặc định';
    }
}

// Hàm mở modal để sửa thưởng/phạt
async function editBonus(thuongId, id_nhan_vien, noi_dung_thuong, ngay, loai, tien_thuong) {
    if (!userPermissions.quyen_sua) {
        alert("Bạn không có quyền chỉnh sửa thưởng/phạt!");
        return;
    }

    // Cập nhật tiêu đề và nút submit
    document.getElementById('modalTitle').textContent = 'Sửa Thưởng';
    document.getElementById('submitButton').textContent = 'Cập Nhật';
    document.getElementById('thuongId').value = thuongId;

    // Tải danh sách nhân viên và loại thưởng/phạt trước
    try {
        await Promise.all([loadEmployees(), loadBonusTypes()]);

        // Đặt giá trị và vô hiệu hóa dropdown nhân viên
        const employeeSelect = document.getElementById('employeeSelect');
        employeeSelect.value = id_nhan_vien;
        employeeSelect.disabled = true;

        // Kiểm tra xem nhân viên có được chọn đúng không
        if (!employeeSelect.value) {
            console.warn(`Không tìm thấy nhân viên với ID: ${id_nhan_vien}`);
            alert('Nhân viên không tồn tại trong danh sách. Vui lòng kiểm tra dữ liệu.');
            employeeSelect.disabled = false; // Cho phép chọn nếu có lỗi
            return;
        }

        // Điền các trường còn lại
        document.getElementById('bonusContent').value = noi_dung_thuong;
        document.getElementById('bonusDate').value = ngay;
        document.getElementById('bonusType').value = loai;
        document.getElementById('bonusAmount').value = tien_thuong || '';

        // Cập nhật placeholder cho số tiền
        updateDefaultAmount();

        // Hiển thị modal
        bonusModal.style.display = 'flex';
    } catch (error) {
        console.error('Lỗi khi tải dữ liệu cho modal chỉnh sửa:', error);
        alert('Lỗi khi mở modal chỉnh sửa: ' + error.message);
    }
}

// Hàm xóa thưởng/phạt
async function deleteBonus(thuongId) {
    if (!userPermissions.quyen_xoa) {
        alert("Bạn không có quyền xóa thưởng/phạt!");
        return;
    }
    if (!confirm('Bạn có chắc chắn muốn xóa thưởng/phạt này?')) {
        return;
    }

    showLoading();
    try {
        const response = await fetch(`http://localhost/doanqlns/index.php/api/thuong?id=${thuongId}`, {
            method: 'DELETE'
        });

        const result = await response.json();
        if (!response.ok) {
            throw new Error(result.message || 'Lỗi khi xóa thưởng/phạt');
        }

        if (result.success) {
            alert('Xóa thưởng/phạt thành công');
            loadBonusData();
        } else {
            throw new Error(result.message || 'Lỗi khi xóa thưởng/phạt');
        }
    } catch (error) {
        console.error('Lỗi khi xóa thưởng/phạt:', error);
        alert('Lỗi khi xóa thưởng/phạt: ' + error.message);
    } finally {
        hideLoading();
    }
}

// Xử lý modal thêm/sửa thưởng/phạt
addBonusBtn.addEventListener('click', async () => {
    if (!userPermissions.quyen_sua) {
        alert("Bạn không có quyền thêm thưởng/phạt!");
        return;
    }
    document.getElementById('modalTitle').textContent = 'Thêm Thưởng';
    document.getElementById('submitButton').textContent = 'Lưu';
    document.getElementById('bonusForm').reset();
    document.getElementById('thuongId').value = '';
    document.getElementById('employeeSelect').disabled = false; // Cho phép chọn khi thêm
    await Promise.all([loadEmployees(), loadBonusTypes()]);
    // preload configs
    try { const res = await fetch('/doanqlns/simple_quan_ly_thuong_api.php/quan-ly-thuong'); const json = await res.json(); if (json && json.success) bonusTypeConfigs = json.data; } catch (e) {}
    const month = document.getElementById('selectMonth').value.padStart(2, '0');
    const year = document.getElementById('selectYear').value || new Date().getFullYear();
    document.getElementById('bonusDate').value = `${year}-${month}-01`;
    bonusModal.style.display = 'flex';
});

// Đóng modal thêm/sửa
function closeBonusModal() {
    bonusModal.style.display = 'none';
    bonusForm.reset();
    document.getElementById('thuongId').value = '';
    document.getElementById('employeeSelect').disabled = false;
}

closeModal.addEventListener('click', closeBonusModal);
cancelBtn.addEventListener('click', closeBonusModal);

// Đóng modal khi click bên ngoài
window.addEventListener('click', (e) => {
    if (e.target === bonusModal) {
        closeBonusModal();
    }
    if (e.target === detailThuongModal) {
        closeDetailModal();
    }
});

// Xử lý submit form
bonusForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    // Tạm thời bật employeeSelect để đảm bảo giá trị được gửi
    const employeeSelect = document.getElementById('employeeSelect');
    const wasDisabled = employeeSelect.disabled;
    if (wasDisabled) {
        employeeSelect.disabled = false;
    }

    const formData = new FormData(bonusForm);
    const thuongId = formData.get('thuongId');
    const data = {
        id_nhan_vien: formData.get('id_nhan_vien'),
        noi_dung_thuong: formData.get('noi_dung_thuong'),
        ngay: formData.get('ngay'),
        loai: formData.get('loai'),
        tien_thuong: formData.get('tien_thuong')
    };

    // Khôi phục trạng thái disabled
    if (wasDisabled) {
        employeeSelect.disabled = true;
    }

    // Ghi log để debug
    console.log('Form data:', data);

    // Kiểm tra các trường bắt buộc với thông báo cụ thể
    if (!data.id_nhan_vien) {
        alert('Vui lòng chọn nhân viên!');
        return;
    }
    if (!data.ngay) {
        alert('Vui lòng chọn ngày!');
        return;
    }
    if (!data.loai) {
        alert('Vui lòng chọn loại thưởng/phạt!');
        return;
    }

    const isEdit = !!thuongId;
    const method = isEdit ? 'PUT' : 'POST';
    const url = isEdit 
        ? `http://localhost/doanqlns/index.php/api/thuong?id=${thuongId}` 
        : 'http://localhost/doanqlns/index.php/api/thuong';

    showLoading();
    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        if (!response.ok) {
            throw new Error(result.message || (isEdit ? 'Lỗi khi sửa thưởng/phạt' : 'Lỗi khi thêm thưởng/phạt'));
        }

        if (result.success) {
            // Cập nhật tiền thưởng trong bảng lương
            await updateLuongTienThuong(data.id_nhan_vien, data.ngay);
            
            alert(isEdit ? 'Sửa thưởng/phạt thành công' : 'Thêm thưởng/phạt thành công');
            closeBonusModal();
            loadBonusData();
        } else {
            throw new Error(result.message || (isEdit ? 'Lỗi khi sửa thưởng/phạt' : 'Lỗi khi thêm thưởng/phạt'));
        }
    } catch (error) {
        console.error(`Lỗi khi ${isEdit ? 'sửa' : 'thêm'} thưởng/phạt:`, error);
        alert(`Lỗi khi ${isEdit ? 'sửa' : 'thêm'} thưởng/phạt: ${error.message}`);
    } finally {
        hideLoading();
    }
});

// Khởi tạo khi trang tải
document.addEventListener('DOMContentLoaded', () => {
    const currentDate = new Date();
    document.getElementById('selectMonth').value = currentDate.getMonth() + 1;
    document.getElementById('selectYear').value = currentDate.getFullYear();
    loadBonusData();

    // Sự kiện thay đổi tháng/năm
    document.getElementById('selectMonth').addEventListener('change', loadBonusData);
    document.getElementById('selectYear').addEventListener('change', loadBonusData);
    
    // Sự kiện click nút ứng lương
    ungLuongBtn.addEventListener('click', handleUngLuong);

    // Tải cấu hình mặc định từ quan_ly_thuong
    fetch('http://localhost/doanqlns/index.php/api/thuong/configs')
        .then(response => response.json())
        .then(configs => {
            bonusTypeConfigs = configs;
            // Cập nhật placeholder cho số tiền mặc định khi modal được mở
            updateDefaultAmount();
        })
        .catch(error => {
            console.error('Lỗi khi tải cấu hình mặc định:', error);
            alert('Không thể tải cấu hình mặc định. Vui lòng thử lại sau.');
        });
});
</script>
<?php include(__DIR__ . '/../includes/footer.php'); ?>
        </div>
    </div>
</div>
</body>
</html>