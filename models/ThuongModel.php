<?php
include_once(__DIR__ . '/../config/Database.php');

class ThuongModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllThuong() {
        $query = "SELECT * FROM thuong t 
                  INNER JOIN nhan_vien nv ON t.id_nhan_vien = nv.id_nhan_vien";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $users;
    }

    public function addThuong($data) {
        $query = "INSERT INTO thuong (id_nhan_vien, noi_dung_thuong, ngay, loai, tien_thuong) 
                  VALUES (:id_nhan_vien, :noi_dung_thuong, :ngay, :loai, :tien_thuong)";
        $stmt = $this->conn->prepare($query);

        // Sử dụng tiền thưởng từ form hoặc giá trị mặc định
        $tien_thuong = isset($data['tien_thuong']) && $data['tien_thuong'] !== '' 
            ? floatval($data['tien_thuong']) 
            : $this->getDefaultAmount($data['loai']);

        $stmt->bindParam(':id_nhan_vien', $data['id_nhan_vien']);
        $stmt->bindParam(':noi_dung_thuong', $data['noi_dung_thuong']);
        $stmt->bindParam(':ngay', $data['ngay']);
        $stmt->bindParam(':loai', $data['loai']);
        $stmt->bindParam(':tien_thuong', $tien_thuong);

        $success = $stmt->execute();

        if ($success) {
            // Cập nhật bảng luong sau khi thêm thưởng/phạt
            $this->updateLuong($data['id_nhan_vien'], date('Y-m', strtotime($data['ngay'])));
        }

        return $success;
    }

    public function updateThuong($thuongId, $data) {
        $query = "UPDATE thuong 
                  SET id_nhan_vien = :id_nhan_vien, 
                      noi_dung_thuong = :noi_dung_thuong, 
                      ngay = :ngay, 
                      loai = :loai, 
                      tien_thuong = :tien_thuong 
                  WHERE id_thuong = :id_thuong";
        $stmt = $this->conn->prepare($query);

        // Sử dụng tiền thưởng từ form hoặc giá trị mặc định
        $tien_thuong = isset($data['tien_thuong']) && $data['tien_thuong'] !== '' 
            ? floatval($data['tien_thuong']) 
            : $this->getDefaultAmount($data['loai']);

        $stmt->bindParam(':id_thuong', $thuongId);
        $stmt->bindParam(':id_nhan_vien', $data['id_nhan_vien']);
        $stmt->bindParam(':noi_dung_thuong', $data['noi_dung_thuong']);
        $stmt->bindParam(':ngay', $data['ngay']);
        $stmt->bindParam(':loai', $data['loai']);
        $stmt->bindParam(':tien_thuong', $tien_thuong);

        $success = $stmt->execute();

        if ($success) {
            // Cập nhật bảng luong sau khi sửa thưởng/phạt
            $this->updateLuong($data['id_nhan_vien'], date('Y-m', strtotime($data['ngay'])));
        }

        return $success;
    }

    public function deleteThuong($thuongId) {
        $query = "SELECT id_nhan_vien, ngay FROM thuong WHERE id_thuong = :id_thuong";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_thuong', $thuongId);
        $stmt->execute();
        $thuong = $stmt->fetch(PDO::FETCH_ASSOC);

        $success = false;
        if ($thuong) {
            $query = "DELETE FROM thuong WHERE id_thuong = :id_thuong";
            $stmt = $this->conn->prepare($query);
            $success = $stmt->execute([':id_thuong' => $thuongId]);

            if ($success) {
                // Cập nhật bảng luong sau khi xóa thưởng/phạt
                $this->updateLuong($thuong['id_nhan_vien'], date('Y-m', strtotime($thuong['ngay'])));
            }
        }

        return $success;
    }

    public function getBonusTypes() {
        try {
            $query = "SELECT COLUMN_TYPE 
                      FROM INFORMATION_SCHEMA.COLUMNS 
                      WHERE TABLE_NAME = 'thuong' 
                      AND COLUMN_NAME = 'loai'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Extract ENUM values from COLUMN_TYPE
            if ($result && preg_match("/enum\('(.*?)'\)/", $result['COLUMN_TYPE'], $matches)) {
                return explode("','", $matches[1]);
            }
            return [];
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy danh sách loại thưởng/phạt: " . $e->getMessage());
            return [];
        }
    }

    // Thêm method mới để lấy giá trị mặc định
    private function getDefaultAmount($loai) {
        try {
            // Ưu tiên lấy từ bảng cấu hình quan_ly_thuong
            $stmt = $this->conn->prepare("SELECT so_tien_mac_dinh FROM quan_ly_thuong WHERE loai = :loai LIMIT 1");
            $stmt->bindParam(':loai', $loai);
            if ($stmt->execute()) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row && isset($row['so_tien_mac_dinh'])) {
                    return (float)$row['so_tien_mac_dinh'];
                }
            }
        } catch (PDOException $e) {
            error_log('getDefaultAmount fallback to hardcoded due to error: ' . $e->getMessage());
        }
        // Fallback cứng nếu chưa có cấu hình trong bảng
        return match ($loai) {
            'nghỉ lễ' => 1500000,
            'thăng chức' => 2000000,
            'thành tích cá nhân' => 1000000,
            'phạt kỷ luật' => -1000000,
            'phạt trách nhiệm công việc' => -1500000,
            default => 0
        };
    }

    private function updateLuong($id_nhan_vien, $thang) {
        $query = "CALL CalculatePayroll(:id_nhan_vien, :thang, NULL)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_nhan_vien', $id_nhan_vien);
        $stmt->bindParam(':thang', $thang);
        $stmt->execute();
    }
}
?>