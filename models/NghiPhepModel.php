<?php
class NghiPhepModel {
    private $conn;

    public function __construct() {
        $this->conn = new mysqli("localhost", "root", "", "doanqlns");
        if ($this->conn->connect_error) {
            error_log("Kết nối cơ sở dữ liệu thất bại: " . $this->conn->connect_error);
            throw new Exception("Không thể kết nối đến cơ sở dữ liệu");
        }
    }

    public function getAllNghiPhep() {
        try {
            $query = "SELECT p.*, nv.ho_ten, nv.gioi_tinh, nv.ngay_sinh, nv.email, nv.so_dien_thoai, nv.dia_chi, 
                             pb.ten_phong_ban AS phong_ban, cv.ten_chuc_vu AS chuc_vu 
                      FROM nghi_phep p 
                      INNER JOIN nhan_vien nv ON p.id_nhan_vien = nv.id_nhan_vien 
                      LEFT JOIN phong_ban pb ON nv.id_phong_ban = pb.id_phong_ban 
                      LEFT JOIN chuc_vu cv ON nv.id_chuc_vu = cv.id_chuc_vu";
            $result = $this->conn->query($query);
            if (!$result) {
                error_log("Lỗi truy vấn SQL: " . $this->conn->error);
                throw new Exception("Lỗi truy vấn cơ sở dữ liệu: " . $this->conn->error);
            }
            $records = [];
            while ($row = $result->fetch_assoc()) {
                $records[] = $row;
            }
            return $records;
        } catch (Exception $e) {
            error_log("Lỗi trong getAllNghiPhep: " . $e->getMessage());
            throw $e;
        }
    }

    public function getDonNghiPhep($idNghiPhep) {
        try {
            $query = "SELECT np.*, nv.ho_ten, nv.email 
                      FROM nghi_phep np
                      JOIN nhan_vien nv ON np.id_nhan_vien = nv.id_nhan_vien
                      WHERE np.id_nghi_phep = ?";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Lỗi chuẩn bị truy vấn: " . $this->conn->error);
            }
            $stmt->bind_param("i", $idNghiPhep);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();
            if (!$data) {
                error_log("Không tìm thấy đơn nghỉ phép với ID: $idNghiPhep");
                throw new Exception("Không tìm thấy đơn nghỉ phép");
            }
            return $data;
        } catch (Exception $e) {
            error_log("Lỗi trong getDonNghiPhep: " . $e->getMessage());
            throw $e;
        }
    }

    public function calculateUsedAnnualLeaveDays($idNhanVien, $year) {
        try {
            $query = "SELECT ngay_bat_dau, ngay_ket_thuc 
                      FROM nghi_phep 
                      WHERE id_nhan_vien = ? AND loai_nghi = 'Phép Năm' AND trang_thai1 = 'Đã duyệt' 
                      AND YEAR(ngay_bat_dau) = ?";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Lỗi chuẩn bị truy vấn: " . $this->conn->error);
            }
            $stmt->bind_param("ii", $idNhanVien, $year);
            $stmt->execute();
            $result = $stmt->get_result();

            $totalDays = 0;
            while ($row = $result->fetch_assoc()) {
                $startDate = new DateTime($row['ngay_bat_dau']);
                $endDate = new DateTime($row['ngay_ket_thuc']);
                while ($startDate <= $endDate) {
                    if ($startDate->format('w') !== '0') { // Bỏ qua Chủ Nhật
                        $totalDays++;
                    }
                    $startDate->modify('+1 day');
                }
            }
            $stmt->close();
            return $totalDays;
        } catch (Exception $e) {
            error_log("Lỗi trong calculateUsedAnnualLeaveDays: " . $e->getMessage());
            throw $e;
        }
    }

    public function getEmployeeName($idNhanVien) {
        try {
            $query = "SELECT ho_ten FROM nhan_vien WHERE id_nhan_vien = ?";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Lỗi chuẩn bị truy vấn: " . $this->conn->error);
            }
            $stmt->bind_param("i", $idNhanVien);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();
            return $data ? $data['ho_ten'] : 'Nhân viên';
        } catch (Exception $e) {
            error_log("Lỗi trong getEmployeeName: " . $e->getMessage());
            return 'Nhân viên';
        }
    }

    public function addNghiPhep($idNhanVien, $ngayBatDau, $ngayKetThuc, $lyDo, $loaiNghi, $trangThai, $lyDoTuChoi = null, $minhChung = null) {
        try {
            // Kiểm tra nhân viên tồn tại
            $queryCheck = "SELECT id_nhan_vien FROM nhan_vien WHERE id_nhan_vien = ?";
            $stmtCheck = $this->conn->prepare($queryCheck);
            if (!$stmtCheck) {
                throw new Exception("Lỗi chuẩn bị truy vấn kiểm tra nhân viên: " . $this->conn->error);
            }
            $stmtCheck->bind_param("i", $idNhanVien);
            $stmtCheck->execute();
            $resultCheck = $stmtCheck->get_result();
            if ($resultCheck->num_rows === 0) {
                $stmtCheck->close();
                throw new Exception("Nhân viên với ID $idNhanVien không tồn tại");
            }
            $stmtCheck->close();

            // Chèn bản ghi vào bảng nghi_phep
            $query = "INSERT INTO nghi_phep (id_nhan_vien, ngay_bat_dau, ngay_ket_thuc, ly_do, loai_nghi, trang_thai1, ly_do_tu_choi, minh_chung) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Lỗi chuẩn bị truy vấn: " . $this->conn->error);
            }
            $stmt->bind_param("isssssss", $idNhanVien, $ngayBatDau, $ngayKetThuc, $lyDo, $loaiNghi, $trangThai, $lyDoTuChoi, $minhChung);
            $success = $stmt->execute();

            if ($success) {
                $idNghiPhep = $this->conn->insert_id;
                $stmt->close();
                error_log("Thêm đơn nghỉ phép thành công, ID: $idNghiPhep, ly_do_tu_choi: " . ($lyDoTuChoi ?? 'NULL'));
                return $idNghiPhep;
            } else {
                error_log("Lỗi SQL khi thêm nghi_phep: " . $stmt->error);
                $stmt->close();
                throw new Exception("Lỗi khi thêm đơn nghỉ phép: " . $stmt->error);
            }
        } catch (Exception $e) {
            error_log("Lỗi trong addNghiPhep: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateNghiPhep($id, $trangThai, $idNguoiDuyet, $ngayDuyet, $lyDoTuChoi) {
        try {
            $query = "UPDATE nghi_phep SET trang_thai1 = ?, id_nguoi_duyet = ?, ngay_duyet = ?, ly_do_tu_choi = ? WHERE id_nghi_phep = ?";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Lỗi chuẩn bị truy vấn: " . $this->conn->error);
            }
            $stmt->bind_param("sissi", $trangThai, $idNguoiDuyet, $ngayDuyet, $lyDoTuChoi, $id);
            $success = $stmt->execute();
            $stmt->close();
            
            if ($success) {
                $donNghiPhep = $this->getDonNghiPhep($id);
                if ($donNghiPhep) {
                    error_log("Cập nhật đơn nghỉ phép thành công, ID: $id, ly_do_tu_choi: " . ($lyDoTuChoi ?? 'NULL'));
                    return $donNghiPhep;
                } else {
                    error_log("Không thể lấy thông tin đơn nghỉ phép sau khi cập nhật: ID $id");
                    throw new Exception("Không thể lấy thông tin đơn sau khi cập nhật");
                }
            } else {
                error_log("Lỗi SQL khi cập nhật nghi_phep: " . $stmt->error);
                throw new Exception("Lỗi khi cập nhật trạng thái: " . $stmt->error);
            }
        } catch (Exception $e) {
            error_log("Lỗi trong updateNghiPhep: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteNghiPhep($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM nghi_phep WHERE id_nghi_phep = ?");
            if (!$stmt) {
                throw new Exception("Lỗi chuẩn bị truy vấn: " . $this->conn->error);
            }
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            if (!$result) {
                error_log("Lỗi SQL khi xóa nghi_phep: " . $stmt->error);
                throw new Exception("Lỗi khi xóa đơn nghỉ phép");
            }
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            error_log("Lỗi trong deleteNghiPhep: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAllUsers() {
        try {
            $query = "SELECT id_nhan_vien, ho_ten FROM nhan_vien";
            $result = $this->conn->query($query);
            if (!$result) {
                throw new Exception("Lỗi truy vấn cơ sở dữ liệu: " . $this->conn->error);
            }
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            return $users;
        } catch (Exception $e) {
            error_log("Lỗi trong getAllUsers: " . $e->getMessage());
            throw $e;
        }
    }

    public function getNghiPhepByYear($year) {
        try {
            $query = "SELECT * FROM nghi_phep WHERE YEAR(ngay_bat_dau) = ?";
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
            error_log("Lỗi trong getNghiPhepByYear: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateEmployeeStatus($idNhanVien, $trangThai, $ngayKetThucThaiSan = null) {
        try {
            $query = "UPDATE nhan_vien SET trang_thai = ?";
            $params = [$trangThai];
            $types = "s";
            
            if ($ngayKetThucThaiSan) {
                $query .= ", ngay_ket_thuc_thai_san = ?";
                $params[] = $ngayKetThucThaiSan;
                $types .= "s";
            }
            
            $query .= " WHERE id_nhan_vien = ?";
            $params[] = $idNhanVien;
            $types .= "i";
            
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Lỗi chuẩn bị truy vấn: " . $this->conn->error);
            }
            
            $stmt->bind_param($types, ...$params);
            $success = $stmt->execute();
            $stmt->close();
            
            if (!$success) {
                throw new Exception("Lỗi khi cập nhật trạng thái nhân viên: " . $this->conn->error);
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Lỗi trong updateEmployeeStatus: " . $e->getMessage());
            throw $e;
        }
    }

    public function checkExistingMaternityLeave($idNhanVien) {
        try {
            $query = "SELECT id_nghi_phep FROM nghi_phep 
                      WHERE id_nhan_vien = ? AND loai_nghi = 'Nghỉ thai sản' 
                      AND trang_thai1 IN ('Chờ duyệt', 'Đã duyệt') 
                      AND ngay_ket_thuc >= CURDATE()";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Lỗi chuẩn bị truy vấn: " . $this->conn->error);
            }
            
            $stmt->bind_param("i", $idNhanVien);
            $stmt->execute();
            $result = $stmt->get_result();
            $exists = $result->num_rows > 0;
            $stmt->close();
            
            return $exists;
        } catch (Exception $e) {
            error_log("Lỗi trong checkExistingMaternityLeave: " . $e->getMessage());
            return false;
        }
    }
}
?>