<?php
/**
 * Network Debug Helper
 * File này giúp debug và test hệ thống kiểm tra mạng
 */

require_once 'network_check.php';

// Bắt đầu session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Xử lý cập nhật SSID từ client
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'update_ssid':
            if (isset($_POST['ssid'])) {
                $ssid = trim($_POST['ssid']);
                
                // Log để debug
                error_log("Network Debug - Received SSID update request: " . $ssid);
                
                if (updateClientSSID($ssid)) {
                    $response = ['success' => true, 'message' => 'SSID updated successfully: ' . $ssid];
                    error_log("Network Debug - SSID update successful: " . $ssid);
                } else {
                    $response = ['success' => false, 'message' => 'Failed to update SSID: ' . $ssid];
                    error_log("Network Debug - SSID update failed: " . $ssid);
                }
                
                // Trả về JSON response
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } else {
                $response = ['success' => false, 'message' => 'SSID parameter missing'];
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }
            break;
            
        case 'test_ip':
            if (isset($_POST['test_ip'])) {
                $test_ip = trim($_POST['test_ip']);
                if (filter_var($test_ip, FILTER_VALIDATE_IP)) {
                    $_SESSION['test_ip'] = $test_ip;
                    $response = ['success' => true, 'message' => 'Test IP set to: ' . $test_ip];
                } else {
                    $response = ['success' => false, 'message' => 'Invalid IP address'];
                }
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }
            break;
            
        case 'clear_test':
            unset($_SESSION['test_ip']);
            unset($_SESSION['client_ssid']);
            $response = ['success' => true, 'message' => 'Test data cleared'];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
            break;
    }
}

// Lấy thông tin mạng hiện tại
$network_info = getNetworkInfo();
$gate_info = networkGateInfo();
$can_show_button = canShowAttendanceButton();

// Xử lý ngôn ngữ
$lang = getPreferredLang();
if (isset($_GET['lang']) && in_array($_GET['lang'], ['vi', 'en'])) {
    $lang = $_GET['lang'];
}

// Xử lý hiển thị
$is_ajax = isset($_GET['ajax']) && $_GET['ajax'] === '1';
if ($is_ajax) {
    header('Content-Type: application/json');
    echo json_encode([
        'network_info' => $network_info,
        'gate_info' => $gate_info,
        'can_show_button' => $can_show_button,
        'lang' => $lang
    ]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Network Debug - <?php echo $lang === 'vi' ? 'Gỡ lỗi mạng' : 'Network Debug'; ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #007bff;
        }
        .status-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .status-ok {
            border-left: 4px solid #28a745;
            background: #d4edda;
        }
        .status-error {
            border-left: 4px solid #dc3545;
            background: #f8d7da;
        }
        .status-warning {
            border-left: 4px solid #ffc107;
            background: #fff3cd;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .info-item {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
            margin-bottom: 5px;
        }
        .info-value {
            color: #212529;
            font-family: monospace;
            word-break: break-all;
        }
        .button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
            font-size: 14px;
        }
        .button:hover {
            background: #0056b3;
        }
        .button.success {
            background: #28a745;
        }
        .button.danger {
            background: #dc3545;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
        }
        .language-switch {
            text-align: right;
            margin-bottom: 20px;
        }
        .language-switch a {
            margin-left: 10px;
            text-decoration: none;
            color: #007bff;
        }
        .refresh-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
            width: 50px;
            height: 50px;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <button class="refresh-btn" onclick="location.reload()" title="Refresh">🔄</button>
    
    <div class="container">
        <div class="header">
            <h1><?php echo $lang === 'vi' ? 'Gỡ lỗi hệ thống kiểm tra mạng' : 'Network Check System Debug'; ?></h1>
            <p><?php echo $lang === 'vi' ? 'Kiểm tra trạng thái mạng và SSID Wi-Fi' : 'Check network status and Wi-Fi SSID'; ?></p>
        </div>

        <div class="language-switch">
            <a href="?lang=vi">Tiếng Việt</a>
            <a href="?lang=en">English</a>
        </div>

        <!-- Trạng thái tổng quan -->
        <div class="status-card <?php echo $can_show_button ? 'status-ok' : 'status-error'; ?>">
            <h3><?php echo $lang === 'vi' ? 'Trạng thái điểm danh' : 'Attendance Status'; ?></h3>
            <p><strong><?php echo $gate_info['message']; ?></strong></p>
            <p>
                <?php echo $lang === 'vi' ? 'Có thể hiển thị nút điểm danh:' : 'Can show attendance button:'; ?>
                <span style="color: <?php echo $can_show_button ? '#28a745' : '#dc3545'; ?>; font-weight: bold;">
                    <?php echo $can_show_button ? '✓ CÓ' : '✗ KHÔNG'; ?>
                </span>
            </p>
        </div>

        <!-- Thông tin mạng -->
        <div class="info-grid">
            <div class="info-item">
                <h4><?php echo $lang === 'vi' ? 'Thông tin IP người dùng' : 'User IP Information'; ?></h4>
                <div class="info-label"><?php echo $lang === 'vi' ? 'IP hiện tại:' : 'Current IP:'; ?></div>
                <div class="info-value"><?php echo htmlspecialchars($network_info['user_ip']); ?></div>
                <div class="info-label"><?php echo $lang === 'vi' ? 'Thuộc mạng công ty:' : 'On company network:'; ?></div>
                <div class="info-value"><?php echo $network_info['is_company_network'] ? '✓ CÓ' : '✗ KHÔNG'; ?></div>
            </div>

            <div class="info-item">
                <h4><?php echo $lang === 'vi' ? 'Thông tin máy chủ' : 'Server Information'; ?></h4>
                <div class="info-label"><?php echo $lang === 'vi' ? 'IP máy chủ:' : 'Server IP:'; ?></div>
                <div class="info-value"><?php echo htmlspecialchars($network_info['server_ip']); ?></div>
                <div class="info-label"><?php echo $lang === 'vi' ? 'Máy chủ trên mạng công ty:' : 'Server on company network:'; ?></div>
                <div class="info-value"><?php echo $network_info['server_on_company_network'] ? '✓ CÓ' : '✗ KHÔNG'; ?></div>
            </div>

            <div class="info-item">
                <h4><?php echo $lang === 'vi' ? 'Thông tin SSID' : 'SSID Information'; ?></h4>
                <div class="info-label"><?php echo $lang === 'vi' ? 'SSID đã kiểm tra:' : 'SSID checked:'; ?></div>
                <div class="info-value"><?php echo $gate_info['ssid_checked'] ? '✓ CÓ' : '✗ KHÔNG'; ?></div>
                <div class="info-label"><?php echo $lang === 'vi' ? 'SSID hợp lệ:' : 'SSID valid:'; ?></div>
                <div class="info-value">
                    <?php 
                    if ($gate_info['ssid_ok'] === null) {
                        echo $lang === 'vi' ? 'Không thể kiểm tra' : 'Cannot check';
                    } elseif ($gate_info['ssid_ok']) {
                        echo '✓ CÓ';
                    } else {
                        echo '✗ KHÔNG';
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Form test -->
        <div class="status-card">
            <h3><?php echo $lang === 'vi' ? 'Test và Debug' : 'Test and Debug'; ?></h3>
            
            <div class="form-group">
                <label><?php echo $lang === 'vi' ? 'Test IP:' : 'Test IP:'; ?></label>
                <input type="text" id="test_ip" placeholder="192.168.0.100" value="<?php echo $_SESSION['test_ip'] ?? ''; ?>">
                <button class="button" onclick="setTestIP()"><?php echo $lang === 'vi' ? 'Đặt IP test' : 'Set Test IP'; ?></button>
            </div>

            <div class="form-group">
                <label><?php echo $lang === 'vi' ? 'Test SSID:' : 'Test SSID:'; ?></label>
                <input type="text" id="test_ssid" placeholder="CompanyWiFi" value="<?php echo $_SESSION['client_ssid'] ?? ''; ?>">
                <button class="button" onclick="setTestSSID()"><?php echo $lang === 'vi' ? 'Đặt SSID test' : 'Set Test SSID'; ?></button>
            </div>

            <button class="button danger" onclick="clearTest()"><?php echo $lang === 'vi' ? 'Xóa dữ liệu test' : 'Clear Test Data'; ?></button>
        </div>

        <!-- Thông tin chi tiết -->
        <div class="status-card">
            <h3><?php echo $lang === 'vi' ? 'Thông tin chi tiết' : 'Detailed Information'; ?></h3>
            <div class="info-item">
                <div class="info-label"><?php echo $lang === 'vi' ? 'User Agent:' : 'User Agent:'; ?></div>
                <div class="info-value"><?php echo htmlspecialchars($network_info['user_agent']); ?></div>
                
                <div class="info-label"><?php echo $lang === 'vi' ? 'Remote Addr:' : 'Remote Addr:'; ?></div>
                <div class="info-value"><?php echo htmlspecialchars($network_info['remote_addr']); ?></div>
                
                <div class="info-label"><?php echo $lang === 'vi' ? 'X-Forwarded-For:' : 'X-Forwarded-For:'; ?></div>
                <div class="info-value"><?php echo htmlspecialchars($network_info['http_x_forwarded_for']); ?></div>
                
                <div class="info-label"><?php echo $lang === 'vi' ? 'Client IP:' : 'Client IP:'; ?></div>
                <div class="info-value"><?php echo htmlspecialchars($network_info['http_client_ip']); ?></div>
            </div>
        </div>
    </div>

    <script>
        function setTestIP() {
            const ip = document.getElementById('test_ip').value;
            if (!ip) return;
            
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=test_ip&test_ip=${encodeURIComponent(ip)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }

        function setTestSSID() {
            const ssid = document.getElementById('test_ssid').value;
            if (!ssid) return;
            
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update_ssid&ssid=${encodeURIComponent(ssid)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }

        function clearTest() {
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=clear_test'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }

        // Auto-refresh every 30 seconds
        setInterval(() => {
            location.reload();
        }, 30000);
    </script>
</body>
</html>
