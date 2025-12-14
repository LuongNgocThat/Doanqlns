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
    <title>HRM Pro - Lương Của Tôi</title>
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

        .salary-container {
            max-width: 1400px;
            /* Add left space to avoid overlapping with sidebar on desktop */
            margin: 20px 20px 20px 300px;
            padding: 20px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .salary-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            margin: -20px -20px 30px -20px;
            border-radius: 20px 20px 0 0;
        }

        .salary-header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .salary-header i {
            font-size: 2.2rem;
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

        .date-filter {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
            justify-content: center;
        }

        .date-filter select,
        .date-filter button {
            padding: 12px 20px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .date-filter select:focus,
        .date-filter button:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .date-filter button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.3s ease;
        }

        .date-filter button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .salary-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .salary-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            border: 1px solid #f0f0f0;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .salary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .salary-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .salary-card h3 {
            color: #2c3e50;
            margin-bottom: 12px;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .salary-card h3 i {
            color: #667eea;
            font-size: 1rem;
        }

        .salary-amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: #27ae60;
            margin-bottom: 8px;
        }

        .salary-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 15px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .detail-label {
            font-size: 0.8rem;
            color: #6c757d;
            font-weight: 500;
        }

        .detail-value {
            font-size: 1rem;
            color: #2c3e50;
            font-weight: 600;
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
            .salary-container {
                margin: 10px;
                padding: 15px;
                border-radius: 15px;
                
            }

            .salary-header {
                margin: -15px -15px 20px -15px;
                padding: 15px;
                border-radius: 15px 15px 0 0;
            }

            .salary-header h1 {
                font-size: 1.8rem;
                flex-direction: column;
                gap: 10px;
            }

            .date-filter {
                flex-direction: column;
                align-items: stretch;
            }

            .date-filter select,
            .date-filter button {
                width: 100%;
                margin-bottom: 10px;
            }

            .salary-content {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .salary-card {
                padding: 15px;
            }

            .salary-card h3 {
                font-size: 1rem;
                margin-bottom: 8px;
            }

            .salary-amount {
                font-size: 1.2rem;
                margin-bottom: 6px;
            }

            .salary-details {
                grid-template-columns: 1fr;
                gap: 8px;
                margin-top: 10px;
            }

            .detail-label {
                font-size: 0.75rem;
            }

            .detail-value {
                font-size: 0.9rem;
            }
        }

        /* Extra small mobile */
        @media (max-width: 480px) {
            .salary-content {
                grid-template-columns: 1fr;
                gap: 12px;
            }
        }

        /* Tablet Responsive */
        @media (min-width: 769px) and (max-width: 1024px) {
            .salary-content {
                grid-template-columns: repeat(2, 1fr);
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

        .salary-card {
            animation: fadeInUp 0.6s ease-out;
        }

        .salary-card:nth-child(1) { animation-delay: 0.1s; }
        .salary-card:nth-child(2) { animation-delay: 0.2s; }
        .salary-card:nth-child(3) { animation-delay: 0.3s; }
        .salary-card:nth-child(4) { animation-delay: 0.4s; }

        /* Employee Info Section */
        .employee-info-section {
            margin-bottom: 30px;
        }

        .employee-info-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .employee-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            flex-shrink: 0;
        }

        .employee-details h3 {
            margin: 0 0 10px 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .employee-details p {
            margin: 5px 0;
            opacity: 0.9;
            font-size: 1rem;
        }

        @media (max-width: 768px) {
            .employee-info-card {
                flex-direction: column;
                text-align: center;
                padding: 20px;
            }

            .employee-avatar {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }

            .employee-details h3 {
                font-size: 1.3rem;
            }

            .employee-details p {
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <div class="salary-container">
        <!-- Hidden input for current user ID -->
        <input type="hidden" id="currentUserId" value="<?= $_SESSION['user_id'] ?? '' ?>">
        
       

        <div class="filter-section">
            <h3><i class="fas fa-filter"></i> Lọc theo tháng</h3>
            <div class="date-filter">
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
                
                <button onclick="loadSalaryData()">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
            </div>
        </div>

        <!-- Employee Info Section -->
        <div class="employee-info-section" id="employeeInfoSection" style="display: none;">
            <div class="employee-info-card">
                <div class="employee-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="employee-details">
                    <h3 id="employeeName">Đang tải...</h3>
                    <p id="employeeId">Mã nhân viên: Đang tải...</p>
                    <p id="employeeDept">Phòng ban: Đang tải...</p>
                    <p id="employeePosition">Chức vụ: Đang tải...</p>
                </div>
            </div>
        </div>

        <div class="salary-content" id="salaryContent">
            <div class="loading">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Đang tải dữ liệu...</p>
            </div>
        </div>

    </div>

    <script>
        // Load salary data on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Set current month and year as default
            const now = new Date();
            document.getElementById('monthFilter').value = String(now.getMonth() + 1).padStart(2, '0');
            document.getElementById('yearFilter').value = now.getFullYear();
            
            // Load employee data first
            loadEmployeeData();
            loadSalaryData();
        });

        // Load employee data from users API
        function loadEmployeeData() {
            fetch('/doanqlns/index.php/api/users')
                .then(response => response.json())
                .then(data => {
                    // Get current user ID from session
                    const currentUserId = getCurrentUserId();
                    if (currentUserId) {
                        // Find employee by user ID (assuming there's a user_id field in the API response)
                        const employee = data.find(emp => emp.user_id == currentUserId || emp.id == currentUserId);
                        if (employee) {
                            displayEmployeeInfo(employee);
                        } else {
                            console.log('Employee not found for user ID:', currentUserId);
                            // Hide employee info section if not found
                            document.getElementById('employeeInfoSection').style.display = 'none';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading employee data:', error);
                    // Hide employee info section if error
                    document.getElementById('employeeInfoSection').style.display = 'none';
                });
        }

        // Display employee information
        function displayEmployeeInfo(employee) {
            document.getElementById('employeeName').textContent = employee.ho_ten || 'N/A';
            document.getElementById('employeeId').textContent = `Mã nhân viên: ${employee.id_nhan_vien || 'N/A'}`;
            document.getElementById('employeeDept').textContent = `Phòng ban: ${employee.ten_phong_ban || 'N/A'}`;
            document.getElementById('employeePosition').textContent = `Chức vụ: ${employee.chuc_vu || 'N/A'}`;
            
            // Show employee info section
            document.getElementById('employeeInfoSection').style.display = 'block';
        }

        // Get current user ID (you may need to implement this based on your session management)
        function getCurrentUserId() {
            // This should return the current user's employee ID
            // You may need to pass this from PHP or get it from a hidden input
            return document.getElementById('currentUserId')?.value || null;
        }

        function loadSalaryData() {
            const month = document.getElementById('monthFilter').value;
            const year = document.getElementById('yearFilter').value;
            
            if (!month || !year) {
                alert('Vui lòng chọn tháng và năm');
                return;
            }

            // Show loading
            document.getElementById('salaryContent').innerHTML = `
                <div class="loading">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Đang tải dữ liệu...</p>
                </div>
            `;

            // Call new API
            fetch(`<?= BASE_URL ?>/api/get_my_salary.php?month=${month}&year=${year}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        displaySalaryData(data.data);
                    } else {
                        console.error('Error loading salary data:', data.message);
                        document.getElementById('salaryContent').innerHTML = `
                            <div class="no-data">
                                <i class="fas fa-exclamation-triangle"></i>
                                <p>Không thể tải dữ liệu lương</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('salaryContent').innerHTML = `
                        <div class="no-data">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p>Lỗi kết nối. Vui lòng thử lại sau.</p>
                        </div>
                    `;
                });
        }

        function displaySalaryData(data) {
            // Parse money values safely
            function parseMoney(value) {
                if (value === null || value === undefined) return 0;
                if (typeof value === 'number') return isNaN(value) ? 0 : value;
                try {
                    const cleaned = String(value).replace(/\./g, '').replace(/,/g, '');
                    const n = Number(cleaned);
                    return isNaN(n) ? 0 : n;
                } catch (e) {
                    return 0;
                }
            }

            // Calculate total deductions from luong table
            const totalDeductions = parseMoney(data.bhxh_nv) + 
                                  parseMoney(data.bhyt_nv) + 
                                  parseMoney(data.bhtn_nv) + 
                                  parseMoney(data.thue_tncn);

            const content = `
                <!-- Thông tin cơ bản từ bảng nhan_vien -->
                <div class="salary-card" style="background: #92B9E3; color: black;">
                    <h3><i class="fas fa-user-tie"></i> Thông Tin Cơ Bản (Bảng nhan_vien)</h3>
                    <div class="salary-amount" style="color: black; font-size: 1.8rem;">${data.luong_co_ban_nv} VNĐ</div>
                    <div class="salary-details">
                        <div class="detail-item">
                            <div class="detail-label" style="color: #333;">Lương cơ bản</div>
                            <div class="detail-value" style="color: black;">${data.luong_co_ban_nv} VNĐ</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label" style="color: #333;">Phụ cấp chức vụ</div>
                            <div class="detail-value" style="color: black;">${data.phu_cap_chuc_vu_nv} VNĐ</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label" style="color: #333;">Phụ cấp bằng cấp</div>
                            <div class="detail-value" style="color: black;">${data.phu_cap_bang_cap_nv} VNĐ</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label" style="color: #333;">Phụ cấp khác</div>
                            <div class="detail-value" style="color: black;">${data.phu_cap_khac_nv} VNĐ</div>
                        </div>
                    </div>
                </div>

                <!-- Thông tin công việc từ bảng luong -->
                <div class="salary-card" style="background: #92B9E3; color: black;">
                    <h3><i class="fas fa-calendar-check"></i> Thông Tin Công Việc (Bảng luong)</h3>
                    <div class="salary-amount" style="color: black; font-size: 1.8rem;">${data.so_ngay_cong} ngày</div>
                    <div class="salary-details">
                        <div class="detail-item">
                            <div class="detail-label" style="color: #333;">Số ngày công</div>
                            <div class="detail-value" style="color: black;">${data.so_ngay_cong} ngày</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label" style="color: #333;">Số ngày nghỉ phép</div>
                            <div class="detail-value" style="color: black;">${data.so_ngay_nghi_phep} ngày</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label" style="color: #333;">Số ngày nghỉ không phép</div>
                            <div class="detail-value" style="color: black;">${data.so_ngay_nghi_khong_phep} ngày</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label" style="color: #333;">Lương theo ngày</div>
                            <div class="detail-value" style="color: black;">${data.luong_theo_ngay} VNĐ</div>
                        </div>
                    </div>
                </div>

                <!-- Lương và phụ cấp từ bảng luong -->
                <div class="salary-card" style="background: #92B9E3; color: black;">
                    <h3><i class="fas fa-coins"></i> Lương và Phụ Cấp (Bảng luong)</h3>
                    <div class="salary-amount" style="color: black; font-size: 1.8rem;">${data.luong_co_ban} VNĐ</div>
                    <div class="salary-details">
                        <div class="detail-item">
                            <div class="detail-label" style="color: #333;">Lương cơ bản</div>
                            <div class="detail-value" style="color: black;">${data.luong_co_ban} VNĐ</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label" style="color: #333;">Phụ cấp chức vụ</div>
                            <div class="detail-value" style="color: black;">${data.phu_cap_chuc_vu} VNĐ</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label" style="color: #333;">Phụ cấp bằng cấp</div>
                            <div class="detail-value" style="color: black;">${data.phu_cap_bang_cap} VNĐ</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label" style="color: #333;">Phụ cấp khác</div>
                            <div class="detail-value" style="color: black;">${data.phu_cap_khac} VNĐ</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label" style="color: #333;">Thưởng</div>
                            <div class="detail-value" style="color: black;">${data.tien_thuong} VNĐ</div>
                        </div>
                    </div>
                </div>

                <!-- Thu nhập và giảm trừ -->
                <div class="salary-card" style="background: #92B9E3; color: black;">
                    <h3><i class="fas fa-calculator"></i> Thu Nhập và Giảm Trừ (Bảng luong)</h3>
                    <div class="salary-amount" style="color: black; font-size: 1.8rem;">${data.thu_nhap_truoc_thue} VNĐ</div>
                    <div class="salary-details">
                        <div class="detail-item">
                            <div class="detail-label" style="color: #333;">Thu nhập trước thuế</div>
                            <div class="detail-value" style="color: black;">${data.thu_nhap_truoc_thue} VNĐ</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label" style="color: #333;">Số người phụ thuộc</div>
                            <div class="detail-value" style="color: black;">${data.so_nguoi_phu_thuoc} người</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label" style="color: #333;">Giảm trừ gia cảnh</div>
                            <div class="detail-value" style="color: black;">${data.giam_tru_gia_canh} VNĐ</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label" style="color: #333;">Thu nhập chịu thuế</div>
                            <div class="detail-value" style="color: black;">${data.thu_nhap_chiu_thue} VNĐ</div>
                        </div>
                    </div>
                </div>

                <!-- Khấu trừ từ bảng luong -->
                <div class="salary-card" style="background: #92B9E3; color: black;">
                    <h3><i class="fas fa-minus-circle"></i> Các Khoản Khấu Trừ (Bảng luong)</h3>
                    <div class="salary-amount" style="color: black; font-size: 1.8rem;">${formatCurrency(totalDeductions)} VNĐ</div>
                    <div class="salary-details">
                        <div class="detail-item">
                            <div class="detail-label" style="color: #333;">BHTN (1%)</div>
                            <div class="detail-value" style="color: black;">${data.bhtn_nv} VNĐ</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label" style="color: #333;">Thuế TNCN</div>
                            <div class="detail-value" style="color: black;">${data.thue_tncn} VNĐ</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label" style="color: #333;">BHYT (1.5%)</div>
                            <div class="detail-value" style="color: black;">${data.bhyt_nv} VNĐ</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label" style="color: #333;">BHXH (8%)</div>
                            <div class="detail-value" style="color: black;">${data.bhxh_nv} VNĐ</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label" style="color: #333;">Các khoản trừ khác</div>
                            <div class="detail-value" style="color: black;">${data.cac_khoan_tru_khac} VNĐ</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label" style="color: #333;">Tổng khoản trừ</div>
                            <div class="detail-value" style="color: black;">${data.tong_khoan_tru} VNĐ</div>
                        </div>
                    </div>
                </div>

               <!-- Tổng lương thực nhận từ bảng luong -->
                <div class="salary-card" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); border: 3px solid #27ae60;">
                    <h3><i class="fas fa-money-bill-wave"></i> Tổng Lương Thực Nhận (Bảng luong)</h3>
                    <div class="salary-amount" style="color: #27ae60; font-size: 2.5rem; font-weight: bold; text-shadow: 2px 2px 4px rgba(0,0,0,0.1);">${data.luong_thuc_nhan} VNĐ</div>
                    <div class="salary-details">
                        <div class="detail-item">
                            <div class="detail-label" style="color: #2c3e50; font-weight: bold;">Tháng lương</div>
                            <div class="detail-value" style="color: #27ae60; font-weight: bold;">${data.thang}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label" style="color: #2c3e50; font-weight: bold;">Trạng thái</div>
                            <div class="detail-value" style="color: #27ae60; font-weight: bold;">${data.trang_thai}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label" style="color: #2c3e50; font-weight: bold;">Ngày chấm công</div>
                            <div class="detail-value" style="color: #27ae60; font-weight: bold;">${data.ngay_cham_cong || 'N/A'}</div>
                        </div>
                    </div>
                </div>

            `;

            document.getElementById('salaryContent').innerHTML = content;
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount);
        }
    </script>
</body>
</html>
