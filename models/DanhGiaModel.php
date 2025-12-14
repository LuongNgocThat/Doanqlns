<?php
class DanhGiaModel {
    private $conn;

    public function __construct() {
        $this->conn = new mysqli("localhost", "root", "", "doanqlns");
        if ($this->conn->connect_error) {
            error_log("Kết nối cơ sở dữ liệu thất bại: " . $this->conn->connect_error);
            throw new Exception("Không thể kết nối đến cơ sở dữ liệu");
        }
    }

    // Lấy danh sách tiêu chí đánh giá
    public function getAllTieuChi() {
        try {
            $query = "SELECT * FROM tieu_chi_danh_gia WHERE trang_thai = 'active' ORDER BY loai_tieu_chi, trong_so DESC";
            $result = $this->conn->query($query);
            if (!$result) {
                throw new Exception("Lỗi truy vấn SQL: " . $this->conn->error);
            }
            $records = [];
            while ($row = $result->fetch_assoc()) {
                $records[] = $row;
            }
            return $records;
        } catch (Exception $e) {
            error_log("Lỗi trong getAllTieuChi: " . $e->getMessage());
            throw $e;
        }
    }

    // Lấy danh sách nhân viên
    public function getAllNhanVien() {
        try {
            $query = "SELECT nv.*, pb.ten_phong_ban, cv.ten_chuc_vu 
                      FROM nhan_vien nv 
                      LEFT JOIN phong_ban pb ON nv.id_phong_ban = pb.id_phong_ban 
                      LEFT JOIN chuc_vu cv ON nv.id_chuc_vu = cv.id_chuc_vu 
                      WHERE nv.trang_thai = 'active' 
                      ORDER BY nv.ho_ten";
            $result = $this->conn->query($query);
            if (!$result) {
                throw new Exception("Lỗi truy vấn SQL: " . $this->conn->error);
            }
            $records = [];
            while ($row = $result->fetch_assoc()) {
                $records[] = $row;
            }
            return $records;
        } catch (Exception $e) {
            error_log("Lỗi trong getAllNhanVien: " . $e->getMessage());
            throw $e;
        }
    }

    // Lấy danh sách đánh giá theo tháng/năm
    public function getDanhGiaByMonth($thang, $nam) {
        try {
            $query = "SELECT dg.*, nv.ho_ten, nv_dg.ho_ten as ten_nguoi_danh_gia,
                             pb.ten_phong_ban, cv.ten_chuc_vu
                      FROM danh_gia_nhan_vien dg
                      INNER JOIN nhan_vien nv ON dg.id_nhan_vien = nv.id_nhan_vien
                      INNER JOIN nhan_vien nv_dg ON dg.id_nguoi_danh_gia = nv_dg.id_nhan_vien
                      LEFT JOIN phong_ban pb ON nv.id_phong_ban = pb.id_phong_ban
                      LEFT JOIN chuc_vu cv ON nv.id_chuc_vu = cv.id_chuc_vu
                      WHERE dg.thang_danh_gia = ? AND dg.nam_danh_gia = ?
                      ORDER BY dg.ngay_tao DESC";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Lỗi chuẩn bị truy vấn: " . $this->conn->error);
            }
            $stmt->bind_param("ii", $thang, $nam);
            $stmt->execute();
            $result = $stmt->get_result();
            $records = [];
            while ($row = $result->fetch_assoc()) {
                $records[] = $row;
            }
            $stmt->close();
            return $records;
        } catch (Exception $e) {
            error_log("Lỗi trong getDanhGiaByMonth: " . $e->getMessage());
            throw $e;
        }
    }

    // Lấy chi tiết đánh giá
    public function getChiTietDanhGia($idDanhGia) {
        try {
            $query = "SELECT ctdg.*, tcdg.ten_tieu_chi, tcdg.trong_so, tcdg.thang_diem, tcdg.loai_tieu_chi
                      FROM chi_tiet_danh_gia ctdg
                      INNER JOIN tieu_chi_danh_gia tcdg ON ctdg.id_tieu_chi = tcdg.id_tieu_chi
                      WHERE ctdg.id_danh_gia = ?
                      ORDER BY tcdg.loai_tieu_chi, tcdg.trong_so DESC";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Lỗi chuẩn bị truy vấn: " . $this->conn->error);
            }
            $stmt->bind_param("i", $idDanhGia);
            $stmt->execute();
            $result = $stmt->get_result();
            $records = [];
            while ($row = $result->fetch_assoc()) {
                $records[] = $row;
            }
            $stmt->close();
            return $records;
        } catch (Exception $e) {
            error_log("Lỗi trong getChiTietDanhGia: " . $e->getMessage());
            throw $e;
        }
    }

    // Thêm đánh giá mới
    public function addDanhGia($idNhanVien, $idNguoiDanhGia, $thang, $nam, $diemTieuChi, $ghiChu = '', $diemChuyenCanFromRequest = null) {
        try {
            $this->conn->begin_transaction();

            // Lấy điểm chuyên cần từ request body (ưu tiên)
            $diemChuyenCan = $diemChuyenCanFromRequest !== null ? floatval($diemChuyenCanFromRequest) : 0;
            if ($diemChuyenCan < 0) $diemChuyenCan = 0;
            if ($diemChuyenCan > 10) $diemChuyenCan = 10;
            
            // Lấy điểm hiệu quả và thái độ từ diem_tieu_chi (cần tìm id_tieu_chi tương ứng)
            $diemHieuQua = 0;
            $diemThaiDo = 0;
            
            // Tìm id_tieu_chi cho hiệu quả và thái độ
            $queryTieuChi = "SELECT id_tieu_chi, ten_tieu_chi FROM tieu_chi_danh_gia WHERE trang_thai = 'active'";
            $stmtTieuChi = $this->conn->prepare($queryTieuChi);
            $stmtTieuChi->execute();
            $resultTieuChi = $stmtTieuChi->get_result();
            $tieuChiMap = [];
            while ($row = $resultTieuChi->fetch_assoc()) {
                $tieuChiMap[$row['id_tieu_chi']] = $row['ten_tieu_chi'];
            }
            $stmtTieuChi->close();
            
            // Tìm điểm hiệu quả và thái độ từ diem_tieu_chi
            foreach ($diemTieuChi as $idTieuChi => $diem) {
                $tenTieuChi = isset($tieuChiMap[$idTieuChi]) ? $tieuChiMap[$idTieuChi] : '';
                $diemSo = is_numeric($diem) ? floatval($diem) : 0;
                if ($diemSo < 0) $diemSo = 0;
                if ($diemSo > 10) $diemSo = 10;
                
                // Debug: log tên tiêu chí để kiểm tra
                error_log("Processing tieu chi: ID=$idTieuChi, Ten='$tenTieuChi', Diem=$diemSo");
                
                // Tìm hiệu quả - có thể là "Hiệu suất công việc" hoặc "Chất lượng công việc"
                if (strpos(strtolower($tenTieuChi), 'hiệu suất') !== false || 
                    strpos(strtolower($tenTieuChi), 'hieu suat') !== false ||
                    strpos(strtolower($tenTieuChi), 'chất lượng') !== false ||
                    strpos(strtolower($tenTieuChi), 'chat luong') !== false) {
                    $diemHieuQua = $diemSo;
                    error_log("Found hieu qua: $diemSo");
                } 
                // Tìm thái độ - có thể là "Thái độ làm việc"
                elseif (strpos(strtolower($tenTieuChi), 'thái độ') !== false || 
                        strpos(strtolower($tenTieuChi), 'thai do') !== false) {
                    $diemThaiDo = $diemSo;
                    error_log("Found thai do: $diemSo");
                }
            }
            
            // Tính tổng điểm theo công thức trọng số (thang 10)
            $tongDiemQuyDoi = ($diemChuyenCan * 0.4) + ($diemHieuQua * 0.4) + ($diemThaiDo * 0.2);
            
            // Debug: log kết quả tính toán
            error_log("Final calculation: chuyenCan=$diemChuyenCan, hieuQua=$diemHieuQua, thaiDo=$diemThaiDo, tongDiem=$tongDiemQuyDoi");

            // Xác định xếp loại
            $xepLoai = $this->getXepLoaiByDiem($tongDiemQuyDoi);
            
            // Thêm đánh giá chính
            $query = "INSERT INTO danh_gia_nhan_vien (id_nhan_vien, id_nguoi_danh_gia, thang_danh_gia, nam_danh_gia, diem_chuyen_can, diem_hieu_qua, diem_thai_do, tong_diem, xep_loai, trang_thai)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Nháp')";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Lỗi chuẩn bị truy vấn: " . $this->conn->error);
            }
            $stmt->bind_param("iiiidddss", $idNhanVien, $idNguoiDanhGia, $thang, $nam, $diemChuyenCan, $diemHieuQua, $diemThaiDo, $tongDiemQuyDoi, $xepLoai);
            $stmt->execute();
            $idDanhGia = $this->conn->insert_id;
            $stmt->close();

            // Thêm chi tiết điểm
            foreach ($diemTieuChi as $idTieuChi => $diem) {
                // Lưu điểm chi tiết; cột diem_trong_so sẽ lưu cùng điểm (không dùng trọng số)
                $diemSo = is_numeric($diem) ? floatval($diem) : 0;
                if ($diemSo < 0) { $diemSo = 0; }
                if ($diemSo > 10) { $diemSo = 10; }

                $queryChiTiet = "INSERT INTO chi_tiet_danh_gia (id_danh_gia, id_tieu_chi, diem_so, diem_trong_so)
                                VALUES (?, ?, ?, ?)";
                $stmtChiTiet = $this->conn->prepare($queryChiTiet);
                if (!$stmtChiTiet) {
                    throw new Exception("Lỗi chuẩn bị truy vấn chi tiết: " . $this->conn->error);
                }
                $stmtChiTiet->bind_param("iidd", $idDanhGia, $idTieuChi, $diemSo, $diemSo);
                $stmtChiTiet->execute();
                $stmtChiTiet->close();
            }

            $this->conn->commit();
            return $idDanhGia;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Lỗi trong addDanhGia: " . $e->getMessage());
            throw $e;
        }
    }

    // Lấy tiêu chí theo ID
    private function getTieuChiById($idTieuChi) {
        $query = "SELECT * FROM tieu_chi_danh_gia WHERE id_tieu_chi = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idTieuChi);
        $stmt->execute();
        $result = $stmt->get_result();
        $tieuChi = $result->fetch_assoc();
        $stmt->close();
        return $tieuChi;
    }

    // Xác định xếp loại theo điểm (thang 10)
    private function getXepLoaiByDiem($diem) {
        if ($diem >= 9) return 'Xuất sắc';
        if ($diem >= 8) return 'Tốt';
        if ($diem >= 7) return 'Khá';
        if ($diem >= 6) return 'Đạt';
        return 'Cần cải thiện';
    }

    // Cập nhật trạng thái đánh giá
    public function updateTrangThaiDanhGia($idDanhGia, $trangThai) {
        try {
            // Nếu duyệt, cần tính lại tong_diem và cập nhật xếp loại
            if ($trangThai === 'Đã duyệt') {
                // Lấy thông tin đánh giá để tính lại tong_diem
                $query = "SELECT diem_chuyen_can, diem_hieu_qua, diem_thai_do, tong_diem FROM danh_gia_nhan_vien WHERE id_danh_gia = ?";
                $stmt = $this->conn->prepare($query);
                if (!$stmt) {
                    throw new Exception("Lỗi chuẩn bị truy vấn: " . $this->conn->error);
                }
                $stmt->bind_param("i", $idDanhGia);
                $stmt->execute();
                $result = $stmt->get_result();
                $danhGia = $result->fetch_assoc();
                $stmt->close();
                
                if ($danhGia) {
                    $tongDiem = 0;
                    $hasAllScores = false;
                    
                    // Tính lại tong_diem từ 3 điểm thành phần
                    if (isset($danhGia['diem_chuyen_can']) && isset($danhGia['diem_hieu_qua']) && isset($danhGia['diem_thai_do'])) {
                        $chuyenCan = (float)$danhGia['diem_chuyen_can'];
                        $hieuQua = (float)$danhGia['diem_hieu_qua'];
                        $thaiDo = (float)$danhGia['diem_thai_do'];
                        
                        $tongDiem = ($chuyenCan * 0.4) + ($hieuQua * 0.4) + ($thaiDo * 0.2);
                        $hasAllScores = true;
                        
                        // Debug log
                        error_log("updateTrangThaiDanhGia DEBUG: ID=$idDanhGia, CC=$chuyenCan, HQ=$hieuQua, TD=$thaiDo, Tong=$tongDiem");
                    } else {
                        // Fallback: sử dụng tong_diem hiện có nếu không có đủ 3 điểm
                        $tongDiem = floatval($danhGia['tong_diem']) || 0;
                    }
                    
                    $xepLoai = $this->getXepLoaiFromDiem($tongDiem);
                    
                    // Cập nhật cả trạng thái, tong_diem và xếp loại
                    if ($hasAllScores) {
                        $query = "UPDATE danh_gia_nhan_vien SET trang_thai = ?, tong_diem = ?, xep_loai = ? WHERE id_danh_gia = ?";
                        $stmt = $this->conn->prepare($query);
                        if (!$stmt) {
                            throw new Exception("Lỗi chuẩn bị truy vấn: " . $this->conn->error);
                        }
                        $stmt->bind_param("sdsi", $trangThai, $tongDiem, $xepLoai, $idDanhGia);
                    } else {
                        // Chỉ cập nhật trạng thái và xếp loại (không tính lại tong_diem)
                        $query = "UPDATE danh_gia_nhan_vien SET trang_thai = ?, xep_loai = ? WHERE id_danh_gia = ?";
                        $stmt = $this->conn->prepare($query);
                        if (!$stmt) {
                            throw new Exception("Lỗi chuẩn bị truy vấn: " . $this->conn->error);
                        }
                        $stmt->bind_param("ssi", $trangThai, $xepLoai, $idDanhGia);
                    }
                    
                    $success = $stmt->execute();
                    $stmt->close();
                    return $success;
                }
            }
            
            // Nếu không phải duyệt, chỉ cập nhật trạng thái
            $query = "UPDATE danh_gia_nhan_vien SET trang_thai = ? WHERE id_danh_gia = ?";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Lỗi chuẩn bị truy vấn: " . $this->conn->error);
            }
            $stmt->bind_param("si", $trangThai, $idDanhGia);
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        } catch (Exception $e) {
            error_log("Lỗi trong updateTrangThaiDanhGia: " . $e->getMessage());
            throw $e;
        }
    }
    
    // Hàm tính xếp loại từ điểm (thang 10)
    private function getXepLoaiFromDiem($diem) {
        if ($diem >= 9) return 'Xuất sắc';
        if ($diem >= 8) return 'Tốt';
        if ($diem >= 7) return 'Khá';
        if ($diem >= 6) return 'Đạt';
        return 'Cần cải thiện';
    }

    // Xóa đánh giá
    public function deleteDanhGia($idDanhGia) {
        try {
            $query = "DELETE FROM danh_gia_nhan_vien WHERE id_danh_gia = ?";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Lỗi chuẩn bị truy vấn: " . $this->conn->error);
            }
            $stmt->bind_param("i", $idDanhGia);
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        } catch (Exception $e) {
            error_log("Lỗi trong deleteDanhGia: " . $e->getMessage());
            throw $e;
        }
    }

    // Lấy thống kê đánh giá
    public function getThongKeDanhGia($thang, $nam) {
        try {
            $query = "SELECT 
                        COUNT(*) as tong_so_danh_gia,
                        AVG(tong_diem) as diem_trung_binh,
                        SUM(CASE WHEN xep_loai = 'Xuất sắc' THEN 1 ELSE 0 END) as xuat_sac,
                        SUM(CASE WHEN xep_loai = 'Tốt' THEN 1 ELSE 0 END) as tot,
                        SUM(CASE WHEN xep_loai = 'Khá' THEN 1 ELSE 0 END) as kha,
                        SUM(CASE WHEN xep_loai = 'Trung bình' THEN 1 ELSE 0 END) as trung_binh,
                        SUM(CASE WHEN xep_loai = 'Yếu' THEN 1 ELSE 0 END) as yeu
                      FROM danh_gia_nhan_vien 
                      WHERE thang_danh_gia = ? AND nam_danh_gia = ? AND trang_thai = 'approved'";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Lỗi chuẩn bị truy vấn: " . $this->conn->error);
            }
            $stmt->bind_param("ii", $thang, $nam);
            $stmt->execute();
            $result = $stmt->get_result();
            $thongKe = $result->fetch_assoc();
            $stmt->close();
            return $thongKe;
        } catch (Exception $e) {
            error_log("Lỗi trong getThongKeDanhGia: " . $e->getMessage());
            throw $e;
        }
    }

    // Lấy ID nhân viên từ mã nhân viên (tìm theo id_nhan_vien hoặc tên)
    public function getNhanVienIdByMa($maNhanVien) {
        try {
            // Thử tìm theo id_nhan_vien trước
            $query = "SELECT id_nhan_vien FROM nhan_vien WHERE id_nhan_vien = ?";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Lỗi chuẩn bị truy vấn: " . $this->conn->error);
            }
            $stmt->bind_param("i", $maNhanVien);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            
            if ($row) {
                return $row['id_nhan_vien'];
            }
            
            // Nếu không tìm thấy, trả về null để tạo mới
            return null;
        } catch (Exception $e) {
            error_log("Lỗi trong getNhanVienIdByMa: " . $e->getMessage());
            throw $e;
        }
    }

    // Cập nhật điểm hiệu quả
    public function updateDiemHieuQua($idNhanVien, $diem, $thang = null, $nam = null) {
        try {
            // Nếu không có tháng/năm, dùng tháng/năm hiện tại
            if ($thang === null) $thang = date('n');
            if ($nam === null) $nam = date('Y');
            
            // Kiểm tra xem đã có bản ghi với tháng/năm này chưa
            $checkQuery = "SELECT id_danh_gia FROM danh_gia_nhan_vien WHERE id_nhan_vien = ? AND thang_danh_gia = ? AND nam_danh_gia = ?";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bind_param("iii", $idNhanVien, $thang, $nam);
            $checkStmt->execute();
            $existing = $checkStmt->get_result()->fetch_assoc();
            $checkStmt->close();

            if ($existing) {
                // Cập nhật bản ghi hiện có với đúng tháng/năm
                $query = "UPDATE danh_gia_nhan_vien SET diem_hieu_qua = ? WHERE id_nhan_vien = ? AND thang_danh_gia = ? AND nam_danh_gia = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param("diii", $diem, $idNhanVien, $thang, $nam);
            } else {
                // Tạo bản ghi mới với tháng/năm được chỉ định
                $query = "INSERT INTO danh_gia_nhan_vien (id_nhan_vien, diem_hieu_qua, thang_danh_gia, nam_danh_gia, nguoi_danh_gia) VALUES (?, ?, ?, ?, ?)";
                $stmt = $this->conn->prepare($query);
                $nguoiDanhGia = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'System';
                $stmt->bind_param("idiis", $idNhanVien, $diem, $thang, $nam, $nguoiDanhGia);
            }
            
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        } catch (Exception $e) {
            error_log("Lỗi trong updateDiemHieuQua: " . $e->getMessage());
            throw $e;
        }
    }

    // Cập nhật điểm thái độ
    public function updateDiemThaiDo($idNhanVien, $diem, $thang = null, $nam = null) {
        try {
            // Nếu không có tháng/năm, dùng tháng/năm hiện tại
            if ($thang === null) $thang = date('n');
            if ($nam === null) $nam = date('Y');
            
            // Kiểm tra xem đã có bản ghi với tháng/năm này chưa
            $checkQuery = "SELECT id_danh_gia FROM danh_gia_nhan_vien WHERE id_nhan_vien = ? AND thang_danh_gia = ? AND nam_danh_gia = ?";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bind_param("iii", $idNhanVien, $thang, $nam);
            $checkStmt->execute();
            $existing = $checkStmt->get_result()->fetch_assoc();
            $checkStmt->close();

            if ($existing) {
                // Cập nhật bản ghi hiện có với đúng tháng/năm
                $query = "UPDATE danh_gia_nhan_vien SET diem_thai_do = ? WHERE id_nhan_vien = ? AND thang_danh_gia = ? AND nam_danh_gia = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param("diii", $diem, $idNhanVien, $thang, $nam);
            } else {
                // Tạo bản ghi mới với tháng/năm được chỉ định
                $query = "INSERT INTO danh_gia_nhan_vien (id_nhan_vien, diem_thai_do, thang_danh_gia, nam_danh_gia, nguoi_danh_gia) VALUES (?, ?, ?, ?, ?)";
                $stmt = $this->conn->prepare($query);
                $nguoiDanhGia = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'System';
                $stmt->bind_param("idiis", $idNhanVien, $diem, $thang, $nam, $nguoiDanhGia);
            }
            
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        } catch (Exception $e) {
            error_log("Lỗi trong updateDiemThaiDo: " . $e->getMessage());
            throw $e;
        }
    }

    // Tính tổng công điểm theo quý và cập nhật điểm hiệu quả
    public function syncCongDiemTheoQuy($idNhanVien, $thang, $nam) {
        try {
            // Xác định quý từ tháng
            $quy = 1;
            if ($thang >= 4 && $thang <= 6) $quy = 2;
            elseif ($thang >= 7 && $thang <= 9) $quy = 3;
            elseif ($thang >= 10 && $thang <= 12) $quy = 4;
            
            // Xác định tháng cuối quý
            $thangCuoiQuy = [1 => 3, 2 => 6, 3 => 9, 4 => 12][$quy];
            
            // Xác định tháng đầu và cuối của quý
            $thangDauQuy = [1 => 1, 2 => 4, 3 => 7, 4 => 10][$quy];
            $thangCuoiQuy = [1 => 3, 2 => 6, 3 => 9, 4 => 12][$quy];
            
            // Tính tổng công điểm từ doanh_so_thang trong quý
            $sql = "SELECT SUM(cong_diem_danh_gia) as tong_cong_diem 
                    FROM doanh_so_thang 
                    WHERE id_nhan_vien = ? 
                    AND nam = ? 
                    AND thang >= ? 
                    AND thang <= ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iiii", $idNhanVien, $nam, $thangDauQuy, $thangCuoiQuy);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            
            $tongCongDiem = floatval($row['tong_cong_diem'] ?? 0);
            
            // Cập nhật điểm hiệu quả vào danh_gia_nhan_vien với tháng cuối quý
            return $this->updateDiemHieuQua($idNhanVien, $tongCongDiem, $thangCuoiQuy, $nam);
        } catch (Exception $e) {
            error_log("Lỗi trong syncCongDiemTheoQuy: " . $e->getMessage());
            return false;
        }
    }

    // Tạo nhân viên mới từ mã nhân viên
    public function createNhanVienFromMa($maNhanVien, $tenNhanVien) {
        try {
            file_put_contents('debug_model.txt', "DEBUG: Creating employee - Name: $tenNhanVien\n", FILE_APPEND);
            // Tạo nhân viên mới với thông tin cơ bản (không có cột ma_nhan_vien)
            $query = "INSERT INTO nhan_vien (ho_ten, gioi_tinh, ngay_sinh, email, so_dien_thoai, dia_chi, can_cuoc_cong_dan, ngay_cap, noi_cap, que_quan, hinh_anh, id_phong_ban, id_chuc_vu, loai_hop_dong, luong_co_ban, ngay_vao_lam, ngay_nghi_viec, trang_thai, ngay_bat_dau_thai_san, ngay_ket_thuc_thai_san, so_nguoi_phu_thuoc, phu_cap_chuc_vu, phu_cap_bang_cap, phu_cap_khac, tinh_trang_hon_nhan, so_bhxh, so_bhyt, so_bhtn, ngay_tham_gia_bhxh, so_tai_khoan, ten_ngan_hang, chi_nhanh_ngan_hang) VALUES (?, 'Nam', '1990-01-01', '', '', '', '', '2020-01-01', '', '', NULL, 1, 1, 'Toàn thời gian', 5000000, CURDATE(), '0000-00-00', 'Đang làm việc', '0000-00-00', '0000-00-00', 0, 0, 0, 0, 'Độc thân', '', '', '', '0000-00-00', '', '', '')";
            
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                file_put_contents('debug_model.txt', "ERROR: Prepare failed - " . $this->conn->error . "\n", FILE_APPEND);
                return false;
            }
            $stmt->bind_param("s", $tenNhanVien);
            
            if ($stmt->execute()) {
                $idNhanVien = $this->conn->insert_id;
                $stmt->close();
                file_put_contents('debug_model.txt', "SUCCESS: Created employee with ID: $idNhanVien\n", FILE_APPEND);
                return $idNhanVien;
            } else {
                file_put_contents('debug_model.txt', "ERROR: Execute failed - " . $stmt->error . "\n", FILE_APPEND);
                $stmt->close();
                return false;
            }
        } catch (Exception $e) {
            file_put_contents('debug_model.txt', "EXCEPTION: " . $e->getMessage() . "\n", FILE_APPEND);
            error_log("Lỗi trong createNhanVienFromMa: " . $e->getMessage());
            return false;
        }
    }

    // Lấy đánh giá theo nhân viên
    public function getDanhGiaByNhanVien($idNhanVien) {
        try {
            $query = "SELECT diem_chuyen_can, diem_hieu_qua, diem_thai_do, tong_diem, xep_loai 
                     FROM danh_gia_nhan_vien 
                     WHERE id_nhan_vien = ? 
                     ORDER BY ngay_tao DESC 
                     LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $idNhanVien);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();
            
            return $data;
        } catch (Exception $e) {
            error_log("Lỗi trong getDanhGiaByNhanVien: " . $e->getMessage());
            return false;
        }
    }

    // Lấy tất cả đánh giá
    public function getAllDanhGia() {
        try {
            $query = "SELECT 
                        id_danh_gia,
                        id_nhan_vien,
                        thang_danh_gia,
                        nam_danh_gia,
                        ngay_tao,
                        diem_chuyen_can,
                        diem_hieu_qua,
                        diem_thai_do,
                        tong_diem,
                        xep_loai,
                        trang_thai 
                     FROM danh_gia_nhan_vien 
                     ORDER BY id_nhan_vien, ngay_tao DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = [];
            
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            
            $stmt->close();
            return $data;
        } catch (Exception $e) {
            error_log("Lỗi trong getAllDanhGia: " . $e->getMessage());
            return [];
        }
    }

    // Upsert diem_chuyen_can theo quý: ghi vào bản ghi của tháng cuối quý, nếu chưa có thì tạo mới
    public function upsertDiemChuyenCanTheoQuy($idNhanVien, $thang, $nam, $diemChuyenCan, $idNguoiDanhGia) {
        try {
            // Kiểm tra tồn tại
            $check = $this->conn->prepare("SELECT id_danh_gia FROM danh_gia_nhan_vien WHERE id_nhan_vien = ? AND thang_danh_gia = ? AND nam_danh_gia = ? LIMIT 1");
            $check->bind_param("iii", $idNhanVien, $thang, $nam);
            $check->execute();
            $res = $check->get_result()->fetch_assoc();
            $check->close();

            if ($res) {
                $idDanhGia = intval($res['id_danh_gia']);
                if ($diemChuyenCan !== null) {
                    $upd = $this->conn->prepare("UPDATE danh_gia_nhan_vien SET diem_chuyen_can = ? WHERE id_danh_gia = ?");
                    $upd->bind_param("di", $diemChuyenCan, $idDanhGia);
                    $ok = $upd->execute();
                    $upd->close();
                } else {
                    // Không ghi đè điểm nếu null; chỉ coi như đảm bảo bản ghi tồn tại
                    $ok = true;
                }
                return $ok;
            } else {
                $ins = $this->conn->prepare("INSERT INTO danh_gia_nhan_vien (id_nhan_vien, id_nguoi_danh_gia, thang_danh_gia, nam_danh_gia, diem_chuyen_can, diem_hieu_qua, diem_thai_do, tong_diem, xep_loai, trang_thai) VALUES (?, ?, ?, ?, ?, 0, 0, 0, 'Chưa xếp loại', 'Nháp')");
                if (!$ins) { throw new Exception($this->conn->error); }
                $valueToInsert = ($diemChuyenCan !== null) ? $diemChuyenCan : 0.0;
                $ins->bind_param("iiiid", $idNhanVien, $idNguoiDanhGia, $thang, $nam, $valueToInsert);
                $ok = $ins->execute();
                $ins->close();
                return $ok;
            }
        } catch (Exception $e) {
            error_log('upsertDiemChuyenCanTheoQuy error: ' . $e->getMessage());
            return false;
        }
    }
}
?>
