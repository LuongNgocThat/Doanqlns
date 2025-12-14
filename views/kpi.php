<?php
$current_page = 'kpi.php';
require_once __DIR__ . '/../includes/check_login.php';
include(__DIR__ . '/../includes/header.php');
include(__DIR__ . '/../includes/sidebar.php');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRM Pro - Quản lý KPI</title>

    <!-- Font Awesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- CSS chính -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/stylenghiphep.css">

    <!-- CSS riêng -->
    <style>
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
        .btn-add:hover, .btn-edit:hover, .btn-delete:hover {
            opacity: 0.9;
        }
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
        .modal.show {
            display: flex !important;
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
            font-size: 18px;
            font-weight: 500;
        }
        .close {
            background: none;
            border: none;
            color: #fff;
            font-size: 24px;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .close:hover {
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
        }
        .modal input:focus, .modal select:focus, .modal textarea:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        }
        .modal-body {
            padding: 20px;
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
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        }
        .modal-footer {
            padding: 15px 20px;
            background: #f8f9fa;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #007bff;
            color: white;
        }
        .btn-primary:hover {
            background: #0056b3;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #545b62;
        }
        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            margin: 0 auto;
            max-width: 100%;
        }
        .table-header {
            background: linear-gradient(90deg, #007bff, #0056b3);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .table-header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 500;
        }
        .table-content {
            padding: 0;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table th {
            background: #f8f9fa;
            padding: 25px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
        }
        .data-table td {
            padding: 25px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
        }
        .data-table tr:hover {
            background: #f8f9fa;
        }
        .currency {
            font-weight: 600;
            color: #28a745;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            font-style: italic;
        }
        .tab-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            margin: 0 auto;
            max-width: 100%;
        }
        .tab-header {
            background: linear-gradient(90deg, #007bff, #0056b3);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .tab-header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 500;
        }
        .tab-buttons {
            display: flex;
            gap: 10px;
        }
        .tab-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .tab-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        .tab-btn.active {
            background: white;
            color: #007bff;
        }
        .tab-content {
            display: none;
            padding: 0;
        }
        .tab-content.active {
            display: block;
        }
        .filter-container {
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .filter-container select, .filter-container button {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        .stats-container {
            padding: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 24px;
            font-weight: 600;
        }
        .stat-card p {
            margin: 0;
            font-size: 14px;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container-fluid" style="padding: 25px 25px 25px 260px;">
        <div class="row">
            <div class="col-12">
                <div class="tab-container" style="max-width: 1200px; margin: 0 auto;">
                    <div class="tab-header">
                        <h2><i class="fas fa-chart-line"></i> Quản lý KPI</h2>
                        <div class="tab-buttons">
                            <button class="tab-btn active" onclick="switchTab('hopdong')">
                                <i class="fas fa-file-contract"></i> Hợp Đồng
                            </button>
                            <button class="tab-btn" onclick="switchTab('doanhso')">
                                <i class="fas fa-chart-bar"></i> Doanh Số
                            </button>
                            <button class="tab-btn" onclick="switchTab('cauhin')">
                                <i class="fas fa-cog"></i> Cấu Hình
                            </button>
                        </div>
                    </div>

                    <!-- Hợp Đồng KPI Tab -->
                    <div id="hopdong" class="tab-content active">
                        <div style="padding: 20px; background: #f8f9fa; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
                            <h4 style="margin: 0; color: #495057;">Hợp Đồng KPI</h4>
                            <button class="btn-add" onclick="showAddHopDongModal()">
                                <i class="fas fa-plus"></i> Thêm Hợp Đồng
                            </button>
                        </div>
                        <div class="table-content">
                            <table class="data-table" id="hopDongTable">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Ngày Ký</th>
                                        <th>Mã HĐ</th>
                                        <th>Giá Trị HĐ (VNĐ)</th>
                                        <th>Nhân Viên</th>
                                        <th>File Hợp Đồng</th>
                                        <th>Trạng Thái</th>
                                        <th>Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody id="hopDongTableBody">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Doanh Số Tháng Tab -->
                    <div id="doanhso" class="tab-content">
                        <div style="padding: 20px; background: #f8f9fa; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
                            <h4 style="margin: 0; color: #495057;">Doanh Số Tháng</h4>
                            <button class="btn-add" onclick="showAddDoanhSoModal()">
                                <i class="fas fa-plus"></i> Thêm Doanh Số
                            </button>
                            <button class="btn btn-info btn-sm" onclick="autoSyncAllHopDong()" style="margin-left: 10px;">
                                <i class="fas fa-sync"></i> Sync Tự Động
                            </button>
                        </div>
                        <div class="filter-container">
                            <select id="filterThang">
                                <option value="">Tất cả tháng</option>
                                <?php for($i=1; $i<=12; $i++): ?>
                                <option value="<?= $i ?>">Tháng <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                            <select id="filterNam">
                                <option value="">Tất cả năm</option>
                                <?php for($i=date('Y'); $i>=2020; $i--): ?>
                                <option value="<?= $i ?>">Năm <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                            <button class="btn btn-primary" onclick="filterDoanhSo()">
                                <i class="fas fa-search"></i> Lọc
                            </button>
                        </div>
                        <div class="table-content">
                            <table class="data-table" id="doanhSoTable">
                                <thead>
                                    <tr>
                                        <th>Nhân Viên</th>
                                        <th>Tháng/Năm</th>
                                        <th>Doanh Số (VNĐ)</th>
                                        <th>% Hoa Hồng</th>
                                        <th>Hoa Hồng Nhận</th>
                                        <th>Ghi Chú</th>
                                        <th>Công Điểm</th>
                                        <th>Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody id="doanhSoTableBody">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Cấu Hình Tab -->
                    <div id="cauhin" class="tab-content">
                        <div style="padding: 20px; background: #f8f9fa; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
                            <h4 style="margin: 0; color: #495057;">Cấu Hình Thang Điểm</h4>
                            <button class="btn-add" onclick="showCauHinhModal()">
                                <i class="fas fa-cog"></i> Quản Lý Cấu Hình
                            </button>
                        </div>
                        <div class="table-content">
                            <table class="data-table" id="cauHinhTable">
                                <thead>
                                    <tr>
                                        <th>Tên Cấu Hình</th>
                                        <th>Doanh Số Min (VNĐ)</th>
                                        <th>Doanh Số Max (VNĐ)</th>
                                        <th>% Hoa Hồng</th>
                                        <th>Ghi Chú</th>
                                        <th>Công Điểm</th>
                                        <th>Trạng Thái</th>
                                        <th>Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody id="cauHinhTableBody">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

<!-- Modals -->
<?php include 'modals/kpi_modals.php'; ?>

<script>
// Global variables
let hopDongData = [];
let doanhSoData = [];
let nhanVienData = [];
let cauHinhData = [];

// Load data on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, starting data load...');
    loadHopDongData();
    loadDoanhSoData();
    loadNhanVienData();
    loadCauHinhData(); // loadCauHinhData() đã gọi loadCauHinhTable() bên trong
    
    // Force load cấu hình data after a short delay
    setTimeout(() => {
        console.log('Force loading cau hinh data...');
        loadCauHinhData();
    }, 1000);
    
    // Force load again after 3 seconds to ensure data is loaded
    setTimeout(() => {
        console.log('Second force load...');
        loadCauHinhData();
    }, 3000);
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            if (event.target === modal) {
                closeModal();
            }
        });
    });
});

// Tab switching function
function switchTab(tabName) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => content.classList.remove('active'));
    
    // Remove active class from all tab buttons
    const tabButtons = document.querySelectorAll('.tab-btn');
    tabButtons.forEach(btn => btn.classList.remove('active'));
    
    // Show selected tab content
    document.getElementById(tabName).classList.add('active');
    
    // Add active class to clicked button
    event.target.classList.add('active');
    
    // Load data for specific tabs
    if (tabName === 'hopdong') {
        // Tự động load dữ liệu hợp đồng khi chuyển sang tab
        loadHopDongData();
    } else if (tabName === 'doanhso') {
        // Tự động load dữ liệu doanh số khi chuyển sang tab
        loadDoanhSoData();
    } else if (tabName === 'cauhin') {
        // Luôn load data khi chuyển sang tab cấu hình
        loadCauHinhData();
    }
}

// Load Hợp Đồng KPI data
async function loadHopDongData() {
    try {
        // Show loading indicator
        const tbody = document.getElementById('hopDongTableBody');
        tbody.innerHTML = '<tr><td colspan="8" class="text-center"><i class="fas fa-spinner fa-spin"></i> Đang tải dữ liệu...</td></tr>';
        
        const response = await fetch('/doanqlns/simple_kpi_api.php/hop-dong-kpi');
        const result = await response.json();
        
        if (result.success) {
            hopDongData = result.data;
            renderHopDongTable();
        } else {
            showAlert('Lỗi khi tải dữ liệu hợp đồng: ' + result.message, 'danger');
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Lỗi tải dữ liệu</td></tr>';
        }
    } catch (error) {
        console.error('Error loading hop dong data:', error);
        showAlert('Lỗi kết nối server', 'danger');
        const tbody = document.getElementById('hopDongTableBody');
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Lỗi kết nối server</td></tr>';
    }
}

// Render Hợp Đồng KPI table
function renderHopDongTable() {
    const tbody = document.getElementById('hopDongTableBody');
    tbody.innerHTML = '';
    
    hopDongData.forEach((item, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${index + 1}</td>
            <td>${formatDate(item.ngay_ky)}</td>
            <td><span class="badge bg-primary">${item.ma_hop_dong}</span></td>
            <td>${formatMoney(item.gia_tri_hop_dong)}</td>
            <td>${item.ho_ten || 'N/A'}</td>
            <td>
                ${item.file_hop_dong ? 
                    `<a href="${item.file_hop_dong}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt"></i> Xem
                    </a>` : 
                    '<span class="text-muted">Chưa có</span>'
                }
            </td>
            <td><span class="badge bg-${getStatusColor(item.trang_thai)}">${item.trang_thai}</span></td>
            <td>
                <button class="btn-edit" onclick="editHopDong(${item.id_hop_dong})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn-delete" onclick="deleteHopDong(${item.id_hop_dong})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Load Doanh Số Tháng data
async function loadDoanhSoData() {
    try {
        // Show loading indicator
        const tbody = document.getElementById('doanhSoTableBody');
        tbody.innerHTML = '<tr><td colspan="8" class="text-center"><i class="fas fa-spinner fa-spin"></i> Đang tải dữ liệu...</td></tr>';
        
        const thang = document.getElementById('filterThang').value;
        const nam = document.getElementById('filterNam').value;
        
        let url = '/doanqlns/index.php/api/doanh-so-thang';
        const params = new URLSearchParams();
        if (thang) params.append('thang', thang);
        if (nam) params.append('nam', nam);
        if (params.toString()) url += '?' + params.toString();
        
        const response = await fetch(url);
        const result = await response.json();
        
        if (result.success) {
            doanhSoData = result.data;
            renderDoanhSoTable();
        } else {
            showAlert('Lỗi khi tải dữ liệu doanh số: ' + result.message, 'danger');
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Lỗi tải dữ liệu</td></tr>';
        }
    } catch (error) {
        console.error('Error loading doanh so data:', error);
        showAlert('Lỗi kết nối server', 'danger');
        const tbody = document.getElementById('doanhSoTableBody');
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Lỗi kết nối server</td></tr>';
    }
}

// Filter Doanh Số function
function filterDoanhSo() {
    loadDoanhSoData();
}

// Render Doanh Số Tháng table
function renderDoanhSoTable() {
    const tbody = document.getElementById('doanhSoTableBody');
    tbody.innerHTML = '';
    
    doanhSoData.forEach((item, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.ho_ten || 'N/A'}</td>
            <td><span class="badge bg-info">${item.thang}/${item.nam}</span></td>
            <td>${formatMoney(item.doanh_so_thang)}</td>
            <td>${item.phan_tram_hoa_hong}%</td>
            <td>${formatMoney(item.hoa_hong_nhan)}</td>
            <td>${item.ghi_chu || '-'}</td>
            <td><span class="badge bg-success">${item.cong_diem_danh_gia} điểm</span></td>
            <td>
                <button class="btn-edit" onclick="editDoanhSo(${item.id_doanh_so})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn-delete" onclick="deleteDoanhSo(${item.id_doanh_so})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Load Nhân Viên data
async function loadNhanVienData() {
    try {
        const response = await fetch('/doanqlns/simple_kpi_api.php/nhan-vien');
        const result = await response.json();
        
        if (result.success) {
            nhanVienData = result.data;
        }
    } catch (error) {
        console.error('Error loading nhan vien data:', error);
    }
}

// Load Cấu Hình data
async function loadCauHinhData() {
    try {
        // Show loading indicator
        const tbody = document.getElementById('cauHinhTableBody');
        if (tbody) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center"><i class="fas fa-spinner fa-spin"></i> Đang tải dữ liệu...</td></tr>';
        }
        
        console.log('Loading cau hinh data...');
        const response = await fetch('/doanqlns/simple_kpi_api.php');
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        console.log('API result:', result);
        
        if (result.success) {
            cauHinhData = result.data;
            console.log('Loaded cau hinh data:', cauHinhData);
            loadCauHinhTable(); // Gọi loadCauHinhTable sau khi load data
        } else {
            console.error('API returned success: false -', result.message);
            if (tbody) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Lỗi tải dữ liệu</td></tr>';
            }
        }
    } catch (error) {
        console.error('Error loading cau hinh data:', error);
        const tbody = document.getElementById('cauHinhTableBody');
        if (tbody) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Lỗi kết nối server</td></tr>';
        }
    }
}



// Utility functions
function formatMoney(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('vi-VN');
}

function getStatusColor(status) {
    const colors = {
        'Đang thực hiện': 'primary',
        'Hoàn thành': 'success',
        'Tạm dừng': 'warning',
        'Hủy bỏ': 'danger'
    };
    return colors[status] || 'secondary';
}

function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.insertBefore(alertDiv, document.body.firstChild);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Modal functions
function closeModal() {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.style.display = 'none';
        modal.classList.remove('show');
    });
    
    // Hide auto-calculation notice
    const noticeDiv = document.getElementById('auto-calculation-notice');
    if (noticeDiv) {
        noticeDiv.style.display = 'none';
        noticeDiv.innerHTML = '';
    }
}

// Utility function to format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

// Function to auto sync all hop dong to doanh so
async function autoSyncAllHopDong() {
    if (!confirm('Bạn có chắc chắn muốn sync tất cả hợp đồng sang doanh số? Hệ thống sẽ tự động tạo doanh số cho các nhân viên có hợp đồng trong tháng hiện tại.')) {
        return;
    }
    
    try {
        showAlert('Đang sync hợp đồng sang doanh số...', 'info');
        
        // Get current month and year
        const now = new Date();
        const currentMonth = now.getMonth() + 1;
        const currentYear = now.getFullYear();
        
        // Get all employees with hop dong in current month
        const response = await fetch(`/doanqlns/simple_kpi_api.php/hop-dong-kpi`);
        const hopDongData = await response.json();
        
        if (!hopDongData.success) {
            throw new Error('Không thể lấy dữ liệu hợp đồng');
        }
        
        // Group by employee and month
        const employeeMonths = new Set();
        hopDongData.data.forEach(hopDong => {
            const hopDongDate = new Date(hopDong.ngay_ky);
            const hopDongMonth = hopDongDate.getMonth() + 1;
            const hopDongYear = hopDongDate.getFullYear();
            
            if (hopDongMonth === currentMonth && hopDongYear === currentYear) {
                employeeMonths.add(`${hopDong.id_nhan_vien}-${hopDongMonth}-${hopDongYear}`);
            }
        });
        
        let successCount = 0;
        let errorCount = 0;
        
        // Sync each employee-month combination
        for (const key of employeeMonths) {
            const [idNhanVien, thang, nam] = key.split('-');
            
            try {
                const syncResponse = await fetch(`/doanqlns/simple_kpi_api.php/auto-sync-hop-dong-doanh-so?id_nhan_vien=${idNhanVien}&thang=${thang}&nam=${nam}`);
                const syncResult = await syncResponse.json();
                
                if (syncResult.success) {
                    successCount++;
                } else {
                    errorCount++;
                }
            } catch (error) {
                errorCount++;
                console.error('Error syncing for employee', idNhanVien, ':', error);
            }
        }
        
        // Show result
        if (successCount > 0) {
            showAlert(`Đã sync thành công ${successCount} nhân viên. ${errorCount > 0 ? `Có ${errorCount} lỗi.` : ''}`, 'success');
            loadDoanhSoData(); // Reload doanh so data
        } else {
            showAlert('Không có hợp đồng nào để sync hoặc đã tồn tại doanh số', 'warning');
        }
        
    } catch (error) {
        console.error('Error in autoSyncAllHopDong:', error);
        showAlert('Lỗi khi sync hợp đồng: ' + error.message, 'danger');
    }
}

// Function to calculate total contract value
async function calculateTongGiaTriHopDong() {
    const idNhanVien = document.getElementById('add_ds_id_nhan_vien').value;
    const thang = document.getElementById('add_ds_thang').value;
    const nam = document.getElementById('add_ds_nam').value;
    
    if (!idNhanVien || !thang || !nam) {
        return;
    }
    
    try {
        const response = await fetch(`/doanqlns/simple_kpi_api.php/tong-gia-tri-hop-dong?id_nhan_vien=${idNhanVien}&thang=${thang}&nam=${nam}`);
        const result = await response.json();
        
        if (result.success) {
            const tongGiaTri = result.data.tong_gia_tri;
            document.getElementById('add_ds_doanh_so_thang').value = tongGiaTri;
            
            // Show notification in modal
            const noticeDiv = document.getElementById('auto-calculation-notice');
            if (tongGiaTri > 0) {
                noticeDiv.innerHTML = `✅ Đã tự động điền doanh số: ${formatCurrency(tongGiaTri)} từ tổng giá trị hợp đồng`;
                noticeDiv.style.display = 'block';
                noticeDiv.style.background = '#e7f3ff';
                noticeDiv.style.borderColor = '#b3d9ff';
                noticeDiv.style.color = '#0066cc';
            } else {
                noticeDiv.innerHTML = '⚠️ Không có hợp đồng nào trong tháng này';
                noticeDiv.style.display = 'block';
                noticeDiv.style.background = '#fff3cd';
                noticeDiv.style.borderColor = '#ffeaa7';
                noticeDiv.style.color = '#856404';
            }
        } else {
            console.error('Error calculating total contract value:', result.message);
            const noticeDiv = document.getElementById('auto-calculation-notice');
            noticeDiv.innerHTML = '❌ Lỗi khi tính tổng giá trị hợp đồng';
            noticeDiv.style.display = 'block';
            noticeDiv.style.background = '#f8d7da';
            noticeDiv.style.borderColor = '#f5c6cb';
            noticeDiv.style.color = '#721c24';
        }
    } catch (error) {
        console.error('Error fetching total contract value:', error);
    }
}

function showAddHopDongModal() {
    // Populate nhân viên dropdown - chỉ hiển thị nhân viên thuộc phòng ban Kinh doanh
    const nhanVienSelect = document.getElementById('add_id_nhan_vien');
    nhanVienSelect.innerHTML = '<option value="">Chọn nhân viên</option>';
    nhanVienData.forEach(nv => {
        // Chỉ hiển thị nhân viên thuộc phòng ban Kinh doanh
        if (nv.ten_phong_ban && nv.ten_phong_ban.toLowerCase().includes('kinh doanh')) {
        const option = document.createElement('option');
        option.value = nv.id_nhan_vien;
        option.textContent = nv.ho_ten;
        nhanVienSelect.appendChild(option);
        }
    });
    
    // Reset form
    document.getElementById('addHopDongForm').reset();
    document.getElementById('add_hop_dong_id').value = '';
    
    // Update modal title
    document.querySelector('#addHopDongModal .modal-header h2').textContent = 'Thêm Hợp Đồng KPI';
    
    // Show modal
    document.getElementById('addHopDongModal').style.display = 'flex';
    document.getElementById('addHopDongModal').classList.add('show');
}

function showAddDoanhSoModal() {
    // Populate nhân viên dropdown
    const nhanVienSelect = document.getElementById('add_ds_id_nhan_vien');
    nhanVienSelect.innerHTML = '<option value="">Chọn nhân viên</option>';
    nhanVienData.forEach(nv => {
        const option = document.createElement('option');
        option.value = nv.id_nhan_vien;
        option.textContent = nv.ho_ten;
        nhanVienSelect.appendChild(option);
    });
    
    // Reset form
    document.getElementById('addDoanhSoForm').reset();
    
    // Add event listeners for auto-calculation
    nhanVienSelect.addEventListener('change', calculateTongGiaTriHopDong);
    document.getElementById('add_ds_thang').addEventListener('change', calculateTongGiaTriHopDong);
    document.getElementById('add_ds_nam').addEventListener('change', calculateTongGiaTriHopDong);
    
    // Show modal
    document.getElementById('addDoanhSoModal').style.display = 'flex';
    document.getElementById('addDoanhSoModal').classList.add('show');
}

function showCauHinhModal() {
    loadCauHinhData();
    // Show modal instead of switching tab
    const modal = document.getElementById('cauHinhModal');
    if (modal) {
        modal.style.display = 'flex';
        modal.classList.add('show');
    }
}

// Save functions
async function saveHopDong() {
    try {
        const hopDongId = document.getElementById('add_hop_dong_id').value;
        const formData = {
            ngay_ky: document.getElementById('add_ngay_ky').value,
            ma_hop_dong: document.getElementById('add_ma_hop_dong').value,
            gia_tri_hop_dong: parseFloat(document.getElementById('add_gia_tri_hop_dong').value),
            id_nhan_vien: document.getElementById('add_id_nhan_vien').value,
            file_hop_dong: document.getElementById('add_file_hop_dong').value,
            ghi_chu: document.getElementById('add_ghi_chu').value,
            trang_thai: document.getElementById('add_trang_thai').value
        };
        
        let url = '/doanqlns/simple_kpi_api.php/hop-dong-kpi';
        let method = 'POST';
        
        if (hopDongId) {
            // Update existing hop dong
            url = `/doanqlns/simple_kpi_api.php/hop-dong-kpi/${hopDongId}`;
            method = 'PUT';
        }
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert(hopDongId ? 'Cập nhật hợp đồng thành công!' : 'Thêm hợp đồng thành công!', 'success');
            closeModal();
            loadHopDongData();
            
            // Nếu là cập nhật và trạng thái là "Hoàn thành", reload dữ liệu doanh số
            if (hopDongId && formData.trang_thai === 'Hoàn thành') {
                loadDoanhSoData();
            }
        } else {
            showAlert('Lỗi: ' + result.message, 'danger');
        }
    } catch (error) {
        console.error('Error saving hop dong:', error);
        showAlert('Lỗi kết nối server', 'danger');
    }
}

async function saveDoanhSo() {
    try {
        const formData = {
            id_nhan_vien: document.getElementById('add_ds_id_nhan_vien').value,
            thang: parseInt(document.getElementById('add_ds_thang').value),
            nam: parseInt(document.getElementById('add_ds_nam').value),
            doanh_so_thang: parseFloat(document.getElementById('add_ds_doanh_so_thang').value),
            ghi_chu: document.getElementById('add_ds_ghi_chu').value
        };
        
        const response = await fetch('/doanqlns/index.php/api/doanh-so-thang', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Thêm doanh số thành công!', 'success');
            closeModal();
            loadDoanhSoData();
        } else {
            showAlert('Lỗi: ' + result.message, 'danger');
        }
    } catch (error) {
        console.error('Error saving doanh so:', error);
        showAlert('Lỗi kết nối server', 'danger');
    }
}

// Cấu hình functions
function loadCauHinhTable() {
    const tbody = document.getElementById('cauHinhTableBody');
    
    if (!tbody) {
        console.error('cauHinhTableBody element not found!');
        return;
    }
    
    tbody.innerHTML = '';
    
    if (!cauHinhData || cauHinhData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center">Chưa có dữ liệu cấu hình</td></tr>';
        return;
    }
    
    cauHinhData.forEach((item, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.ten_cau_hinh}</td>
            <td>${formatMoney(item.doanh_so_min)}</td>
            <td>${item.doanh_so_max ? formatMoney(item.doanh_so_max) : 'Không giới hạn'}</td>
            <td>${item.phan_tram_hoa_hong}%</td>
            <td>${item.ghi_chu}</td>
            <td><span class="badge bg-success">${item.cong_diem} điểm</span></td>
            <td><span class="badge bg-${item.trang_thai === 'Hoạt động' ? 'success' : 'warning'}">${item.trang_thai}</span></td>
            <td>
                <button class="btn-edit" onclick="editCauHinh(${item.id_cau_hinh})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn-delete" onclick="deleteCauHinh(${item.id_cau_hinh})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function showAddCauHinhForm() {
    document.getElementById('formTitle').textContent = 'Thêm Cấu Hình Mới';
    document.getElementById('addCauHinhForm').reset();
    document.getElementById('edit_cau_hinh_id').value = '';
    document.getElementById('cauHinhForm').style.display = 'block';
}

function editCauHinh(id) {
    const item = cauHinhData.find(c => c.id_cau_hinh == id);
    if (item) {
        // Show modal first
        const modal = document.getElementById('cauHinhModal');
        if (modal) {
            modal.style.display = 'flex';
            modal.classList.add('show');
        }
        
        // Populate form with existing data
        document.getElementById('formTitle').textContent = 'Sửa Cấu Hình';
        document.getElementById('edit_cau_hinh_id').value = item.id_cau_hinh;
        document.getElementById('edit_ten_cau_hinh').value = item.ten_cau_hinh;
        document.getElementById('edit_doanh_so_min').value = item.doanh_so_min;
        document.getElementById('edit_doanh_so_max').value = item.doanh_so_max || '';
        document.getElementById('edit_phan_tram_hoa_hong').value = item.phan_tram_hoa_hong;
        document.getElementById('edit_ghi_chu').value = item.ghi_chu;
        document.getElementById('edit_cong_diem').value = item.cong_diem;
        document.getElementById('edit_trang_thai').value = item.trang_thai;
        document.getElementById('cauHinhForm').style.display = 'block';
    }
}

function cancelCauHinhForm() {
    document.getElementById('cauHinhForm').style.display = 'none';
}

async function saveCauHinh() {
    try {
        const id = document.getElementById('edit_cau_hinh_id').value;
        const formData = {
            ten_cau_hinh: document.getElementById('edit_ten_cau_hinh').value,
            doanh_so_min: parseFloat(document.getElementById('edit_doanh_so_min').value),
            doanh_so_max: document.getElementById('edit_doanh_so_max').value ? parseFloat(document.getElementById('edit_doanh_so_max').value) : null,
            phan_tram_hoa_hong: parseFloat(document.getElementById('edit_phan_tram_hoa_hong').value),
            ghi_chu: document.getElementById('edit_ghi_chu').value,
            cong_diem: parseInt(document.getElementById('edit_cong_diem').value),
            trang_thai: document.getElementById('edit_trang_thai').value
        };
        
        const url = id ? 
            `/doanqlns/simple_kpi_api.php/cau-hinh-thang-diem/${id}` : 
            '/doanqlns/simple_kpi_api.php/cau-hinh-thang-diem';
        const method = id ? 'PUT' : 'POST';
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert(id ? 'Cập nhật cấu hình thành công!' : 'Thêm cấu hình thành công!', 'success');
            loadCauHinhData(); // loadCauHinhData() đã gọi loadCauHinhTable() bên trong
            cancelCauHinhForm();
        } else {
            showAlert('Lỗi: ' + result.message, 'danger');
        }
    } catch (error) {
        console.error('Error saving cau hinh:', error);
        showAlert('Lỗi kết nối server', 'danger');
    }
}

async function deleteCauHinh(id) {
    if (confirm('Bạn có chắc chắn muốn xóa cấu hình này?')) {
        try {
            const response = await fetch(`/doanqlns/simple_kpi_api.php/cau-hinh-thang-diem/${id}`, {
                method: 'DELETE'
            });
            
            const result = await response.json();
            
            if (result.success) {
                showAlert('Xóa cấu hình thành công!', 'success');
                loadCauHinhData(); // loadCauHinhData() đã gọi loadCauHinhTable() bên trong
            } else {
                showAlert('Lỗi: ' + result.message, 'danger');
            }
        } catch (error) {
            console.error('Error deleting cau hinh:', error);
            showAlert('Lỗi kết nối server', 'danger');
        }
    }
}

// Edit/Delete functions
function editHopDong(id) {
    const item = hopDongData.find(h => h.id_hop_dong == id);
    if (item) {
        // Populate nhân viên dropdown first - chỉ hiển thị nhân viên thuộc phòng ban Kinh doanh
        const nhanVienSelect = document.getElementById('add_id_nhan_vien');
        nhanVienSelect.innerHTML = '<option value="">Chọn nhân viên</option>';
        nhanVienData.forEach(nv => {
            // Chỉ hiển thị nhân viên thuộc phòng ban Kinh doanh
            if (nv.ten_phong_ban && nv.ten_phong_ban.toLowerCase().includes('kinh doanh')) {
            const option = document.createElement('option');
            option.value = nv.id_nhan_vien;
            option.textContent = nv.ho_ten;
            nhanVienSelect.appendChild(option);
            }
        });
        
        // Populate form with existing data
        document.getElementById('add_hop_dong_id').value = item.id_hop_dong;
        document.getElementById('add_ngay_ky').value = item.ngay_ky;
        document.getElementById('add_ma_hop_dong').value = item.ma_hop_dong;
        document.getElementById('add_gia_tri_hop_dong').value = item.gia_tri_hop_dong;
        document.getElementById('add_id_nhan_vien').value = item.id_nhan_vien;
        document.getElementById('add_file_hop_dong').value = item.file_hop_dong || '';
        document.getElementById('add_ghi_chu').value = item.ghi_chu || '';
        document.getElementById('add_trang_thai').value = item.trang_thai;
        
        // Update modal title
        document.querySelector('#addHopDongModal .modal-header h2').textContent = 'Sửa Hợp Đồng KPI';
        
        // Show modal
        document.getElementById('addHopDongModal').style.display = 'flex';
        document.getElementById('addHopDongModal').classList.add('show');
    }
}

async function deleteHopDong(id) {
    if (confirm('Bạn có chắc chắn muốn xóa hợp đồng này?')) {
        try {
            const response = await fetch(`/doanqlns/simple_kpi_api.php/hop-dong-kpi/${id}`, {
                method: 'DELETE'
            });
            
            const result = await response.json();
            
            if (result.success) {
                showAlert('Xóa hợp đồng thành công!', 'success');
                loadHopDongData();
            } else {
                showAlert('Lỗi: ' + result.message, 'danger');
            }
        } catch (error) {
            console.error('Error deleting hop dong:', error);
            showAlert('Lỗi kết nối server', 'danger');
        }
    }
}

function editDoanhSo(id) {
    const item = doanhSoData.find(d => d.id_doanh_so == id);
    if (item) {
        // Populate form with existing data
        document.getElementById('add_ds_id_nhan_vien').value = item.id_nhan_vien;
        document.getElementById('add_ds_thang').value = item.thang;
        document.getElementById('add_ds_nam').value = item.nam;
        document.getElementById('add_ds_doanh_so_thang').value = item.doanh_so_thang;
        document.getElementById('add_ds_ghi_chu').value = item.ghi_chu || '';
        
        // Show modal
        document.getElementById('addDoanhSoModal').style.display = 'flex';
        document.getElementById('addDoanhSoModal').classList.add('show');
    }
}

async function deleteDoanhSo(id) {
    if (confirm('Bạn có chắc chắn muốn xóa doanh số này?')) {
        try {
            const response = await fetch(`/doanqlns/index.php/api/doanh-so-thang/${id}`, {
                method: 'DELETE'
            });
            
            const result = await response.json();
            
            if (result.success) {
                showAlert('Xóa doanh số thành công!', 'success');
                loadDoanhSoData();
            } else {
                showAlert('Lỗi: ' + result.message, 'danger');
            }
        } catch (error) {
            console.error('Error deleting doanh so:', error);
            showAlert('Lỗi kết nối server', 'danger');
        }
    }
}
</script>

<?php include_once '../includes/footer.php'; ?>
