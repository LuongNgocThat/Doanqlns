<?php
class ChamCongModel {
    private $conn;

    public function __construct() {
        $this->conn = new mysqli("localhost", "root", "", "doanqlns");

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }
public function getAllChamCong() {
    $query = "SELECT c.*, nv.ho_ten 
              FROM CHAM_CONG c 
              INNER JOIN NHAN_VIEN nv ON c.id_nhan_vien = nv.id_nhan_vien";
    $result = $this->conn->query($query);

    $records = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Đảm bảo trạng thái được xử lý đúng
            if (in_array($row['trang_thai'], ['Có phép', 'Không phép', 'Phép Năm', 'Chưa điểm danh', 'Đúng giờ', 'Đi trễ', 'Nghỉ Lễ'])) {
                $records[] = $row;
            }
        }
    }
    return $records;
}

    public function addChamCong($data) {
        try {
            $query = "INSERT INTO CHAM_CONG (id_nhan_vien, ngay_lam_viec, gio_vao, gio_ra, trang_thai, ghi_chu, month, year) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Lỗi chuẩn bị truy vấn: " . $this->conn->error);
            }
            $stmt->bind_param(
                "isssssii",
                $data['id_nhan_vien'],
                $data['ngay_lam_viec'],
                $data['gio_vao'],
                $data['gio_ra'],
                $data['trang_thai'],
                $data['ghi_chu'],
                $data['month'],
                $data['year']
            );
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        } catch (Exception $e) {
            error_log("Lỗi trong addChamCong: " . $e->getMessage());
            throw $e;
        }
    }

    public function getChamCongByYear($year) {
        try {
            $query = "SELECT * FROM CHAM_CONG WHERE YEAR(ngay_lam_viec) = ?";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Lỗi chuẩn bị truy vấn: " . $this->conn->error);
            }
            $stmt->bind_param("i", $year);
            $stmt->execute();
            $result = $stmt->get_result();
            $records = [];
            while ($row = $result->fetch_assoc()) {
                $records[] = $row;
            }
            $stmt->close();
            return $records;
        } catch (Exception $e) {
            error_log("Lỗi trong getChamCongByYear: " . $e->getMessage());
            throw $e;
        }
    }
}
?>