<?php
ob_start();
ini_set('display_errors', 0); // Disable error display to prevent HTML output
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

// Debug log
file_put_contents('debug_api.txt', "DEBUG API: REQUEST_URI = " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "\n", FILE_APPEND);
file_put_contents('debug_api.txt', "DEBUG API: REQUEST_METHOD = " . ($_SERVER['REQUEST_METHOD'] ?? 'NOT SET') . "\n", FILE_APPEND);
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
session_start();

// Hàm chuyển đổi Excel thành CSV (giải pháp đơn giản)
function convertExcelToCsv($excelPath) {
    // Tạo file CSV tạm thời
    $csvPath = tempnam(sys_get_temp_dir(), 'excel_convert_') . '.csv';
    
    // Sử dụng Python để chuyển đổi (nếu có)
    $pythonScript = "
import pandas as pd
import sys
try:
    df = pd.read_excel('$excelPath')
    df.to_csv('$csvPath', index=False, encoding='utf-8')
    print('SUCCESS')
except Exception as e:
    print('ERROR:' + str(e))
";
    
    $tempScript = tempnam(sys_get_temp_dir(), 'convert_') . '.py';
    file_put_contents($tempScript, $pythonScript);
    
    $output = shell_exec("python \"$tempScript\" 2>&1");
    unlink($tempScript);
    
    error_log("Python conversion output: " . $output);
    
    if (strpos($output, 'SUCCESS') !== false && file_exists($csvPath)) {
        error_log("Excel conversion successful: " . $csvPath);
        return $csvPath;
    }
    
    // Fallback: thử đọc Excel như CSV (có thể không hoạt động)
    if (file_exists($excelPath)) {
        $content = file_get_contents($excelPath);
        // Excel file có thể chứa dữ liệu có thể đọc được
        file_put_contents($csvPath, $content);
        error_log("Fallback conversion used: " . $csvPath);
        return $csvPath;
    }
    
    error_log("Excel conversion failed completely");
    return false;
}

include_once(__DIR__ . '/../controllers/UserController.php');
include_once(__DIR__ . '/../controllers/NguoiPhuThuocController.php');
include_once(__DIR__ . '/../controllers/ChucVuController.php');
include_once(__DIR__ . '/../controllers/ChamCongController.php');
include_once(__DIR__ . '/../controllers/NghiPhepController.php');
include_once(__DIR__ . '/../controllers/LuongController.php');
include_once(__DIR__ . '/../controllers/BaoHiemController.php');
include_once(__DIR__ . '/../controllers/SettingsController.php');
include_once(__DIR__ . '/../controllers/ThuongController.php');
include_once(__DIR__ . '/../controllers/TuyenDungController.php');
include_once(__DIR__ . '/../controllers/EmployeeController.php');
include_once(__DIR__ . '/../controllers/FingerprintController.php');
include_once(__DIR__ . '/../controllers/CauHinhGioLamViecController.php');
include_once(__DIR__ . '/../controllers/DanhGiaController.php');
include_once(__DIR__ . '/../controllers/KpiController.php');
include_once(__DIR__ . '/../controllers/UngLuongController.php');
include_once(__DIR__ . '/../controllers/HopDongController.php');

$userController = new UserController();
$nguoiPhuThuocController = new NguoiPhuThuocController();
$chucVuController = new ChucVuController();
$chamCongController = new ChamCongController();
$nghiPhepController = new NghiPhepController();
$luongController = new LuongController();
$baoHiemController = new BaoHiemThueController();
$settingsController = new SettingsController();
$thuongController = new ThuongController();
$tuyenDungController = new TuyenDungController();
$employeeController = new EmployeeController();
$fingerprintController = new FingerprintController();
$cauHinhGioLamViecController = new CauHinhGioLamViecController();
$danhGiaController = new DanhGiaController();
$kpiController = new KpiController();
$ungLuongController = new UngLuongController();
$hopDongController = new HopDongController();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    ob_end_clean();
    exit();
}

// Tạm thời bỏ qua kiểm tra đăng nhập cho import_employees, chucvu, nguoiphuthuoc, nhanvien, baohiem, update-so-nguoi-phu-thuoc, users, sync-phu-cap-chuc-vu, update-luong-phu-cap, KPI APIs
$allowed_without_login = [
    '/doanqlns/index.php/api/import_employees',
    '/doanqlns/index.php/api/chucvu',
    '/doanqlns/index.php/api/nguoiphuthuoc',
    '/doanqlns/index.php/api/nhanvien',
    '/doanqlns/index.php/api/baohiem',
    '/doanqlns/index.php/api/nghiphep',
    '/doanqlns/index.php/api/chamcong',
    '/doanqlns/index.php/api/update-so-nguoi-phu-thuoc',
    '/doanqlns/index.php/api/users',
    '/doanqlns/index.php/api/sync-phu-cap-chuc-vu',
    '/doanqlns/index.php/api/update-luong-phu-cap',
    '/doanqlns/index.php/api/update-luong-tien-thuong',
    '/doanqlns/routes/api.php/api/hop-dong-kpi',
    '/doanqlns/routes/api.php/api/doanh-so-thang',
    '/doanqlns/routes/api.php/api/cau-hinh-thang-diem',
    '/doanqlns/routes/api.php/api/thong-ke-kpi',
    '/doanqlns/routes/api.php/api/kpi/nhan-vien',
    '/doanqlns/routes/api.php/api/tong-gia-tri-hop-dong',
    '/doanqlns/routes/api.php/api/auto-sync-hop-dong-doanh-so',
    '/doanqlns/routes/api.php/api/ung-luong',
    '/doanqlns/index.php/api/hopdong'
];

// Kiểm tra đăng nhập, bỏ qua cho các route được phép
$is_allowed_without_login = in_array($_SERVER['REQUEST_URI'], $allowed_without_login) || 
                           preg_match('/\/doanqlns\/index\.php\/api\/luong(\?.*)?$/', $_SERVER['REQUEST_URI']) ||
                           preg_match('/\/doanqlns\/index\.php\/api\/quan-ly-nghi-phep(\?.*)?$/', $_SERVER['REQUEST_URI']) ||
                           preg_match('/\/doanqlns\/index\.php\/api\/danhgia\/sync-quarter(\?.*)?$/', $_SERVER['REQUEST_URI']) ||
                           preg_match('/\/doanqlns\/routes\/api\.php\/api\/tong-gia-tri-hop-dong(\?.*)?$/', $_SERVER['REQUEST_URI']) ||
                           preg_match('/\/doanqlns\/routes\/api\.php\/api\/auto-sync-hop-dong-doanh-so(\?.*)?$/', $_SERVER['REQUEST_URI']) ||
                           preg_match('/\/doanqlns\/index\.php\/api\/doanh-so-thang(\?.*)?$/', $_SERVER['REQUEST_URI']) ||
                           preg_match('/\/doanqlns\/index\.php\/api\/import-evaluation-csv$/', $_SERVER['REQUEST_URI']) ||
                           preg_match('/\/doanqlns\/index\.php\/api\/danhgia\/nhan-vien\/(\d+)$/', $_SERVER['REQUEST_URI']) ||
                           $_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/danhgia/all' ||
                           preg_match('/\/doanqlns\/routes\/api\.php\/api\/hop-dong-kpi(\?.*)?$/', $_SERVER['REQUEST_URI']) ||
                           preg_match('/\/doanqlns\/routes\/api\.php\/api\/hop-dong-kpi\/(\d+)$/', $_SERVER['REQUEST_URI']) ||
                           preg_match('/\/doanqlns\/routes\/api\.php\/api\/cau-hinh-thang-diem(\?.*)?$/', $_SERVER['REQUEST_URI']) ||
                           preg_match('/\/doanqlns\/routes\/api\.php\/api\/cau-hinh-thang-diem\/(\d+)$/', $_SERVER['REQUEST_URI']);

if (!isset($_SESSION['user_id']) && !$is_allowed_without_login) {
    http_response_code(401);
    ob_end_clean();
    echo json_encode(["success" => false, "message" => "Vui lòng đăng nhập"]);
    exit();
}

// Check if van_tay table exists (commented out for now)
/*
try {
    $db = new Database();
    $conn = $db->getConnection();
    $conn->query("DESCRIBE van_tay");
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    ob_end_clean();
    echo json_encode(["success" => false, "message" => "Lỗi cơ sở dữ liệu: Bảng van_tay không tồn tại hoặc không thể truy cập"]);
    exit();
}
*/

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/users') {
            $userController->getAllUsers();
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/nhanvien') {
            $userController->getAllUsers();
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/user\?id=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            $userId = intval($matches[1]);
            $userController->getUserById($userId);
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/phongban') {
            $userController->getAllPhongBan();
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/chucvu') {
            $chucVuController->getAllChucVu();
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/baohiem(\?month=(\d+)&year=(\d+))?/', $_SERVER['REQUEST_URI'], $matches)) {
            $month = isset($matches[2]) ? intval($matches[2]) : null;
            $year = isset($matches[3]) ? intval($matches[3]) : null;
            $baoHiemController->getAllBaoHiemThue($month, $year);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/chamcong(\?.*)?$/', $_SERVER['REQUEST_URI'])) {
            // Kiểm tra xem có tham số tìm kiếm không
            if (isset($_GET['id_nhan_vien']) && isset($_GET['ngay_cham_cong'])) {
                $id_nhan_vien = intval($_GET['id_nhan_vien']);
                $ngay_cham_cong = $_GET['ngay_cham_cong'];
                
                // Kiểm tra quyền: chỉ cho phép xem chấm công của chính mình hoặc có quyền quyen_them
                if ($id_nhan_vien != $_SESSION['user_id'] && (!isset($_SESSION['quyen_them']) || !$_SESSION['quyen_them'])) {
                    http_response_code(403);
                    ob_end_clean();
                    echo json_encode(["success" => false, "message" => "Không có quyền xem chấm công của người khác"]);
                    exit();
                }
                
                $chamCongController->getChamCongByEmployeeAndDate($id_nhan_vien, $ngay_cham_cong);
            } elseif (isset($_GET['thang']) && isset($_GET['nam'])) {
                // Xuất Excel theo tháng và năm
                $thang = intval($_GET['thang']);
                $nam = intval($_GET['nam']);
                $chamCongController->getChamCongByMonth($thang, $nam);
            } else {
                $chamCongController->getAllChamCong();
            }
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/nghiphep') {
            $nghiPhepController->getAllNghiPhep();
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/quan-ly-nghi-phep(\?.*)?$/', $_SERVER['REQUEST_URI'])) {
            $nghiPhepController->getQuanLyNghiPhep();
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/luong(\?.*)?$/', $_SERVER['REQUEST_URI'])) {
            if (isset($_GET['thang']) && isset($_GET['nam'])) {
                // Xuất Excel theo tháng và năm
                $thang = intval($_GET['thang']);
                $nam = intval($_GET['nam']);
                $luongController->getLuongByMonth($thang, $nam);
            } else {
                $luongController->getAllLuong();
            }
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/thuong') {
            $thuongController->getAllThuong();
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/thuong/types') {
            $thuongController->getBonusTypes();
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/ung-luong') {
            $ungLuongController->getAllUngLuong();
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/tuyendung\?month=(\d+)&year=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            $month = intval($matches[1]);
            $year = intval($matches[2]);
            $tuyenDungController->getAllData($month, $year);
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/tuyendung/options') {
            $tuyenDungController->getOptions();
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/tuyendung\/(\w+)\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            $type = $matches[1];
            $id = intval($matches[2]);
            $tuyenDungController->getRecordById($type, $id);
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/ungvien') {
            $tuyenDungController->getAllUngVien();
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/tuyendung\?id=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            $dotTuyenDungId = intval($matches[1]);
            $tuyenDungController->getDotTuyenDungById($dotTuyenDungId);
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/tuyendung/plans') {
            $tuyenDungController->getAllKeHoachTuyenDung();
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/tuyendung\/plan\?id=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            $planId = intval($matches[1]);
            $tuyenDungController->getKeHoachTuyenDungById($planId);
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/chucvu') {
            $chucVuController->getAllChucVu();
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/nguoiphuthuoc') {
            $nguoiPhuThuocController->getAllNguoiPhuThuoc();
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/nhanvien') {
            $nguoiPhuThuocController->getAllNhanVien();
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/update-so-nguoi-phu-thuoc') {
            $nguoiPhuThuocController->updateSoNguoiPhuThuoc();
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/sync-phu-cap-chuc-vu') {
            $userController->syncPhuCapChucVu();
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/update-luong-phu-cap') {
            $luongController->updatePhuCapChucVu();
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/update-luong-tien-thuong') {
            $luongController->updateLuongTienThuong();
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/employee/profile') {
            $userController->getEmployeeProfile();
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/employee\/salary/', $_SERVER['REQUEST_URI'])) {
            $userController->getEmployeeSalary();
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/nguoiphuthuoc\?nhanvien=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            $nhanVienId = intval($matches[1]);
            $nguoiPhuThuocController->getNguoiPhuThuocByNhanVien($nhanVienId);
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/settings/users') {
            if (!isset($_SESSION['quyen_them']) || !$_SESSION['quyen_them']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền truy cập cài đặt"]);
                exit();
            }
            $result = $settingsController->getAllUsers();
            ob_end_clean();
            echo json_encode($result);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/settings\/user-status\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_sua']) || !$_SESSION['quyen_sua']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền cập nhật trạng thái"]);
                exit();
            }
            $userId = intval($matches[1]);
            $result = $settingsController->toggleUserStatus($userId);
            ob_end_clean();
            echo json_encode($result);
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/settings/fingerprints') {
            if (!isset($_SESSION['quyen_them']) || !$_SESSION['quyen_them']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền truy cập vân tay"]);
                exit();
            }
            $fingerprintController->getAllFingerprints();
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/settings\/fingerprint\/status\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_them']) || !$_SESSION['quyen_them']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền kiểm tra trạng thái vân tay"]);
                exit();
            }
            $enrollmentId = intval($matches[1]);
            $fingerprintController->checkStatus($enrollmentId);
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/employee/profile') {
            $employeeController->getProfile($_SESSION['user_id']);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/employee\/salary(\?month=(\d+)&year=(\d+))?/', $_SERVER['REQUEST_URI'], $matches)) {
            $month = isset($matches[2]) ? intval($matches[2]) : date('n');
            $year = isset($matches[3]) ? intval($matches[3]) : date('Y');
            $employeeController->getSalary($_SESSION['user_id'], $month, $year);
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/employee/leave') {
            $employeeController->getLeaveRequests($_SESSION['user_id']);
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/settings/fingerprints/manage') {
            error_log("DEBUG: Accessing fingerprint management endpoint");
            error_log("DEBUG: User ID: " . $_SESSION['user_id']);
            error_log("DEBUG: User permissions: " . json_encode([
                'quyen_them' => isset($_SESSION['quyen_them']) ? $_SESSION['quyen_them'] : 'not set',
                'quyen_sua' => isset($_SESSION['quyen_sua']) ? $_SESSION['quyen_sua'] : 'not set',
                'quyen_xoa' => isset($_SESSION['quyen_xoa']) ? $_SESSION['quyen_xoa'] : 'not set'
            ]));

            if (!isset($_SESSION['quyen_them']) || !$_SESSION['quyen_them']) {
                error_log("DEBUG: Permission denied - quyen_them not set or false");
                http_response_code(403);
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode(["success" => false, "message" => "Không có quyền truy cập quản lý vân tay"]);
                exit();
            }

            try {
                error_log("DEBUG: About to call getAllFingerprintsWithDetails");
                $fingerprintController->getAllFingerprintsWithDetails();
                error_log("DEBUG: Successfully called getAllFingerprintsWithDetails");
            } catch (Exception $e) {
                error_log("ERROR in fingerprint management: " . $e->getMessage());
                error_log("ERROR stack trace: " . $e->getTraceAsString());
                http_response_code(500);
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode([
                    "success" => false, 
                    "message" => "Lỗi khi lấy danh sách vân tay: " . $e->getMessage()
                ]);
            }
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/settings/attendance-time') {
            if (!isset($_SESSION['quyen_sua']) || !$_SESSION['quyen_sua']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền cài đặt giờ điểm danh"]);
                exit();
            }
            $settingsController->getAttendanceTimeSettings();
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/cau-hinh-gio-lam-viec/hien-tai') {
            // Cho phép tất cả nhân viên đã đăng nhập xem cấu hình giờ làm việc (không cần quyền sửa)
            // Vì nhân viên cần xem cấu hình để biết điểm danh đúng giờ
            $cauHinhGioLamViecController->getCauHinhHienTai();
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/cau-hinh-gio-lam-viec') {
            if (!isset($_SESSION['quyen_sua']) || !$_SESSION['quyen_sua']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền xem cấu hình giờ làm việc"]);
                exit();
            }
            $cauHinhGioLamViecController->getAllCauHinh();
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/cau-hinh-gio-lam-viec\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_sua']) || !$_SESSION['quyen_sua']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền xem cấu hình giờ làm việc"]);
                exit();
            }
            $id = intval($matches[1]);
            $cauHinhGioLamViecController->getCauHinhById($id);
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/danhgia/tieu-chi') {
            $danhGiaController->getAllTieuChi();
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/danhgia/nhan-vien') {
            $danhGiaController->getAllNhanVien();
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/danhgia\?thang=(\d+)&nam=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            $thang = intval($matches[1]);
            $nam = intval($matches[2]);
            $danhGiaController->getDanhGiaByMonth($thang, $nam);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/danhgia\/(\d+)\/chi-tiet/', $_SERVER['REQUEST_URI'], $matches)) {
            $idDanhGia = intval($matches[1]);
            $danhGiaController->getChiTietDanhGia($idDanhGia);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/danhgia\/(\d+)\/thong-ke\?thang=(\d+)&nam=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            $thang = intval($matches[2]);
            $nam = intval($matches[3]);
            $danhGiaController->getThongKeDanhGia($thang, $nam);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/danhgia\/nhan-vien\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            $idNhanVien = intval($matches[1]);
            $danhGiaController->getDanhGiaByNhanVien($idNhanVien);
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/danhgia/all') {
            $danhGiaController->getAllDanhGia();
        } elseif (preg_match('/\/doanqlns\/routes\/api\.php\/api\/hop-dong-kpi(\?.*)?$/', $_SERVER['REQUEST_URI'])) {
            $kpiController->getAllHopDongKpi();
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/doanh-so-thang(\?.*)?$/', $_SERVER['REQUEST_URI'])) {
            $kpiController->getAllDoanhSoThang();
        } elseif (preg_match('/\/doanqlns\/routes\/api\.php\/api\/cau-hinh-thang-diem(\?.*)?$/', $_SERVER['REQUEST_URI'])) {
            $kpiController->getAllCauHinhThangDiem();
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/routes/api.php/api/thong-ke-kpi') {
            $kpiController->getThongKeKpi();
        } elseif (preg_match('/\/doanqlns\/routes\/api\.php\/api\/tong-gia-tri-hop-dong(\?.*)?$/', $_SERVER['REQUEST_URI'])) {
            $kpiController->getTongGiaTriHopDong();
        } elseif (preg_match('/\/doanqlns\/routes\/api\.php\/api\/auto-sync-hop-dong-doanh-so(\?.*)?$/', $_SERVER['REQUEST_URI'])) {
            $kpiController->autoSyncHopDongToDoanhSo();
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/routes/api.php/api/kpi/nhan-vien') {
            $kpiController->getAllNhanVien();
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/routes/api.php/api/ung-luong') {
            $ungLuongController->getAllUngLuong();
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/hopdong') {
            $result = $hopDongController->getAllHopDong();
            ob_end_clean();
            echo json_encode($result);
        } elseif (preg_match('/^\/doanqlns\/routes\/api\.php\/api\/ung-luong\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches) && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $ungLuongController->getUngLuongById($matches[1]);
        } else {
            http_response_code(404);
            ob_end_clean();
            error_log("DEBUG: No GET route matched for: " . $_SERVER['REQUEST_URI']);
            echo json_encode(["success" => false, "message" => "Invalid route"]);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/chamcong') {
            $data = json_decode(file_get_contents("php://input"), true);
            
            // Cho phép điểm danh bằng gương mặt mà không cần kiểm tra quyền nghiêm ngặt
            // Chỉ kiểm tra quyền khi cần thiết
            $chamCongController->addOrUpdateChamCong($data);
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/chamcong?action=markHoliday') {
            if (!isset($_SESSION['quyen_them']) || !$_SESSION['quyen_them']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền điểm danh nghỉ lễ"]);
                exit();
            }
            $data = json_decode(file_get_contents("php://input"), true);
            $chamCongController->markHolidayAttendance($data);
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/nghiphep') {
            $data = json_decode(file_get_contents("php://input"), true);
            if (isset($data['action']) && $data['action'] === 'update') {
                $nghiPhepController->updateNghiPhep($data['id_nghi_phep'], $data);
            } else {
                $nghiPhepController->addNghiPhep($data);
            }
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/user') {
            if (!isset($_SESSION['quyen_them']) || !$_SESSION['quyen_them']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền thêm nhân viên"]);
                exit();
            }
            if (!empty($_FILES) || strpos($_SERVER['CONTENT_TYPE'], 'multipart/form-data') !== false) {
                $data = $_POST;
                if (isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] === UPLOAD_ERR_OK) {
                    $data['hinh_anh'] = $_FILES['hinh_anh'];
                }
                $userController->addUser($data);
            } else {
                $data = json_decode(file_get_contents("php://input"), true);
                $userController->addUser($data);
            }
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/baohiem') {
            if (!isset($_SESSION['quyen_them']) || !$_SESSION['quyen_them']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền thêm bảo hiểm/thuế"]);
                exit();
            }
            $data = json_decode(file_get_contents("php://input"), true);
            $baoHiemController->addBaoHiemThue($data);
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/thuong') {
            if (!isset($_SESSION['quyen_them']) || !$_SESSION['quyen_them']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền thêm thưởng"]);
                exit();
            }
            $data = json_decode(file_get_contents("php://input"), true);
            $thuongController->addThuong($data);
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/ung-luong') {
            if (!isset($_SESSION['quyen_them']) || !$_SESSION['quyen_them']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền thêm ứng lương"]);
                exit();
            }
            $data = json_decode(file_get_contents("php://input"), true);
            $ungLuongController->addUngLuong($data);
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/tuyendung') {
            if (!isset($_SESSION['quyen_them']) || !$_SESSION['quyen_them']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền thêm dữ liệu tuyển dụng"]);
                exit();
            }
            $data = json_decode(file_get_contents("php://input"), true);
            $tuyenDungController->handleRequest('POST', $data);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/danhgia\/sync-quarter\?nam=(\d{4})&quy=(\d)/', $_SERVER['REQUEST_URI'], $matches)) {
            // Đồng bộ điểm chuyên cần theo quý (POST)
            $nam = intval($matches[1]);
            $quy = intval($matches[2]);
            $input = json_decode(file_get_contents("php://input"), true);
            $scores = is_array($input) ? $input : [];
            $danhGiaController->syncQuarterChuyenCan($nam, $quy, $scores);
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/ungvien') {
            if (!isset($_SESSION['quyen_them']) || !$_SESSION['quyen_them']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền thêm ứng viên"]);
                exit();
            }
            $data = json_decode(file_get_contents("php://input"), true);
            $tuyenDungController->addUngVien($data);
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/chucvu') {
            if (!isset($_SESSION['quyen_them']) || !$_SESSION['quyen_them']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền thêm chức vụ"]);
                exit();
            }
            $data = json_decode(file_get_contents("php://input"), true);
            $chucVuController->addChucVu($data);
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/nguoiphuthuoc') {
            // Cho phép nhân viên tự gửi yêu cầu người phụ thuộc cho chính họ (giống nghỉ phép)
            $selfAllowed = false;
            $sessionNhanVienId = isset($_SESSION['id_nhan_vien']) ? intval($_SESSION['id_nhan_vien']) : 0;

            // Trường hợp gửi bằng form-data (FormData)
            if (!empty($_POST) && isset($_POST['id_nhan_vien'])) {
                $selfAllowed = ($sessionNhanVienId > 0 && intval($_POST['id_nhan_vien']) === $sessionNhanVienId);
            } else {
                // Trường hợp gửi JSON
                $raw = file_get_contents("php://input");
                $payload = json_decode($raw, true);
                if (is_array($payload) && isset($payload['id_nhan_vien'])) {
                    $selfAllowed = ($sessionNhanVienId > 0 && intval($payload['id_nhan_vien']) === $sessionNhanVienId);
                }
                // Reset lại input stream cho controller nếu cần
                if (!empty($raw)) {
                    $GLOBALS['HTTP_RAW_POST_DATA'] = $raw; // lưu lại phòng controller cần đọc tiếp
                }
            }

            if (!$selfAllowed && (!isset($_SESSION['quyen_them']) || !$_SESSION['quyen_them'])) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền thêm người phụ thuộc"]);
                exit();
            }

            // Chuẩn hóa data cho controller
            $data = !empty($_POST) ? $_POST : (isset($payload) && is_array($payload) ? $payload : json_decode(file_get_contents("php://input"), true));
            $nguoiPhuThuocController->addNguoiPhuThuoc($data);
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/import_employees') {
            // Debug session
            error_log("Session data: " . print_r($_SESSION, true));
            error_log("quyen_them: " . (isset($_SESSION['quyen_them']) ? $_SESSION['quyen_them'] : 'not set'));
            
            // Tạm thời bỏ qua kiểm tra quyền để test
            // if (!isset($_SESSION['quyen_them']) || !$_SESSION['quyen_them']) {
            //     http_response_code(403);
            //     ob_end_clean();
            //     echo json_encode(["success" => false, "message" => "Không có quyền cập nhật nhân viên"]);
            //     exit();
            // }
            
            if (!empty($_FILES) && isset($_FILES['csvFile']) && isset($_POST['employeeId'])) {
                $file = $_FILES['csvFile'];
                $employeeId = $_POST['employeeId']; // Không convert sang int để xử lý trường hợp rỗng
                error_log("Received employeeId: '$employeeId'");
                
                // Nếu employeeId rỗng hoặc 0, sẽ thêm nhân viên mới
                $isNewEmployee = empty($employeeId) || intval($employeeId) <= 0;
                if ($file['error'] === UPLOAD_ERR_OK) {
                    $filePath = $file['tmp_name'];
                    $fileName = $file['name'];
                    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    
                    // Xử lý file Excel (.xlsx) - chuyển đổi thành CSV tạm thời
                    if ($fileExtension === 'xlsx') {
                        $csvPath = convertExcelToCsv($filePath);
                        if (!$csvPath) {
                            http_response_code(400);
                            ob_end_clean();
                            echo json_encode(['success' => false, 'message' => 'Không thể đọc file Excel. Vui lòng sử dụng file CSV.']);
                            exit();
                        }
                        $filePath = $csvPath;
                    }
                    
                    // Log để debug
                    error_log("Processing file: $fileName, Extension: $fileExtension, Path: $filePath");
                    
                    $requiredColumns = ['ho_ten', 'gioi_tinh', 'ngay_sinh', 'so_dien_thoai', 'dia_chi', 'can_cuoc_cong_dan', 'ngay_cap', 'noi_cap', 'que_quan', 'id_phong_ban', 'id_chuc_vu', 'loai_hop_dong', 'luong_co_ban', 'ngay_vao_lam', 'trang_thai'];
                    $optionalColumns = ['email', 'hinh_anh', 'ngay_nghi_viec', 'so_nguoi_phu_thuoc', 'tinh_trang_hon_nhan', 'phu_cap_chuc_vu', 'phu_cap_bang_cap', 'phu_cap_khac', 'so_bhxh', 'so_bhyt', 'so_bhtn', 'ngay_tham_gia_bhxh', 'so_tai_khoan', 'ten_ngan_hang', 'chi_nhanh_ngan_hang'];
                    if (($handle = fopen($filePath, 'r')) !== false) {
                        $header = fgetcsv($handle, 1000, ',');
                        error_log("Header read: " . print_r($header, true));
                        
                        if ($header === false) {
                            error_log("ERROR: Cannot read header from file");
                            http_response_code(400);
                            ob_end_clean();
                            echo json_encode(['success' => false, 'message' => 'Không đọc được header của file CSV']);
                            exit();
                        }
                        // Loại bỏ BOM nếu có và chuẩn hóa về chữ thường, trim khoảng trắng
                        $normalizedHeader = [];
                        foreach ($header as $idx => $col) {
                            if ($idx === 0) {
                                // Strip UTF-8 BOM
                                $col = preg_replace('/^\xEF\xBB\xBF/', '', $col);
                            }
                            $normalizedHeader[] = strtolower(trim($col));
                        }

                        // Chuẩn hóa requiredColumns để so sánh không phân biệt hoa thường
                        $normalizedRequired = array_map('strtolower', $requiredColumns);
                        $missingColumns = array_diff($normalizedRequired, $normalizedHeader);
                        
                        error_log("Required columns: " . print_r($normalizedRequired, true));
                        error_log("Normalized header: " . print_r($normalizedHeader, true));
                        error_log("Missing columns: " . print_r($missingColumns, true));
                        
                        if (!empty($missingColumns)) {
                            fclose($handle);
                            error_log("ERROR: Missing required columns: " . implode(', ', $missingColumns));
                            http_response_code(400);
                            ob_end_clean();
                            echo json_encode(['success' => false, 'message' => 'File CSV thiếu các cột bắt buộc: ' . implode(', ', $missingColumns)]);
                            exit();
                        }
                        $data = [];
                        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                            $row = array_map('trim', $row);
                            if (count($row) === count($header)) {
                                // Tạo mảng dữ liệu với key là header đã chuẩn hóa
                                $combined = array_combine($normalizedHeader, $row);
                                $data[] = $combined;
                            }
                        }
                        fclose($handle);
                        
                        // Cleanup temporary CSV file if it was created from Excel
                        if ($fileExtension === 'xlsx' && $filePath !== $file['tmp_name']) {
                            unlink($filePath);
                        }
                        
                        // Nếu là cập nhật nhân viên có sẵn thì chỉ cho phép đúng 1 dòng
                        if (!$isNewEmployee && count($data) !== 1) {
                            http_response_code(400);
                            ob_end_clean();
                            echo json_encode(['success' => false, 'message' => 'Cập nhật nhân viên chỉ chấp nhận 1 dòng dữ liệu']);
                            exit();
                        }
                        try {
                            // Kiểm tra xem có Google Drive links không
                            $hasGoogleDriveLink = false;
                            foreach ($data as $row) {
                                if (isset($row['hinh_anh']) && strpos($row['hinh_anh'], 'drive.google.com') !== false) {
                                    $hasGoogleDriveLink = true;
                                    break;
                                }
                            }
                            
                            // Kiểm tra Gmail access token
                            $hasGmailToken = isset($_SESSION['gmail_access_token']);
                            
                            if ($isNewEmployee) {
                                // Thêm nhiều nhân viên từ CSV (1..N)
                                error_log("Adding new employees from CSV, count=" . count($data));
                                $added = 0; $errors = [];
                                $imageDownloaded = false;
                                
                                foreach ($data as $row) {
                                    try {
                                        $userController->addEmployeeFromCSV($row);
                                        $added++;
                                        
                                        // Kiểm tra xem có tải được hình ảnh không
                                        if (isset($row['hinh_anh']) && strpos($row['hinh_anh'], 'drive.google.com') !== false && $hasGmailToken) {
                                            $imageDownloaded = true;
                                        }
                                    } catch (Exception $inner) {
                                        $errors[] = $inner->getMessage();
                                    }
                                }
                                
                                $response = ['success' => true, 'message' => 'Đã thêm ' . $added . ' nhân viên', 'errors' => $errors];
                                if ($imageDownloaded) {
                                    $response['image_downloaded'] = true;
                                } elseif ($hasGoogleDriveLink && !$hasGmailToken) {
                                    $response['has_google_drive_link'] = true;
                                }
                                
                                ob_end_clean();
                                echo json_encode($response);
                            } else {
                                // Cập nhật nhân viên có sẵn
                                $data[0]['employeeId'] = intval($employeeId);
                                error_log("Updating employee from CSV for employeeId: $employeeId");
                            $userController->importEmployeeFromCSV($data[0]);
                                
                                $response = ['success' => true, 'message' => 'Cập nhật hồ sơ nhân viên thành công'];
                                if ($hasGoogleDriveLink && $hasGmailToken) {
                                    $response['image_downloaded'] = true;
                                } elseif ($hasGoogleDriveLink && !$hasGmailToken) {
                                    $response['has_google_drive_link'] = true;
                                }
                                
                            ob_end_clean();
                                echo json_encode($response);
                            }
                        } catch (Exception $e) {
                            error_log("Error processing CSV: " . $e->getMessage());
                            http_response_code(500);
                            ob_end_clean();
                            echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật hồ sơ: ' . $e->getMessage()]);
                        }
                    } else {
                        http_response_code(500);
                        ob_end_clean();
                        echo json_encode(['success' => false, 'message' => 'Không thể mở file CSV']);
                    }
                } else {
                    http_response_code(400);
                    ob_end_clean();
                    echo json_encode(['success' => false, 'message' => 'Lỗi khi tải file lên: ' . $file['error']]);
                }
            } else {
                http_response_code(400);
                ob_end_clean();
                echo json_encode(['success' => false, 'message' => 'Thiếu file CSV hoặc ID nhân viên']);
            }
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/settings/register-regular') {
            if (!isset($_SESSION['quyen_them']) || !$_SESSION['quyen_them']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền thêm người dùng"]);
                exit();
            }
            $data = json_decode(file_get_contents("php://input"), true);
            $result = $settingsController->registerRegularUser($data);
            ob_end_clean();
            echo json_encode($result);
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/settings/register-google') {
            if (!isset($_SESSION['quyen_them']) || !$_SESSION['quyen_them']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền thêm người dùng"]);
                exit();
            }
            $data = json_decode(file_get_contents("php://input"), true);
            $result = $settingsController->registerRegularUser($data);
            ob_end_clean();
            echo json_encode($result);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/settings\/permissions\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_sua']) || !$_SESSION['quyen_sua']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền cập nhật quyền"]);
                exit();
            }
            $userId = intval($matches[1]);
            $data = json_decode(file_get_contents("php://input"), true);
            $result = $settingsController->updateUserPermissions($userId, $data);
            ob_end_clean();
            echo json_encode($result);
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/settings/fingerprint/enroll') {
            if (!isset($_SESSION['quyen_them']) || !$_SESSION['quyen_them']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền thêm vân tay"]);
                exit();
            }
            $fingerprintController->startEnrollment();
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/settings/face/enroll') {
            if (!isset($_SESSION['quyen_them']) || !$_SESSION['quyen_them']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền thêm gương mặt"]);
                exit();
            }
            $settingsController->enrollFace();
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/settings/faces') {
            if (!isset($_SESSION['quyen_them']) || !$_SESSION['quyen_them']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền truy cập gương mặt"]);
                exit();
            }
            $settingsController->getAllFaces();
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/settings/faces/manage') {
            if (!isset($_SESSION['quyen_them']) || !$_SESSION['quyen_them']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền quản lý gương mặt"]);
                exit();
            }
            $settingsController->getAllFacesForManagement();
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/settings/attendance-time') {
            if (!isset($_SESSION['quyen_sua']) || !$_SESSION['quyen_sua']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền cài đặt giờ điểm danh"]);
                exit();
            }
                $data = json_decode(file_get_contents("php://input"), true);
                $settingsController->updateAttendanceTimeSettings($data);
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/employee/leave') {
            $data = json_decode(file_get_contents("php://input"), true);
            $employeeController->submitLeaveRequest($_SESSION['user_id'], $data);
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/cau-hinh-gio-lam-viec') {
            if (!isset($_SESSION['quyen_sua']) || !$_SESSION['quyen_sua']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền thêm cấu hình giờ làm việc"]);
                exit();
            }
            $data = json_decode(file_get_contents("php://input"), true);
            $cauHinhGioLamViecController->themCauHinh($data);
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/danhgia') {
            if (!isset($_SESSION['quyen_them']) || !$_SESSION['quyen_them']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền thêm đánh giá"]);
                exit();
            }
            $data = json_decode(file_get_contents("php://input"), true);
            $danhGiaController->addDanhGia($data);
        } elseif (preg_match('/\/doanqlns\/routes\/api\.php\/api\/hop-dong-kpi(\?.*)?$/', $_SERVER['REQUEST_URI'])) {
            $data = json_decode(file_get_contents("php://input"), true);
            $kpiController->addHopDongKpi();
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/doanh-so-thang(\?.*)?$/', $_SERVER['REQUEST_URI'])) {
            $data = json_decode(file_get_contents("php://input"), true);
            $kpiController->addDoanhSoThang();
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/routes/api.php/api/cau-hinh-thang-diem') {
            $data = json_decode(file_get_contents("php://input"), true);
            $kpiController->addCauHinhThangDiem();
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/routes/api.php/api/ung-luong') {
            if (!isset($_SESSION['quyen_them']) || !$_SESSION['quyen_them']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền thêm ứng lương"]);
                exit();
            }
            $data = json_decode(file_get_contents("php://input"), true);
            $ungLuongController->addUngLuong($data);
        } elseif ($_SERVER['REQUEST_URI'] === '/doanqlns/index.php/api/hopdong') {
            if (!isset($_SESSION['quyen_them']) || !$_SESSION['quyen_them']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền thêm hợp đồng"]);
                exit();
            }
            $data = json_decode(file_get_contents("php://input"), true);
            $result = $hopDongController->addHopDong($data);
            ob_end_clean();
            echo json_encode($result);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/ung-luong\?id=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_sua']) || !$_SESSION['quyen_sua']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền sửa ứng lương"]);
                exit();
            }
            $ungLuongId = intval($matches[1]);
            $data = json_decode(file_get_contents("php://input"), true);
            $ungLuongController->updateUngLuong($ungLuongId, $data);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/import-evaluation-csv$/', $_SERVER['REQUEST_URI'])) {
            // Bỏ qua kiểm tra quyền vì đã thêm vào $is_allowed_without_login
            
            $input = json_decode(file_get_contents("php://input"), true);
            $type = $input['type'] ?? '';
            $data = $input['data'] ?? [];
            
            file_put_contents('debug_import.txt', "DEBUG: Input received - Type: $type, Data count: " . count($data) . "\n", FILE_APPEND);
            
            if (empty($data)) {
                http_response_code(400);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có dữ liệu để import"]);
                exit();
            }
            
            try {
                $result = $danhGiaController->importEvaluationCSV($type, $data);
                ob_end_clean();
                echo json_encode($result);
            } catch (Exception $e) {
                file_put_contents('debug_import.txt', "ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
                error_log("Lỗi import CSV: " . $e->getMessage());
                http_response_code(500);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Lỗi khi import dữ liệu: " . $e->getMessage()]);
            }
        } else {
            http_response_code(404);
            ob_end_clean();
            error_log("DEBUG: No GET route matched for: " . $_SERVER['REQUEST_URI']);
            echo json_encode(["success" => false, "message" => "Invalid route"]);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        if (preg_match('/\/doanqlns\/index\.php\/api\/user\?id=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_sua']) || !$_SESSION['quyen_sua']) {
                http_response_code(403);
                ob_end_clean();
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(["success" => false, "message" => "Không có quyền sửa nhân viên"], JSON_UNESCAPED_UNICODE);
                exit();
            }
            $userId = intval($matches[1]);
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                ob_end_clean();
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(["success" => false, "message" => "Dữ liệu JSON không hợp lệ"], JSON_UNESCAPED_UNICODE);
                exit();
            }
            
            try {
                $userController->updateUser($userId, $data);
            } catch (Exception $e) {
                http_response_code(500);
                ob_end_clean();
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(["success" => false, "message" => "Lỗi server: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
                exit();
            }
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/chamcong\?id_nhan_vien=(\d+)&ngay_cham_cong=([\d-]+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_sua']) || !$_SESSION['quyen_sua']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền sửa chấm công"]);
                exit();
            }
            $idNhanVien = intval($matches[1]);
            $ngayChamCong = $matches[2];
            $data = json_decode(file_get_contents("php://input"), true);
            $chamCongController->addOrUpdateChamCong($data);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/nghiphep\?id=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            $nghiPhepId = intval($matches[1]);
            $data = json_decode(file_get_contents("php://input"), true);
            $nghiPhepController->updateNghiPhep($nghiPhepId, $data);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/baohiem\?id=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_sua']) || !$_SESSION['quyen_sua']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền sửa bảo hiểm/thuế"]);
                exit();
            }
            $baoHiemId = intval($matches[1]);
            $data = json_decode(file_get_contents("php://input"), true);
            $baoHiemController->updateBaoHiemThue($baoHiemId, $data);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/thuong\?id=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_sua']) || !$_SESSION['quyen_sua']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền sửa thưởng"]);
                exit();
            }
            $thuongId = intval($matches[1]);
            $data = json_decode(file_get_contents("php://input"), true);
            $thuongController->updateThuong($thuongId, $data);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/chucvu\?id=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_sua']) || !$_SESSION['quyen_sua']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền sửa chức vụ"]);
                exit();
            }
            $chucVuId = intval($matches[1]);
            $data = json_decode(file_get_contents("php://input"), true);
            $chucVuController->updateChucVu($chucVuId, $data);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/nguoiphuthuoc\?id=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_sua']) || !$_SESSION['quyen_sua']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền sửa người phụ thuộc"]);
                exit();
            }
            $nguoiPhuThuocId = intval($matches[1]);
            $data = json_decode(file_get_contents("php://input"), true);
            $nguoiPhuThuocController->updateNguoiPhuThuoc($nguoiPhuThuocId, $data);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/luong\?id=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_sua']) || !$_SESSION['quyen_sua']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền sửa dữ liệu lương"]);
                exit();
            }
            $id_luong = intval($matches[1]);
            $data = json_decode(file_get_contents("php://input"), true);
            
            // Kiểm tra xem có dữ liệu bảo hiểm không
            if (isset($data['bhxh_nv']) || isset($data['bhyt_nv']) || isset($data['bhtn_nv'])) {
                $luongController->updateBaoHiemThue($id_luong, $data);
            } else {
                $luongController->updateLuongStatus($id_luong, $data);
            }
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/tuyendung\/(\w+)\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_sua']) || !$_SESSION['quyen_sua']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền sửa"]);
                exit();
            }
            $type = $matches[1];
            $id = intval($matches[2]);
            $data = json_decode(file_get_contents("php://input"), true);
            $tuyenDungController->handleRequest('PUT', $data, $type, $id);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/ung-luong\/trang-thai\?id=(\d+)&trang_thai=(.+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_sua']) || !$_SESSION['quyen_sua']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền cập nhật trạng thái ứng lương"]);
                exit();
            }
            $ungLuongId = intval($matches[1]);
            $trangThai = urldecode($matches[2]);
            $ungLuongController->updateTrangThai($ungLuongId, $trangThai);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/settings\/fingerprint\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_sua']) || !$_SESSION['quyen_sua']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền sửa vân tay"]);
                exit();
            }
            $fingerprintId = intval($matches[1]);
            $data = json_decode(file_get_contents("php://input"), true);
            $fingerprintController->updateFingerprint($fingerprintId, $data);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/settings\/face\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_sua']) || !$_SESSION['quyen_sua']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền sửa gương mặt"]);
                exit();
            }
            $faceId = intval($matches[1]);
            $data = json_decode(file_get_contents("php://input"), true);
            $settingsController->updateFace($faceId, $data);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/settings\/face\/status\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_sua']) || !$_SESSION['quyen_sua']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền sửa trạng thái gương mặt"]);
                exit();
            }
            $faceId = intval($matches[1]);
            $data = json_decode(file_get_contents("php://input"), true);
            $settingsController->updateFaceStatus($faceId, $data);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/cau-hinh-gio-lam-viec\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_sua']) || !$_SESSION['quyen_sua']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền sửa cấu hình giờ làm việc"]);
                exit();
            }
            $id = intval($matches[1]);
            $data = json_decode(file_get_contents("php://input"), true);
            $cauHinhGioLamViecController->capNhatCauHinh($id, $data);
        } elseif (preg_match('/\/doanqlns\/routes\/api\.php\/api\/ung-luong\?id=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_sua']) || !$_SESSION['quyen_sua']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền sửa ứng lương"]);
                exit();
            }
            $ungLuongId = intval($matches[1]);
            $data = json_decode(file_get_contents("php://input"), true);
            $ungLuongController->updateUngLuong($ungLuongId, $data);
        } elseif (preg_match('/\/doanqlns\/routes\/api\.php\/api\/ung-luong\/trang-thai\?id=(\d+)&trang_thai=(.+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_sua']) || !$_SESSION['quyen_sua']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền cập nhật trạng thái ứng lương"]);
                exit();
            }
            $ungLuongId = intval($matches[1]);
            $trangThai = urldecode($matches[2]);
            $ungLuongController->updateTrangThai($ungLuongId, $trangThai);
        } elseif (preg_match('/\/doanqlns\/routes\/api\.php\/api\/hop-dong-kpi\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_sua']) || !$_SESSION['quyen_sua']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền sửa hợp đồng"]);
                exit();
            }
            $hopDongId = intval($matches[1]);
            $kpiController->updateHopDongKpi($hopDongId);
        } elseif (preg_match('/\/doanqlns\/routes\/api\.php\/api\/cau-hinh-thang-diem\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_sua']) || !$_SESSION['quyen_sua']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền sửa cấu hình"]);
                exit();
            }
            $cauHinhId = intval($matches[1]);
            $kpiController->updateCauHinhThangDiem($cauHinhId);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/danhgia\/(\d+)\/trang-thai/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_sua']) || !$_SESSION['quyen_sua']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền cập nhật trạng thái đánh giá"]);
                exit();
            }
            $idDanhGia = intval($matches[1]);
            $data = json_decode(file_get_contents("php://input"), true);
            $trangThai = isset($data['trang_thai']) ? $data['trang_thai'] : 'Nháp';
            $danhGiaController->updateTrangThaiDanhGia($idDanhGia, $trangThai);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/hopdong\?id=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_sua']) || !$_SESSION['quyen_sua']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền sửa hợp đồng"]);
                exit();
            }
            $hopDongId = intval($matches[1]);
            $data = json_decode(file_get_contents("php://input"), true);
            $result = $hopDongController->updateHopDong($hopDongId, $data);
            ob_end_clean();
            echo json_encode($result);
        } else {
            http_response_code(404);
            ob_end_clean();
            error_log("DEBUG: No GET route matched for: " . $_SERVER['REQUEST_URI']);
            echo json_encode(["success" => false, "message" => "Invalid route"]);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        if (preg_match('/\/doanqlns\/index\.php\/api\/user\?id=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_xoa']) || !$_SESSION['quyen_xoa']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền xóa nhân viên"]);
                exit();
            }
            $userId = intval($matches[1]);
            $userController->deleteUser($userId);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/chamcong\?id=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_xoa']) || !$_SESSION['quyen_xoa']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền xóa chấm công"]);
                exit();
            }
            $chamCongId = intval($matches[1]);
            $chamCongController->deleteChamCong($chamCongId);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/nghiphep\?id=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            $nghiPhepId = intval($matches[1]);
            $nghiPhepController->deleteNghiPhep($nghiPhepId);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/baohiem\?id=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            $baoHiemId = intval($matches[1]);
            $baoHiemController->deleteBaoHiemThue($baoHiemId);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/nguoiphuthuoc\?id=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_xoa']) || !$_SESSION['quyen_xoa']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền xóa người phụ thuộc"]);
                exit();
            }
            $nguoiPhuThuocId = intval($matches[1]);
            $nguoiPhuThuocController->deleteNguoiPhuThuoc($nguoiPhuThuocId);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/thuong\?id=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_xoa']) || !$_SESSION['quyen_xoa']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền xóa thưởng"]);
                exit();
            }
            $thuongId = intval($matches[1]);
            $thuongController->deleteThuong($thuongId);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/ung-luong\?id=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_xoa']) || !$_SESSION['quyen_xoa']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền xóa ứng lương"]);
                exit();
            }
            $ungLuongId = intval($matches[1]);
            $ungLuongController->deleteUngLuong($ungLuongId);
        } elseif (preg_match('/\/doanqlns\/routes\/api\.php\/api\/hop-dong-kpi\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_xoa']) || !$_SESSION['quyen_xoa']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền xóa hợp đồng"]);
                exit();
            }
            $hopDongId = intval($matches[1]);
            $kpiController->deleteHopDongKpi($hopDongId);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/doanh-so-thang\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_xoa']) || !$_SESSION['quyen_xoa']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền xóa doanh số"]);
                exit();
            }
            $doanhSoId = intval($matches[1]);
            $kpiController->deleteDoanhSoThang($doanhSoId);
        } elseif (preg_match('/\/doanqlns\/routes\/api\.php\/api\/cau-hinh-thang-diem\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_xoa']) || !$_SESSION['quyen_xoa']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền xóa cấu hình"]);
                exit();
            }
            $cauHinhId = intval($matches[1]);
            $kpiController->deleteCauHinhThangDiem($cauHinhId);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/danhgia\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            file_put_contents('debug_danhgia.txt', "DEBUG: Matched danhgia/(\d+) route for: " . $_SERVER['REQUEST_URI'] . "\n", FILE_APPEND);
            if (!isset($_SESSION['quyen_xoa']) || !$_SESSION['quyen_xoa']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền xóa đánh giá"]);
                exit();
            }
            $idDanhGia = intval($matches[1]);
            $danhGiaController->deleteDanhGia($idDanhGia);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/tuyendung\/(\w+)\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_xoa']) || !$_SESSION['quyen_xoa']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền xóa"]);
                exit();
            }
            $type = $matches[1];
            $id = intval($matches[2]);
            $tuyenDungController->handleRequest('DELETE', [], $type, $id);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/settings\/user\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_xoa']) || !$_SESSION['quyen_xoa']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền cập nhật trạng thái người dùng"]);
                exit();
            }
            $userId = intval($matches[1]);
            $result = $settingsController->updateUserStatus($userId);
            ob_end_clean();
            echo json_encode($result);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/settings\/fingerprint\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_xoa']) || !$_SESSION['quyen_xoa']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền xóa vân tay"]);
                exit();
            }
            $fingerprintId = intval($matches[1]);
            $fingerprintController->deleteFingerprint($fingerprintId);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/settings\/face\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_xoa']) || !$_SESSION['quyen_xoa']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền xóa gương mặt"]);
                exit();
            }
            $faceId = intval($matches[1]);
            $settingsController->deleteFace($faceId);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/luong\?id=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_xoa']) || !$_SESSION['quyen_xoa']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền xóa dữ liệu lương"]);
                exit();
            }
            $id_luong = intval($matches[1]);
            $luongController->deleteLuong($id_luong);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/cau-hinh-gio-lam-viec\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_xoa']) || !$_SESSION['quyen_xoa']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền xóa cấu hình giờ làm việc"]);
                exit();
            }
            $id = intval($matches[1]);
            $cauHinhGioLamViecController->xoaCauHinh($id);
        } elseif (preg_match('/\/doanqlns\/routes\/api\.php\/api\/ung-luong\?id=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            $ungLuongId = intval($matches[1]);
            $ungLuongController->deleteUngLuong($ungLuongId);
        } elseif (preg_match('/\/doanqlns\/index\.php\/api\/hopdong\?id=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
            if (!isset($_SESSION['quyen_xoa']) || !$_SESSION['quyen_xoa']) {
                http_response_code(403);
                ob_end_clean();
                echo json_encode(["success" => false, "message" => "Không có quyền xóa hợp đồng"]);
                exit();
            }
            $hopDongId = intval($matches[1]);
            $result = $hopDongController->deleteHopDong($hopDongId);
            ob_end_clean();
            echo json_encode($result);
        } else {
            http_response_code(404);
            ob_end_clean();
            error_log("DEBUG: No GET route matched for: " . $_SERVER['REQUEST_URI']);
            echo json_encode(["success" => false, "message" => "Invalid route"]);
        }
    } else {
        http_response_code(405);
        ob_end_clean();
        echo json_encode(["success" => false, "message" => "Phương thức không được hỗ trợ"]);
    }
} catch (Exception $e) {
    error_log("API error: " . $e->getMessage());
    http_response_code(500);
    ob_end_clean();
    echo json_encode(["success" => false, "message" => "Đã xảy ra lỗi: " . $e->getMessage()]);
}

// User status update route
if (preg_match('/^\/doanqlns\/index\.php\/api\/settings\/user-status\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['quyen_sua']) || !$_SESSION['quyen_sua']) {
        http_response_code(403);
        echo json_encode(["success" => false, "message" => "Không có quyền cập nhật trạng thái"]);
        exit();
    }
    $userId = intval($matches[1]);
    $settingsController->updateUserStatus($userId);
    exit();
}

// =====================================================
// CHAT API ENDPOINTS
// =====================================================

// Get messages for a conversation
if (isset($_GET['action']) && $_GET['action'] === 'get_messages' && isset($_GET['conversation_id'])) {
    $conversationId = intval($_GET['conversation_id']);
    
    // Debug logging
    error_log("API: Getting messages for conversation ID: $conversationId");
    error_log("API: Session user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'not set'));
    
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        // First check if conversation exists and user has access
        $checkQuery = "
            SELECT ctc.id_cuoc_tro_chuyen 
            FROM cuoc_tro_chuyen ctc
            INNER JOIN tham_gia_cuoc_tro_chuyen tgct ON ctc.id_cuoc_tro_chuyen = tgct.id_cuoc_tro_chuyen
            WHERE ctc.id_cuoc_tro_chuyen = ? AND tgct.id_nguoi_dung = ? AND tgct.trang_thai = 'Đang tham gia'
        ";
        
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->execute([$conversationId, $_SESSION['user_id']]);
        
        if ($checkStmt->rowCount() == 0) {
            if (ob_get_level()) {
                ob_end_clean();
            }
            echo json_encode(['success' => false, 'message' => 'Không có quyền truy cập cuộc trò chuyện này']);
            exit();
        }
        
        $query = "
            SELECT 
                tn.id_tin_nhan,
                tn.id_nguoi_gui,
                tn.noi_dung,
                tn.ngay_gui,
                tn.loai_tin_nhan,
                nv.ho_ten as ten_nguoi_gui,
                nv.hinh_anh as avatar_nguoi_gui
            FROM tin_nhan tn
            INNER JOIN nguoi_dung nd ON tn.id_nguoi_gui = nd.id
            INNER JOIN nhan_vien nv ON nd.id_nhan_vien = nv.id_nhan_vien
            WHERE tn.id_cuoc_tro_chuyen = ? AND tn.trang_thai != 'Đã xóa'
            ORDER BY tn.ngay_gui ASC
        ";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([$conversationId]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("API: Found " . count($messages) . " messages");
        
        // Mark messages as read
        $markReadQuery = "
            INSERT INTO trang_thai_doc_tin_nhan (id_tin_nhan, id_nguoi_doc, ngay_doc)
            SELECT tn.id_tin_nhan, ?, NOW()
            FROM tin_nhan tn
            WHERE tn.id_cuoc_tro_chuyen = ? 
            AND tn.id_nguoi_gui != ?
            AND tn.id_tin_nhan NOT IN (
                SELECT id_tin_nhan FROM trang_thai_doc_tin_nhan 
                WHERE id_nguoi_doc = ?
            )
        ";
        
        $markStmt = $conn->prepare($markReadQuery);
        $markStmt->execute([$_SESSION['user_id'], $conversationId, $_SESSION['user_id'], $_SESSION['user_id']]);
        
        // Clear any output buffer
        if (ob_get_level()) {
            ob_end_clean();
        }
        echo json_encode(['success' => true, 'messages' => $messages]);
        exit();
        
    } catch (Exception $e) {
        error_log("API Error: " . $e->getMessage());
        if (ob_get_level()) {
            ob_end_clean();
        }
        echo json_encode(['success' => false, 'message' => 'Lỗi tải tin nhắn: ' . $e->getMessage()]);
        exit();
    }
}

// Send message
if (isset($_POST['action']) && $_POST['action'] === 'send_message') {
    $conversationId = intval($_POST['conversation_id']);
    $message = $_POST['message'];
    
    error_log("API: Send message request - conversationId: $conversationId, message: $message");
    error_log("API: Session user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'not set'));
    
    if (!$conversationId || !$message) {
        if (ob_get_level()) {
            ob_end_clean();
        }
        echo json_encode(['success' => false, 'message' => 'Thiếu thông tin cần thiết']);
        exit();
    }
    
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        // Check if user has access to conversation
        $checkQuery = "
            SELECT ctc.id_cuoc_tro_chuyen 
            FROM cuoc_tro_chuyen ctc
            INNER JOIN tham_gia_cuoc_tro_chuyen tgct ON ctc.id_cuoc_tro_chuyen = tgct.id_cuoc_tro_chuyen
            WHERE ctc.id_cuoc_tro_chuyen = ? AND tgct.id_nguoi_dung = ? AND tgct.trang_thai = 'Đang tham gia'
        ";
        
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->execute([$conversationId, $_SESSION['user_id']]);
        
        if ($checkStmt->rowCount() == 0) {
            if (ob_get_level()) {
                ob_end_clean();
            }
            echo json_encode(['success' => false, 'message' => 'Không có quyền gửi tin nhắn trong cuộc trò chuyện này']);
            exit();
        }
        
        $query = "
            INSERT INTO tin_nhan (id_cuoc_tro_chuyen, id_nguoi_gui, noi_dung, loai_tin_nhan)
            VALUES (?, ?, ?, 'Văn bản')
        ";
        
        $stmt = $conn->prepare($query);
        $result = $stmt->execute([
            $conversationId,
            $_SESSION['user_id'],
            $message
        ]);
        
        error_log("API: Message insert result: " . ($result ? 'success' : 'failed'));
        
        if ($result) {
            // Update conversation last update time
            $updateQuery = "UPDATE cuoc_tro_chuyen SET ngay_cap_nhat = NOW() WHERE id_cuoc_tro_chuyen = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->execute([$conversationId]);
            
            error_log("API: Message sent successfully");
            ob_end_clean();
            echo json_encode(['success' => true, 'message' => 'Tin nhắn đã được gửi']);
        } else {
            error_log("API: Failed to insert message");
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Lỗi gửi tin nhắn']);
        }
        exit();
        
    } catch (Exception $e) {
        error_log("API: Send message error: " . $e->getMessage());
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Lỗi gửi tin nhắn: ' . $e->getMessage()]);
        exit();
    }
}

// Create new conversation
if ($_SERVER['REQUEST_URI'] === '/api/chat/create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['recipientId'])) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Thiếu thông tin người nhận']);
        exit();
    }
    
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        // Check if conversation already exists
        $checkQuery = "
            SELECT ctc.id_cuoc_tro_chuyen
            FROM cuoc_tro_chuyen ctc
            INNER JOIN tham_gia_cuoc_tro_chuyen tgct1 ON ctc.id_cuoc_tro_chuyen = tgct1.id_cuoc_tro_chuyen
            INNER JOIN tham_gia_cuoc_tro_chuyen tgct2 ON ctc.id_cuoc_tro_chuyen = tgct2.id_cuoc_tro_chuyen
            WHERE tgct1.id_nguoi_dung = ? AND tgct2.id_nguoi_dung = ?
            AND ctc.loai_cuoc_tro_chuyen = 'Riêng tư'
            AND tgct1.trang_thai = 'Đang tham gia' AND tgct2.trang_thai = 'Đang tham gia'
        ";
        
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->execute([$_SESSION['user_id'], $data['recipientId']]);
        $existingConv = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingConv) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Cuộc trò chuyện đã tồn tại']);
            exit();
        }
        
        // Get recipient name
        $recipientQuery = "
            SELECT nv.ho_ten 
            FROM nhan_vien nv 
            INNER JOIN nguoi_dung nd ON nv.id_nhan_vien = nd.id_nhan_vien 
            WHERE nd.id = ?
        ";
        $recipientStmt = $conn->prepare($recipientQuery);
        $recipientStmt->execute([$data['recipientId']]);
        $recipient = $recipientStmt->fetch(PDO::FETCH_ASSOC);
        
        // Create conversation
        $convQuery = "
            INSERT INTO cuoc_tro_chuyen (ten_cuoc_tro_chuyen, loai_cuoc_tro_chuyen, id_nguoi_tao)
            VALUES (?, 'Riêng tư', ?)
        ";
        
        $convStmt = $conn->prepare($convQuery);
        $convStmt->execute([$recipient['ho_ten'], $_SESSION['user_id']]);
        $conversationId = $conn->lastInsertId();
        
        // Add participants
        $participantQuery = "
            INSERT INTO tham_gia_cuoc_tro_chuyen (id_cuoc_tro_chuyen, id_nguoi_dung, vai_tro)
            VALUES (?, ?, 'Thành viên')
        ";
        
        $participantStmt = $conn->prepare($participantQuery);
        $participantStmt->execute([$conversationId, $_SESSION['user_id']]);
        $participantStmt->execute([$conversationId, $data['recipientId']]);
        
        // Send first message if provided
        if (!empty($data['firstMessage'])) {
            $messageQuery = "
                INSERT INTO tin_nhan (id_cuoc_tro_chuyen, id_nguoi_gui, noi_dung, loai_tin_nhan)
                VALUES (?, ?, ?, 'Văn bản')
            ";
            $messageStmt = $conn->prepare($messageQuery);
            $messageStmt->execute([$conversationId, $_SESSION['user_id'], $data['firstMessage']]);
        }
        
        ob_end_clean();
        echo json_encode([
            'success' => true, 
            'conversationId' => $conversationId, 
            'conversationName' => $recipient['ho_ten'],
            'message' => 'Cuộc trò chuyện đã được tạo'
        ]);
        exit();
        
    } catch (Exception $e) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Lỗi tạo cuộc trò chuyện: ' . $e->getMessage()]);
        exit();
    }
}

// ===== KPI ROUTES =====

// KPI routes đã được di chuyển lên phần GET ở trên

ob_end_flush();
?>