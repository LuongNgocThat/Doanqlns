<?php
require_once __DIR__ . '/../includes/check_login.php';
require_once __DIR__ . '/../includes/base_url.php';
include(__DIR__ . '/../includes/header.php');
include(__DIR__ . '/../includes/sidebar.php');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRM Pro - Nghỉ Phép Của Tôi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
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

        .leave-container {
            max-width: 1400px;
            margin: 20px 20px 20px 300px;
            padding: 20px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .leave-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            margin: -20px -20px 30px -20px;
            border-radius: 20px 20px 0 0;
        }

        .leave-header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .leave-header i {
            font-size: 2.2rem;
        }

        .stats-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #f0f0f0;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-card h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .stat-card h3 i {
            color: #667eea;
            font-size: 1rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #27ae60;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #7f8c8d;
            font-weight: 500;
        }

        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .filter-section h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-controls {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
            justify-content: center;
        }

        .filter-dropdowns {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .filter-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .filter-controls select,
        .filter-controls button {
            padding: 12px 20px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .filter-controls select:focus,
        .filter-controls button:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .filter-controls button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.3s ease;
        }

        .filter-controls button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 15px 15px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s;
        }

        .modal-close:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .modal-body {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
        }

        .btn-cancel,
        .btn-save {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-cancel {
            background-color: #6c757d;
            color: white;
        }

        .btn-cancel:hover {
            background-color: #5a6268;
        }

        .btn-save {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }

        .leave-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-top: 25px;
            overflow-x: auto;
        }

        .leave-table table {
            width: 100%;
            min-width: 800px;
            border-collapse: collapse;
        }

        .leave-table th {
            background: linear-gradient(135deg,rgb(230, 233, 233) 0%,rgb(235, 237, 237) 100%);

            color: #4a5568;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }

        .leave-table td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            font-style: italic;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #667eea;
        }

        .loading i {
            font-size: 2rem;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .leave-container {
                margin: 10px;
                padding: 15px;
                border-radius: 15px;
            }

            .leave-header {
                margin: -15px -15px 20px -15px;
                padding: 15px;
                border-radius: 15px 15px 0 0;
            }

            .leave-header h1 {
                font-size: 1.8rem;
                flex-direction: column;
                gap: 10px;
            }

            .stats-section {
                grid-template-columns: repeat(4, 1fr);
                gap: 8px;
            }

            .stat-card {
                padding: 15px 10px;
                border-radius: 10px;
            }

            .stat-card h3 {
                font-size: 0.8rem;
                margin-bottom: 8px;
                gap: 5px;
            }

            .stat-card h3 i {
                font-size: 0.8rem;
            }

            .stat-number {
                font-size: 1.5rem;
                margin-bottom: 5px;
            }

            .stat-label {
                font-size: 0.7rem;
                color: #666;
            }

            .filter-section {
                padding: 15px;
                margin-bottom: 20px;
            }

            .filter-section h3 {
                font-size: 1rem;
                margin-bottom: 10px;
            }

            .filter-controls {
                flex-direction: column;
                gap: 15px;
            }

            .filter-dropdowns {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 8px;
            }

            .filter-dropdowns select {
                width: 100%;
                padding: 6px 8px;
                font-size: 0.8rem;
                margin-bottom: 0;
            }

            .filter-buttons {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .filter-buttons button {
                width: 100%;
                padding: 6px 8px;
                font-size: 0.8rem;
                margin-bottom: 0;
            }

            /* Modal responsive */
            .modal-content {
                margin: 10% auto;
                width: 95%;
                max-width: none;
            }

            .modal-body {
                padding: 20px;
            }

            .form-actions {
                flex-direction: column;
                gap: 10px;
            }

            .btn-cancel,
            .btn-save {
                width: 100%;
            }

            .leave-table {
                overflow-x: auto;
            }

            .leave-table table {
                min-width: 600px;
            }
        }

        /* Small Mobile Responsive */
        @media (max-width: 480px) {
            .stats-section {
                grid-template-columns: repeat(4, 1fr);
                gap: 5px;
            }

            .stat-card {
                padding: 10px 5px;
                border-radius: 8px;
            }

            .stat-card h3 {
                font-size: 0.7rem;
                margin-bottom: 5px;
                gap: 3px;
            }

            .stat-card h3 i {
                font-size: 0.7rem;
            }

            .stat-number {
                font-size: 1.2rem;
                margin-bottom: 3px;
            }

            .stat-label {
                font-size: 0.6rem;
                line-height: 1.2;
            }
        }

        /* Laptop/Desktop Responsive */
        @media (min-width: 1025px) {
            .leave-container {
                max-width: 1200px;
                margin: 40px auto;
                padding: 30px;
                padding-left: 100px;
            }

            .leave-table {
                margin: 30px auto 0;
                max-width: 100%;
                overflow-x: auto;
            }

            .stats-section {
                max-width: 1200px;
                margin: 0 auto 30px;
            }

            .filter-section {
                max-width: 1200px;
                margin: 0 auto;
            }
        }

        /* Tablet Responsive */
        @media (min-width: 769px) and (max-width: 1024px) {
            .stats-section {
                grid-template-columns: repeat(2, 1fr);
            }

            .leave-container {
                max-width: 1200px;
                margin: 30px auto;
            }

            .filter-section {
                max-width: 1000px;
                margin: 0 auto;
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

        .stat-card {
            animation: fadeInUp 0.6s ease-out;
        }

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }
    </style>
</head>

<body>
    <div class="leave-container">
        <div class="leave-header">
            <h1><i class="fas fa-calendar-alt"></i> Nghỉ Phép Của Tôi</h1>
        </div>

        <div class="stats-section">
            <div class="stat-card">
                <h3><i class="fas fa-calendar-check"></i> Đã Duyệt</h3>
                <div class="stat-number" id="approvedCount">0</div>
                <div class="stat-label">Ngày nghỉ phép</div>
            </div>

            <div class="stat-card">
                <h3><i class="fas fa-clock"></i> Chờ Duyệt</h3>
                <div class="stat-number" id="pendingCount">0</div>
                <div class="stat-label">Đơn chờ xử lý</div>
            </div>

            <div class="stat-card">
                <h3><i class="fas fa-times-circle"></i> Từ Chối</h3>
                <div class="stat-number" id="rejectedCount">0</div>
                <div class="stat-label">Đơn bị từ chối</div>
            </div>

            <div class="stat-card">
                <h3><i class="fas fa-calendar-plus"></i> Còn Lại</h3>
                <div class="stat-number" id="remainingCount">12</div>
                <div class="stat-label">Ngày phép còn lại</div>
            </div>
        </div>

        <div class="filter-section">
            <h3><i class="fas fa-filter"></i> Lọc đơn nghỉ phép</h3>
            <div class="filter-controls">
                <div class="filter-dropdowns">
                    <select id="statusFilter">
                        <option value="">Tất cả trạng thái</option>
                        <option value="approved">Đã duyệt</option>
                        <option value="pending">Chờ duyệt</option>
                        <option value="rejected">Từ chối</option>
                    </select>
                    
                    <select id="monthFilter">
                        <option value="">Chọn tháng</option>
                        <option value="01">Tháng 1</option>
                        <option value="02">Tháng 2</option>
                        <option value="03">Tháng 3</option>
                        <option value="04">Tháng 4</option>
                        <option value="05">Tháng 5</option>
                        <option value="06">Tháng 6</option>
                        <option value="07">Tháng 7</option>
                        <option value="08">Tháng 8</option>
                        <option value="09">Tháng 9</option>
                        <option value="10">Tháng 10</option>
                        <option value="11">Tháng 11</option>
                        <option value="12">Tháng 12</option>
                    </select>
                    
                    <select id="yearFilter">
                        <option value="">Chọn năm</option>
                        <option value="2025">2025</option>
                        <option value="2024">2024</option>
                        <option value="2023">2023</option>
                    </select>
                </div>
                
                <div class="filter-buttons">
                    <button onclick="loadLeaveData()">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                    
                    <button onclick="showAddNghiPhepModal()" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                        <i class="fas fa-plus"></i> Thêm Nghỉ Phép
                    </button>
                </div>
            </div>
        </div>

        <div class="leave-table">
            <table>
                <thead>
                    <tr>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th>Số ngày</th>
                        <th>Lý do</th>
                        <th>Trạng thái</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody id="leaveTableBody">
                    <tr>
                        <td colspan="6" class="loading">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p>Đang tải dữ liệu...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const API_BASE_CANDIDATES = Array.from(new Set([
            '<?= rtrim($base_url, '/') ?>/index.php/api',
            `${window.location.origin}/doanqlns/index.php/api`,
            `${window.location.origin}/index.php/api`
        ])).filter(Boolean);

        let allLeaveRequests = [];

        document.addEventListener('DOMContentLoaded', async function() {
            const now = new Date();
            document.getElementById('monthFilter').value = String(now.getMonth() + 1).padStart(2, '0');
            document.getElementById('yearFilter').value = now.getFullYear();

            await fetchLeaveRequests();
            renderLeaveData();
        });

        function buildFetchOptions(options = {}) {
            const finalOptions = {
                method: options.method || 'GET',
                credentials: 'include'
            };

            const isFormData = options.body instanceof FormData;
            const defaultHeaders = {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            };

            if (!isFormData) {
                defaultHeaders['Content-Type'] = 'application/json';
            }

            finalOptions.headers = {
                ...defaultHeaders,
                ...(options.headers || {})
            };

            if (options.body) {
                finalOptions.body = options.body;
            }

            return finalOptions;
        }

        async function callApi(endpoint, options = {}) {
            const normalizedEndpoint = endpoint.startsWith('/') ? endpoint : `/${endpoint}`;
            let lastError = null;

            for (const base of API_BASE_CANDIDATES) {
                const normalizedBase = base.replace(/\/+$/, '');
                const url = `${normalizedBase}${normalizedEndpoint}`;
                try {
                    const response = await fetch(url, buildFetchOptions(options));
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }
                    const text = await response.text();
                    const cleaned = text.trim();
                    return cleaned ? JSON.parse(cleaned) : {};
                } catch (error) {
                    console.warn('[nghiphep] API call failed:', url, error);
                    lastError = error;
                }
            }

            throw lastError || new Error('Không thể kết nối API');
        }

        async function fetchLeaveRequests() {
            try {
                const payload = await callApi('/employee/leave');
                const list = Array.isArray(payload)
                    ? payload
                    : Array.isArray(payload.data)
                        ? payload.data
                        : [];
                allLeaveRequests = list || [];
            } catch (error) {
                console.error('Error fetching leave requests:', error);
                allLeaveRequests = [];
            }
        }

        function loadLeaveData() {
            renderLeaveData();
        }

        function renderLeaveData() {
            const status = document.getElementById('statusFilter').value;
            const month = document.getElementById('monthFilter').value;
            const year = document.getElementById('yearFilter').value;

            document.getElementById('leaveTableBody').innerHTML = `
                <tr>
                    <td colspan="6" class="loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Đang tải dữ liệu...</p>
                    </td>
                </tr>
            `;

            const filtered = filterLeaveData(allLeaveRequests, { status, month, year });
            displayLeaveData(filtered);
            updateStats(filtered);
        }

        function filterLeaveData(data, filters) {
            return data.filter(item => {
                const startDate = item.ngay_bat_dau || item.startDate;
                const statusRaw = item.trang_thai || item.status || '';

                if (filters.status) {
                    const normalizedStatus = mapStatusKey(statusRaw);
                    if (normalizedStatus !== filters.status) {
                        return false;
                    }
                }

                if (filters.month) {
                    const monthValue = startDate ? String(new Date(startDate).getMonth() + 1).padStart(2, '0') : '';
                    if (monthValue !== filters.month) {
                        return false;
                    }
                }

                if (filters.year) {
                    const yearValue = startDate ? String(new Date(startDate).getFullYear()) : '';
                    if (yearValue !== filters.year) {
                        return false;
                    }
                }

                return true;
            });
        }

        function displayLeaveData(data) {
            const tbody = document.getElementById('leaveTableBody');
            
            if (data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="no-data">Không có dữ liệu</td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = data.map(leave => {
                const start = leave.ngay_bat_dau || leave.startDate;
                const end = leave.ngay_ket_thuc || leave.endDate;
                const days = calculateLeaveDays(leave);
                const reason = leave.ly_do || leave.reason || '—';
                const note = leave.ghi_chu || leave.note || '';
                const statusKey = mapStatusKey(leave.trang_thai || leave.status);
                const statusText = getStatusText(statusKey);

                return `
                <tr>
                    <td>${formatDate(start)}</td>
                    <td>${formatDate(end)}</td>
                    <td>${days} ngày</td>
                    <td>${reason}</td>
                    <td><span class="status-badge status-${statusKey}">${statusText}</span></td>
                    <td>${note}</td>
                </tr>`;
            }).join('');
        }

        function updateStats(data) {
            const approvedDays = data
                .filter(item => mapStatusKey(item.trang_thai || item.status) === 'approved')
                .reduce((sum, item) => sum + calculateLeaveDays(item), 0);
            const approvedCount = data.filter(item => mapStatusKey(item.trang_thai || item.status) === 'approved').length;
            const pendingCount = data.filter(item => mapStatusKey(item.trang_thai || item.status) === 'pending').length;
            const rejectedCount = data.filter(item => mapStatusKey(item.trang_thai || item.status) === 'rejected').length;
            const remaining = Math.max(0, 12 - approvedDays);

            document.getElementById('approvedCount').textContent = approvedCount;
            document.getElementById('pendingCount').textContent = pendingCount;
            document.getElementById('rejectedCount').textContent = rejectedCount;
            document.getElementById('remainingCount').textContent = remaining;
        }

        function formatDate(dateString) {
            if (!dateString) return '—';
            const date = new Date(dateString);
            if (isNaN(date)) return '—';
            return date.toLocaleDateString('vi-VN');
        }

        function mapStatusKey(status = '') {
            const normalized = status.toString().trim().toLowerCase();
            if (normalized.includes('đã duyệt') || normalized.includes('approved')) return 'approved';
            if (normalized.includes('từ chối') || normalized.includes('reject')) return 'rejected';
            if (normalized.includes('chờ') || normalized.includes('pending')) return 'pending';
            return 'pending';
        }

        function getStatusText(statusKey) {
            const statusMap = {
                'approved': 'Đã duyệt',
                'pending': 'Chờ duyệt',
                'rejected': 'Từ chối'
            };
            return statusMap[statusKey] || 'Chờ duyệt';
        }

        function calculateLeaveDays(leave) {
            if (leave.so_ngay) return Number(leave.so_ngay);
            const start = leave.ngay_bat_dau || leave.startDate;
            const end = leave.ngay_ket_thuc || leave.endDate || start;
            if (!start) return 0;
            const startDate = new Date(start);
            const endDate = new Date(end);
            if (isNaN(startDate) || isNaN(endDate)) return 0;
            const diff = Math.abs(endDate - startDate);
            return Math.max(1, Math.round(diff / (1000 * 60 * 60 * 24)) + 1);
        }

        // Modal Thêm Nghỉ Phép
        function showAddNghiPhepModal() {
            // Tạo modal HTML
            const modalHTML = `
                <div id="addNghiPhepModal" class="modal" style="display: flex;">
                    <div class="modal-content" style="max-width: 600px; width: 90%;">
                        <div class="modal-header">
                            <h2><i class="fas fa-plus"></i> Thêm Đơn Nghỉ Phép</h2>
                            <button class="modal-close" onclick="closeAddNghiPhepModal()">×</button>
                        </div>
                        <div class="modal-body">
                            <form id="addNghiPhepForm">
                                <div class="form-group">
                                    <label for="addLoaiNghi">Loại nghỉ:</label>
                                    <select id="addLoaiNghi" name="loai_nghi" required onchange="toggleMaternityFields()">
                                        <option value="">Chọn loại nghỉ</option>
                                        <option value="Có phép">Có phép</option>
                                        <option value="Phép Năm">Phép Năm</option>
                                        <option value="Không phép">Không phép</option>
                                        <option value="Nghỉ thai sản">Nghỉ thai sản</option>
                                        <option value="Nghỉ tai nạn">Nghỉ tai nạn</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="addNgayBatDau">Ngày bắt đầu:</label>
                                    <input type="date" id="addNgayBatDau" name="ngay_bat_dau" required>
                                </div>

                                <div class="form-group">
                                    <label for="addNgayKetThuc">Ngày kết thúc:</label>
                                    <input type="date" id="addNgayKetThuc" name="ngay_ket_thuc" required>
                                </div>

                                <div class="form-group">
                                    <label for="addLyDo">Lý do:</label>
                                    <textarea id="addLyDo" name="ly_do" rows="3" required></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="addMinhChung">Minh chứng (link):</label>
                                    <input type="url" id="addMinhChung" name="minh_chung" placeholder="https://...">
                                </div>

                                <!-- Maternity fields -->
                                <div id="maternity_fields" style="display: none;">
                                    <div class="form-group">
                                        <label for="add_ngay_bat_dau_thai_san">Ngày bắt đầu nghỉ thai sản:</label>
                                        <input type="date" id="add_ngay_bat_dau_thai_san" name="ngay_bat_dau_thai_san">
                                    </div>
                                    <div class="form-group">
                                        <label for="add_ngay_ket_thuc_thai_san">Ngày kết thúc nghỉ thai sản (tự động tính):</label>
                                        <input type="date" id="add_ngay_ket_thuc_thai_san" name="ngay_ket_thuc_thai_san" readonly>
                                        <small style="color: #666;">Tự động tính: ngày bắt đầu + 6 tháng</small>
                                    </div>
                                </div>

                                <!-- Accident fields -->
                                <div id="accident_fields" style="display: none;">
                                    <div class="form-group">
                                        <label for="add_loai_tai_nan">Loại tai nạn:</label>
                                        <select id="add_loai_tai_nan" name="loai_tai_nan">
                                            <option value="Tai nạn giao thông">Tai nạn giao thông</option>
                                            <option value="Tai nạn lao động">Tai nạn lao động</option>
                                            <option value="Tai nạn khác">Tai nạn khác</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="add_muc_do_tai_nan">Mức độ tai nạn:</label>
                                        <select id="add_muc_do_tai_nan" name="muc_do_tai_nan">
                                            <option value="Nhẹ">Nhẹ</option>
                                            <option value="Trung bình">Trung bình</option>
                                            <option value="Nặng">Nặng</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="button" onclick="closeAddNghiPhepModal()" class="btn-cancel">Hủy</button>
                                    <button type="submit" class="btn-save">Lưu</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            `;
            
            // Thêm modal vào body
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            
            // Reset form
            document.getElementById('addLoaiNghi').value = '';
            document.getElementById('addNgayBatDau').value = '';
            document.getElementById('addNgayKetThuc').value = '';
            document.getElementById('addLyDo').value = '';
            document.getElementById('addMinhChung').value = '';
            document.getElementById('add_ngay_bat_dau_thai_san').value = '';
            document.getElementById('add_ngay_ket_thuc_thai_san').value = '';
            document.getElementById('add_loai_tai_nan').value = 'Tai nạn giao thông';
            document.getElementById('add_muc_do_tai_nan').value = 'Nhẹ';
            toggleMaternityFields();
        }

        function closeAddNghiPhepModal() {
            const modal = document.getElementById('addNghiPhepModal');
            if (modal) {
                modal.remove();
            }
        }

        function toggleMaternityFields() {
            const loaiNghi = document.getElementById('addLoaiNghi').value;
            const maternityFields = document.getElementById('maternity_fields');
            const accidentFields = document.getElementById('accident_fields');
            
            if (loaiNghi === 'Nghỉ thai sản') {
                maternityFields.style.display = 'block';
                accidentFields.style.display = 'none';
            } else if (loaiNghi === 'Nghỉ tai nạn') {
                maternityFields.style.display = 'none';
                accidentFields.style.display = 'block';
            } else {
                maternityFields.style.display = 'none';
                accidentFields.style.display = 'none';
            }
        }

        function calculateMaternityEndDate() {
            const startDate = document.getElementById('add_ngay_bat_dau_thai_san').value;
            if (startDate) {
                const date = new Date(startDate);
                date.setMonth(date.getMonth() + 6);
                document.getElementById('add_ngay_ket_thuc_thai_san').value = date.toISOString().split('T')[0];
            }
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Form submit handler
            document.addEventListener('submit', function(e) {
                if (e.target.id === 'addNghiPhepForm') {
                    e.preventDefault();
                    saveNewNghiPhep();
                }
            });

            // Maternity date change handler
            document.addEventListener('change', function(e) {
                if (e.target.id === 'add_ngay_bat_dau_thai_san') {
                    calculateMaternityEndDate();
                }
            });
        });

        async function saveNewNghiPhep() {
            const form = document.getElementById('addNghiPhepForm');
            const formData = new FormData(form);
            
            // Validate required fields
            const loaiNghi = formData.get('loai_nghi');
            const ngayBatDau = formData.get('ngay_bat_dau');
            const ngayKetThuc = formData.get('ngay_ket_thuc');
            const lyDo = formData.get('ly_do');
            
            if (!loaiNghi || !ngayBatDau || !ngayKetThuc || !lyDo) {
                alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
                return;
            }

            // Special validation for maternity leave
            if (loaiNghi === 'Nghỉ thai sản') {
                const ngayBatDauThaiSan = formData.get('ngay_bat_dau_thai_san');
                if (!ngayBatDauThaiSan) {
                    alert('Vui lòng chọn ngày bắt đầu nghỉ thai sản!');
                    return;
                }
            }

            try {
                const payload = {
                    loai_nghi: loaiNghi,
                    ngay_bat_dau: ngayBatDau,
                    ngay_ket_thuc: ngayKetThuc,
                    ly_do: lyDo,
                    minh_chung: formData.get('minh_chung') || null,
                    ngay_bat_dau_thai_san: formData.get('ngay_bat_dau_thai_san') || null,
                    ngay_ket_thuc_thai_san: formData.get('ngay_ket_thuc_thai_san') || null,
                    loai_tai_nan: formData.get('loai_tai_nan') || null,
                    muc_do_tai_nan: formData.get('muc_do_tai_nan') || null
                };

                const result = await callApi('/nghiphep', {
                    method: 'POST',
                    body: JSON.stringify(payload)
                });

                if (result.success) {
                    alert('Thêm đơn nghỉ phép thành công!');
                    closeAddNghiPhepModal();
                    await fetchLeaveRequests();
                    renderLeaveData();
                } else {
                    alert('Lỗi: ' + (result.message || 'Không thể thêm đơn nghỉ phép'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Lỗi khi thêm đơn nghỉ phép: ' + error.message);
            }
        }
    </script>
</body>
</html>
