<?php
include_once(__DIR__ . '/../config/Database.php');

class LuongModelSimple {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllLuong($month, $year) {
        try {
            $thang = sprintf('%04d-%02d', $year, $month);
            
            // Query đơn giản chỉ lấy các cột cần thiết từ bảng luong đã gộp chung
            // Loại trừ nhân viên có trạng thái "Nghỉ thai sản"
            // Thêm dữ liệu ứng lương đã duyệt và phạt vào cột Các Khoản Trừ Khác
            // Thêm dữ liệu thưởng vào cột Thưởng
            $query = "SELECT 
                        COALESCE(l.id_luong, 0) as id_luong,
                        nv.id_nhan_vien,
                        nv.ho_ten,
                        pb.ten_phong_ban,
                        cv.ten_chuc_vu,
                        ? as thang,
                        COALESCE(l.so_ngay_cong, 0) as so_ngay_cong,
                        COALESCE(l.trang_thai, 'Tạm tính') as trang_thai,
                        CASE WHEN l.luong_co_ban IS NULL OR l.luong_co_ban = 0 THEN nv.luong_co_ban ELSE l.luong_co_ban END as luong_co_ban,
                        COALESCE(l.luong_theo_ngay, 0) as luong_theo_ngay,
                        CASE WHEN l.phu_cap_chuc_vu IS NULL OR l.phu_cap_chuc_vu = 0 THEN nv.phu_cap_chuc_vu ELSE l.phu_cap_chuc_vu END as phu_cap_chuc_vu,
                        CASE WHEN l.phu_cap_bang_cap IS NULL OR l.phu_cap_bang_cap = 0 THEN nv.phu_cap_bang_cap ELSE l.phu_cap_bang_cap END as phu_cap_bang_cap,
                        CASE WHEN l.phu_cap_khac IS NULL OR l.phu_cap_khac = 0 THEN nv.phu_cap_khac ELSE l.phu_cap_khac END as phu_cap_khac,
                        COALESCE(l.tien_thuong, 0) + COALESCE(thuong.total_thuong, 0) as tien_thuong,
                        COALESCE(dst.hoa_hong_nhan, 0) as hoa_hong,
                        COALESCE(nv.so_nguoi_phu_thuoc, 0) as so_nguoi_phu_thuoc,
                        COALESCE(l.thu_nhap_truoc_thue, 0) as thu_nhap_truoc_thue,
                        COALESCE(l.bhxh_nv, 0) as bhxh_nv,
                        COALESCE(l.bhyt_nv, 0) as bhyt_nv,
                        COALESCE(l.bhtn_nv, 0) as bhtn_nv,
                        COALESCE(l.bhxh_cty, 0) as bhxh_cty,
                        COALESCE(l.bhyt_cty, 0) as bhyt_cty,
                        COALESCE(l.bhtn_cty, 0) as bhtn_cty,
                        (11000000 + (COALESCE(nv.so_nguoi_phu_thuoc, 0) * 4400000)) as giam_tru_gia_canh,
                        GREATEST(0, (CASE WHEN l.luong_co_ban IS NULL OR l.luong_co_ban = 0 THEN nv.luong_co_ban ELSE l.luong_co_ban END + CASE WHEN l.phu_cap_chuc_vu IS NULL OR l.phu_cap_chuc_vu = 0 THEN nv.phu_cap_chuc_vu ELSE l.phu_cap_chuc_vu END + CASE WHEN l.phu_cap_bang_cap IS NULL OR l.phu_cap_bang_cap = 0 THEN nv.phu_cap_bang_cap ELSE l.phu_cap_bang_cap END + CASE WHEN l.phu_cap_khac IS NULL OR l.phu_cap_khac = 0 THEN nv.phu_cap_khac ELSE l.phu_cap_khac END + COALESCE(l.tien_thuong, 0) + COALESCE(thuong.total_thuong, 0) + COALESCE(dst.hoa_hong_nhan, 0)) - (COALESCE(l.bhxh_nv, 0) + COALESCE(l.bhyt_nv, 0) + COALESCE(l.bhtn_nv, 0)) - (11000000 + (COALESCE(nv.so_nguoi_phu_thuoc, 0) * 4400000))) as thu_nhap_chiu_thue,
                        COALESCE(l.thue_tncn, 0) as thue_tncn,
                        COALESCE(l.cac_khoan_tru_khac, 0) + COALESCE(ung_luong.total_ung_luong, 0) + COALESCE(phat.total_phat, 0) as cac_khoan_tru_khac,
                        COALESCE(l.bhxh_nv, 0) + COALESCE(l.bhyt_nv, 0) + COALESCE(l.bhtn_nv, 0) + COALESCE(l.thue_tncn, 0) + COALESCE(l.cac_khoan_tru_khac, 0) + COALESCE(ung_luong.total_ung_luong, 0) + COALESCE(phat.total_phat, 0) as tong_khoan_tru,
                        COALESCE(l.luong_thuc_nhan, 0) as luong_thuc_nhan
                    FROM (
                        SELECT l1.*
                        FROM luong l1
                        WHERE MONTH(l1.ngay_tao) = ? AND YEAR(l1.ngay_tao) = ?
                        AND l1.id_luong = (
                            SELECT l2.id_luong
                            FROM luong l2
                            WHERE l2.id_nhan_vien = l1.id_nhan_vien
                            AND MONTH(l2.ngay_tao) = ? AND YEAR(l2.ngay_tao) = ?
                            ORDER BY l2.so_ngay_cong DESC, l2.id_luong DESC
                            LIMIT 1
                        )
                    ) l
                    INNER JOIN nhan_vien nv ON l.id_nhan_vien = nv.id_nhan_vien
                    LEFT JOIN phong_ban pb ON nv.id_phong_ban = pb.id_phong_ban
                    LEFT JOIN chuc_vu cv ON nv.id_chuc_vu = cv.id_chuc_vu
                    LEFT JOIN (
                        SELECT 
                            id_nhan_vien,
                            SUM(so_tien_ung) as total_ung_luong
                        FROM ung_luong 
                        WHERE trang_thai = 'Đã duyệt'
                        AND MONTH(ngay_tao) = ? AND YEAR(ngay_tao) = ?
                        GROUP BY id_nhan_vien
                    ) ung_luong ON nv.id_nhan_vien = ung_luong.id_nhan_vien
                    LEFT JOIN (
                        SELECT 
                            id_nhan_vien,
                            SUM(tien_thuong) as total_thuong
                        FROM thuong 
                        WHERE loai IN ('thăng chức', 'thành tích cá nhân', 'nghỉ lễ')
                        AND MONTH(ngay) = ? AND YEAR(ngay) = ?
                        GROUP BY id_nhan_vien
                    ) thuong ON nv.id_nhan_vien = thuong.id_nhan_vien
                    LEFT JOIN (
                        SELECT 
                            id_nhan_vien,
                            SUM(ABS(tien_thuong)) as total_phat
                        FROM thuong 
                        WHERE loai IN ('phạt kỷ luật', 'phạt trách nhiệm công việc')
                        AND MONTH(ngay) = ? AND YEAR(ngay) = ?
                        GROUP BY id_nhan_vien
                    ) phat ON nv.id_nhan_vien = phat.id_nhan_vien
                    LEFT JOIN doanh_so_thang dst ON nv.id_nhan_vien = dst.id_nhan_vien 
                        AND dst.thang = ? AND dst.nam = ?
                    WHERE nv.trang_thai != 'Nghỉ thai sản'
                    AND MONTH(l.ngay_tao) = ? 
                    AND YEAR(l.ngay_tao) = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$thang, $month, $year, $month, $year, $month, $year, $month, $year, $month, $year, $month, $year, $month, $year]);
            $luongData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $luongData;
            
        } catch (Exception $e) {
            error_log("Lỗi khi lấy dữ liệu lương: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Cập nhật tiền thưởng trong bảng lương
     */
    public function updateLuongTienThuong($id_nhan_vien, $thang, $tien_thuong) {
        try {
            // Kiểm tra xem bản ghi lương có tồn tại không
            $checkQuery = "SELECT id_luong FROM luong WHERE id_nhan_vien = ? AND thang = ?";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->execute([$id_nhan_vien, $thang]);
            $existingRecord = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existingRecord) {
                // Cập nhật bản ghi hiện có
                $updateQuery = "UPDATE luong SET tien_thuong = ? WHERE id_nhan_vien = ? AND thang = ?";
                $updateStmt = $this->conn->prepare($updateQuery);
                $result = $updateStmt->execute([$tien_thuong, $id_nhan_vien, $thang]);
                
                if ($result) {
                    return ['success' => true, 'message' => 'Cập nhật tiền thưởng thành công'];
                } else {
                    return ['success' => false, 'message' => 'Lỗi khi cập nhật tiền thưởng'];
                }
            } else {
                // Tạo bản ghi mới nếu chưa có
                $insertQuery = "INSERT INTO luong (id_nhan_vien, thang, tien_thuong, trang_thai, ngay_tao) VALUES (?, ?, ?, 'Tạm tính', NOW())";
                $insertStmt = $this->conn->prepare($insertQuery);
                $result = $insertStmt->execute([$id_nhan_vien, $thang, $tien_thuong]);
                
                if ($result) {
                    return ['success' => true, 'message' => 'Tạo bản ghi lương mới với tiền thưởng thành công'];
                } else {
                    return ['success' => false, 'message' => 'Lỗi khi tạo bản ghi lương mới'];
                }
            }
            
        } catch (Exception $e) {
            error_log("Lỗi khi cập nhật tiền thưởng: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()];
        }
    }

    /**
     * Cập nhật trạng thái lương
     */
    public function updateLuongStatus($id_luong, $trang_thai) {
        try {
            $query = "UPDATE luong SET trang_thai = ? WHERE id_luong = ?";
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([$trang_thai, $id_luong]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Cập nhật trạng thái lương thành công'];
            } else {
                return ['success' => false, 'message' => 'Lỗi khi cập nhật trạng thái lương'];
            }
        } catch (Exception $e) {
            error_log("Lỗi khi cập nhật trạng thái lương: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()];
        }
    }

    /**
     * Cập nhật chi tiết lương
     */
    public function updateLuongDetails($id_luong, $data) {
        try {
            $query = "UPDATE luong SET 
                        so_ngay_cong = ?, 
                        so_ngay_nghi_phep = ?, 
                        so_ngay_nghi_khong_phep = ?, 
                        luong_co_ban = ?, 
                        phu_cap_chuc_vu = ?, 
                        phu_cap_bang_cap = ?, 
                        phu_cap_khac = ?, 
                        tien_thuong = ?, 
                        cac_khoan_tru_khac = ? 
                      WHERE id_luong = ?";
            
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([
                $data['so_ngay_cong'],
                $data['so_ngay_nghi_phep'],
                $data['so_ngay_nghi_khong_phep'],
                $data['luong_co_ban'],
                $data['phu_cap_chuc_vu'],
                $data['phu_cap_bang_cap'],
                $data['phu_cap_khac'],
                $data['tien_thuong'],
                $data['cac_khoan_tru_khac'],
                $id_luong
            ]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Cập nhật chi tiết lương thành công'];
            } else {
                return ['success' => false, 'message' => 'Lỗi khi cập nhật chi tiết lương'];
            }
        } catch (Exception $e) {
            error_log("Lỗi khi cập nhật chi tiết lương: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()];
        }
    }

    /**
     * Cập nhật phụ cấp chức vụ
     */
    public function updatePhuCapChucVu() {
        try {
            $query = "UPDATE luong l 
                      INNER JOIN nhan_vien nv ON l.id_nhan_vien = nv.id_nhan_vien 
                      SET l.phu_cap_chuc_vu = nv.phu_cap_chuc_vu,
                          l.phu_cap_khac = nv.phu_cap_khac 
                      WHERE l.phu_cap_chuc_vu != nv.phu_cap_chuc_vu 
                         OR l.phu_cap_khac != nv.phu_cap_khac";
            
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute();
            
            if ($result) {
                return ['success' => true, 'message' => 'Cập nhật phụ cấp chức vụ và phụ cấp khác thành công'];
            } else {
                return ['success' => false, 'message' => 'Lỗi khi cập nhật phụ cấp chức vụ'];
            }
        } catch (Exception $e) {
            error_log("Lỗi khi cập nhật phụ cấp chức vụ: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()];
        }
    }

    /**
     * Cập nhật dữ liệu bảo hiểm và thuế TNCN
     */
    public function updateBaoHiemThue($id_luong, $data) {
        try {
            $query = "UPDATE luong SET 
                        bhxh_nv = ?, 
                        bhyt_nv = ?, 
                        bhtn_nv = ?, 
                        bhxh_cty = ?, 
                        bhyt_cty = ?, 
                        bhtn_cty = ?, 
                        thue_tncn = ?,
                        tong_khoan_tru = ?
                      WHERE id_luong = ?";
            
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([
                $data['bhxh_nv'],
                $data['bhyt_nv'],
                $data['bhtn_nv'],
                $data['bhxh_cty'],
                $data['bhyt_cty'],
                $data['bhtn_cty'],
                $data['thue_tncn'],
                $data['tong_khoan_tru'],
                $id_luong
            ]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Cập nhật bảo hiểm và thuế TNCN thành công'];
            } else {
                return ['success' => false, 'message' => 'Lỗi khi cập nhật bảo hiểm và thuế TNCN'];
            }
        } catch (Exception $e) {
            error_log("Lỗi khi cập nhật bảo hiểm và thuế TNCN: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()];
        }
    }

    /**
     * Xóa bản ghi lương
     */
    public function deleteLuong($id_luong) {
        try {
            $query = "DELETE FROM luong WHERE id_luong = ?";
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([$id_luong]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Xóa bản ghi lương thành công'];
            } else {
                return ['success' => false, 'message' => 'Lỗi khi xóa bản ghi lương'];
            }
        } catch (Exception $e) {
            error_log("Lỗi khi xóa bản ghi lương: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()];
        }
    }
}
?>
