<?php
/**
 * Network Check Helper
 * Kiểm tra xem người dùng có đang kết nối với WiFi công ty hay không
 */

// Cấu hình dải mạng Wi‑Fi công ty (có thể thêm nhiều CIDR)
// Hỗ trợ cả IP cũ và mới của mạng HRM
$COMPANY_NETWORKS = [
    '192.168.0.0/24',    // Mạng gốc
    '192.168.1.0/24',    // NGOC THAT - 5G
    '192.168.112.0/24',  // HRM (IP cũ - 192.168.112.x)
    '10.152.144.0/24',   //  IPv4 Address HRM (IP mới - 10.152.144.x)
    '10.103.214.233',     // HRM (dải rộng hơn - 10.152.x.x)
    '172.16.0.150',  
];

// Cấu hình SSID Wi‑Fi công ty
$COMPANY_SSIDS = [
    'NGOC THAT',
    'NGOC THAT - 5G',
    'HRM',
];

/**
 * Tự động phát hiện mạng công ty dạng /24 dựa trên IP máy chủ nếu cần
 */
function autoDetectCompanyNetworks() {
    $configured = $GLOBALS['COMPANY_NETWORKS'] ?? [];
    if (!empty($configured)) return $configured;
    $serverIp = getServerIP();
    if (filter_var($serverIp, FILTER_VALIDATE_IP)) {
        $parts = explode('.', $serverIp);
        if (count($parts) === 4) {
            $cidr = $parts[0] . '.' . $parts[1] . '.' . $parts[2] . '.0/24';
            return [$cidr];
        }
    }
    return [];
}

/**
 * Ngôn ngữ ưu tiên: 'vi' hoặc 'en'
 */
function getPreferredLang() {
    if (isset($_GET['lang']) && in_array($_GET['lang'], ['vi', 'en'], true)) {
        return $_GET['lang'];
    }
    if (isset($_SESSION['lang']) && in_array($_SESSION['lang'], ['vi', 'en'], true)) {
        return $_SESSION['lang'];
    }
    return 'vi';
}

function t($key, $lang = 'vi') {
    $dict = [
        'vi' => [
            'server_not_company' => 'Máy chủ không kết nối đúng Wi‑Fi công ty.',
            'client_not_company' => 'Thiết bị của bạn không kết nối đúng Wi‑Fi công ty.',
            'both_ok' => 'Đang ở trong mạng Wi‑Fi công ty. Bạn có thể điểm danh.',
        ],
        'en' => [
            'server_not_company' => 'Server is not connected to the company Wi‑Fi.',
            'client_not_company' => 'Your device is not on the company Wi‑Fi.',
            'both_ok' => 'On company Wi‑Fi. You can check in.',
        ],
    ];
    return $dict[$lang][$key] ?? $key;
}

function isCompanyNetwork() {
    // Lấy IP của người dùng
    $user_ip = getUserIP();
    
    // Debug: Log IP để kiểm tra
    error_log("Network Check - User IP: " . $user_ip);
    
    // Kiểm tra nếu đang truy cập qua ngrok
    $is_ngrok = isset($_SERVER['HTTP_HOST']) && 
                (strpos($_SERVER['HTTP_HOST'], 'ngrok') !== false || 
                 strpos($_SERVER['HTTP_HOST'], 'ngrok-free.app') !== false);
    
    if ($is_ngrok) {
        error_log("Network Check - Detected ngrok access, allowing company network");
        return true; // Cho phép khi truy cập qua ngrok
    }
    
    // Tùy chọn test: Nếu có session test IP, sử dụng IP đó
    if (isset($_SESSION['test_ip']) && !empty($_SESSION['test_ip'])) {
        $user_ip = $_SESSION['test_ip'];
        error_log("Network Check - Using test IP from session: " . $user_ip);
    }
    
    // Tùy chọn test: Nếu có GET parameter test_ip, sử dụng IP đó
    if (isset($_GET['test_ip']) && !empty($_GET['test_ip'])) {
        $user_ip = $_GET['test_ip'];
        error_log("Network Check - Using test IP from GET parameter: " . $user_ip);
    }
    
    // Danh sách các mạng công ty (ưu tiên cấu hình, fallback tự động)
    $company_networks = $GLOBALS['COMPANY_NETWORKS'] ?? [];
    if (empty($company_networks)) {
        $company_networks = autoDetectCompanyNetworks();
    }
    
    // Kiểm tra xem IP có thuộc mạng công ty nào không
    foreach ($company_networks as $network) {
        if (ipInRange($user_ip, $network)) {
            error_log("Network Check - IP {$user_ip} thuộc mạng {$network}");
            return true;
        }
    }
    
    error_log("Network Check - IP {$user_ip} KHÔNG thuộc mạng công ty");
    return false;
}

/**
 * Lấy IP thực của người dùng
 */
function getUserIP() {
    // Debug: Log tất cả các header IP có sẵn
    error_log("Network Check - REMOTE_ADDR: " . ($_SERVER['REMOTE_ADDR'] ?? 'None'));
    error_log("Network Check - HTTP_X_FORWARDED_FOR: " . ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? 'None'));
    error_log("Network Check - HTTP_CLIENT_IP: " . ($_SERVER['HTTP_CLIENT_IP'] ?? 'None'));
    
    // Kiểm tra các header có thể chứa IP thực
    $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
    
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                error_log("Network Check - Checking IP from {$key}: {$ip}");
                
                // Nếu là localhost, bỏ qua
                if ($ip === '127.0.0.1' || $ip === '::1' || $ip === 'localhost') {
                    error_log("Network Check - Skipping localhost IP: {$ip}");
                    continue;
                }
                
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    error_log("Network Check - Found public IP: {$ip}");
                    return $ip;
                }
            }
        }
    }
    
    // Thử lấy IP thực từ network interface
    $real_ip = getRealNetworkIP();
    if ($real_ip) {
        error_log("Network Check - Found real IP from network interface: {$real_ip}");
        return $real_ip;
    }
    
    // Nếu không tìm thấy IP thực, trả về IP local
    $final_ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    error_log("Network Check - Final IP selected: {$final_ip}");
    return $final_ip;
}

/**
 * Lấy IP thực từ network interface (Windows)
 */
function getRealNetworkIP() {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        try {
            $output = shell_exec('ipconfig | findstr "IPv4"');
            if ($output) {
                preg_match_all('/\d+\.\d+\.\d+\.\d+/', $output, $matches);
                    foreach ($matches[0] as $ip) {
                    if ($ip !== '127.0.0.1' && $ip !== '::1' && !preg_match('/^169\.254\./', $ip)) {
                        error_log("Network Check - Found real IP from ipconfig: {$ip}");
                            return $ip;
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Network Check - Error getting real IP: " . $e->getMessage());
        }
    }
    return null;
}

/**
 * Lấy IP của máy chủ (desktop chạy XAMPP)
 */
function getServerIP() {
    // Thử lấy IP thực từ network interface trước
    $real_ip = getRealNetworkIP();
    if ($real_ip) {
        error_log("Network Check - Using real IP from network interface: {$real_ip}");
        return $real_ip;
    }
    
    // Fallback về cách cũ
    if (!empty($_SERVER['SERVER_ADDR'])) {
        return $_SERVER['SERVER_ADDR'];
    }
    $host = gethostname();
    $ip = gethostbyname($host);
    return $ip ?: '127.0.0.1';
}

/**
 * Kiểm tra máy chủ có nằm trong mạng công ty
 */
function isServerOnCompanyNetwork() {
    $server_ip = getServerIP();
    error_log("Network Check - Server IP: " . $server_ip);
    
    $company_networks = $GLOBALS['COMPANY_NETWORKS'] ?? [];
    if (empty($company_networks)) {
        $company_networks = autoDetectCompanyNetworks();
    }
    foreach ($company_networks as $network) {
        if (ipInRange($server_ip, $network)) {
            error_log("Network Check - SERVER {$server_ip} thuộc mạng {$network}");
            return true;
        }
    }
    error_log("Network Check - SERVER {$server_ip} KHÔNG thuộc mạng công ty");
    return false;
}

/**
 * Kiểm tra xem IP có thuộc range mạng hay không
 */
function ipInRange($ip, $range) {
    if (strpos($range, '/') === false) {
        return $ip === $range;
    }
    
    list($range, $netmask) = explode('/', $range, 2);
    $range_decimal = ip2long($range);
    $ip_decimal = ip2long($ip);
    $wildcard_decimal = pow(2, (32 - $netmask)) - 1;
    $netmask_decimal = ~$wildcard_decimal;
    
    return (($ip_decimal & $netmask_decimal) == ($range_decimal & $netmask_decimal));
}

/**
 * Lấy thông tin mạng hiện tại (để debug)
 */
function getNetworkInfo() {
    $user_ip = getUserIP();
    $is_company = isCompanyNetwork();
    $server_ip = getServerIP();
    $server_ok = isServerOnCompanyNetwork();
    
    return [
        'user_ip' => $user_ip,
        'is_company_network' => $is_company,
        'server_ip' => $server_ip,
        'server_on_company_network' => $server_ok,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
        'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
        'http_x_forwarded_for' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 'None',
        'http_client_ip' => $_SERVER['HTTP_CLIENT_IP'] ?? 'None'
    ];
}

// Hàm này sẽ được định nghĩa lại ở cuối file với logic đầy đủ hơn

/**
 * Trả về trạng thái để hiển thị UI, kèm thông điệp theo ngôn ngữ
 */
function networkGateInfo($lang = null, $clientTime = null) {
    $lang = $lang ?: getPreferredLang();
    $server_ok = isServerOnCompanyNetwork();
    $client_ok = isCompanyNetwork();
    $ssid_ok = checkClientSSID();
    $time_ok = isAttendanceTimeAllowed($clientTime);
    
    $message = '';
    if (!$server_ok) {
        $message = t('server_not_company', $lang);
    } elseif (!$client_ok) {
        $message = t('client_not_company', $lang);
    } elseif ($ssid_ok === false) {
        $message = 'Thiết bị không kết nối đúng WiFi công ty.';
    } elseif (!$time_ok) {
        $message = 'Hiện tại không trong khung giờ điểm danh.';
    } else {
        $message = t('both_ok', $lang);
    }
    
    // Tính toán kết quả cuối cùng
    $network_ok = $server_ok && $client_ok && ($ssid_ok !== false);
    $allowed = $network_ok && $time_ok;
    
    return [
        'allowed' => $allowed,
        'server_ok' => $server_ok,
        'client_ok' => $client_ok,
        'ssid_ok' => $ssid_ok,
        'ssid_checked' => ($ssid_ok !== null),
        'time_ok' => $time_ok,
        'network_ok' => $network_ok,
        'message' => $message,
    ];
}

/**
 * Kiểm tra SSID của client có phải là SSID công ty không
 */
function checkClientSSID() {
    // Nếu không có SSID trong session, không thể kiểm tra
    if (!isset($_SESSION['client_ssid']) || empty($_SESSION['client_ssid'])) {
        error_log("Network Check - No client SSID in session");
        return null;
    }
    
    $client_ssid = $_SESSION['client_ssid'];
    $company_ssids = $GLOBALS['COMPANY_SSIDS'] ?? [];
    
    // Kiểm tra xem SSID có trong danh sách SSID công ty không
    foreach ($company_ssids as $company_ssid) {
        if (strcasecmp($client_ssid, $company_ssid) === 0) {
            error_log("Network Check - Client SSID '{$client_ssid}' hợp lệ");
            return true;
        }
    }
    
    error_log("Network Check - Client SSID '{$client_ssid}' KHÔNG hợp lệ");
    return false;
}

/**
 * Cập nhật SSID của client vào session
 */
function updateClientSSID($ssid) {
    if (empty($ssid)) {
        error_log("Network Check - Cannot update empty SSID");
        return false;
    }
    
    // Lưu SSID vào session
    $_SESSION['client_ssid'] = trim($ssid);
    error_log("Network Check - Client SSID updated to: " . $_SESSION['client_ssid']);
    
        return true;
}

/**
 * Quyết định có hiển thị nút điểm danh hay không (server và client đều phải đúng mạng VÀ SSID VÀ đúng giờ)
 */
function canShowAttendanceButton($clientTime = null) {
    $server_ok = isServerOnCompanyNetwork();
    $client_ok = isCompanyNetwork();
    $ssid_ok = checkClientSSID();
    $time_ok = isAttendanceTimeAllowed($clientTime);
    
    // Nếu không thể kiểm tra SSID (null), chỉ kiểm tra IP
    if ($ssid_ok === null) {
        error_log("Network Check - Cannot check SSID, falling back to IP-only check");
        $network_result = $server_ok && $client_ok;
    } else {
        // Kiểm tra cả IP và SSID
        $network_result = $server_ok && $client_ok && $ssid_ok;
    }
    
    // Kiểm tra cả mạng và thời gian
    $result = $network_result && $time_ok;
    error_log("Network Check - Attendance button check: Server={$server_ok}, Client={$client_ok}, SSID={$ssid_ok}, Time={$time_ok}, Result={$result}");
    return $result;
}

/**
 * Kiểm tra xem có trong khung giờ điểm danh được phép hay không
 * Sử dụng thời gian từ client (JavaScript) thay vì server
 */
function isAttendanceTimeAllowed($clientTime = null) {
    try {
        // Kết nối database
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $conn = $database->getConnection();
        
        // Lấy cài đặt giờ điểm danh
        $stmt = $conn->prepare("SELECT * FROM cai_dat_gio_diem_danh WHERE id = 1");
        $stmt->execute();
        $settings = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Nếu không có cài đặt hoặc tắt kiểm tra giờ, cho phép
        if (!$settings || !$settings['bat_kiem_tra_gio']) {
            error_log("Attendance Time Check - No time restriction or disabled, allowing");
            return true;
        }
        
        // Sử dụng thời gian từ client nếu có, nếu không thì dùng server
        if ($clientTime) {
            $currentTime = $clientTime;
            error_log("Attendance Time Check - Using client time: {$currentTime}");
        } else {
            $currentTime = date('H:i:s');
            error_log("Attendance Time Check - Using server time: {$currentTime}");
        }
        
        // Kiểm tra xem có trong khung giờ điểm danh sáng, trưa hoặc chiều không
        $isMorningTime = $currentTime >= $settings['gio_sang_bat_dau'] && $currentTime <= $settings['gio_sang_ket_thuc'];
        $isLunchTime = $currentTime >= $settings['gio_trua_bat_dau'] && $currentTime <= $settings['gio_trua_ket_thuc'];
        $isAfternoonTime = $currentTime >= $settings['gio_chieu_bat_dau'] && $currentTime <= $settings['gio_chieu_ket_thuc'];
        
        $result = $isMorningTime || $isLunchTime || $isAfternoonTime;
        
        error_log("Attendance Time Check - Time: {$currentTime}, Morning: {$settings['gio_sang_bat_dau']}-{$settings['gio_sang_ket_thuc']}, Lunch: {$settings['gio_trua_bat_dau']}-{$settings['gio_trua_ket_thuc']}, Afternoon: {$settings['gio_chieu_bat_dau']}-{$settings['gio_chieu_ket_thuc']}, Result: {$result}");
        
        return $result;
        
    } catch (Exception $e) {
        error_log("Attendance Time Check - Error: " . $e->getMessage());
        // Nếu có lỗi, mặc định cho phép
        return true;
    }
}
?>
