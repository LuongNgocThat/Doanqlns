<?php
require_once __DIR__ . '/../config/Database.php';

class KpiModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // ===== QUẢN LÝ HỢP ĐỒNG KPI =====

    public function getAllHopDongKpi($limit = null, $offset = 0) {
        try {
            $sql = "SELECT hd.*, nv.ho_ten, pb.ten_phong_ban 
                    FROM hop_dong_kpi hd
                    LEFT JOIN nhan_vien nv ON hd.id_nhan_vien = nv.id_nhan_vien
                    LEFT JOIN phong_ban pb ON nv.id_phong_ban = pb.id_phong_ban
                    ORDER BY hd.ngay_ky DESC, hd.id_hop_dong DESC";
            
            if ($limit) {
                $sql .= " LIMIT :limit OFFSET :offset";
            }
            
            $stmt = $this->conn->prepare($sql);
            if ($limit) {
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getAllHopDongKpi: " . $e->getMessage());
            return [];
        }
    }

    public function addHopDongKpi($data) {
        try {
            $sql = "INSERT INTO hop_dong_kpi (ngay_ky, ma_hop_dong, gia_tri_hop_dong, id_nhan_vien, file_hop_dong, ghi_chu, trang_thai) 
                    VALUES (:ngay_ky, :ma_hop_dong, :gia_tri_hop_dong, :id_nhan_vien, :file_hop_dong, :ghi_chu, :trang_thai)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':ngay_ky', $data['ngay_ky']);
            $stmt->bindParam(':ma_hop_dong', $data['ma_hop_dong']);
            $stmt->bindParam(':gia_tri_hop_dong', $data['gia_tri_hop_dong']);
            $stmt->bindParam(':id_nhan_vien', $data['id_nhan_vien']);
            $stmt->bindParam(':file_hop_dong', $data['file_hop_dong']);
            $stmt->bindParam(':ghi_chu', $data['ghi_chu']);
            $stmt->bindParam(':trang_thai', $data['trang_thai']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in addHopDongKpi: " . $e->getMessage());
            return false;
        }
    }

    public function updateHopDongKpi($id, $data) {
        try {
            $sql = "UPDATE hop_dong_kpi 
                    SET ngay_ky = :ngay_ky, ma_hop_dong = :ma_hop_dong, gia_tri_hop_dong = :gia_tri_hop_dong, 
                        id_nhan_vien = :id_nhan_vien, file_hop_dong = :file_hop_dong, ghi_chu = :ghi_chu, 
                        trang_thai = :trang_thai
                    WHERE id_hop_dong = :id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':ngay_ky', $data['ngay_ky']);
            $stmt->bindParam(':ma_hop_dong', $data['ma_hop_dong']);
            $stmt->bindParam(':gia_tri_hop_dong', $data['gia_tri_hop_dong']);
            $stmt->bindParam(':id_nhan_vien', $data['id_nhan_vien']);
            $stmt->bindParam(':file_hop_dong', $data['file_hop_dong']);
            $stmt->bindParam(':ghi_chu', $data['ghi_chu']);
            $stmt->bindParam(':trang_thai', $data['trang_thai']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in updateHopDongKpi: " . $e->getMessage());
            return false;
        }
    }

    public function deleteHopDongKpi($id) {
        try {
            $sql = "DELETE FROM hop_dong_kpi WHERE id_hop_dong = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in deleteHopDongKpi: " . $e->getMessage());
            return false;
        }
    }

    public function getHopDongKpiById($id) {
        try {
            $sql = "SELECT hd.*, nv.ho_ten, pb.ten_phong_ban 
                    FROM hop_dong_kpi hd
                    LEFT JOIN nhan_vien nv ON hd.id_nhan_vien = nv.id_nhan_vien
                    LEFT JOIN phong_ban pb ON nv.id_phong_ban = pb.id_phong_ban
                    WHERE hd.id_hop_dong = :id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getHopDongKpiById: " . $e->getMessage());
            return false;
        }
    }

    // ===== QUẢN LÝ DOANH SỐ THÁNG =====

    public function getAllDoanhSoThang($thang = null, $nam = null) {
        try {
            $sql = "SELECT dst.*, nv.ho_ten, pb.ten_phong_ban 
                    FROM doanh_so_thang dst
                    LEFT JOIN nhan_vien nv ON dst.id_nhan_vien = nv.id_nhan_vien
                    LEFT JOIN phong_ban pb ON nv.id_phong_ban = pb.id_phong_ban
                    WHERE 1=1";
            
            $params = [];
            if ($thang) {
                $sql .= " AND dst.thang = :thang";
                $params[':thang'] = $thang;
            }
            if ($nam) {
                $sql .= " AND dst.nam = :nam";
                $params[':nam'] = $nam;
            }
            
            $sql .= " ORDER BY dst.nam DESC, dst.thang DESC, nv.ho_ten ASC";
            
            $stmt = $this->conn->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getAllDoanhSoThang: " . $e->getMessage());
            return [];
        }
    }

    public function addDoanhSoThang($data) {
        try {
            $sql = "INSERT INTO doanh_so_thang (id_nhan_vien, thang, nam, doanh_so_thang, phan_tram_hoa_hong, hoa_hong_nhan, cong_diem_danh_gia, ghi_chu) 
                    VALUES (:id_nhan_vien, :thang, :nam, :doanh_so_thang, :phan_tram_hoa_hong, :hoa_hong_nhan, :cong_diem_danh_gia, :ghi_chu)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_nhan_vien', $data['id_nhan_vien']);
            $stmt->bindParam(':thang', $data['thang']);
            $stmt->bindParam(':nam', $data['nam']);
            $stmt->bindParam(':doanh_so_thang', $data['doanh_so_thang']);
            $stmt->bindParam(':phan_tram_hoa_hong', $data['phan_tram_hoa_hong']);
            $stmt->bindParam(':hoa_hong_nhan', $data['hoa_hong_nhan']);
            $stmt->bindParam(':cong_diem_danh_gia', $data['cong_diem_danh_gia']);
            $stmt->bindParam(':ghi_chu', $data['ghi_chu']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in addDoanhSoThang: " . $e->getMessage());
            return false;
        }
    }

    public function updateDoanhSoThang($id, $data) {
        try {
            $sql = "UPDATE doanh_so_thang 
                    SET doanh_so_thang = :doanh_so_thang, phan_tram_hoa_hong = :phan_tram_hoa_hong, 
                        hoa_hong_nhan = :hoa_hong_nhan, cong_diem_danh_gia = :cong_diem_danh_gia, ghi_chu = :ghi_chu
                    WHERE id_doanh_so = :id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':doanh_so_thang', $data['doanh_so_thang']);
            $stmt->bindParam(':phan_tram_hoa_hong', $data['phan_tram_hoa_hong']);
            $stmt->bindParam(':hoa_hong_nhan', $data['hoa_hong_nhan']);
            $stmt->bindParam(':cong_diem_danh_gia', $data['cong_diem_danh_gia']);
            $stmt->bindParam(':ghi_chu', $data['ghi_chu']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in updateDoanhSoThang: " . $e->getMessage());
            return false;
        }
    }

    public function getDoanhSoThangById($id) {
        try {
            $sql = "SELECT * FROM doanh_so_thang WHERE id_doanh_so = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getDoanhSoThangById: " . $e->getMessage());
            return false;
        }
    }

    public function deleteDoanhSoThang($id) {
        try {
            $sql = "DELETE FROM doanh_so_thang WHERE id_doanh_so = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in deleteDoanhSoThang: " . $e->getMessage());
            return false;
        }
    }

    // ===== QUẢN LÝ CẤU HÌNH THANG ĐIỂM =====

    public function getAllCauHinhThangDiem() {
        try {
            $sql = "SELECT * FROM cau_hinh_thang_diem_kpi 
                    WHERE trang_thai = 'Hoạt động' 
                    ORDER BY doanh_so_min ASC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getAllCauHinhThangDiem: " . $e->getMessage());
            return [];
        }
    }

    public function addCauHinhThangDiem($data) {
        try {
            $sql = "INSERT INTO cau_hinh_thang_diem_kpi (ten_cau_hinh, doanh_so_min, doanh_so_max, phan_tram_hoa_hong, ghi_chu, cong_diem, trang_thai) 
                    VALUES (:ten_cau_hinh, :doanh_so_min, :doanh_so_max, :phan_tram_hoa_hong, :ghi_chu, :cong_diem, :trang_thai)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':ten_cau_hinh', $data['ten_cau_hinh']);
            $stmt->bindParam(':doanh_so_min', $data['doanh_so_min']);
            $stmt->bindParam(':doanh_so_max', $data['doanh_so_max']);
            $stmt->bindParam(':phan_tram_hoa_hong', $data['phan_tram_hoa_hong']);
            $stmt->bindParam(':ghi_chu', $data['ghi_chu']);
            $stmt->bindParam(':cong_diem', $data['cong_diem']);
            $stmt->bindParam(':trang_thai', $data['trang_thai']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in addCauHinhThangDiem: " . $e->getMessage());
            return false;
        }
    }

    public function updateCauHinhThangDiem($id, $data) {
        try {
            $sql = "UPDATE cau_hinh_thang_diem_kpi 
                    SET ten_cau_hinh = :ten_cau_hinh, doanh_so_min = :doanh_so_min, doanh_so_max = :doanh_so_max, 
                        phan_tram_hoa_hong = :phan_tram_hoa_hong, ghi_chu = :ghi_chu, cong_diem = :cong_diem, 
                        trang_thai = :trang_thai
                    WHERE id_cau_hinh = :id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':ten_cau_hinh', $data['ten_cau_hinh']);
            $stmt->bindParam(':doanh_so_min', $data['doanh_so_min']);
            $stmt->bindParam(':doanh_so_max', $data['doanh_so_max']);
            $stmt->bindParam(':phan_tram_hoa_hong', $data['phan_tram_hoa_hong']);
            $stmt->bindParam(':ghi_chu', $data['ghi_chu']);
            $stmt->bindParam(':cong_diem', $data['cong_diem']);
            $stmt->bindParam(':trang_thai', $data['trang_thai']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in updateCauHinhThangDiem: " . $e->getMessage());
            return false;
        }
    }

    public function deleteCauHinhThangDiem($id) {
        try {
            $sql = "DELETE FROM cau_hinh_thang_diem_kpi WHERE id_cau_hinh = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in deleteCauHinhThangDiem: " . $e->getMessage());
            return false;
        }
    }

    // ===== TÍNH TOÁN KPI =====

    public function tinhKpiChoNhanVien($id_nhan_vien, $thang, $nam) {
        try {
            // Lấy doanh số hiện tại
            $sql = "SELECT * FROM doanh_so_thang 
                    WHERE id_nhan_vien = :id_nhan_vien AND thang = :thang AND nam = :nam";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_nhan_vien', $id_nhan_vien);
            $stmt->bindParam(':thang', $thang);
            $stmt->bindParam(':nam', $nam);
            $stmt->execute();
            $doanhSo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$doanhSo) {
                return false;
            }
            
            // Tìm cấu hình phù hợp
            $sql = "SELECT * FROM cau_hinh_thang_diem_kpi 
                    WHERE trang_thai = 'Hoạt động' 
                    AND doanh_so_min <= :doanh_so 
                    AND (doanh_so_max IS NULL OR doanh_so_max >= :doanh_so)
                    ORDER BY doanh_so_min DESC 
                    LIMIT 1";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':doanh_so', $doanhSo['doanh_so_thang']);
            $stmt->execute();
            $cauHinh = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($cauHinh) {
                // Cập nhật KPI
                $hoaHong = ($doanhSo['doanh_so_thang'] * $cauHinh['phan_tram_hoa_hong']) / 100;
                
                $updateSql = "UPDATE doanh_so_thang 
                              SET phan_tram_hoa_hong = :phan_tram, hoa_hong_nhan = :hoa_hong, 
                                  cong_diem_danh_gia = :cong_diem, ghi_chu = :ghi_chu
                              WHERE id_doanh_so = :id";
                
                $updateStmt = $this->conn->prepare($updateSql);
                $updateStmt->bindParam(':id', $doanhSo['id_doanh_so']);
                $updateStmt->bindParam(':phan_tram', $cauHinh['phan_tram_hoa_hong']);
                $updateStmt->bindParam(':hoa_hong', $hoaHong);
                $updateStmt->bindParam(':cong_diem', $cauHinh['cong_diem']);
                $updateStmt->bindParam(':ghi_chu', $cauHinh['ghi_chu']);
                
                return $updateStmt->execute();
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Error in tinhKpiChoNhanVien: " . $e->getMessage());
            return false;
        }
    }

    // ===== THỐNG KÊ =====

    public function getThongKeKpi($thang = null, $nam = null) {
        try {
            $whereClause = "WHERE 1=1";
            $params = [];
            
            if ($thang) {
                $whereClause .= " AND dst.thang = :thang";
                $params[':thang'] = $thang;
            }
            if ($nam) {
                $whereClause .= " AND dst.nam = :nam";
                $params[':nam'] = $nam;
            }
            
            $sql = "SELECT 
                        COUNT(DISTINCT dst.id_nhan_vien) as tong_nhan_vien,
                        SUM(dst.doanh_so_thang) as tong_doanh_so,
                        SUM(dst.hoa_hong_nhan) as tong_hoa_hong,
                        AVG(dst.cong_diem_danh_gia) as diem_trung_binh
                    FROM doanh_so_thang dst
                    $whereClause";
            
            $stmt = $this->conn->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getThongKeKpi: " . $e->getMessage());
            return false;
        }
    }

    // ===== TÍNH TỔNG GIÁ TRỊ HỢP ĐỒNG =====

    public function getTongGiaTriHopDong($id_nhan_vien, $thang, $nam) {
        try {
            $sql = "SELECT COALESCE(SUM(gia_tri_hop_dong), 0) as tong_gia_tri
                    FROM hop_dong_kpi 
                    WHERE id_nhan_vien = :id_nhan_vien 
                    AND MONTH(ngay_ky) = :thang 
                    AND YEAR(ngay_ky) = :nam
                    AND trang_thai = 'Hoàn thành'";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_nhan_vien', $id_nhan_vien, PDO::PARAM_INT);
            $stmt->bindParam(':thang', $thang, PDO::PARAM_INT);
            $stmt->bindParam(':nam', $nam, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['tong_gia_tri'];
        } catch (PDOException $e) {
            error_log("Error in getTongGiaTriHopDong: " . $e->getMessage());
            return 0;
        }
    }

    // ===== TỰ ĐỘNG SYNC HỢP ĐỒNG SANG DOANH SỐ =====

    public function autoSyncHopDongToDoanhSo($id_nhan_vien, $thang, $nam) {
        try {
            // Lấy tổng giá trị hợp đồng đã hoàn thành
            $tongGiaTri = $this->getTongGiaTriHopDong($id_nhan_vien, $thang, $nam);
            
            if ($tongGiaTri <= 0) {
                return false; // Không có hợp đồng đã hoàn thành
            }
            
            // Kiểm tra xem đã có doanh số chưa
            $sqlCheck = "SELECT id_doanh_so FROM doanh_so_thang 
                        WHERE id_nhan_vien = :id_nhan_vien 
                        AND thang = :thang 
                        AND nam = :nam";
            
            $stmtCheck = $this->conn->prepare($sqlCheck);
            $stmtCheck->bindParam(':id_nhan_vien', $id_nhan_vien, PDO::PARAM_INT);
            $stmtCheck->bindParam(':thang', $thang, PDO::PARAM_INT);
            $stmtCheck->bindParam(':nam', $nam, PDO::PARAM_INT);
            $stmtCheck->execute();
            
            $existingDoanhSo = $stmtCheck->fetch();
            
            // Nếu đã có doanh số, cập nhật thay vì tạo mới
            if ($existingDoanhSo) {
                return $this->updateDoanhSoFromHopDong($id_nhan_vien, $thang, $nam, $tongGiaTri);
            }
            
            // Lấy cấu hình thang điểm phù hợp
            $sqlConfig = "SELECT * FROM cau_hinh_thang_diem_kpi 
                         WHERE trang_thai = 'Hoạt động' 
                         AND doanh_so_min <= :doanh_so 
                         AND (doanh_so_max >= :doanh_so OR doanh_so_max = 0)
                         ORDER BY doanh_so_min DESC 
                         LIMIT 1";
            
            $stmtConfig = $this->conn->prepare($sqlConfig);
            $stmtConfig->bindParam(':doanh_so', $tongGiaTri, PDO::PARAM_STR);
            $stmtConfig->execute();
            $config = $stmtConfig->fetch(PDO::FETCH_ASSOC);
            
            if (!$config) {
                // Nếu không có cấu hình phù hợp, dùng cấu hình mặc định
                $config = [
                    'phan_tram_hoa_hong' => 1.0,
                    'cong_diem' => 2,
                    'ghi_chu' => 'Tự động sync từ hợp đồng'
                ];
            }
            
            // Tính hoa hồng
            $hoaHongNhan = $tongGiaTri * ($config['phan_tram_hoa_hong'] / 100);
            
            // Tạo doanh số
            $sqlInsert = "INSERT INTO doanh_so_thang 
                         (id_nhan_vien, thang, nam, doanh_so_thang, phan_tram_hoa_hong, hoa_hong_nhan, cong_diem_danh_gia, ghi_chu) 
                         VALUES (:id_nhan_vien, :thang, :nam, :doanh_so_thang, :phan_tram_hoa_hong, :hoa_hong_nhan, :cong_diem_danh_gia, :ghi_chu)";
            
            $stmtInsert = $this->conn->prepare($sqlInsert);
            $stmtInsert->bindParam(':id_nhan_vien', $id_nhan_vien, PDO::PARAM_INT);
            $stmtInsert->bindParam(':thang', $thang, PDO::PARAM_INT);
            $stmtInsert->bindParam(':nam', $nam, PDO::PARAM_INT);
            $stmtInsert->bindParam(':doanh_so_thang', $tongGiaTri, PDO::PARAM_STR);
            $stmtInsert->bindParam(':phan_tram_hoa_hong', $config['phan_tram_hoa_hong'], PDO::PARAM_STR);
            $stmtInsert->bindParam(':hoa_hong_nhan', $hoaHongNhan, PDO::PARAM_STR);
            $stmtInsert->bindParam(':cong_diem_danh_gia', $config['cong_diem'], PDO::PARAM_INT);
            $stmtInsert->bindParam(':ghi_chu', $config['ghi_chu'], PDO::PARAM_STR);
            
            return $stmtInsert->execute();
        } catch (PDOException $e) {
            error_log("Error in autoSyncHopDongToDoanhSo: " . $e->getMessage());
            return false;
        }
    }

    // ===== CẬP NHẬT DOANH SỐ TỪ HỢP ĐỒNG =====

    public function updateDoanhSoFromHopDong($id_nhan_vien, $thang, $nam, $tongGiaTri) {
        try {
            // Lấy tổng giá trị hợp đồng đã hoàn thành
            $tongGiaTri = $this->getTongGiaTriHopDong($id_nhan_vien, $thang, $nam);
            
            if ($tongGiaTri <= 0) {
                return false; // Không có hợp đồng đã hoàn thành
            }
            
            // Lấy cấu hình thang điểm phù hợp
            $sqlConfig = "SELECT * FROM cau_hinh_thang_diem_kpi 
                         WHERE trang_thai = 'Hoạt động' 
                         AND doanh_so_min <= :doanh_so 
                         AND (doanh_so_max >= :doanh_so OR doanh_so_max = 0)
                         ORDER BY doanh_so_min DESC 
                         LIMIT 1";
            
            $stmtConfig = $this->conn->prepare($sqlConfig);
            $stmtConfig->bindParam(':doanh_so', $tongGiaTri, PDO::PARAM_STR);
            $stmtConfig->execute();
            $config = $stmtConfig->fetch(PDO::FETCH_ASSOC);
            
            if (!$config) {
                // Nếu không có cấu hình phù hợp, dùng cấu hình mặc định
                $config = [
                    'phan_tram_hoa_hong' => 1.0,
                    'cong_diem' => 2,
                    'ghi_chu' => 'Tự động cập nhật từ hợp đồng'
                ];
            }
            
            // Tính hoa hồng
            $hoaHongNhan = $tongGiaTri * ($config['phan_tram_hoa_hong'] / 100);
            
            // Cập nhật doanh số
            $sqlUpdate = "UPDATE doanh_so_thang 
                         SET doanh_so_thang = :doanh_so_thang,
                             phan_tram_hoa_hong = :phan_tram_hoa_hong,
                             hoa_hong_nhan = :hoa_hong_nhan,
                             cong_diem_danh_gia = :cong_diem_danh_gia,
                             ghi_chu = :ghi_chu,
                             ngay_cap_nhat = CURRENT_TIMESTAMP
                         WHERE id_nhan_vien = :id_nhan_vien 
                         AND thang = :thang 
                         AND nam = :nam";
            
            $stmtUpdate = $this->conn->prepare($sqlUpdate);
            $stmtUpdate->bindParam(':doanh_so_thang', $tongGiaTri, PDO::PARAM_STR);
            $stmtUpdate->bindParam(':phan_tram_hoa_hong', $config['phan_tram_hoa_hong'], PDO::PARAM_STR);
            $stmtUpdate->bindParam(':hoa_hong_nhan', $hoaHongNhan, PDO::PARAM_STR);
            $stmtUpdate->bindParam(':cong_diem_danh_gia', $config['cong_diem'], PDO::PARAM_INT);
            $stmtUpdate->bindParam(':ghi_chu', $config['ghi_chu'], PDO::PARAM_STR);
            $stmtUpdate->bindParam(':id_nhan_vien', $id_nhan_vien, PDO::PARAM_INT);
            $stmtUpdate->bindParam(':thang', $thang, PDO::PARAM_INT);
            $stmtUpdate->bindParam(':nam', $nam, PDO::PARAM_INT);
            
            return $stmtUpdate->execute();
        } catch (PDOException $e) {
            error_log("Error in updateDoanhSoFromHopDong: " . $e->getMessage());
            return false;
        }
    }
}
?>
