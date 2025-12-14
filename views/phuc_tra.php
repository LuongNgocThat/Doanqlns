<?php
session_start();
require_once '../config/Database.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Kiểm tra quyền admin
$is_admin = isset($_SESSION['quyen_them']) && $_SESSION['quyen_them'] && 
           isset($_SESSION['quyen_sua']) && $_SESSION['quyen_sua'] && 
           isset($_SESSION['quyen_xoa']) && $_SESSION['quyen_xoa'];

if (!$is_admin) {
    header('Location: giaodien_nhanvien.php');
    exit();
}

$database = new Database();
$conn = $database->getConnection();

// Xử lý thêm phúc tra mới
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'add_phuc_tra') {
    try {
        $id_nhan_vien = $_POST['id_nhan_vien'];
        $ngay = $_POST['ngay'];
        $buoi = $_POST['buoi'];
        $ly_do_phuc_tra = $_POST['ly_do_phuc_tra'];
        $minh_chung = $_POST['minh_chung'] ?? '';
        $nguoi_xac_nhan = $_POST['nguoi_xac_nhan'] ?? '';
        $trang_thai = 'Đang chờ';
        $ghi_chu = $_POST['ghi_chu'] ?? '';

        // Lấy thông tin nhân viên từ bảng nhan_vien
        $sql_nv = "SELECT ho_ten FROM nhan_vien WHERE id_nhan_vien = ?";
        $stmt_nv = $conn->prepare($sql_nv);
        $stmt_nv->execute([$id_nhan_vien]);
        $nhan_vien = $stmt_nv->fetch(PDO::FETCH_ASSOC);
        
        if (!$nhan_vien) {
            throw new Exception("Không tìm thấy thông tin nhân viên");
        }

        $ma_nv = str_pad($id_nhan_vien, 3, '0', STR_PAD_LEFT); // Tạo mã NV từ ID
        $ho_ten = $nhan_vien['ho_ten'];

        $sql = "INSERT INTO phuc_tra (ma_nv, ho_ten, ngay, buoi, ly_do_phuc_tra, minh_chung, nguoi_xac_nhan, trang_thai, ghi_chu) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([$ma_nv, $ho_ten, $ngay, $buoi, $ly_do_phuc_tra, $minh_chung, $nguoi_xac_nhan, $trang_thai, $ghi_chu]);
        
        if ($result) {
            $success_message = "Thêm phúc tra thành công!";
        } else {
            $error_message = "Lỗi khi thêm phúc tra!";
        }
    } catch (Exception $e) {
        $error_message = "Lỗi: " . $e->getMessage();
    }
}

// Xử lý cập nhật trạng thái
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    try {
        $id = $_POST['id'];
        $trang_thai = $_POST['trang_thai'];

        $sql = "UPDATE phuc_tra SET trang_thai = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([$trang_thai, $id]);
        
        if ($result) {
            // Nếu trạng thái là "Đã duyệt", cập nhật vào bảng cham_cong
            if ($trang_thai === 'Đã duyệt') {
                // Lấy thông tin phúc tra
                $sql_phuc_tra = "SELECT * FROM phuc_tra WHERE id = ?";
                $stmt_phuc_tra = $conn->prepare($sql_phuc_tra);
                $stmt_phuc_tra->execute([$id]);
                $phuc_tra_info = $stmt_phuc_tra->fetch(PDO::FETCH_ASSOC);
                
                if ($phuc_tra_info) {
                    $id_nhan_vien = $phuc_tra_info['ma_nv']; // Lấy mã NV từ phuc_tra
                    $ngay_lam_viec = $phuc_tra_info['ngay'];
                    $buoi = $phuc_tra_info['buoi'];
                    
                    // Xác định thời gian điểm danh dựa trên buổi
                    $gio_vao = null;
                    $gio_trua = null;
                    $gio_ra = null;
                    $trang_thai_sang = null;
                    $trang_thai_trua = null;
                    $trang_thai_chieu = null;
                    
                    switch ($buoi) {
                        case 'Sáng':
                            $gio_vao = '08:16:00'; // Thời gian mặc định cho ca sáng
                            $trang_thai_sang = 'Đi trễ';
                            break;
                        case 'Trưa':
                            $gio_trua = '13:01:00'; // Thời gian mặc định cho ca trưa
                            $trang_thai_trua = 'Đi trễ';
                            break;
                        case 'Chiều':
                            $gio_ra = '15:59:00'; // Thời gian mặc định cho ca chiều
                            $trang_thai_chieu = 'Đi trễ';
                            break;
                    }
                    
                    // Kiểm tra xem đã có bản ghi chấm công cho ngày này chưa
                    $sql_check = "SELECT id_cham_cong FROM cham_cong WHERE id_nhan_vien = ? AND ngay_lam_viec = ?";
                    $stmt_check = $conn->prepare($sql_check);
                    $stmt_check->execute([$id_nhan_vien, $ngay_lam_viec]);
                    $existing_record = $stmt_check->fetch(PDO::FETCH_ASSOC);
                    
                    if ($existing_record) {
                        // Cập nhật bản ghi đã tồn tại
                        $sql_update = "UPDATE cham_cong SET 
                            gio_vao = COALESCE(?, gio_vao),
                            gio_trua = COALESCE(?, gio_trua), 
                            gio_ra = COALESCE(?, gio_ra),
                            trang_thai_sang = COALESCE(?, trang_thai_sang),
                            trang_thai_trua = COALESCE(?, trang_thai_trua),
                            trang_thai_chieu = COALESCE(?, trang_thai_chieu),
                            trang_thai = 'Đi trễ'
                            WHERE id_nhan_vien = ? AND ngay_lam_viec = ?";
                        $stmt_update = $conn->prepare($sql_update);
                        $stmt_update->execute([
                            $gio_vao, $gio_trua, $gio_ra,
                            $trang_thai_sang, $trang_thai_trua, $trang_thai_chieu,
                            $id_nhan_vien, $ngay_lam_viec
                        ]);
                    } else {
                        // Tạo bản ghi mới
                        $sql_insert = "INSERT INTO cham_cong (id_nhan_vien, ngay_lam_viec, gio_vao, gio_trua, gio_ra, trang_thai_sang, trang_thai_trua, trang_thai_chieu, trang_thai) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt_insert = $conn->prepare($sql_insert);
                        $stmt_insert->execute([
                            $id_nhan_vien, $ngay_lam_viec, $gio_vao, $gio_trua, $gio_ra,
                            $trang_thai_sang, $trang_thai_trua, $trang_thai_chieu, 'Đi trễ'
                        ]);
                    }
                }
            }
            
            $success_message = "Cập nhật trạng thái thành công!";
        } else {
            $error_message = "Lỗi khi cập nhật trạng thái!";
        }
    } catch (Exception $e) {
        $error_message = "Lỗi: " . $e->getMessage();
    }
}

// Lấy danh sách nhân viên
$sql_nv = "SELECT id_nhan_vien, ho_ten FROM nhan_vien ORDER BY ho_ten";
$stmt_nv = $conn->prepare($sql_nv);
$stmt_nv->execute();
$nhan_vien_list = $stmt_nv->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách phúc tra
$sql = "SELECT * FROM phuc_tra ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$phuc_tra_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi" class="light-style layout-navbar-fixed layout-menu-fixed">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRM Pro - Quản lý Phúc tra</title>

    <!-- Font Awesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- CSS chính -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
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

        .page-header {
            background: linear-gradient(90deg, #007bff,rgb(32, 124, 222));
            color: white;
            padding: 2rem;
            margin-bottom: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }

        .page-header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .page-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: opacity 0.2s, transform 0.2s;
            text-decoration: none;
        }

        .btn-primary {
            background-color: #007bff;
        }

        .btn-success {
            background-color: #28a745;
        }

        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-info {
            background-color: #17a2b8;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-dark {
            background-color: #343a40;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn-sm {
            padding: 4px 8px;
            font-size: 12px;
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

        th {
            background: linear-gradient(90deg,rgb(18, 132, 254),rgb(20, 130, 247));
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .status {
            padding: 4px 8px;
            border-radius: 4px;
            color: white;
            font-size: 12px;
            font-weight: 600;
        }

        .status-dangcho { 
            background-color: #ffc107;
            color: #212529;
        }
        .status-Đangcho { 
            background-color: #ffc107;
            color: #212529;
        }
        .status-daduyet { 
            background-color: #28a745; 
        }
        .status-Đaduyet { 
            background-color: #28a745; 
        }
        .status-tuchoi { 
            background-color: #dc3545; 
        }
        
        /* Fallback cho các trạng thái khác */
        

        /* Modal Container */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow: auto;
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Modal Content */
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 0;
            border: none;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        /* Modal Header */
        .modal-header {
            background: linear-gradient(90deg, #007bff, #0056b3);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .modal-close {
            color: white;
            font-size: 1.8rem;
            font-weight: bold;
            background: none;
            border: none;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .modal-close:hover {
            transform: scale(1.2);
            color: #f1f1f1;
        }

        /* Modal Body */
        .modal-body {
            padding: 20px;
            max-height: 60vh;
            overflow-y: auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
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
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 30px;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 500;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .close {
            color: white;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            background: none;
            border: none;
        }

        .close:hover {
            opacity: 0.7;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        @media (max-width: 768px) {
            .layout-page {
                padding-left: 0;
            }
            
            .page-header h1 {
                font-size: 2rem;
            }
            
            table {
                font-size: 12px;
            }
            
            th, td {
                padding: 10px 8px;
            }
            
            .btn-diemdanh, .btn-edit, .btn-delete, .btn-export {
                padding: 4px 8px;
                font-size: 12px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="layout-wrapper">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="layout-page">
            <div class="content-wrapper">
                
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= $success_message ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= $error_message ?>
                </div>
            <?php endif; ?>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2><i class="fas fa-list"></i> Danh sách Phúc tra</h2>
                    <button class="btn btn-success" onclick="openAddModal()">
                        <i class="fas fa-plus"></i> Thêm Phúc tra
                    </button>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Mã NV</th>
                            <th>Họ và Tên</th>
                            <th>Ngày</th>
                            <th>Buổi</th>
                            <th>Lý do phúc tra</th>
                            <th>Minh chứng</th>
                            <th>Trạng thái</th>
                            <th>Ghi chú</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($phuc_tra_list)): ?>
                            <tr>
                                <td colspan="10" style="text-align: center; padding: 40px; color: #666;">
                                    <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>
                                    Chưa có yêu cầu phúc tra nào
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($phuc_tra_list as $item): ?>
                                <tr>
                                    <td><?= $item['id'] ?></td>
                                    <td><?= htmlspecialchars($item['ma_nv']) ?></td>
                                    <td><?= htmlspecialchars($item['ho_ten']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($item['ngay'])) ?></td>
                                    <td><?= $item['buoi'] ?></td>
                                    <td><?= $item['ly_do_phuc_tra'] ?></td>
                                    <td>
                                        <?php if ($item['minh_chung']): ?>
                                            <a href="<?= htmlspecialchars($item['minh_chung']) ?>" target="_blank" class="btn btn-sm btn-info">
                                                <i class="fas fa-link"></i> Xem
                                            </a>
                                        <?php else: ?>
                                            <span style="color: #999;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($item['trang_thai']): ?>
                                            <?php 
                                            $trang_thai_class = strtolower(str_replace([' ', 'á', 'à', 'ả', 'ã', 'ạ', 'ă', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ', 'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ', 'é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ế', 'ề', 'ể', 'ễ', 'ệ', 'í', 'ì', 'ỉ', 'ĩ', 'ị', 'ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ', 'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ', 'ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'ứ', 'ừ', 'ử', 'ữ', 'ự', 'ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ', 'đ'], ['', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'y', 'y', 'y', 'y', 'y', 'd'], $item['trang_thai']));
                                            ?>
                                            <span class="status status-<?= $trang_thai_class ?>">
                                                <?= $item['trang_thai'] ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="color: #999;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($item['ghi_chu'] ?: '-') ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-primary btn-sm" onclick="openEditModal(<?= $item['id'] ?>, '<?= $item['trang_thai'] ?>')">
                                                <i class="fas fa-edit"></i> Sửa
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Thêm Phúc tra -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fas fa-plus"></i> Thêm Phúc tra</h3>
                <button class="modal-close" onclick="closeModal('addModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addForm" method="POST" novalidate>
                    <input type="hidden" name="action" value="add_phuc_tra">
                    
                    <div class="form-group">
                        <label for="id_nhan_vien">Chọn Nhân viên *</label>
                        <select id="id_nhan_vien" name="id_nhan_vien" required onchange="updateEmployeeInfo()">
                            <option value="">Chọn nhân viên</option>
                            <?php foreach ($nhan_vien_list as $nv): ?>
                                <option value="<?= $nv['id_nhan_vien'] ?>" data-ma-nv="<?= str_pad($nv['id_nhan_vien'], 3, '0', STR_PAD_LEFT) ?>" data-ho-ten="<?= htmlspecialchars($nv['ho_ten']) ?>">
                                    <?= htmlspecialchars($nv['ho_ten']) ?> (ID: <?= $nv['id_nhan_vien'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="ma_nv">Mã NV</label>
                        <input type="text" id="ma_nv" name="ma_nv" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="ho_ten">Họ và Tên</label>
                        <input type="text" id="ho_ten" name="ho_ten" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="ngay">Ngày *</label>
                        <input type="date" id="ngay" name="ngay" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="buoi">Buổi *</label>
                        <select id="buoi" name="buoi" required>
                            <option value="">Chọn buổi</option>
                            <option value="Sáng">Sáng</option>
                            <option value="Trưa">Trưa</option>
                            <option value="Chiều">Chiều</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="ly_do_phuc_tra">Lý do phúc tra *</label>
                        <select id="ly_do_phuc_tra" name="ly_do_phuc_tra" required>
                            <option value="">Chọn lý do</option>
                            <option value="Quên điểm danh">Quên điểm danh</option>
                            <option value="Máy hỏng">Máy hỏng</option>
                            <option value="Lý do khác">Lý do khác</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="minh_chung">Minh chứng</label>
                        <input type="url" id="minh_chung" name="minh_chung" placeholder="Link hoặc URL ảnh">
                    </div>
                    
                    <div class="form-group">
                        <label for="nguoi_xac_nhan">Người xác nhận</label>
                        <input type="text" id="nguoi_xac_nhan" name="nguoi_xac_nhan" placeholder="Tên người xác nhận">
                    </div>
                    
                    <div class="form-group">
                        <label for="ghi_chu">Ghi chú</label>
                        <textarea id="ghi_chu" name="ghi_chu" rows="3" placeholder="Thêm thông tin nếu cần"></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('addModal')">Hủy</button>
                        <button type="submit" class="btn btn-success">Thêm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Sửa Trạng thái -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fas fa-edit"></i> Cập nhật Trạng thái</h3>
                <button class="modal-close" onclick="closeModal('editModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST" novalidate>
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" id="edit_id" name="id">
                    
                    <div class="form-group">
                        <label for="edit_trang_thai">Trạng thái *</label>
                        <select id="edit_trang_thai" name="trang_thai" required>
                            <option value="Đang chờ">Đang chờ</option>
                            <option value="Đã duyệt">Đã duyệt</option>
                            <option value="Từ chối">Từ chối</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Hủy</button>
                        <button type="submit" class="btn btn-success">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Ngăn chặn thông báo "Trang mà bạn đang tìm sử dụng thông tin..."
        // Sử dụng history.replaceState để loại bỏ thông báo
        window.addEventListener('load', function() {
            // Thay thế state hiện tại để loại bỏ thông báo
            history.replaceState(null, null, window.location.href);
        });

        // Ngăn chặn thông báo khi rời khỏi trang
        window.addEventListener('beforeunload', function(e) {
            // Không hiển thị thông báo
            return undefined;
        });

        // Reset form khi đóng modal
        function resetForm(formId) {
            const form = document.getElementById(formId);
            if (form) {
                form.reset();
            }
        }

        function openAddModal() {
            // Reset form
            document.getElementById('id_nhan_vien').value = '';
            document.getElementById('ma_nv').value = '';
            document.getElementById('ho_ten').value = '';
            document.getElementById('ngay').value = '';
            document.getElementById('buoi').value = '';
            document.getElementById('ly_do_phuc_tra').value = '';
            document.getElementById('minh_chung').value = '';
            document.getElementById('nguoi_xac_nhan').value = '';
            document.getElementById('ghi_chu').value = '';
            
            document.getElementById('addModal').style.display = 'block';
        }

        function updateEmployeeInfo() {
            const select = document.getElementById('id_nhan_vien');
            const selectedOption = select.options[select.selectedIndex];
            
            if (selectedOption.value) {
                document.getElementById('ma_nv').value = selectedOption.getAttribute('data-ma-nv');
                document.getElementById('ho_ten').value = selectedOption.getAttribute('data-ho-ten');
            } else {
                document.getElementById('ma_nv').value = '';
                document.getElementById('ho_ten').value = '';
            }
        }

        function openEditModal(id, trang_thai) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_trang_thai').value = trang_thai;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
            
            // Reset form khi đóng modal
            if (modalId === 'addModal') {
                resetForm('addForm');
            } else if (modalId === 'editModal') {
                resetForm('editForm');
            }
        }

        // Đóng modal khi click bên ngoài
        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }
    </script>
            </div>
        </div>
    </div>
</body>
</html>
