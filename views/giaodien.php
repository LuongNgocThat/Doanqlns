<?php
require_once __DIR__ . '/../includes/check_login.php';
include(__DIR__ . '/../includes/header.php');
include(__DIR__ . '/../includes/sidebar.php');

// Kiểm tra quyền người dùng
$is_admin = isset($_SESSION['quyen_them']) && $_SESSION['quyen_them'] && 
            isset($_SESSION['quyen_sua']) && $_SESSION['quyen_sua'] && 
            isset($_SESSION['quyen_xoa']) && $_SESSION['quyen_xoa'];

$is_manager = isset($_SESSION['quyen_them']) && $_SESSION['quyen_them'] && 
              isset($_SESSION['quyen_sua']) && $_SESSION['quyen_sua'];

if ($is_admin || $is_manager) {
    // Chuyển hướng đến trang chủ admin/manager
    header("Location: /doanqlns/giaodien.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRM Pro - Trang Chủ</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .main-content {
            padding: 20px;
        }

        .welcome-image {
            width: 100%;
            max-width: 1200px;
            height: auto;
            display: block;
            margin: 0 auto;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .welcome-container {
            text-align: center;
            padding: 20px;
        }

        .welcome-title {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="welcome-container">
            <h1 class="welcome-title">Chào mừng đến với HRM Pro</h1>
            <img src="../img/giaodien.jpg" alt="Giao diện chào mừng" class="welcome-image">
        </div>
    </div>

    <?php include(__DIR__ . '/../includes/footer.php'); ?>
</body>
</html> 