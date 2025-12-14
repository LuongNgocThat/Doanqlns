<?php
require_once __DIR__ . '/includes/check_login.php';
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/EmployeeRepository.php';

// Kiểm tra quyền admin
$isAdmin = isset($_SESSION['quyen_them']) && $_SESSION['quyen_them'] == 1;

// Nếu không phải admin, chuyển hướng đến giao diện nhân viên
if (!$isAdmin) {
    header("Location: /doanqlns/views/giaodien_nhanvien.php");
    exit();
}

// Khởi tạo kết nối database
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    error_log("Failed to connect to the database.");
    die("Không thể kết nối đến cơ sở dữ liệu. Vui lòng thử lại sau.");
}

// Khởi tạo repository
$employeeRepo = new EmployeeRepository($conn);

// Tính tổng số nhân viên
$totalEmployees = $employeeRepo->getTotalEmployees();

// Tính tỷ lệ đi làm đúng giờ
$punctuality = $employeeRepo->getPunctualityComparison();

// Tính số đơn xin nghỉ phép chờ xét duyệt
$leaveCount = $employeeRepo->getPendingLeaveCount();

// Tính số nhân viên mới
$newEmployees = $employeeRepo->getNewEmployees();

// Tính thống kê thai sản
$maternityStats = $employeeRepo->getMaternityStats();
?>

<!DOCTYPE html>
<html lang="vi" class="light-style layout-navbar-fixed layout-menu-fixed">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - HRM Pro</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/doanqlns/assets/img/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/doanqlns/assets/img/favicon.png">
    <link rel="icon" type="image/svg+xml" href="/doanqlns/assets/img/favicon.svg">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
   <style>
    
        :root {
            --bs-blue: #696cff;
            --bs-primary: #696cff;
            --bs-body-bg: #f5f5f9;
        }

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

        .card {
            background: #fff;
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.12);
        }

        .card-header {
            background: transparent;
            padding: 1.5rem;
            border-bottom: 1px solid #d9dee3;
        }

        .card-title {
            color: #566a7f;
            font-size: 1.125rem;
            font-weight: 500;
            margin-bottom: 0;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Welcome card with soft colors and bubbles */
        .welcome-card {
            position: relative;
            background: linear-gradient(135deg, #eef2ff 0%, #e6fbff 100%);
            border: 1px solid #e0e7ff;
            box-shadow: 0 6px 20px rgba(63, 109, 252, 0.06);
            overflow: hidden;
        }

        .welcome-card::before {
            content: '';
            position: absolute;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            top: -80px;
            right: -80px;
            background: radial-gradient(circle at 50% 50%, rgba(99,102,241,0.25), rgba(99,102,241,0.08) 60%, transparent 70%);
            filter: blur(0.5px);
        }

        .welcome-card::after {
            content: '';
            position: absolute;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            bottom: -60px;
            left: -60px;
            background: radial-gradient(circle at 50% 50%, rgba(14,165,233,0.22), rgba(14,165,233,0.08) 60%, transparent 70%);
            filter: blur(0.5px);
        }

        .welcome-card .card-title {
            color: #3f6dfc;
            font-weight: 700;
        }

        .stats-card {
            position: relative;
            padding: 1.5rem;
            background: linear-gradient(72.47deg, #7367f0 22.16%, rgba(115, 103, 240, 0.7) 76.47%);
            color: #fff;
            overflow: hidden;
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M50 0C22.4 0 0 22.4 0 50C0 77.6 22.4 100 50 100C77.6 100 100 77.6 100 50C100 22.4 77.6 0 50 0ZM50 90C27.9 90 10 72.1 10 50C10 27.9 27.9 10 50 10C72.1 10 90 27.9 90 50C90 72.1 72.1 90 50 90Z' fill='rgba(255,255,255,0.1)'/%3E%3C/svg%3E") center/cover no-repeat;
        }

        .stats-card .card-title {
            color: #fff;
            margin-bottom: 2rem;
        }

        .stats-value {
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .stats-desc {
            font-size: 0.875rem;
            opacity: 0.8;
        }

        .stats-icon {
            position: absolute;
            right: 1.5rem;
            top: 1.5rem;
            font-size: 2rem;
            opacity: 0.8;
        }

        @media (max-width: 1199.98px) {
            .layout-page {
                padding-left: 0;
            }
}  

        /* Navbar styles */
        .layout-navbar {
            position: fixed;
            top: 0;
            right: 0;
            left: 260px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 0 1.5rem;
            background: #fff;
            box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.12);
            z-index: 998;
        }

        .navbar-dropdown {
            position: relative;
            display: inline-block;
        }

        .navbar-dropdown-toggle {
            display: flex;
            align-items: center;
            padding: 0.5rem;
            color: #697a8d;
            text-decoration: none;
            cursor: pointer;
        }

        .navbar-dropdown-toggle:hover {
            color: #696cff;
        }

        .navbar-dropdown-toggle img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 0.5rem;
        }

        .navbar-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            min-width: 200px;
            padding: 0.5rem 0;
            margin: 0.125rem 0 0;
            background-color: #fff;
            border: 1px solid rgba(67, 89, 113, 0.1);
            border-radius: 0.5rem;
            box-shadow: 0 5px 25px rgba(67, 89, 113, 0.15);
            display: none;
        }

        .navbar-dropdown-menu.show {
            display: block;
        }

        .navbar-dropdown-item {
            display: flex;
            align-items: center;
            padding: 0.532rem 1.25rem;
            color: #697a8d;
            text-decoration: none;
        }

        .navbar-dropdown-item:hover {
            background: rgba(105, 108, 255, 0.08);
            color: #696cff;
        }

        .navbar-dropdown-item i {
            margin-right: 0.5rem;
        }

        /* Notification Bell Styles */
        .navbar-notification {
            position: relative;
            display: inline-block;
        }

        .notification-bell {
            position: relative;
            cursor: pointer;
            padding: 0.5rem;
            color:rgb(153, 153, 150);
            transition: color 0.3s;
        }

        .notification-bell:hover {
            color: #696cff;
        }

        .notification-bell i {
            font-size: 1.25rem;
        }

        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: #ff3e1d;
            color: white;
            font-size: 0.65rem;
            font-weight: 600;
            padding: 0.15rem 0.35rem;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
            display: none;
        }

        .notification-badge.active {
            display: block;
        }

        .notification-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            width: 380px;
            max-height: 500px;
            margin-top: 0.5rem;
            background: white;
            border: 1px solid rgba(67, 89, 113, 0.1);
            border-radius: 0.5rem;
            box-shadow: 0 5px 25px rgba(67, 89, 113, 0.15);
            display: none;
            flex-direction: column;
            z-index: 1000;
        }

        .notification-dropdown.show {
            display: flex;
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #e7e7e7;
        }

        .notification-header h6 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: #333;
        }

        .btn-mark-read {
            background: none;
            border: none;
            color: #696cff;
            font-size: 0.75rem;
            cursor: pointer;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            transition: background 0.3s;
        }

        .btn-mark-read:hover {
            background: rgba(105, 108, 255, 0.1);
        }

        .notification-list {
            overflow-y: auto;
            max-height: 400px;
        }

        .notification-item {
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background 0.3s;
            display: flex;
            gap: 0.75rem;
        }

        .notification-item:hover {
            background:rgb(230, 230, 230);
        }

        .notification-item.unread {
            background: #f0f4ff;
        }

        .notification-icon {
            flex-shrink: 0;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .notification-icon.leave {
            background: #fff4e6;
            color: #f08a24;
        }

        .notification-icon.attendance {
            background: #e6f9ff;
            color: #0ea5e9;
        }

        .notification-content {
            flex: 1;
        }

        .notification-title {
            font-weight: 600;
            font-size: 0.875rem;
            color: #333;
            margin-bottom: 0.25rem;
        }

        .notification-desc {
            font-size: 0.75rem;
            color: #666;
            margin-bottom: 0.25rem;
        }

        .notification-time {
            font-size: 0.7rem;
            color: #999;
        }

        .loading-notifications {
            text-align: center;
            padding: 2rem;
            color: #999;
        }

        .no-notifications {
            text-align: center;
            padding: 2rem;
            color: #999;
        }

        .no-notifications i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        /* Quick actions modal styles */
        #employeeModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        #employeeModal.show {
            display: flex;
        }

        #employeeModal > div {
            background: white;
            width: 800px;
            max-width: 95vw;
            max-height: 90vh;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            overflow-y: auto;
        }

        #employeeSelect {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .loading::after {
            content: '';
            border: 4px solid #f3f3f3;
            border-top: 4px solid #696cff;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
            display: inline-block;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        #fileInput {
            display: none;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 10px 18px;
            border: 1px solid transparent;
            border-radius: 12px;
            cursor: pointer;
            background: #fff;
            color: #4a5568;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            font-weight: 600;
        }

        /* Soft color tags like the sample image */
        .btn-soft-blue { background: #eef2ff; color: #3f6dfc; border-color: #dde3ff; }
        .btn-soft-blue:hover { background: #e4e9ff; box-shadow: 0 2px 8px rgba(63,109,252,.15); }
        .btn-soft-orange { background: #fff4e6; color: #f08a24; border-color: #ffe7cc; }
        .btn-soft-orange:hover { background: #ffe9cf; box-shadow: 0 2px 8px rgba(240,138,36,.15); }
        .btn-soft-green { background: #eafff6; color: #22c55e; border-color: #d1fae5; }
        .btn-soft-green:hover { background: #dff9ef; box-shadow: 0 2px 8px rgba(34,197,94,.15); }
        .btn-soft-cyan { background: #e6f9ff; color: #0ea5e9; border-color: #cfefff; }
        .btn-soft-cyan:hover { background: #dbf3ff; box-shadow: 0 2px 8px rgba(14,165,233,.15); }
        .btn-soft-indigo { background: #eef2ff; color: #6366f1; border-color: #e0e7ff; }
        .btn-soft-indigo:hover { background: #e6ebff; box-shadow: 0 2px 8px rgba(99,102,241,.15); }
        .btn-soft-red { background: #ffefef; color: #ef4444; border-color: #ffe1e1; }
        .btn-soft-red:hover { background: #ffe6e6; box-shadow: 0 2px 8px rgba(239,68,68,.15); }

        .layout-page {
            padding-top: 64px; /* Add padding for navbar */
        }
    </style>
</head>
<body>
    <?php include(__DIR__ . '/includes/sidebar.php'); ?>

    <!-- Navbar -->
    <nav class="layout-navbar">
        <!-- Notification Bell -->
        <div class="navbar-notification me-3">
            <div class="notification-bell" onclick="toggleNotifications()">
                <i class="fas fa-bell"></i>
                <span class="notification-badge" id="notificationBadge">0</span>
            </div>
            <div class="notification-dropdown" id="notificationDropdown">
                <div class="notification-header">
                    <h6>Thông báo</h6>
                    <button class="btn-mark-read" onclick="markAllAsRead()">
                        <i class="fas fa-check-double"></i> Đọc tất cả
                    </button>
                </div>
                <div class="notification-list" id="notificationList">
                    <div class="loading-notifications">
                        <i class="fas fa-spinner fa-spin"></i> Đang tải...
                    </div>
                </div>
            </div>
        </div>

        <div class="navbar-dropdown">
            <div class="navbar-dropdown-toggle" onclick="toggleDropdown()">
                <?php
                    $isAdmin = isset($_SESSION['quyen_them']) && $_SESSION['quyen_them'] &&
                               isset($_SESSION['quyen_sua']) && $_SESSION['quyen_sua'] &&
                               isset($_SESSION['quyen_xoa']) && $_SESSION['quyen_xoa'];
                    $adminAvatarCdn = 'https://modernize-react-dark.netlify.app/assets/user-1-CznVQ9Sv.jpg';
                    $avatarUrl = $isAdmin
                        ? $adminAvatarCdn
                        : ('https://ui-avatars.com/api/?name=' . urlencode($_SESSION['username']) . '&background=random');
                ?>
                <img src="<?= $avatarUrl ?>" alt="Avatar" style="width:40px;height:40px;border-radius:50%;object-fit:cover;background:#e8f1f7;">
                <span><?= htmlspecialchars($_SESSION['username']) ?></span>
                <i class="fas fa-chevron-down ms-2"></i>
            </div>
            <div class="navbar-dropdown-menu" id="userDropdown">
               
                <a href="/doanqlns/views/setting.php" class="navbar-dropdown-item">
                    <i class="fas fa-cog"></i>
                    Cài đặt
                </a>
                <hr class="dropdown-divider">
                <a href="/doanqlns/views/logout.php" class="navbar-dropdown-item" onclick="return confirmLogout()">
                    <i class="fas fa-sign-out-alt"></i>
                    Đăng xuất
                </a>
            </div>
        </div>
    </nav>

    <div class="layout-wrapper">
        <div class="layout-page">
            <div class="content-wrapper">
                <!-- Welcome Banner -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card welcome-card">
                            <div class="card-body">
                                <h4 class="card-title mb-3">Chào mừng <?= htmlspecialchars($_SESSION['username']) ?> 👋</h4>
                                <p class="mb-0">Chào mừng bạn đến với hệ thống quản lý nhân sự HRM Pro.</p>
            </div>
        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row">
                    <!-- Total Employees -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card">
                            <div class="stats-card bg-primary">
                                <h5 class="card-title">Tổng nhân viên</h5>
                                <div class="stats-value"><?php echo number_format($totalEmployees); ?></div>
                                <div class="stats-desc">+<?php echo $newEmployees; ?> so với tháng trước</div>
                                <i class="stats-icon fas fa-users"></i>
                </div>
            </div>
                    </div>

                    <!-- Punctuality -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card">
                            <div class="stats-card" style="background: linear-gradient(72.47deg, #28c76f 22.16%, rgba(40, 199, 111, 0.7) 76.47%);">
                                <h5 class="card-title">Đi làm đúng giờ</h5>
                                <div class="stats-value"><?php echo $punctuality['current']; ?>%</div>
                                <div class="stats-desc">
                        <?php
                        $diff = abs($punctuality['difference']);
                        if ($diff > 0) {
                            echo ($punctuality['difference'] >= 0 ? '↑' : '↓') . ' ' . $diff . '% so với tháng trước';
                        } else {
                            echo 'Không thay đổi so với tháng trước';
                        }
                        ?>
                    </div>
                                <i class="stats-icon fas fa-calendar-check"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Leave Requests -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card">
                            <div class="stats-card" style="background: linear-gradient(72.47deg, #ff9f43 22.16%, rgba(255, 159, 67, 0.7) 76.47%);">
                                <h5 class="card-title">Nghỉ phép chờ duyệt</h5>
                                <div class="stats-value"><?php echo number_format($leaveCount); ?></div>
                                <div class="stats-desc">Đơn nghỉ phép đang chờ xét duyệt</div>
                                <i class="stats-icon fas fa-calendar-times"></i>
                </div>
                </div>
            </div>

                    <!-- New Employees -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card">
                            <div class="stats-card" style="background: linear-gradient(72.47deg, #ea5455 22.16%, rgba(234, 84, 85, 0.7) 76.47%);">
                                <h5 class="card-title">Nhân viên mới</h5>
                                <div class="stats-value"><?php echo number_format($newEmployees); ?></div>
                                <div class="stats-desc">Trong tháng này</div>
                                <i class="stats-icon fas fa-user-plus"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Banner cảnh báo thai sản -->
                <?php if ($maternityStats['ending_soon'] > 0 || $maternityStats['overdue'] > 0): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-warning maternity-alert" style="border-left: 4px solid #ff9800; background: linear-gradient(135deg, #fff3e0, #ffe0b2); border-radius: 8px; padding: 20px;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle text-warning me-3" style="font-size: 24px;"></i>
                                <div class="flex-grow-1">
                                    <h5 class="alert-heading mb-2">
                                        <i class="fas fa-baby"></i> Cảnh báo nghỉ thai sản
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Tổng đang nghỉ:</strong> <?php echo $maternityStats['total']; ?> nhân viên
                                        </div>
                                        <div class="col-md-4">
                                            <strong class="text-warning">Sắp hết (≤30 ngày):</strong> <?php echo $maternityStats['ending_soon']; ?> nhân viên
                                        </div>
                                        <div class="col-md-4">
                                            <strong class="text-danger">Quá hạn:</strong> <?php echo $maternityStats['overdue']; ?> nhân viên
                                        </div>
                                    </div>
                                    <?php if (!empty($maternityStats['ending_soon_list'])): ?>
                                    <div class="mt-3">
                                        <strong>Danh sách sắp hết thai sản:</strong>
                                        <ul class="mb-0 mt-2">
                                            <?php foreach ($maternityStats['ending_soon_list'] as $employee): ?>
                                            <li>
                                                <strong><?php echo htmlspecialchars($employee['ho_ten']); ?></strong> 
                                                (<?php echo htmlspecialchars($employee['ten_phong_ban']); ?>) - 
                                                Hết hạn: <?php echo date('d/m/Y', strtotime($employee['ngay_ket_thuc_thai_san'])); ?>
                                            </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($maternityStats['overdue_list'])): ?>
                                    <div class="mt-3">
                                        <strong class="text-danger">Danh sách quá hạn thai sản:</strong>
                                        <ul class="mb-0 mt-2">
                                            <?php foreach ($maternityStats['overdue_list'] as $employee): ?>
                                            <li class="text-danger">
                                                <strong><?php echo htmlspecialchars($employee['ho_ten']); ?></strong> 
                                                (<?php echo htmlspecialchars($employee['ten_phong_ban']); ?>) - 
                                                Hết hạn: <?php echo date('d/m/Y', strtotime($employee['ngay_ket_thuc_thai_san'])); ?>
                                            </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="ms-3">
                                    <button id="btnSendMaternityEmails" class="btn btn-primary btn-sm me-2">
                                        <i class="fas fa-paper-plane"></i> Gửi email nhắc nhở
                                    </button>
                                    <?php if ($maternityStats['ending_soon'] > 0): ?>
                                    <a href="views/users.php?filter=maternity_ending_soon" class="btn btn-warning btn-sm me-2">
                                        <i class="fas fa-clock"></i> Sắp hết (<?php echo $maternityStats['ending_soon']; ?>)
                                    </a>
                                    <?php endif; ?>
                                    <?php if ($maternityStats['overdue'] > 0): ?>
                                    <a href="views/users.php?filter=maternity_overdue" class="btn btn-danger btn-sm">
                                        <i class="fas fa-exclamation-triangle"></i> Quá hạn (<?php echo $maternityStats['overdue']; ?>)
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Quick Actions -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Thao tác nhanh</h5>
                            </div>
                            <div class="card-body">
                                <div class="action-buttons">
                                    <button class="action-btn btn-soft-blue" onclick="window.location.href='views/users.php?action=add'">
                                        <i class="fas fa-user-plus"></i>
                                        <span>Thêm nhân viên</span>
                                    </button>
                                    <button class="action-btn btn-soft-green" onclick="exportPayrollToExcel()">
                                        <i class="fas fa-file-excel" style="color: #217346;"></i>
                                        <span>Xuất Excel Lương</span>
                                    </button>
                                    <button class="action-btn btn-soft-cyan" onclick="exportAttendanceToExcel()">
                                        <i class="fas fa-file-excel" style="color: #217346;"></i>
                                        <span>Xuất Excel Chấm Công</span>
                                    </button>
                                    <button class="action-btn btn-soft-orange" onclick="triggerFileUpload()">
                                        <i class="fas fa-file-alt" style="color: #17a2b8;"></i>
                                        <span>Nhập Hồ Sơ Nhân Viên</span>
                                    </button>
                                    <!-- <button class="action-btn btn-soft-indigo" onclick="window.location.href='/doanqlns/views/chatnoibo.php'">
                                        <i class="fas fa-comments" style="color: #696cff;"></i>
                                        <span>Chat Nội Bộ</span>
                                    </button> -->
                                    <button class="action-btn btn-soft-red" onclick="window.location.href='/doanqlns/gmail_integration.php'">
                                        <i class="fab fa-google-drive" style="color: #4285f4;"></i>
                                        <span>Tải Ảnh Gmail</span>
                                    </button>
                                </div>
                                <!-- Input file -->
                                <input type="file" id="fileInput" accept=".csv,.xlsx" onchange="handleFileUpload(event)" style="display: none;">
                                <!-- Loading indicator -->
                                <div class="loading" id="loadingIndicator"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employee Modal -->
                <div id="employeeModal">
                    <div style="width: 800px; max-width: 95vw; max-height: 90vh; overflow-y: auto;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
                            <h3 style="margin: 0;">Nhập Hồ Sơ Nhân Viên</h3>
                            <button onclick="closeEmployeeModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #999;">&times;</button>
                        </div>
                        
                        <!-- Nút tải file mẫu -->
                        <div style="margin-bottom: 20px; text-align: center;">
                            <button onclick="downloadTemplate()" style="padding: 12px 24px; border: none; background: #28a745; color: white; border-radius: 5px; cursor: pointer; font-size: 16px; display: inline-flex; align-items: center; gap: 8px;">
                                <i class="fas fa-download"></i>
                                Tải file mẫu Excel
                            </button>
                            <div style="margin-top: 8px; font-size: 12px; color: #666;">
                                File mẫu chứa đầy đủ các trường thông tin cần thiết
                            </div>
                        </div>

                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Chọn nhân viên (tùy chọn):</label>
                            <select id="employeeSelect" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                                <option value="">-- Thêm nhân viên mới --</option>
                            </select>
                            <small style="color: #666; font-size: 12px;">Để trống để thêm nhân viên mới, chọn nhân viên để cập nhật hồ sơ</small>
                        </div>
                        <div style="display: flex; justify-content: flex-end; gap: 10px;">
                            <button onclick="closeEmployeeModal()" style="padding: 8px 16px; border: none; background: #ccc; border-radius: 5px; cursor: pointer;">Hủy</button>
                            <button onclick="confirmEmployeeSelection()" style="padding: 8px 16px; border: none; background: #696cff; color: white; border-radius: 5px; cursor: pointer;">Chọn file CSV</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Core JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle dropdown menu
        function toggleDropdown() {
            document.getElementById('userDropdown').classList.toggle('show');
            // Close notifications if open
            document.getElementById('notificationDropdown').classList.remove('show');
        }

        // Toggle notifications
        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.classList.toggle('show');
            // Close user dropdown if open
            document.getElementById('userDropdown').classList.remove('show');
            
            if (dropdown.classList.contains('show')) {
                loadNotifications();
            }
        }

        // Load notifications
        async function loadNotifications() {
            const notificationList = document.getElementById('notificationList');
            notificationList.innerHTML = '<div class="loading-notifications"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>';
            
            try {
                // Lấy dữ liệu nghỉ phép chờ duyệt
                const leaveResponse = await fetch('/doanqlns/index.php/api/nghiphep');
                const leaveData = await leaveResponse.json();
                
                // Lọc đơn nghỉ phép chờ duyệt
                const pendingLeaves = leaveData.filter(item => item.trang_thai1 === 'Chờ duyệt');
                
                const notifications = [];
                
                // Thêm thông báo nghỉ phép
                pendingLeaves.forEach(leave => {
                    notifications.push({
                        type: 'leave',
                        icon: 'fa-calendar-times',
                        title: 'Đơn xin nghỉ phép',
                        description: `${leave.ho_ten || 'Nhân viên'} xin nghỉ phép từ ${formatDate(leave.ngay_bat_dau)} đến ${formatDate(leave.ngay_ket_thuc)}`,
                        time: getTimeAgo(leave.ngay_tao),
                        link: '/doanqlns/views/nghiphep.php',
                        unread: true
                    });
                });
                
                // Lấy dữ liệu bổ sung điểm danh từ bảng phuc_tra
                try {
                    const phucTraResponse = await fetch('/doanqlns/api/get_all_phuc_tra.php');
                    const phucTraResult = await phucTraResponse.json();
                    
                    if (phucTraResult.success && Array.isArray(phucTraResult.data)) {
                        // Lọc các yêu cầu đang chờ duyệt
                        const pendingPhucTra = phucTraResult.data.filter(item => item.trang_thai === 'Đang chờ');
                        
                        // Thêm vào danh sách thông báo
                        pendingPhucTra.forEach(phucTra => {
                            notifications.push({
                                type: 'attendance',
                                icon: 'fa-user-clock',
                                title: 'Yêu cầu bổ sung điểm danh',
                                description: `${phucTra.ho_ten} yêu cầu bổ sung ${phucTra.buoi} ngày ${formatDate(phucTra.ngay)} - ${phucTra.ly_do_phuc_tra}`,
                                time: getTimeAgo(phucTra.created_at || phucTra.ngay),
                                link: '/doanqlns/views/chamcong.php',
                                unread: true
                            });
                        });
                    }
                } catch (phucTraError) {
                    console.warn('Không thể tải thông báo bổ sung điểm danh:', phucTraError);
                }
                
                // Sắp xếp thông báo theo thời gian (mới nhất trước)
                notifications.sort((a, b) => {
                    // Giả định thời gian càng mới thì time string càng nhỏ (vd: "5 phút trước" < "2 giờ trước")
                    return 0; // Giữ nguyên thứ tự đã thêm vào
                });
                
                // Hiển thị thông báo
                if (notifications.length === 0) {
                    notificationList.innerHTML = `
                        <div class="no-notifications">
                            <i class="fas fa-bell-slash"></i>
                            <p>Không có thông báo mới</p>
                        </div>
                    `;
                } else {
                    notificationList.innerHTML = notifications.map(notif => `
                        <div class="notification-item ${notif.unread ? 'unread' : ''}" onclick="handleNotificationClick('${notif.link}')">
                            <div class="notification-icon ${notif.type}">
                                <i class="fas ${notif.icon}"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">${notif.title}</div>
                                <div class="notification-desc">${notif.description}</div>
                                <div class="notification-time">${notif.time}</div>
                            </div>
                        </div>
                    `).join('');
                }
                
                // Cập nhật badge
                updateNotificationBadge(notifications.filter(n => n.unread).length);
                
            } catch (error) {
                console.error('Lỗi khi tải thông báo:', error);
                notificationList.innerHTML = `
                    <div class="no-notifications">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Lỗi khi tải thông báo</p>
                    </div>
                `;
            }
        }

        // Format date
        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN');
        }

        // Get time ago
        function getTimeAgo(dateString) {
            if (!dateString) return 'Vừa xong';
            const date = new Date(dateString);
            const now = new Date();
            const diff = now - date;
            const minutes = Math.floor(diff / 60000);
            const hours = Math.floor(minutes / 60);
            const days = Math.floor(hours / 24);
            
            if (days > 0) return `${days} ngày trước`;
            if (hours > 0) return `${hours} giờ trước`;
            if (minutes > 0) return `${minutes} phút trước`;
            return 'Vừa xong';
        }

        // Update notification badge
        function updateNotificationBadge(count) {
            const badge = document.getElementById('notificationBadge');
            badge.textContent = count;
            if (count > 0) {
                badge.classList.add('active');
            } else {
                badge.classList.remove('active');
            }
        }

        // Handle notification click
        function handleNotificationClick(link) {
            window.location.href = link;
        }

        // Mark all as read
        function markAllAsRead() {
            updateNotificationBadge(0);
            const items = document.querySelectorAll('.notification-item');
            items.forEach(item => item.classList.remove('unread'));
        }

        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('.navbar-dropdown-toggle') && 
                !event.target.matches('.navbar-dropdown-toggle *') &&
                !event.target.matches('.notification-bell') &&
                !event.target.matches('.notification-bell *')) {
                var dropdowns = document.getElementsByClassName('navbar-dropdown-menu');
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
                document.getElementById('notificationDropdown').classList.remove('show');
            }
        }

        // Auto-load notifications on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadNotifications();
            // Refresh notifications every 60 seconds
            setInterval(loadNotifications, 60000);
        });

        // Existing functions
        function showLoading() {
            const loading = document.getElementById('loadingIndicator');
            if (loading) loading.style.display = 'block';
        }

        function hideLoading() {
            const loading = document.getElementById('loadingIndicator');
            if (loading) loading.style.display = 'none';
        }

        function formatCurrency(value) {
            if (value == null || value == undefined) return '0';
            return Number(value).toLocaleString('vi-VN', { style: 'currency', currency: 'VND' });
        }

        function formatNumber(number) {
            if (Number.isInteger(number)) {
                return number.toString();
            }
            return number.toFixed(2);
        }

        // ... rest of your existing JavaScript functions ...
        
        // Các function helper cần thiết cho xuất Excel
        async function loadAttendanceData(month, year) {
            try {
                const response = await fetch('http://localhost/doanqlns/index.php/api/chamcong');
                if (!response.ok) throw new Error(`Lỗi khi tải dữ liệu chấm công: ${response.status}`);
                const data = await response.json();
                if (!Array.isArray(data)) throw new Error('Dữ liệu chấm công không hợp lệ');

                // Lọc dữ liệu theo tháng/năm
                const filteredData = data.filter(record => {
                    const recordDate = new Date(record.ngay_lam_viec);
                    return recordDate.getMonth() + 1 === month && recordDate.getFullYear() === year;
                });

                // Tính tổng số ngày công cho mỗi nhân viên
                const attendanceByEmployee = {};
                filteredData.forEach(record => {
                    const id = record.id_nhan_vien;
                    if (!attendanceByEmployee[id]) {
                        attendanceByEmployee[id] = 0;
                    }
                    attendanceByEmployee[id]++;
                });

                return attendanceByEmployee;
            } catch (error) {
                console.error('Lỗi khi tải dữ liệu chấm công:', error);
                return {};
            }
        }

        async function loadBonusData(month, year) {
            try {
                const response = await fetch('http://localhost/doanqlns/index.php/api/thuong');
                if (!response.ok) throw new Error(`Lỗi khi tải dữ liệu thưởng: ${response.status}`);
                const data = await response.json();
                if (!Array.isArray(data)) throw new Error('Dữ liệu thưởng không hợp lệ');
                return data.filter(record => {
                    const recordDate = new Date(record.ngay);
                    return recordDate.getMonth() + 1 === month && recordDate.getFullYear() === year;
                });
            } catch (error) {
                console.error('Lỗi khi tải dữ liệu thưởng:', error);
                return [];
            }
        }

        function calculateTotalBonus(bonusData, userId, month, year) {
            const records = bonusData.filter(record => {
                const recordDate = new Date(record.ngay);
                return record.id_nhan_vien == userId && 
                       recordDate.getMonth() + 1 === month && 
                       recordDate.getFullYear() === year &&
                       (record.loai === 'thành tích cá nhân' || record.loai === 'thăng chức' || record.loai === 'nghỉ lễ');
            });

            const totalBonus = records.reduce((sum, record) => sum + (parseFloat(record.tien_thuong) || 0), 0);
            return totalBonus;
        }

        function calculateSalaryByDay(luongCoBan, soNgayCong, ngayCongQuyDinh = 26) {
            const luongTheoNgay = Math.round((luongCoBan / ngayCongQuyDinh) * soNgayCong);
            return luongTheoNgay;
        }

        function calculateBaoHiem(luongCoBan) {
            const bhxh = luongCoBan * 0.08;      // 8%
            const bhyt = luongCoBan * 0.015;     // 1.5%
            const bhtn = luongCoBan * 0.01;      // 1%
            const tongBaoHiem = bhxh + bhyt + bhtn;
            
            return {
                bhxh: Math.round(bhxh),
                bhyt: Math.round(bhyt),
                bhtn: Math.round(bhtn),
                tongBaoHiem: Math.round(tongBaoHiem)
            };
        }

        function calculateThueTNCN(tongThuNhap, tongKhauTruBH, soNguoiPhuThuoc = 0, phuCapKhac = 0) {
            const giamTruGiaCanh = 11000000 + (soNguoiPhuThuoc * 4400000);
            const phuCapComMienThue = Math.min(phuCapKhac, 730000);
            const phuCapKhacChiuThue = Math.max(0, phuCapKhac - phuCapComMienThue);
            const thuNhapChiuThue = Math.max(0, tongThuNhap - tongKhauTruBH - giamTruGiaCanh - phuCapComMienThue);
            
            if (thuNhapChiuThue <= 0) return 0;
            
            let thue = 0;
            if (thuNhapChiuThue <= 5000000) {
                thue = thuNhapChiuThue * 0.05;
            } else if (thuNhapChiuThue <= 10000000) {
                thue = 250000 + (thuNhapChiuThue - 5000000) * 0.10;
            } else if (thuNhapChiuThue <= 18000000) {
                thue = 750000 + (thuNhapChiuThue - 10000000) * 0.15;
            } else if (thuNhapChiuThue <= 32000000) {
                thue = 1950000 + (thuNhapChiuThue - 18000000) * 0.20;
            } else if (thuNhapChiuThue <= 52000000) {
                thue = 4750000 + (thuNhapChiuThue - 32000000) * 0.25;
            } else if (thuNhapChiuThue <= 80000000) {
                thue = 9750000 + (thuNhapChiuThue - 52000000) * 0.30;
            } else {
                thue = 18150000 + (thuNhapChiuThue - 80000000) * 0.35;
            }
            
            return Math.round(thue);
        }

        function calculateNetSalary(tongThuNhap, tongKhauTruBH, thueTNCN, cacKhoanTruKhac = 0) {
            return tongThuNhap - tongKhauTruBH - thueTNCN - cacKhoanTruKhac;
        }
        
        // Hàm xuất Excel Lương (giống như trong luong.php)
        async function exportPayrollToExcel() {
            const currentDate = new Date();
            const month = currentDate.getMonth() + 1; // Tháng hiện tại
            const year = currentDate.getFullYear();
            
            showLoading();
            try {
                // Lấy dữ liệu lương từ API
                const response = await fetch(`http://localhost/doanqlns/index.php/api/luong?thang=${month}&nam=${year}`);
                if (!response.ok) {
                    throw new Error(`Lỗi khi tải dữ liệu lương: ${response.status}`);
                }
                
                const luongData = await response.json();
                
                if (!Array.isArray(luongData) || luongData.length === 0) {
                    throw new Error(`Không có dữ liệu lương cho tháng ${month}/${year}`);
                }
                
                // Lấy dữ liệu chấm công và thưởng
                const attendanceByEmployee = await loadAttendanceData(month, year);
                const bonusData = await loadBonusData(month, year);
                
                // Chuẩn bị dữ liệu CSV với đầy đủ thông tin như trong luong.php
                const headers = [
                    'ID Nhân Viên',
                    'Mã Lương',
                    'Tên Nhân Viên',
                    'Tháng',
                    'Số Ngày Công',
                    'Số Ngày Nghỉ Phép',
                    'Số Ngày Nghỉ Không Phép',
                    'Lương Cơ Bản',
                    'Lương Theo Ngày',
                    'Phụ Cấp Chức Vụ',
                    'Phụ Cấp Bằng Cấp',
                    'Phụ Cấp Khác',
                    'Tiền Thưởng',
                    'Số Người Phụ Thuộc',
                    'Thu Nhập Trước Thuế',
                    'BHXH NV',
                    'BHYT NV',
                    'BHTN NV',
                    'BHXH CTY',
                    'BHYT CTY',
                    'BHTN CTY',
                    'Giảm Trừ Gia Cảnh',
                    'Thu Nhập Chịu Thuế',
                    'Thuế TNCN',
                    'Các Khoản Trừ Khác',
                    'Tổng Các Khoản Trừ',
                    'Lương Thực Nhận',
                    'Trạng Thái'
                ];
                
                const csvRows = [headers.map(header => `"${header}"`).join(',')];
                
                luongData.forEach(record => {
                    const adjustedBasicSalary = parseFloat(record.luong_co_ban) || 0;
                    const totalBonus = calculateTotalBonus(bonusData, record.id_nhan_vien, month, year);
                    const phuCapChucVu = parseFloat(record.phu_cap_chuc_vu) || 0;
                    const phuCapBangCap = parseFloat(record.phu_cap_bang_cap) || 0;
                    const phuCapKhac = parseFloat(record.phu_cap_khac) || 0;
                    const soNgayCong = attendanceByEmployee[record.id_nhan_vien] || 0;
                    const soNguoiPhuThuoc = parseInt(record.so_nguoi_phu_thuoc) || 0;
                    
                    // Tính lương theo ngày
                    const luongTheoNgay = calculateSalaryByDay(adjustedBasicSalary, soNgayCong, 26);
                    
                    // Sử dụng trực tiếp dữ liệu đã prorate từ database
                    const phuCapChucVuTheoNgay = phuCapChucVu;
                    const phuCapComTheoNgay = phuCapKhac;
                    
                    // Tính tổng thu nhập theo công thức mới (dựa trên lương theo ngày)
                    const tongThuNhap = luongTheoNgay + phuCapChucVuTheoNgay + phuCapBangCap + phuCapComTheoNgay + totalBonus;
                    
                    // Tính bảo hiểm nhân viên theo lương theo ngày (đã prorate)
                    const baoHiemData = calculateBaoHiem(luongTheoNgay);
                    
                    // Tính thuế TNCN theo công thức mới (bao gồm xử lý phụ cấp cơm)
                    const thueTNCN = calculateThueTNCN(tongThuNhap, baoHiemData.tongBaoHiem, soNguoiPhuThuoc, phuCapComTheoNgay);
                    
                    // Lấy các khoản trừ khác từ database
                    const cacKhoanTruKhac = parseFloat(record.cac_khoan_tru_khac) || 0;
                    
                    // Tính lương Net theo công thức mới
                    const luongNet = calculateNetSalary(tongThuNhap, baoHiemData.tongBaoHiem, thueTNCN, cacKhoanTruKhac);
                    
                    // Tổng các khoản trừ = BH + Thuế TNCN + Các khoản trừ khác
                    const tongCacKhoanTru = baoHiemData.tongBaoHiem + thueTNCN + cacKhoanTruKhac;

                    const row = [
                        record.id_nhan_vien || '',
                        `L${record.id_nhan_vien}_${record.thang || `${month}/${year}`}`,
                        record.ho_ten || '',
                        record.thang || `${month}/${year}`,
                        formatNumber(soNgayCong),
                        formatNumber(record.so_ngay_nghi_phep || 0),
                        formatNumber(record.so_ngay_nghi_khong_phep || 0),
                        adjustedBasicSalary.toLocaleString('vi-VN'),
                        luongTheoNgay.toLocaleString('vi-VN'),
                        phuCapChucVuTheoNgay.toLocaleString('vi-VN'),
                        phuCapBangCap.toLocaleString('vi-VN'),
                        phuCapComTheoNgay.toLocaleString('vi-VN'),
                        totalBonus.toLocaleString('vi-VN'),
                        record.so_nguoi_phu_thuoc || 0,
                        tongThuNhap.toLocaleString('vi-VN'),
                        baoHiemData.bhxh.toLocaleString('vi-VN'),
                        baoHiemData.bhyt.toLocaleString('vi-VN'),
                        baoHiemData.bhtn.toLocaleString('vi-VN'),
                        (record.bhxh_cty || 0).toLocaleString('vi-VN'),
                        (record.bhyt_cty || 0).toLocaleString('vi-VN'),
                        (record.bhtn_cty || 0).toLocaleString('vi-VN'),
                        (11000000 + (soNguoiPhuThuoc * 4400000)).toLocaleString('vi-VN'),
                        Math.max(0, tongThuNhap - baoHiemData.tongBaoHiem - (11000000 + (soNguoiPhuThuoc * 4400000))).toLocaleString('vi-VN'),
                        thueTNCN.toLocaleString('vi-VN'),
                        cacKhoanTruKhac.toLocaleString('vi-VN'),
                        tongCacKhoanTru.toLocaleString('vi-VN'),
                        luongNet.toLocaleString('vi-VN'),
                        record.trang_thai || ''
                    ].map(value => `"${value.toString().replace(/"/g, '""')}"`);
                    
                    csvRows.push(row.join(','));
                });
                
                // Tạo nội dung CSV với BOM để hỗ trợ tiếng Việt
                const csvContent = '\uFEFF' + csvRows.join('\n');
                
                // Tạo Blob và tải xuống
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.setAttribute('href', url);
                link.setAttribute('download', `BangLuong_Thang${month}_${year}.csv`);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
                
                alert('Xuất Excel Lương thành công!');
            } catch (error) {
                console.error('Lỗi khi xuất Excel Lương:', error);
                alert('Lỗi khi xuất Excel Lương: ' + error.message);
            } finally {
                hideLoading();
            }
        }
        
        // Hàm xuất Excel Chấm Công
        async function exportAttendanceToExcel() {
            const currentDate = new Date();
            const month = currentDate.getMonth() + 1; // Tháng hiện tại
            const year = currentDate.getFullYear();
            
            showLoading();
            try {
                // Lấy dữ liệu chấm công từ API
                const response = await fetch(`http://localhost/doanqlns/index.php/api/chamcong?thang=${month}&nam=${year}`);
                if (!response.ok) {
                    throw new Error(`Lỗi khi tải dữ liệu chấm công: ${response.status}`);
                }
                
                const attendanceData = await response.json();
                
                if (!Array.isArray(attendanceData) || attendanceData.length === 0) {
                    throw new Error(`Không có dữ liệu chấm công cho tháng ${month}/${year}`);
                }
                
                // Lấy danh sách nhân viên
                const usersResponse = await fetch('http://localhost/doanqlns/index.php/api/users');
                if (!usersResponse.ok) {
                    throw new Error(`Lỗi khi tải danh sách nhân viên: ${usersResponse.status}`);
                }
                
                const usersData = await usersResponse.json();
                
                // Tạo map để truy cập nhanh dữ liệu chấm công theo nhân viên và ngày
                const attendanceMap = {};
                attendanceData.forEach(record => {
                    const key = `${record.id_nhan_vien}_${record.ngay_lam_viec}`;
                    attendanceMap[key] = record;
                });
                
                // Chuẩn bị dữ liệu CSV
                const daysInMonth = new Date(year, month, 0).getDate();
                const headers = [
                    'ID Nhân Viên',
                    'Họ Tên',
                    'Phòng Ban',
                    ...Array.from({ length: daysInMonth }, (_, i) => `Ngày ${i + 1}`),
                    'Tổng ngày đi làm',
                    'Tổng ngày nghỉ'
                ];
                
                const csvRows = [headers.map(header => `"${header}"`).join(',')];
                
                usersData.forEach(user => {
                    const row = [
                        user.id_nhan_vien,
                        user.ho_ten,
                        user.ten_phong_ban || ''
                    ];
                    
                    let totalWorkDays = 0;
                    let totalAbsentDays = 0;
                    
                    // Thêm trạng thái cho từng ngày
                    for (let day = 1; day <= daysInMonth; day++) {
                        const dateStr = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
                        const key = `${user.id_nhan_vien}_${dateStr}`;
                        const attendanceRecord = attendanceMap[key];
                        
                        let status = 'Chưa điểm danh';
                        if (attendanceRecord) {
                            status = attendanceRecord.trang_thai || 'Đã điểm danh';
                            if (status === 'Đã điểm danh') {
                                totalWorkDays++;
                            } else if (status === 'Nghỉ') {
                                totalAbsentDays++;
                            }
                        }
                        
                        row.push(status);
                    }
                    
                    // Thêm tổng kết
                    row.push(totalWorkDays);
                    row.push(totalAbsentDays);
                    
                    csvRows.push(row.map(value => `"${value.toString().replace(/"/g, '""')}"`).join(','));
                });
                
                // Tạo nội dung CSV với BOM để hỗ trợ tiếng Việt
                const csvContent = '\uFEFF' + csvRows.join('\n');
                
                // Tạo Blob và tải xuống
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.setAttribute('href', url);
                link.setAttribute('download', `BangChamCong_Thang${month}_${year}.csv`);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
                
                alert('Xuất Excel Chấm Công thành công!');
            } catch (error) {
                console.error('Lỗi khi xuất Excel Chấm Công:', error);
                alert('Lỗi khi xuất Excel Chấm Công: ' + error.message);
            } finally {
                hideLoading();
            }
        }
        
        async function openEmployeeModal() {
            console.log('Opening employee modal...');
            try {
                const response = await fetch('http://localhost/doanqlns/index.php/api/users');
                if (!response.ok) {
                    throw new Error(`Lỗi khi tải danh sách nhân viên: ${response.status}`);
                }
                const usersData = await response.json();
                console.log('Users data:', usersData);

                if (!Array.isArray(usersData)) {
                    throw new Error('Dữ liệu nhân viên không hợp lệ, không phải mảng');
                }

                if (usersData.length === 0) {
                    alert('Không có nhân viên nào trong hệ thống.');
                    return;
                }

                const employeeSelect = document.getElementById('employeeSelect');
                employeeSelect.innerHTML = '<option value="">-- Chọn nhân viên --</option>';
                usersData.forEach(user => {
                    if (user.id_nhan_vien && user.ho_ten) {
                        const option = document.createElement('option');
                        option.value = user.id_nhan_vien;
                        option.textContent = user.ho_ten;
                        employeeSelect.appendChild(option);
                    }
                });

                document.getElementById('employeeModal').classList.add('show');
            } catch (error) {
                console.error('Lỗi khi tải danh sách nhân viên:', error);
                alert('Lỗi khi tải danh sách nhân viên: ' + error.message);
            }
        }

        function closeEmployeeModal() {
            document.getElementById('employeeModal').classList.remove('show');
        }

        function confirmEmployeeSelection() {
            const employeeSelect = document.getElementById('employeeSelect');
            const selectedEmployeeId = employeeSelect.value;

            // Lưu ID nhân viên được chọn (có thể là rỗng)
            window.selectedEmployeeId = selectedEmployeeId;

            closeEmployeeModal();
            document.getElementById('fileInput').click();
        }

        function triggerFileUpload() {
            openEmployeeModal();
        }

        // Function để tải file mẫu Excel
        function downloadTemplate() {
            // Tạo dữ liệu CSV mẫu với đầy đủ thông tin
            const headers = [
                'ho_ten', 'gioi_tinh', 'ngay_sinh', 'email', 'so_dien_thoai', 'dia_chi',
                'can_cuoc_cong_dan', 'ngay_cap', 'noi_cap', 'que_quan', 'hinh_anh',
                'id_phong_ban', 'id_chuc_vu', 'loai_hop_dong', 'luong_co_ban', 'ngay_vao_lam',
                'ngay_nghi_viec', 'trang_thai', 'so_nguoi_phu_thuoc', 'tinh_trang_hon_nhan',
                'phu_cap_chuc_vu', 'phu_cap_bang_cap', 'phu_cap_khac', 'so_bhxh', 'so_bhyt',
                'so_bhtn', 'ngay_tham_gia_bhxh', 'so_tai_khoan', 'ten_ngan_hang', 'chi_nhanh_ngan_hang'
            ];

            // Dữ liệu mẫu với 2 nhân viên
            const sampleData = [
                [
                    'Nguyễn Văn A', 'Nam', '1990-01-15', 'nguyenvana@example.com', '0901234567',
                    '123 Đường ABC, Quận 1, TP.HCM', '123456789012', '2020-01-15', 'CA TP.HCM',
                    'Hà Nội', 'https://drive.google.com/open?id=1qJVXxmdSDc3kWmI0OR1MBdqfLXH4AEtN',
                    '1', '1', 'Toàn thời gian', '15000000', '2020-01-01', '', 'Đang làm việc',
                    '1', 'Đã kết hôn', '2000000', '1000000', '500000', '1234567890',
                    '1234567891', '1234567892', '2020-01-01', '1234567890123456', 'Vietcombank', 'Chi nhánh TP.HCM'
                ],
                [
                    'Trần Thị B', 'Nữ', '1995-05-20', 'tranthib@example.com', '0901234568',
                    '456 Đường XYZ, Quận 2, TP.HCM', '987654321098', '2021-05-20', 'CA TP.HCM',
                    'Đà Nẵng', 'https://drive.google.com/open?id=1qJVXxmdSDc3kWmI0OR1MBdqfLXH4AEtN',
                    '2', '2', 'Toàn thời gian', '12000000', '2021-06-01', '', 'Đang làm việc',
                    '0', 'Độc thân', '1500000', '800000', '300000', '9876543210',
                    '9876543211', '9876543212', '2021-06-01', '9876543210987654', 'Agribank', 'Chi nhánh Quận 2'
                ]
            ];

            // Tạo nội dung CSV
            const csvContent = [
                headers.map(header => `"${header}"`).join(','),
                ...sampleData.map(row => row.map(cell => `"${cell}"`).join(','))
            ].join('\n');

            // Thêm BOM để hỗ trợ tiếng Việt
            const csvWithBOM = '\uFEFF' + csvContent;

            // Tạo và tải file
            const blob = new Blob([csvWithBOM], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.setAttribute('href', url);
            link.setAttribute('download', 'Mau_Nhan_Vien_Du_Lieu.csv');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);

            // Hiển thị thông báo
            alert('✅ Đã tải file mẫu thành công!\n\nFile chứa đầy đủ các trường thông tin:\n- Thông tin cơ bản\n- Thông tin CCCD\n- Thông tin phòng ban, chức vụ\n- Thông tin lương và phụ cấp\n- Thông tin bảo hiểm\n- Thông tin ngân hàng\n\nVui lòng mở file và điền thông tin nhân viên mới.');
        }


        async function handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

        if (!file.name.endsWith('.csv') && !file.name.endsWith('.xlsx')) {
            alert('Vui lòng chọn file .csv hoặc .xlsx!');
            event.target.value = '';
            return;
        }

            const selectedEmployeeId = window.selectedEmployeeId || '';

            showLoading();
            try {
                const formData = new FormData();
                formData.append('csvFile', file);
                formData.append('employeeId', selectedEmployeeId);

                const response = await fetch('http://localhost/doanqlns/index.php/api/import_employees', {
                    method: 'POST',
                    body: formData
                });

                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                const responseText = await response.text();
                console.log('Response text:', responseText);
                
                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (e) {
                    console.error('JSON parse error:', e);
                    throw new Error('Lỗi khi phân tích phản hồi từ server: ' + responseText);
                }

                if (!response.ok) {
                    throw new Error('Lỗi khi tải file lên server: ' + response.status);
                }

                if (result.success) {
                    let message = '';
                    if (selectedEmployeeId) {
                        message = 'Cập nhật hồ sơ nhân viên thành công!';
                    } else {
                        message = 'Thêm nhân viên mới thành công!';
                    }
                    
                    // Thêm thông tin về hình ảnh nếu có
                    if (result.image_downloaded) {
                        message += '\n\n✅ Hình ảnh đã được tải từ Google Drive và lưu thành công!';
                    } else if (result.has_google_drive_link) {
                        message += '\n\n⚠️ Phát hiện Google Drive link nhưng chưa đăng nhập Gmail.\nVui lòng đăng nhập Gmail trước để tải hình ảnh tự động.';
                    }
                    
                    alert(message);
                } else {
                    alert('Lỗi khi xử lý hồ sơ: ' + result.message);
                }
            } catch (error) {
                console.error('Lỗi khi xử lý file:', error);
                alert('Lỗi khi nhập file: ' + error.message);
            } finally {
                hideLoading();
                event.target.value = '';
                window.selectedEmployeeId = ''; // Reset
            }
        }

        
    </script>
 
 <?php include(__DIR__ . '../includes/footer.php'); ?>

</body>
</html>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('btnSendMaternityEmails');
    if (!btn) return;
    btn.addEventListener('click', async function() {
        btn.disabled = true;
        const old = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
        try {
            const resp = await fetch('/doanqlns/api/send_maternity_notifications.php', { method: 'POST' });
            const json = await resp.json();
            if (json && json.success) {
                const total = (json.counts.ending_soon || 0) + (json.counts.overdue || 0);
                const sent = json.counts.sent || 0;
                const errs = json.counts.errors || 0;
                if (errs > 0) {
                    alert(`Gửi email xong: Thành công ${sent}/${total}, Lỗi ${errs}.`);
                } else {
                    alert(`Gửi email thành công: ${sent}/${total}.`);
                }
            } else {
                alert('Gửi email thất bại: ' + (json && json.message ? json.message : 'Không rõ nguyên nhân'));
            }
        } catch (e) {
            alert('Lỗi kết nối khi gửi email');
        } finally {
            btn.disabled = false;
            btn.innerHTML = old;
        }
    });
});
</script>