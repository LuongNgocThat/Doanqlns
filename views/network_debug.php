<?php
session_start();
require_once '../includes/check_login.php';
require_once '../includes/network_check.php';

// Xử lý test IP
if (isset($_POST['test_ip']) && !empty($_POST['test_ip'])) {
    $_SESSION['test_ip'] = $_POST['test_ip'];
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_POST['clear_test_ip'])) {
    unset($_SESSION['test_ip']);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Lấy thông tin mạng
$network_info = getNetworkInfo();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Network Debug - HRM Pro</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }
        .header h1 {
            color: #333;
            margin: 0;
        }
        .info-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .info-card h3 {
            color: #495057;
            margin-top: 0;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: 600;
            color: #495057;
        }
        .value {
            color: #6c757d;
            font-family: monospace;
        }
        .status {
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            text-align: center;
        }
        .status.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status.danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .back-btn:hover {
            background: #0056b3;
        }
        .network-details {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }
        .network-details h4 {
            color: #1976d2;
            margin-top: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-network-wired"></i> Network Debug Information</h1>
            <p>Thông tin kiểm tra mạng và WiFi công ty</p>
        </div>

        <div class="info-card">
            <h3><i class="fas fa-info-circle"></i> Thông tin mạng hiện tại</h3>
            
            <div class="info-row">
                <span class="label">IP của người dùng:</span>
                <span class="value"><?= htmlspecialchars($network_info['user_ip']) ?></span>
            </div>
            
            <div class="info-row">
                <span class="label">Trạng thái mạng công ty:</span>
                <span class="status <?= $network_info['is_company_network'] ? 'success' : 'danger' ?>">
                    <?= $network_info['is_company_network'] ? 'Đang ở mạng công ty' : 'Không ở mạng công ty' ?>
                </span>
            </div>
            
            <div class="info-row">
                <span class="label">Remote Address:</span>
                <span class="value"><?= htmlspecialchars($network_info['remote_addr']) ?></span>
            </div>
            
            <div class="info-row">
                <span class="label">X-Forwarded-For:</span>
                <span class="value"><?= htmlspecialchars($network_info['http_x_forwarded_for']) ?></span>
            </div>
            
            <div class="info-row">
                <span class="label">Client IP:</span>
                <span class="value"><?= htmlspecialchars($network_info['http_client_ip']) ?></span>
            </div>
            
            <div class="info-row">
                <span class="label">User Agent:</span>
                <span class="value"><?= htmlspecialchars($network_info['user_agent']) ?></span>
            </div>
        </div>

        <div class="network-details">
            <h4><i class="fas fa-cog"></i> Cấu hình mạng công ty</h4>
            <p><strong>Mạng được cấu hình:</strong> 192.168.99.0/24 (Subnet mask: 255.255.255.0)</p>
            <p><strong>Range IP:</strong> 192.168.99.1 - 192.168.99.254</p>
            <p><strong>Gateway:</strong> 192.168.99.1</p>
            <p><strong>Trạng thái:</strong> 
                <?php if ($network_info['is_company_network']): ?>
                    <span class="status success">✓ Nút "Điểm danh" sẽ hiển thị</span>
                <?php else: ?>
                    <span class="status danger">✗ Nút "Điểm danh" sẽ ẩn</span>
                <?php endif; ?>
            </p>
        </div>

        <div class="info-card">
            <h3><i class="fas fa-lightbulb"></i> Hướng dẫn</h3>
            <ul>
                <li>Nếu bạn đang ở mạng công ty (192.168.99.x), nút "Điểm danh" sẽ hiển thị trong sidebar</li>
                <li>Nếu bạn không ở mạng công ty, nút "Điểm danh" sẽ bị ẩn</li>
                <li>Để thêm mạng công ty khác, chỉnh sửa file <code>includes/network_check.php</code></li>
                <li>Trang này chỉ dành cho mục đích debug và kiểm tra</li>
            </ul>
        </div>

        <div class="info-card">
            <h3><i class="fas fa-tools"></i> Test với IP khác</h3>
            <p><strong>IP hiện tại đang test:</strong> 
                <?php if (isset($_SESSION['test_ip'])): ?>
                    <span class="status success"><?= htmlspecialchars($_SESSION['test_ip']) ?></span>
                <?php else: ?>
                    <span class="status danger">Không có IP test</span>
                <?php endif; ?>
            </p>
            
            <form method="POST" style="margin-top: 15px;">
                <div style="display: flex; gap: 10px; align-items: center;">
                    <input type="text" name="test_ip" placeholder="Nhập IP để test (VD: 192.168.99.239)" 
                           style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    <button type="submit" style="padding: 8px 16px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Test IP
                    </button>
                    <button type="submit" name="clear_test_ip" style="padding: 8px 16px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Xóa Test
                    </button>
                </div>
            </form>
            
            <div style="margin-top: 15px; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                <p><strong>IP để test:</strong></p>
                <ul style="margin: 5px 0;">
                    <li><code>192.168.99.239</code> - IP WiFi công ty của bạn (sẽ hiển thị nút điểm danh)</li>
                    
                </ul>
            </div>
        </div>

        <a href="/doanqlns/giaodien.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Quay lại Dashboard
        </a>
    </div>
</body>
</html>
