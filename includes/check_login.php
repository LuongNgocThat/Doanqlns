<?php
session_start();
require_once __DIR__ . '/../config/Database.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/doanqlns/views/danhgia.php';
    if (php_sapi_name() !== 'cli') {
        header("Location: /doanqlns/views/login.php");
        exit();
    }
}

$database = new Database();
$db = $database->getConnection();
$query = "SELECT quyen_them, quyen_sua, quyen_xoa, id_nhan_vien FROM nguoi_dung WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $_SESSION['quyen_them'] = $user['quyen_them'];
    $_SESSION['quyen_sua'] = $user['quyen_sua'];
    $_SESSION['quyen_xoa'] = $user['quyen_xoa'];
    $_SESSION['id_nhan_vien'] = $user['id_nhan_vien'];
    
    // Lấy thông tin nhân viên từ bảng nhan_vien
    if ($user['id_nhan_vien']) {
        $query_nv = "SELECT ho_ten FROM nhan_vien WHERE id_nhan_vien = :id_nhan_vien";
        $stmt_nv = $db->prepare($query_nv);
        $stmt_nv->bindParam(':id_nhan_vien', $user['id_nhan_vien']);
        $stmt_nv->execute();
        $nhan_vien = $stmt_nv->fetch(PDO::FETCH_ASSOC);
        
        if ($nhan_vien) {
            $_SESSION['ho_ten'] = $nhan_vien['ho_ten'];
        }
    }
} else {
    header("Location: /doanqlns/views/logout.php");
    exit();
}
?>