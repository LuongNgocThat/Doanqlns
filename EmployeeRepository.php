<?php
require_once 'config/Database.php';

class EmployeeRepository {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getPunctualityComparison() {
        try {
            $currentMonth = date('Y-m');
            $previousMonth = date('Y-m', strtotime('-1 month'));

            $currentRate = $this->getPunctualityRate($currentMonth);
            $previousRate = $this->getPunctualityRate($previousMonth);

            if ($currentRate === null) {
                $currentRate = 0;
            }
            if ($previousRate === null) {
                $previousRate = 0;
            }

            return [
                'current' => $currentRate,
                'difference' => $currentRate - $previousRate
            ];
        } catch (PDOException $e) {
            error_log("Error in getPunctualityComparison: " . $e->getMessage());
            return ['current' => 0, 'difference' => 0];
        }
    }

    public function getPunctualityRate($thang = null) {
        try {
            if ($thang === null) {
                $thang = date('Y-m');
            }
            
            $startDate = $thang . '-01';
            $endDate = date('Y-m-t', strtotime($startDate));
            
            $checkQuery = "SELECT COUNT(*) FROM cham_cong 
                          WHERE ngay_lam_viec BETWEEN :start_date AND :end_date";
            $stmt = $this->conn->prepare($checkQuery);
            $stmt->bindParam(':start_date', $startDate);
            $stmt->bindParam(':end_date', $endDate);
            $stmt->execute();
            
            if ($stmt->fetchColumn() == 0) {
                return 0;
            }
            
            $query = "SELECT 
                (SUM(CASE WHEN trang_thai = 'Đúng giờ' THEN 1 ELSE 0 END) / COUNT(*)) * 100 as rate
                FROM cham_cong 
                WHERE ngay_lam_viec BETWEEN :start_date AND :end_date";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':start_date', $startDate);
            $stmt->bindParam(':end_date', $endDate);
            $stmt->execute();
            
            return round($stmt->fetchColumn(), 1);
        } catch (PDOException $e) {
            error_log("Error in getPunctualityRate: " . $e->getMessage());
            return 0;
        }
    }

    public function getPendingLeaveCount() {
        try {
            $sql = "SELECT COUNT(*) as count FROM nghi_phep WHERE trang_thai1 = 'Chờ duyệt '";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error in getPendingLeaveCount: " . $e->getMessage());
            return 0;
        }
    }

    public function getTotalEmployees() {
        try {
            $query = "SELECT COUNT(*) as total FROM nhan_vien WHERE trang_thai = 'Đang làm việc'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error in getTotalEmployees: " . $e->getMessage());
            return 0;
        }
    }

    public function getNewEmployees($month = null) {
        try {
            if ($month === null) {
                $month = date('Y-m');
            }
            $query = "SELECT COUNT(*) as new_employees 
                     FROM nhan_vien 
                     WHERE DATE_FORMAT(ngay_vao_lam, '%Y-%m') = :month 
                     AND ngay_vao_lam IS NOT NULL";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':month', $month);
            $stmt->execute();
            $count = (int)$stmt->fetchColumn();
            if ($count === 0) {
                error_log("No new employees found for month: $month");
            }
            return $count;
        } catch (PDOException $e) {
            error_log("Error in getNewEmployees: " . $e->getMessage());
            return 0;
        }
    }

   public function updateEmployeeFromCSV($employeeId, $data) {
        try {
            error_log("EmployeeRepository: Executing updateEmployeeFromCSV for employeeId: $employeeId");

            $sql = "
                UPDATE nhan_vien SET
                    ho_ten = :ho_ten,
                    gioi_tinh = :gioi_tinh,
                    ngay_sinh = :ngay_sinh,
                    so_dien_thoai = :so_dien_thoai,
                    dia_chi = :dia_chi,
                    can_cuoc_cong_dan = :can_cuoc_cong_dan,
                    ngay_cap = :ngay_cap,
                    noi_cap = :noi_cap,
                    que_quan = :que_quan,
                    hinh_anh = :hinh_anh,
                    email = :email,
                    id_phong_ban = :id_phong_ban,
                    id_chuc_vu = :id_chuc_vu,
                    loai_hop_dong = :loai_hop_dong,
                    ngay_vao_lam = :ngay_vao_lam,
                    trang_thai = :trang_thai,
                    luong_co_ban = :luong_co_ban
                WHERE id_nhan_vien = :id_nhan_vien
            ";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_nhan_vien', $employeeId, PDO::PARAM_INT);
            $stmt->bindParam(':ho_ten', $data['ho_ten']);
            $stmt->bindParam(':gioi_tinh', $data['gioi_tinh']);
            $stmt->bindParam(':ngay_sinh', $data['ngay_sinh']);
            $stmt->bindParam(':so_dien_thoai', $data['so_dien_thoai']);
            $stmt->bindParam(':dia_chi', $data['dia_chi']);
            $stmt->bindParam(':can_cuoc_cong_dan', $data['can_cuoc_cong_dan']);
            $stmt->bindParam(':ngay_cap', $data['ngay_cap']);
            $stmt->bindParam(':noi_cap', $data['noi_cap']);
            $stmt->bindParam(':que_quan', $data['que_quan']);
            $stmt->bindParam(':hinh_anh', $data['hinh_anh']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':id_phong_ban', $data['id_phong_ban'], PDO::PARAM_INT);
            $stmt->bindParam(':id_chuc_vu', $data['id_chuc_vu'], PDO::PARAM_INT);
            $stmt->bindParam(':loai_hop_dong', $data['loai_hop_dong']);
            $stmt->bindParam(':ngay_vao_lam', $data['ngay_vao_lam']);
            $stmt->bindParam(':trang_thai', $data['trang_thai']);
            $stmt->bindParam(':luong_co_ban', $data['luong_co_ban'], PDO::PARAM_STR);

            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                error_log("EmployeeRepository: No rows updated for employeeId: $employeeId");
                throw new Exception("Không có thay đổi hoặc nhân viên không tồn tại.");
            }

            error_log("EmployeeRepository: Successfully updated employeeId: $employeeId");
        } catch (PDOException $e) {
            error_log("EmployeeRepository: Lỗi khi cập nhật nhân viên từ CSV: " . $e->getMessage());
            throw new Exception("Lỗi database: " . $e->getMessage());
        }
    }

    // Loại bỏ addEmployeeFromCSV hoặc giữ nguyên với log lỗi nếu cần
    public function addEmployeeFromCSV($data) {
        try {
            error_log("EmployeeRepository: Warning: addEmployeeFromCSV called with data: " . json_encode($data));
            throw new Exception("Hàm addEmployeeFromCSV không nên được gọi khi cập nhật nhân viên.");
        } catch (PDOException $e) {
            error_log("EmployeeRepository: Lỗi khi thêm nhân viên từ CSV: " . $e->getMessage());
            throw new Exception("Lỗi database: " . $e->getMessage());
        }
    }
    
    // Thống kê thai sản
    public function getMaternityStats() {
        try {
            $today = date('Y-m-d');
            $thirtyDaysFromNow = date('Y-m-d', strtotime('+30 days'));
            
            // Tổng số nhân viên đang nghỉ thai sản
            $totalMaternityQuery = "SELECT COUNT(*) FROM nhan_vien WHERE trang_thai = 'Nghỉ thai sản'";
            $stmt = $this->conn->prepare($totalMaternityQuery);
            $stmt->execute();
            $totalMaternity = $stmt->fetchColumn();
            
            // Số nhân viên sắp hết thai sản (≤30 ngày)
            $endingSoonQuery = "SELECT COUNT(*) FROM nhan_vien 
                               WHERE trang_thai = 'Nghỉ thai sản' 
                               AND ngay_ket_thuc_thai_san IS NOT NULL 
                               AND ngay_ket_thuc_thai_san BETWEEN :today AND :thirty_days";
            $stmt = $this->conn->prepare($endingSoonQuery);
            $stmt->bindParam(':today', $today);
            $stmt->bindParam(':thirty_days', $thirtyDaysFromNow);
            $stmt->execute();
            $endingSoon = $stmt->fetchColumn();
            
            // Số nhân viên quá hạn thai sản
            $overdueQuery = "SELECT COUNT(*) FROM nhan_vien 
                            WHERE trang_thai = 'Nghỉ thai sản' 
                            AND ngay_ket_thuc_thai_san IS NOT NULL 
                            AND ngay_ket_thuc_thai_san < :today";
            $stmt = $this->conn->prepare($overdueQuery);
            $stmt->bindParam(':today', $today);
            $stmt->execute();
            $overdue = $stmt->fetchColumn();
            
            // Danh sách nhân viên sắp hết thai sản
            $endingSoonListQuery = "SELECT n.id_nhan_vien, n.ho_ten, p.ten_phong_ban, n.ngay_ket_thuc_thai_san 
                                   FROM nhan_vien n 
                                   LEFT JOIN phong_ban p ON n.id_phong_ban = p.id_phong_ban
                                   WHERE n.trang_thai = 'Nghỉ thai sản' 
                                   AND n.ngay_ket_thuc_thai_san IS NOT NULL 
                                   AND n.ngay_ket_thuc_thai_san BETWEEN :today AND :thirty_days
                                   ORDER BY n.ngay_ket_thuc_thai_san ASC";
            $stmt = $this->conn->prepare($endingSoonListQuery);
            $stmt->bindParam(':today', $today);
            $stmt->bindParam(':thirty_days', $thirtyDaysFromNow);
            $stmt->execute();
            $endingSoonList = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Danh sách nhân viên quá hạn thai sản
            $overdueListQuery = "SELECT n.id_nhan_vien, n.ho_ten, p.ten_phong_ban, n.ngay_ket_thuc_thai_san 
                                FROM nhan_vien n 
                                LEFT JOIN phong_ban p ON n.id_phong_ban = p.id_phong_ban
                                WHERE n.trang_thai = 'Nghỉ thai sản' 
                                AND n.ngay_ket_thuc_thai_san IS NOT NULL 
                                AND n.ngay_ket_thuc_thai_san < :today
                                ORDER BY n.ngay_ket_thuc_thai_san ASC";
            $stmt = $this->conn->prepare($overdueListQuery);
            $stmt->bindParam(':today', $today);
            $stmt->execute();
            $overdueList = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'total' => (int)$totalMaternity,
                'ending_soon' => (int)$endingSoon,
                'overdue' => (int)$overdue,
                'ending_soon_list' => $endingSoonList,
                'overdue_list' => $overdueList
            ];
        } catch (PDOException $e) {
            error_log("Error in getMaternityStats: " . $e->getMessage());
            return [
                'total' => 0,
                'ending_soon' => 0,
                'overdue' => 0,
                'ending_soon_list' => [],
                'overdue_list' => []
            ];
        }
    }
    
    
}
?>