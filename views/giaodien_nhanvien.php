<?php
require_once __DIR__ . '/../includes/check_login.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../EmployeeRepository.php';

// Khởi tạo kết nối database
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    error_log("Failed to connect to the database.");
    die("Không thể kết nối đến cơ sở dữ liệu. Vui lòng thử lại sau.");
}

// Khởi tạo repository
$employeeRepo = new EmployeeRepository($conn);

// Lấy thông tin nhân viên hiện tại
$currentUserId = $_SESSION['user_id'];
$query = "SELECT nv.*, nd.ten_dang_nhap, pb.ten_phong_ban, cv.ten_chuc_vu 
          FROM nhan_vien nv 
          JOIN nguoi_dung nd ON nv.id_nhan_vien = nd.id_nhan_vien 
          LEFT JOIN phong_ban pb ON nv.id_phong_ban = pb.id_phong_ban
          LEFT JOIN chuc_vu cv ON nv.id_chuc_vu = cv.id_chuc_vu
          WHERE nd.id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$currentUserId]);
$currentEmployee = $stmt->fetch(PDO::FETCH_ASSOC);

// Tính số ngày đi làm trong tháng
$currentMonth = date('Y-m');
$attendanceQuery = "SELECT COUNT(*) as working_days FROM cham_cong 
                   WHERE id_nhan_vien = ? AND DATE(ngay_lam_viec) LIKE ?";
$attendanceStmt = $conn->prepare($attendanceQuery);
$attendanceStmt->execute([$currentEmployee['id_nhan_vien'], $currentMonth . '%']);
$workingDays = $attendanceStmt->fetch(PDO::FETCH_ASSOC)['working_days'];

// Tính số ngày nghỉ phép trong tháng
$leaveQuery = "SELECT COUNT(*) as leave_days FROM nghi_phep 
              WHERE id_nhan_vien = ? AND DATE(ngay_bat_dau) LIKE ? AND trang_thai1 = 'Đã duyệt'";
$leaveStmt = $conn->prepare($leaveQuery);
$leaveStmt->execute([$currentEmployee['id_nhan_vien'], $currentMonth . '%']);
$leaveDays = $leaveStmt->fetch(PDO::FETCH_ASSOC)['leave_days'];
?>

<?php
// Set current page for sidebar
$current_page = 'giaodien_nhanvien.php';
$is_admin = false; // Nhân viên không phải admin

// Include base URL helper
require_once __DIR__ . '/../includes/base_url.php';
?>

<!DOCTYPE html>
<html lang="vi" class="light-style layout-navbar-fixed layout-menu-fixed">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Nhân Viên - HRM Pro</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= $base_url ?>/assets/img/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= $base_url ?>/assets/img/favicon.png">
    <link rel="icon" type="image/svg+xml" href="<?= $base_url ?>/assets/img/favicon.svg">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Layout CSS -->
    <link href="<?= $base_url ?>/assets/vendor/css/rtl/core.css" rel="stylesheet" class="template-customizer-core-css">
    <link href="<?= $base_url ?>/assets/vendor/css/rtl/theme-default.css" rel="stylesheet" class="template-customizer-theme-css">
    <link href="<?= $base_url ?>/assets/css/demo.css" rel="stylesheet">
    
    <style>
        .hero-section {
            background: linear-gradient(135deg, rgba(105, 108, 255, 0.1) 0%, rgba(255, 255, 255, 0.9) 100%);
            background-image: url('<?= $base_url ?>/img/gdhoso.jpg');
            background-size: 120%;
            background-position: center;
            background-blend-mode: overlay;
            min-height: 400px;
            display: flex;
            align-items: center;
            position: relative;
            border-radius: 1rem;
            margin-bottom: 2rem;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(105, 108, 255, 0.8) 0%, rgba(255, 255, 255, 0.3) 100%);
            z-index: 1;
            border-radius: 1rem;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .stats-card {
            background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-indigo) 100%);
            color: white;
            border-radius: 1rem;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(105, 108, 255, 0.3);
        }

        .stats-card.bg-success {
            background: linear-gradient(135deg, var(--bs-success) 0%, var(--bs-teal) 100%);
        }

        .stats-card.bg-warning {
            background: linear-gradient(135deg, var(--bs-warning) 0%, var(--bs-orange) 100%);
        }

        .stats-card.bg-info {
            background: linear-gradient(135deg, var(--bs-info) 0%, var(--bs-cyan) 100%);
        }

        .stats-icon {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 2.5rem;
            opacity: 0.3;
        }

        .stats-value {
            font-size: 2rem;
            font-weight: 700;
            margin: 0.5rem 0;
        }

        .stats-desc {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .card-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .container {
            padding: 1rem;
        }

        /* Fix layout wrapper positioning */
        .layout-wrapper {
            min-height: 100vh;
            width: 100%;
            position: relative;
        }

        .layout-container {
            display: flex;
            min-height: 100vh;
        }

        .layout-page {
            flex: 1;
            display: flex;
            flex-direction: column;
            margin-left: 0;
            width: calc(100% - 260px);
        }

        .content-wrapper {
            flex: 1;
            padding: 0;
            margin-left: 0;
        }

        .container-xxl {
            max-width: 100%;
            padding: 0 1rem;
        }

        .row {
            margin: 0;
        }

        .col-lg-3, .col-md-6 {
            padding: 0.5rem;
        }

        /* Ensure full width */
        body {
            margin: 0;
            padding: 0;
            width: 100%;
        }

        html {
            width: 100%;
        }

        /* Fix sidebar overlap */
        .layout-menu {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            z-index: 1000;
        }

        .layout-page {
            margin-left: 260px;
            width: calc(100% - 260px);
        }

        @media (max-width: 1199.98px) {
            .layout-menu {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .layout-menu.show {
                transform: translateX(0);
            }
            
            .layout-page {
                margin-left: 0;
                width: 100%;
            }
        }

        /* Contract Notification Styles */
        .notification-item {
            padding: 12px 16px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
        }

        .notification-icon.urgent {
            background: #dc3545;
        }

        .notification-icon.warning {
            background: #ffc107;
            color: #212529;
        }

        .notification-icon.normal {
            background: #17a2b8;
        }

        .notification-details {
            flex: 1;
        }

        .notification-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 4px;
        }

        .notification-desc {
            color: #666;
            font-size: 0.9rem;
        }

        .notification-time {
            color: #999;
            font-size: 0.8rem;
            margin-top: 4px;
        }

        .no-notifications {
            padding: 20px;
            text-align: center;
            color: #999;
        }

        .loading-notifications {
            padding: 20px;
            text-align: center;
            color: #666;
        }
    </style>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <?php include __DIR__ . '/../includes/sidebar.php'; ?>
            
            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                <nav class="layout-navbar container-xxl navbar navbar-expand navbar-navbar-fixed navbar-detached" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="fas fa-bars"></i>
                        </a>
                    </div>
                    
                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- Contract Expiry Notification Bell -->
                            <li class="nav-item">
                                <div id="contractNotificationBell" class="nav-link" style="position: relative; cursor: pointer;" onclick="toggleContractNotification()">
                                    <i class="fas fa-bell" style="font-size: 1.2rem;"></i>
                                    <span id="contractNotificationBadge" class="badge bg-danger rounded-pill" style="position: absolute; top: -5px; right: -5px; font-size: 0.7rem; min-width: 18px; height: 18px; display: none;">0</span>
                                </div>
                                <!-- Contract Notification Dropdown -->
                                <div id="contractNotificationDropdown" class="notification-dropdown" style="display: none; position: absolute; top: 100%; right: 0; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 1000; min-width: 300px; max-height: 400px; overflow-y: auto;">
                                    <div class="notification-header" style="padding: 12px 16px; border-bottom: 1px solid #eee; font-weight: 600; color: #333;">
                                        <i class="fas fa-bell me-2"></i>Thông báo hợp đồng
                                    </div>
                                    <div id="contractNotificationList" class="notification-list" style="max-height: 300px; overflow-y: auto;">
                                        <!-- Notifications will be loaded here -->
                                    </div>
                                </div>
                            </li>
                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="<?= $base_url ?>/img/<?= $currentEmployee['hinh_anh'] ?? 'default-avatar.jpg' ?>" alt class="w-px-40 h-auto rounded-circle">
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="<?= $base_url ?>/views/hoso.php">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="<?= $base_url ?>/img/<?= $currentEmployee['hinh_anh'] ?? 'default-avatar.jpg' ?>" alt class="w-px-40 h-auto rounded-circle">
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0"><?= htmlspecialchars($currentEmployee['ho_ten'] ?? $currentEmployee['ten_dang_nhap']) ?></h6>
                                                    <small class="text-muted">Nhân viên</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="<?= $base_url ?>/views/hoso.php">
                                            <i class="fas fa-user me-2"></i>Hồ sơ của tôi
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?= $base_url ?>/views/luongmy.php">
                                            <i class="fas fa-money-bill-wave me-2"></i>Lương của tôi
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?= $base_url ?>/views/nghiphepmy.php">
                                            <i class="fas fa-calendar-alt me-2"></i>Nghỉ phép của tôi
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="<?= $base_url ?>/views/logout.php">
                                            <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!--/ User -->
                        </ul>
                    </div>
                </nav>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                <!-- Hero Section -->
                <div class="hero-section">
                    <div class="container">
                        <div class="hero-content">
                            <h1 class="display-4 fw-bold mb-3">
                                Chào mừng <?= htmlspecialchars($currentEmployee['ho_ten'] ?? $currentEmployee['ten_dang_nhap']) ?> 👋
                            </h1>
                            <p class="lead mb-4">
                                Chào mừng bạn đến với hệ thống quản lý nhân sự HRM Pro.<br>
                                Quản lý thông tin cá nhân và theo dõi công việc của bạn.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="container mt-5">
                    <div class="row">
                        <!-- Working Days -->
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card">
                                <div class="stats-card bg-primary">
                                    <h5 class="card-title">Ngày đi làm</h5>
                                    <div class="stats-value"><?= $workingDays ?></div>
                                    <div class="stats-desc">Trong tháng này</div>
                                    <i class="stats-icon fas fa-calendar-check"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Leave Days -->
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card">
                                <div class="stats-card bg-success">
                                    <h5 class="card-title">Ngày nghỉ phép</h5>
                                    <div class="stats-value"><?= $leaveDays ?></div>
                                    <div class="stats-desc">Đã được duyệt</div>
                                    <i class="stats-icon fas fa-calendar-times"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Position -->
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card">
                                <div class="stats-card bg-warning">
                                    <h5 class="card-title">Chức vụ</h5>
                                    <div class="stats-value" style="font-size: 1.5rem;"><?= htmlspecialchars($currentEmployee['ten_chuc_vu'] ?? 'Nhân viên') ?></div>
                                    <div class="stats-desc">Vị trí hiện tại</div>
                                    <i class="stats-icon fas fa-briefcase"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Department -->
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card">
                                <div class="stats-card bg-info">
                                    <h5 class="card-title">Phòng ban</h5>
                                    <div class="stats-value" style="font-size: 1.5rem;"><?= htmlspecialchars($currentEmployee['ten_phong_ban'] ?? 'Chưa xác định') ?></div>
                                    <div class="stats-desc">Bộ phận làm việc</div>
                                    <i class="stats-icon fas fa-building"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                </div>
                <!-- / Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>
        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <script src="<?= $base_url ?>/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="<?= $base_url ?>/assets/vendor/libs/popper/popper.js"></script>
    <script src="<?= $base_url ?>/assets/vendor/js/bootstrap.js"></script>
    <script src="<?= $base_url ?>/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="<?= $base_url ?>/assets/vendor/js/menu.js"></script>
    
    <!-- Main JS -->
    <script src="<?= $base_url ?>/assets/js/main.js"></script>
    
    <script>
        function showFaceModal() {
            alert('Tính năng điểm danh sẽ được tích hợp sau!');
        }

        // Contract Notification Functions
        let contractNotifications = [];

        // Toggle notification dropdown
        function toggleContractNotification() {
            const dropdown = document.getElementById('contractNotificationDropdown');
            const isVisible = dropdown.style.display === 'block';
            
            if (isVisible) {
                dropdown.style.display = 'none';
            } else {
                dropdown.style.display = 'block';
                loadContractNotifications();
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const bell = document.getElementById('contractNotificationBell');
            const dropdown = document.getElementById('contractNotificationDropdown');
            
            if (!bell.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.style.display = 'none';
            }
        });

        // Load contract notifications
        async function loadContractNotifications() {
            const notificationList = document.getElementById('contractNotificationList');
            notificationList.innerHTML = '<div class="loading-notifications"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>';
            
            try {
                const response = await fetch('/doanqlns/api/get_contract_expiry.php?employee_id=<?= $currentEmployee['id_nhan_vien'] ?>');
                const result = await response.json();
                
                if (result.success && result.data) {
                    const contract = result.data;
                    const today = new Date();
                    const endDate = new Date(contract.ngay_ket_thuc);
                    const diffTime = endDate.getTime() - today.getTime();
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    
                    if (diffDays <= 90 && diffDays >= 0) {
                        const urgencyClass = diffDays <= 7 ? 'urgent' : (diffDays <= 15 ? 'warning' : 'normal');
                        const urgencyText = diffDays <= 7 ? 'Khẩn cấp' : (diffDays <= 15 ? 'Cảnh báo' : 'Sắp hết hạn');
                        const urgencyIcon = diffDays <= 7 ? 'fa-exclamation-triangle' : (diffDays <= 15 ? 'fa-exclamation-circle' : 'fa-bell');
                        
                        notificationList.innerHTML = `
                            <div class="notification-item" onclick="handleContractNotificationClick()">
                                <div class="notification-content">
                                    <div class="notification-icon ${urgencyClass}">
                                        <i class="fas ${urgencyIcon}"></i>
                                    </div>
                                    <div class="notification-details">
                                        <div class="notification-title">Hợp đồng ${urgencyText}</div>
                                        <div class="notification-desc">Hợp đồng của bạn sẽ hết hạn sau ${diffDays} ngày</div>
                                        <div class="notification-time">Ngày kết thúc: ${formatDate(contract.ngay_ket_thuc)}</div>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        updateNotificationBadge(1);
                    } else {
                        notificationList.innerHTML = `
                            <div class="no-notifications">
                                <i class="fas fa-bell-slash"></i>
                                <p>Không có thông báo hợp đồng</p>
                            </div>
                        `;
                        updateNotificationBadge(0);
                    }
                } else {
                    notificationList.innerHTML = `
                        <div class="no-notifications">
                            <i class="fas fa-bell-slash"></i>
                            <p>Không có thông báo hợp đồng</p>
                        </div>
                    `;
                    updateNotificationBadge(0);
                }
            } catch (error) {
                console.error('Lỗi khi tải thông báo hợp đồng:', error);
                notificationList.innerHTML = `
                    <div class="no-notifications">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Lỗi khi tải thông báo</p>
                    </div>
                `;
                updateNotificationBadge(0);
            }
        }

        // Update notification badge
        function updateNotificationBadge(count) {
            const badge = document.getElementById('contractNotificationBadge');
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }

        // Handle notification click
        function handleContractNotificationClick() {
            // Show contract details in a modal instead of redirecting
            showContractDetails();
        }

        // Show contract details modal
        async function showContractDetails() {
            // Create and show a modal with contract information
            const modal = document.createElement('div');
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10000;
            `;
            
            modal.innerHTML = `
                <div style="background: white; padding: 20px; border-radius: 8px; max-width: 500px; width: 90%;">
                    <h5 style="margin-bottom: 15px; color: #333;">
                        <i class="fas fa-file-contract me-2"></i>Thông tin hợp đồng
                    </h5>
                    <div id="contractDetails" style="margin-bottom: 20px;">
                        <div style="text-align: center; margin-bottom: 15px;">
                            <i class="fas fa-spinner fa-spin" style="color: #007bff;"></i>
                            <span style="margin-left: 8px; color: #666;">Đang tải thông tin...</span>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <button onclick="closeContractModal()" style="background: #007bff; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">
                            <i class="fas fa-times me-1"></i>Đóng
                        </button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            // Load contract details
            try {
                const response = await fetch('/doanqlns/api/get_contract_expiry.php?employee_id=<?= $currentEmployee['id_nhan_vien'] ?>');
                const result = await response.json();
                
                const contractDetails = document.getElementById('contractDetails');
                
                if (result.success && result.data) {
                    const contract = result.data;
                    const today = new Date();
                    const endDate = new Date(contract.ngay_ket_thuc);
                    const diffTime = endDate.getTime() - today.getTime();
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    
                    const urgencyClass = diffDays <= 7 ? 'urgent' : (diffDays <= 15 ? 'warning' : 'normal');
                    const urgencyText = diffDays <= 7 ? 'Khẩn cấp' : (diffDays <= 15 ? 'Cảnh báo' : 'Sắp hết hạn');
                    const urgencyColor = diffDays <= 7 ? '#dc3545' : (diffDays <= 15 ? '#ffc107' : '#17a2b8');
                    
                    contractDetails.innerHTML = `
                        <div style="border: 1px solid #eee; border-radius: 8px; padding: 15px; background: #f8f9fa;">
                            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                <i class="fas fa-user" style="color: #007bff; margin-right: 8px;"></i>
                                <strong>Nhân viên:</strong>
                                <span style="margin-left: 8px;"><?= htmlspecialchars($currentEmployee['ho_ten'] ?? $currentEmployee['ten_dang_nhap']) ?></span>
                            </div>
                            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                <i class="fas fa-file-contract" style="color: #007bff; margin-right: 8px;"></i>
                                <strong>Loại hợp đồng:</strong>
                                <span style="margin-left: 8px;">${contract.loai_hop_dong || 'Chưa xác định'}</span>
                            </div>
                            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                <i class="fas fa-calendar-alt" style="color: #007bff; margin-right: 8px;"></i>
                                <strong>Ngày kết thúc:</strong>
                                <span style="margin-left: 8px;">${formatDate(contract.ngay_ket_thuc)}</span>
                            </div>
                            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                <i class="fas fa-clock" style="color: ${urgencyColor}; margin-right: 8px;"></i>
                                <strong>Thời gian còn lại:</strong>
                                <span style="margin-left: 8px; color: ${urgencyColor}; font-weight: bold;">${diffDays} ngày</span>
                            </div>
                            <div style="background: ${urgencyColor}; color: white; padding: 10px; border-radius: 4px; text-align: center; margin-top: 15px;">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>${urgencyText.toUpperCase()}</strong>
                            </div>
                        </div>
                        <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; padding: 10px; margin-top: 15px;">
                            <i class="fas fa-info-circle" style="color: #856404; margin-right: 8px;"></i>
                            <span style="color: #856404;"><strong>Lưu ý:</strong> Vui lòng liên hệ bộ phận HR để gia hạn hợp đồng trước khi hết hạn.</span>
                        </div>
                    `;
                } else {
                    contractDetails.innerHTML = `
                        <div style="text-align: center; padding: 20px; color: #666;">
                            <i class="fas fa-info-circle" style="font-size: 2rem; margin-bottom: 10px;"></i>
                            <p>Không có thông tin hợp đồng</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Lỗi khi tải thông tin hợp đồng:', error);
                contractDetails.innerHTML = `
                    <div style="text-align: center; padding: 20px; color: #dc3545;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 10px;"></i>
                        <p>Lỗi khi tải thông tin hợp đồng</p>
                    </div>
                `;
            }
        }

        // Close contract modal
        function closeContractModal() {
            const modal = document.querySelector('div[style*="position: fixed"]');
            if (modal) {
                modal.remove();
            }
        }

        // Format date
        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return '';
            return date.toLocaleDateString('vi-VN');
        }

        // Load notifications on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadContractNotifications();
        });
    </script>
</body>
</html>
