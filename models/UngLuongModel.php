<?php
require_once __DIR__ . '/../config/Database.php';

class UngLuongModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Lấy tất cả ứng lương với thông tin nhân viên
    public function getAllUngLuong() {
        try {
            $sql = "
                SELECT 
                    ul.id_ung_luong,
                    ul.id_nhan_vien,
                    ul.so_tien_ung,
                    ul.ngay_ung,
                    ul.ly_do_ung,
                    ul.trang_thai,
                    ul.ngay_tao,
                    ul.ngay_cap_nhat,
                    nv.ho_ten,
                    nv.gioi_tinh,
                    nv.ngay_sinh,
                    nv.email,
                    nv.so_dien_thoai,
                    nv.dia_chi,
                    pb.ten_phong_ban,
                    cv.ten_chuc_vu
                FROM ung_luong ul
                LEFT JOIN nhan_vien nv ON ul.id_nhan_vien = nv.id_nhan_vien
                LEFT JOIN phong_ban pb ON nv.id_phong_ban = pb.id_phong_ban
                LEFT JOIN chuc_vu cv ON nv.id_chuc_vu = cv.id_chuc_vu
                ORDER BY ul.ngay_ung DESC, ul.id_ung_luong DESC
            ";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getAllUngLuong: " . $e->getMessage());
            return [];
        }
    }

    // Lấy ứng lương theo tháng/năm
    public function getUngLuongByMonth($thang, $nam) {
        try {
            $sql = "
                SELECT 
                    ul.id_ung_luong,
                    ul.id_nhan_vien,
                    ul.so_tien_ung,
                    ul.ngay_ung,
                    ul.ly_do_ung,
                    ul.trang_thai,
                    ul.ngay_tao,
                    ul.ngay_cap_nhat,
                    nv.ho_ten,
                    nv.gioi_tinh,
                    nv.ngay_sinh,
                    nv.email,
                    nv.so_dien_thoai,
                    nv.dia_chi,
                    pb.ten_phong_ban,
                    cv.ten_chuc_vu
                FROM ung_luong ul
                LEFT JOIN nhan_vien nv ON ul.id_nhan_vien = nv.id_nhan_vien
                LEFT JOIN phong_ban pb ON nv.id_phong_ban = pb.id_phong_ban
                LEFT JOIN chuc_vu cv ON nv.id_chuc_vu = cv.id_chuc_vu
                WHERE MONTH(ul.ngay_ung) = :thang AND YEAR(ul.ngay_ung) = :nam
                ORDER BY ul.ngay_ung DESC, ul.id_ung_luong DESC
            ";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':thang', $thang, PDO::PARAM_INT);
            $stmt->bindParam(':nam', $nam, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getUngLuongByMonth: " . $e->getMessage());
            return [];
        }
    }

    // Thêm ứng lương mới
    public function addUngLuong($data) {
        try {
            // Validate data
            if (!$data || !is_array($data)) {
                error_log("Invalid data in addUngLuong");
                return false;
            }
            
            // Check required fields
            if (!isset($data['id_nhan_vien']) || !isset($data['so_tien_ung']) || !isset($data['ngay_ung'])) {
                error_log("Missing required fields in addUngLuong");
                return false;
            }
            
            $sql = "
                INSERT INTO ung_luong (id_nhan_vien, so_tien_ung, ngay_ung, ly_do_ung, trang_thai)
                VALUES (:id_nhan_vien, :so_tien_ung, :ngay_ung, :ly_do_ung, :trang_thai)
            ";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_nhan_vien', $data['id_nhan_vien'], PDO::PARAM_INT);
            $stmt->bindParam(':so_tien_ung', $data['so_tien_ung'], PDO::PARAM_STR);
            $stmt->bindParam(':ngay_ung', $data['ngay_ung'], PDO::PARAM_STR);
            $stmt->bindParam(':ly_do_ung', $data['ly_do_ung'], PDO::PARAM_STR);
            $stmt->bindParam(':trang_thai', $data['trang_thai'], PDO::PARAM_STR);
            
            $result = $stmt->execute();
            if (!$result) {
                error_log("Failed to execute addUngLuong query");
            }
            return $result;
        } catch (PDOException $e) {
            error_log("Error in addUngLuong: " . $e->getMessage());
            return false;
        }
    }

    // Cập nhật ứng lương
    public function updateUngLuong($id, $data) {
        try {
            // Validate data
            if (!$data || !is_array($data)) {
                error_log("Invalid data in updateUngLuong");
                return false;
            }
            
            // Check required fields
            if (!isset($data['id_nhan_vien']) || !isset($data['so_tien_ung']) || !isset($data['ngay_ung'])) {
                error_log("Missing required fields in updateUngLuong");
                return false;
            }
            
            $sql = "
                UPDATE ung_luong 
                SET id_nhan_vien = :id_nhan_vien,
                    so_tien_ung = :so_tien_ung, 
                    ngay_ung = :ngay_ung, 
                    ly_do_ung = :ly_do_ung, 
                    trang_thai = :trang_thai
                WHERE id_ung_luong = :id
            ";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':id_nhan_vien', $data['id_nhan_vien'], PDO::PARAM_INT);
            $stmt->bindParam(':so_tien_ung', $data['so_tien_ung'], PDO::PARAM_STR);
            $stmt->bindParam(':ngay_ung', $data['ngay_ung'], PDO::PARAM_STR);
            $stmt->bindParam(':ly_do_ung', $data['ly_do_ung'], PDO::PARAM_STR);
            $stmt->bindParam(':trang_thai', $data['trang_thai'], PDO::PARAM_STR);
            
            $result = $stmt->execute();
            if (!$result) {
                error_log("Failed to execute updateUngLuong query");
            }
            return $result;
        } catch (PDOException $e) {
            error_log("Error in updateUngLuong: " . $e->getMessage());
            return false;
        }
    }

    // Xóa ứng lương
    public function deleteUngLuong($id) {
        try {
            $sql = "DELETE FROM ung_luong WHERE id_ung_luong = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in deleteUngLuong: " . $e->getMessage());
            return false;
        }
    }

    // Lấy ứng lương theo ID
    public function getUngLuongById($id) {
        try {
            $sql = "
                SELECT 
                    ul.*,
                    nv.ho_ten,
                    nv.gioi_tinh,
                    nv.ngay_sinh,
                    nv.email,
                    nv.so_dien_thoai,
                    nv.dia_chi,
                    pb.ten_phong_ban,
                    cv.ten_chuc_vu
                FROM ung_luong ul
                LEFT JOIN nhan_vien nv ON ul.id_nhan_vien = nv.id_nhan_vien
                LEFT JOIN phong_ban pb ON nv.id_phong_ban = pb.id_phong_ban
                LEFT JOIN chuc_vu cv ON nv.id_chuc_vu = cv.id_chuc_vu
                WHERE ul.id_ung_luong = :id
            ";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getUngLuongById: " . $e->getMessage());
            return false;
        }
    }

    // Cập nhật trạng thái ứng lương
    public function updateTrangThai($id, $trang_thai) {
        try {
            $sql = "UPDATE ung_luong SET trang_thai = :trang_thai WHERE id_ung_luong = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':trang_thai', $trang_thai, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in updateTrangThai: " . $e->getMessage());
            return false;
        }
    }
}
?>
