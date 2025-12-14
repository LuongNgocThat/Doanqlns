<?php
require_once __DIR__ . '/../config/Database.php';

class ThuongTetModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // ===== QUẢN LÝ THƯỞNG TẾT =====

    public function getAllThuongTet($nam = null) {
        try {
            $sql = "SELECT tt.*, nv.ho_ten, nv.id_phong_ban, pb.ten_phong_ban 
                    FROM thuong_tet tt
                    LEFT JOIN nhan_vien nv ON tt.id_nhan_vien = nv.id_nhan_vien
                    LEFT JOIN phong_ban pb ON nv.id_phong_ban = pb.id_phong_ban";
            
            $params = [];
            if ($nam) {
                $sql .= " WHERE tt.nam = :nam";
                $params[':nam'] = $nam;
            }
            
            $sql .= " ORDER BY tt.nam DESC, nv.ho_ten ASC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            $bonusData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $bonusData;
        } catch (PDOException $e) {
            error_log("Error in getAllThuongTet: " . $e->getMessage());
            return [];
        }
    }

    public function getThuongTetById($id) {
        try {
            $sql = "SELECT tt.*, nv.ho_ten, nv.id_phong_ban, pb.ten_phong_ban 
                    FROM thuong_tet tt
                    LEFT JOIN nhan_vien nv ON tt.id_nhan_vien = nv.id_nhan_vien
                    LEFT JOIN phong_ban pb ON nv.id_phong_ban = pb.id_phong_ban
                    WHERE tt.id_thuong_tet = :id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getThuongTetById: " . $e->getMessage());
            return false;
        }
    }

    public function getThuongTetByNhanVienAndNam($id_nhan_vien, $nam) {
        try {
            $sql = "SELECT * FROM thuong_tet 
                    WHERE id_nhan_vien = :id_nhan_vien AND nam = :nam";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_nhan_vien', $id_nhan_vien, PDO::PARAM_INT);
            $stmt->bindParam(':nam', $nam, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getThuongTetByNhanVienAndNam: " . $e->getMessage());
            return false;
        }
    }

    public function addThuongTet($data) {
        try {
            $sql = "INSERT INTO thuong_tet (id_nhan_vien, nam, tong_diem, xep_loai, so_ngay_nghi_phep, muc_thuong, ghi_chu, trang_thai) 
                    VALUES (:id_nhan_vien, :nam, :tong_diem, :xep_loai, :so_ngay_nghi_phep, :muc_thuong, :ghi_chu, :trang_thai)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_nhan_vien', $data['id_nhan_vien'], PDO::PARAM_INT);
            $stmt->bindParam(':nam', $data['nam'], PDO::PARAM_INT);
            $stmt->bindParam(':tong_diem', $data['tong_diem']);
            $stmt->bindParam(':xep_loai', $data['xep_loai']);
            $stmt->bindParam(':so_ngay_nghi_phep', $data['so_ngay_nghi_phep'], PDO::PARAM_INT);
            $stmt->bindParam(':muc_thuong', $data['muc_thuong']);
            $stmt->bindParam(':ghi_chu', $data['ghi_chu']);
            $stmt->bindParam(':trang_thai', $data['trang_thai']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in addThuongTet: " . $e->getMessage());
            return false;
        }
    }

    public function updateThuongTet($id, $data) {
        try {
            $sql = "UPDATE thuong_tet SET 
                    tong_diem = :tong_diem,
                    xep_loai = :xep_loai,
                    so_ngay_nghi_phep = :so_ngay_nghi_phep,
                    muc_thuong = :muc_thuong,
                    ghi_chu = :ghi_chu,
                    trang_thai = :trang_thai
                    WHERE id_thuong_tet = :id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':tong_diem', $data['tong_diem']);
            $stmt->bindParam(':xep_loai', $data['xep_loai']);
            $stmt->bindParam(':so_ngay_nghi_phep', $data['so_ngay_nghi_phep'], PDO::PARAM_INT);
            $stmt->bindParam(':muc_thuong', $data['muc_thuong']);
            $stmt->bindParam(':ghi_chu', $data['ghi_chu']);
            $stmt->bindParam(':trang_thai', $data['trang_thai']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in updateThuongTet: " . $e->getMessage());
            return false;
        }
    }

    public function deleteThuongTet($id) {
        try {
            $sql = "DELETE FROM thuong_tet WHERE id_thuong_tet = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in deleteThuongTet: " . $e->getMessage());
            return false;
        }
    }

    // ===== TÍCH HỢP DỮ LIỆU TỪ DANH_GIA_NHAN_VIEN =====

    public function getDanhGiaData($id_nhan_vien, $nam) {
        try {
            // Lấy dữ liệu đánh giá mới nhất, ưu tiên "Đã duyệt" trước, sau đó mới đến "Nháp"
            $sql = "SELECT tong_diem, xep_loai 
                    FROM danh_gia_nhan_vien 
                    WHERE id_nhan_vien = :id_nhan_vien 
                    AND nam_danh_gia = :nam
                    ORDER BY 
                        CASE 
                            WHEN trang_thai = 'Đã duyệt' THEN 1
                            WHEN trang_thai = 'Nháp' THEN 2
                            ELSE 3
                        END,
                        ngay_tao DESC 
                    LIMIT 1";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_nhan_vien', $id_nhan_vien, PDO::PARAM_INT);
            $stmt->bindParam(':nam', $nam, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: ['tong_diem' => null, 'xep_loai' => null];
        } catch (PDOException $e) {
            error_log("Error in getDanhGiaData: " . $e->getMessage());
            return ['tong_diem' => null, 'xep_loai' => null];
        }
    }

    public function getDanhGiaTongHop($id_nhan_vien, $nam) {
        try {
            // Lấy dữ liệu từ 4 quý (tháng 3, 6, 9, 12) - đại diện cho 4 quý
            // Với mỗi tháng, lấy bản ghi có điểm cao nhất, ưu tiên "Đã duyệt" trước, nếu không có thì lấy "Nháp"
            // Chỉ lấy những bản ghi có điểm > 0
            $sql = "SELECT 
                        thang_danh_gia,
                        tong_diem,
                        xep_loai,
                        trang_thai,
                        ROW_NUMBER() OVER (
                            PARTITION BY thang_danh_gia 
                            ORDER BY 
                        CASE 
                                    WHEN trang_thai = 'Đã duyệt' THEN 1
                                    WHEN trang_thai = 'Nháp' THEN 2
                                    ELSE 3
                                END,
                                tong_diem DESC,
                                ngay_tao DESC
                        ) as rn
                    FROM danh_gia_nhan_vien 
                    WHERE id_nhan_vien = :id_nhan_vien 
                    AND nam_danh_gia = :nam
                    AND thang_danh_gia IN (3, 6, 9, 12)
                    AND tong_diem IS NOT NULL
                    AND tong_diem > 0";
            
            // Sử dụng subquery để lấy bản ghi đầu tiên của mỗi tháng
            $sql = "SELECT 
                        thang_danh_gia,
                        tong_diem,
                        xep_loai,
                        trang_thai
                    FROM (
                        SELECT 
                            thang_danh_gia,
                            tong_diem,
                            xep_loai,
                            trang_thai,
                            ROW_NUMBER() OVER (
                                PARTITION BY thang_danh_gia 
                    ORDER BY 
                        CASE 
                            WHEN trang_thai = 'Đã duyệt' THEN 1
                            WHEN trang_thai = 'Nháp' THEN 2
                            ELSE 3
                                    END,
                                    tong_diem DESC,
                                    ngay_tao DESC
                            ) as rn
                        FROM danh_gia_nhan_vien 
                        WHERE id_nhan_vien = :id_nhan_vien 
                        AND nam_danh_gia = :nam
                        AND thang_danh_gia IN (3, 6, 9, 12)
                        AND tong_diem IS NOT NULL
                        AND tong_diem > 0
                    ) as ranked
                    WHERE rn = 1
                    ORDER BY thang_danh_gia ASC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_nhan_vien', $id_nhan_vien, PDO::PARAM_INT);
            $stmt->bindParam(':nam', $nam, PDO::PARAM_INT);
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($results)) {
                return ['tong_diem' => 0, 'xep_loai' => 'Chưa đánh giá'];
            }
            
            // Kiểm tra xem có đủ 4 quý không (tháng 3, 6, 9, 12)
            $thangCoDuLieu = [];
            $tongDiemTongHop = 0;
            $xepLoaiQuy = [];
            
            foreach ($results as $row) {
                $thang = (int)$row['thang_danh_gia'];
                $thangCoDuLieu[$thang] = true;
                $tongDiemTongHop += (float)$row['tong_diem'];
                $xepLoaiQuy[$thang] = $row['xep_loai'];
            }
            
            // Kiểm tra xem có đủ 4 quý không
            $cacThangQuy = [3, 6, 9, 12];
            $duBonQuy = true;
            foreach ($cacThangQuy as $thang) {
                if (!isset($thangCoDuLieu[$thang])) {
                    $duBonQuy = false;
                    break;
                }
            }
            
            // Nếu có đủ 4 quý, tính tổng điểm và xác định xếp loại
            if ($duBonQuy) {
                // Tính trung bình điểm để xác định xếp loại (vì mỗi quý có điểm từ 0-10)
                $diemTrungBinh = $tongDiemTongHop / 4;
                
                // Xác định xếp loại dựa trên điểm trung bình
                $xepLoai = 'Yếu';
                if ($diemTrungBinh >= 9.0) {
                    $xepLoai = 'Xuất sắc';
                } elseif ($diemTrungBinh >= 8.0) {
                    $xepLoai = 'Tốt';
                } elseif ($diemTrungBinh >= 7.0) {
                    $xepLoai = 'Khá';
                } elseif ($diemTrungBinh >= 6.0) {
                    $xepLoai = 'Trung bình';
                }
                
                return [
                    'tong_diem' => round($tongDiemTongHop, 2), // Tổng điểm của 4 quý
                    'xep_loai' => $xepLoai
                ];
            } else {
                // Nếu chưa đủ 4 quý, trả về giá trị mặc định
                return ['tong_diem' => 0, 'xep_loai' => 'Chưa đủ 4 quý'];
            }
            
        } catch (PDOException $e) {
            error_log("Error in getDanhGiaTongHop: " . $e->getMessage());
            // Fallback: thử cách khác nếu ROW_NUMBER() không hỗ trợ
            try {
                // Cách 2: Sử dụng GROUP BY với MAX
                $sql2 = "SELECT 
                            thang_danh_gia,
                            MAX(CASE 
                                WHEN trang_thai = 'Đã duyệt' THEN tong_diem 
                                ELSE NULL 
                            END) as tong_diem_duyet,
                            MAX(CASE 
                                WHEN trang_thai = 'Đã duyệt' THEN xep_loai 
                                ELSE NULL 
                            END) as xep_loai_duyet,
                            MAX(CASE 
                                WHEN trang_thai != 'Đã duyệt' THEN tong_diem 
                                ELSE NULL 
                            END) as tong_diem_khac,
                            MAX(CASE 
                                WHEN trang_thai != 'Đã duyệt' THEN xep_loai 
                                ELSE NULL 
                            END) as xep_loai_khac
                        FROM danh_gia_nhan_vien 
                        WHERE id_nhan_vien = :id_nhan_vien 
                        AND nam_danh_gia = :nam
                        AND thang_danh_gia IN (3, 6, 9, 12)
                        AND tong_diem IS NOT NULL
                        AND tong_diem > 0
                        GROUP BY thang_danh_gia
                        ORDER BY thang_danh_gia ASC";
                
                $stmt2 = $this->conn->prepare($sql2);
                $stmt2->bindParam(':id_nhan_vien', $id_nhan_vien, PDO::PARAM_INT);
                $stmt2->bindParam(':nam', $nam, PDO::PARAM_INT);
                $stmt2->execute();
                
                $results2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
                
                if (empty($results2)) {
                    return ['tong_diem' => 0, 'xep_loai' => 'Chưa đánh giá'];
                }
                
                $thangCoDuLieu = [];
                $tongDiemTongHop = 0;
                
                foreach ($results2 as $row) {
                    $thang = (int)$row['thang_danh_gia'];
                    // Ưu tiên "Đã duyệt", nếu không có thì lấy "Nháp"
                    $tongDiem = $row['tong_diem_duyet'] ?? $row['tong_diem_khac'] ?? 0;
                    if ($tongDiem > 0) {
                        $thangCoDuLieu[$thang] = true;
                        $tongDiemTongHop += (float)$tongDiem;
                    }
                }
                
                $cacThangQuy = [3, 6, 9, 12];
                $duBonQuy = true;
                foreach ($cacThangQuy as $thang) {
                    if (!isset($thangCoDuLieu[$thang])) {
                        $duBonQuy = false;
                        break;
                    }
                }
                
                if ($duBonQuy) {
                    $diemTrungBinh = $tongDiemTongHop / 4;
                    $xepLoai = 'Yếu';
                    if ($diemTrungBinh >= 9.0) {
                        $xepLoai = 'Xuất sắc';
                    } elseif ($diemTrungBinh >= 8.0) {
                        $xepLoai = 'Tốt';
                    } elseif ($diemTrungBinh >= 7.0) {
                        $xepLoai = 'Khá';
                    } elseif ($diemTrungBinh >= 6.0) {
                        $xepLoai = 'Trung bình';
                    }
                    
                    return [
                        'tong_diem' => round($tongDiemTongHop, 2),
                        'xep_loai' => $xepLoai
                    ];
                } else {
                    return ['tong_diem' => 0, 'xep_loai' => 'Chưa đủ 4 quý'];
                }
            } catch (PDOException $e2) {
                error_log("Error in getDanhGiaTongHop fallback: " . $e2->getMessage());
                // Cách 3: Sử dụng cách đơn giản nhất - lấy tất cả và xử lý trong PHP
                try {
                    $sql3 = "SELECT 
                                thang_danh_gia,
                                tong_diem,
                                xep_loai,
                                trang_thai
                            FROM danh_gia_nhan_vien 
                            WHERE id_nhan_vien = :id_nhan_vien 
                            AND nam_danh_gia = :nam
                            AND thang_danh_gia IN (3, 6, 9, 12)
                            AND tong_diem IS NOT NULL
                            AND tong_diem > 0
                            ORDER BY thang_danh_gia ASC, 
                                CASE 
                                    WHEN trang_thai = 'Đã duyệt' THEN 1
                                    WHEN trang_thai = 'Nháp' THEN 2
                                    ELSE 3
                                END,
                                tong_diem DESC";
                    
                    $stmt3 = $this->conn->prepare($sql3);
                    $stmt3->bindParam(':id_nhan_vien', $id_nhan_vien, PDO::PARAM_INT);
                    $stmt3->bindParam(':nam', $nam, PDO::PARAM_INT);
                    $stmt3->execute();
                    
                    $results3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (empty($results3)) {
                        return ['tong_diem' => 0, 'xep_loai' => 'Chưa đánh giá'];
                    }
                    
                    $thangCoDuLieu = [];
                    $tongDiemTongHop = 0;
                    
                    foreach ($results3 as $row) {
                        $thang = (int)$row['thang_danh_gia'];
                        // Chỉ lấy bản ghi đầu tiên của mỗi tháng (đã được sắp xếp)
                        if (!isset($thangCoDuLieu[$thang])) {
                            $thangCoDuLieu[$thang] = true;
                            $tongDiemTongHop += (float)$row['tong_diem'];
                        }
                    }
                    
                    $cacThangQuy = [3, 6, 9, 12];
                    $duBonQuy = true;
                    foreach ($cacThangQuy as $thang) {
                        if (!isset($thangCoDuLieu[$thang])) {
                            $duBonQuy = false;
                            break;
                        }
                    }
                    
                    if ($duBonQuy) {
                        $diemTrungBinh = $tongDiemTongHop / 4;
                        $xepLoai = 'Yếu';
                        if ($diemTrungBinh >= 9.0) {
                            $xepLoai = 'Xuất sắc';
                        } elseif ($diemTrungBinh >= 8.0) {
                            $xepLoai = 'Tốt';
                        } elseif ($diemTrungBinh >= 7.0) {
                            $xepLoai = 'Khá';
                        } elseif ($diemTrungBinh >= 6.0) {
                            $xepLoai = 'Trung bình';
                        }
                        
                        return [
                            'tong_diem' => round($tongDiemTongHop, 2),
                            'xep_loai' => $xepLoai
                        ];
                    } else {
                        return ['tong_diem' => 0, 'xep_loai' => 'Chưa đủ 4 quý'];
                    }
                } catch (PDOException $e3) {
                    error_log("Error in getDanhGiaTongHop final fallback: " . $e3->getMessage());
            return ['tong_diem' => 0, 'xep_loai' => 'Chưa đánh giá'];
                }
            }
        }
    }

    // ===== TÍCH HỢP DỮ LIỆU TỪ NGHIPHEP =====

    public function getNghiPhepData($id_nhan_vien, $nam) {
        try {
            // Lấy tổng số ngày nghỉ phép năm đã sử dụng (tính theo logic trong nghiphep.php)
            $sql = "SELECT ngay_bat_dau, ngay_ket_thuc
                    FROM nghi_phep 
                    WHERE id_nhan_vien = :id_nhan_vien 
                    AND YEAR(ngay_bat_dau) = :nam
                    AND loai_nghi = 'Phép Năm'
                    AND trang_thai1 = 'Đã duyệt'";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_nhan_vien', $id_nhan_vien, PDO::PARAM_INT);
            $stmt->bindParam(':nam', $nam, PDO::PARAM_INT);
            $stmt->execute();
            
            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $tong_ngay_da_nghi = 0;
            
            // Tính số ngày đã sử dụng (bỏ qua Chủ Nhật như trong nghiphep.php)
            foreach ($records as $record) {
                $startDate = new DateTime($record['ngay_bat_dau']);
                $endDate = new DateTime($record['ngay_ket_thuc']);
                $currentDate = clone $startDate;
                
                while ($currentDate <= $endDate) {
                    // Bỏ qua Chủ Nhật (ngày 0)
                    if ($currentDate->format('w') != 0) {
                        $tong_ngay_da_nghi++;
                    }
                    $currentDate->add(new DateInterval('P1D'));
                }
            }
            
            // Tổng phép năm luôn là 12 ngày (như trong nghiphep.php)
            $tong_ngay_phep_nam = 12;
            $so_ngay_con_lai = $tong_ngay_phep_nam - $tong_ngay_da_nghi;
            
            return max(0, $so_ngay_con_lai); // Không âm
        } catch (PDOException $e) {
            error_log("Error in getNghiPhepData: " . $e->getMessage());
            return 12; // Trả về 12 nếu có lỗi
        }
    }

    // ===== TỰ ĐỘNG TẠO THƯỞNG TẾT =====

    public function autoCreateThuongTet($nam) {
        try {
            // Lấy danh sách nhân viên
            $sql = "SELECT id_nhan_vien, ho_ten FROM nhan_vien WHERE trang_thai = 'Đang làm việc'";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $nhanVienList = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $created = 0;
            $errors = [];
            
            foreach ($nhanVienList as $nv) {
                // Kiểm tra xem đã có thưởng tết chưa
                $checkSql = "SELECT id_thuong_tet FROM thuong_tet WHERE id_nhan_vien = :id_nhan_vien AND nam = :nam";
                $checkStmt = $this->conn->prepare($checkSql);
                $checkStmt->bindParam(':id_nhan_vien', $nv['id_nhan_vien'], PDO::PARAM_INT);
                $checkStmt->bindParam(':nam', $nam, PDO::PARAM_INT);
                $checkStmt->execute();
                
                if ($checkStmt->fetch()) {
                    continue; // Đã có thưởng tết
                }
                
                // Lấy dữ liệu đánh giá từ 4 quý (tháng 3, 6, 9, 12)
                $danhGiaData = $this->getDanhGiaTongHop($nv['id_nhan_vien'], $nam);
                
                // Lấy dữ liệu nghỉ phép
                $so_ngay_nghi_phep = $this->getNghiPhepData($nv['id_nhan_vien'], $nam);
                
                // Tính mức thưởng dựa trên xếp loại và nghỉ phép
                $muc_thuong = $this->calculateMucThuong($danhGiaData['xep_loai'], $so_ngay_nghi_phep, $danhGiaData['tong_diem'], 0, $nv['id_nhan_vien']);
                
                // Tạo thưởng tết
                $data = [
                    'id_nhan_vien' => $nv['id_nhan_vien'],
                    'nam' => $nam,
                    'tong_diem' => $danhGiaData['tong_diem'],
                    'xep_loai' => $danhGiaData['xep_loai'],
                    'so_ngay_nghi_phep' => $so_ngay_nghi_phep,
                    'muc_thuong' => $muc_thuong,
                    'ghi_chu' => 'Tự động tạo dựa trên đánh giá và nghỉ phép',
                    'trang_thai' => 'Chưa duyệt'
                ];
                
                if ($this->addThuongTet($data)) {
                    $created++;
                } else {
                    $errors[] = "Không thể tạo thưởng tết cho {$nv['ho_ten']}";
                }
            }
            
            return [
                'success' => true,
                'created' => $created,
                'errors' => $errors
            ];
        } catch (PDOException $e) {
            error_log("Error in autoCreateThuongTet: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    // ===== TÍNH MỨC THƯỞNG =====

    private function calculateMucThuong($xep_loai, $so_ngay_nghi_phep, $tong_diem = 0, $luong_co_ban = 0, $id_nhan_vien = 0) {
        // Mặc định lương cơ bản nếu không có
        if ($luong_co_ban <= 0) {
            $luong_co_ban = 10000000; // 10 triệu VND
        }
        
        // Tính lương tháng 13 theo công thức mới
        $luong_thang_13 = $this->calculateLuongThang13($id_nhan_vien, $luong_co_ban);
        
        return round($luong_thang_13);
    }
    
    /**
     * Tính lương tháng 13 theo tiền lương trung bình 12 tháng
     */
    private function calculateLuongThang13($id_nhan_vien, $luong_co_ban) {
        try {
            $database = new Database();
            $conn = $database->getConnection();
            
            // Lấy lương trung bình 12 tháng gần nhất từ bảng luong
            $sql = "SELECT 
                        AVG(luong_co_ban) as luong_trung_binh,
                        COUNT(*) as so_thang_co_du_lieu
                    FROM luong 
                    WHERE id_nhan_vien = ? 
                    AND YEAR(ngay_tao) = YEAR(CURDATE())
                    ORDER BY ngay_tao DESC 
                    LIMIT 12";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id_nhan_vien]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $luong_trung_binh = $result['luong_trung_binh'] ?? 0;
            $so_thang_co_du_lieu = $result['so_thang_co_du_lieu'] ?? 0;
            
            // Nếu có dữ liệu lương trong bảng luong
            if ($so_thang_co_du_lieu > 0 && $luong_trung_binh > 0) {
                if ($so_thang_co_du_lieu >= 12) {
                    // Đã làm đủ 12 tháng: lương tháng 13 = lương trung bình 12 tháng
                    return $luong_trung_binh;
                } else {
                    // Chưa đủ 12 tháng: (số tháng làm việc/12) × lương trung bình
                    return ($so_thang_co_du_lieu / 12) * $luong_trung_binh;
                }
            } else {
                // Nếu không có dữ liệu trong bảng luong, sử dụng lương cơ bản
                // Giả sử làm đủ 12 tháng với lương cơ bản
                return $luong_co_ban;
            }
            
        } catch (Exception $e) {
            error_log("Error calculating luong thang 13: " . $e->getMessage());
            // Fallback: trả về lương cơ bản
            return $luong_co_ban;
        }
    }

    // ===== THỐNG KÊ =====

    public function getThongKeThuongTet($nam) {
        try {
            $sql = "SELECT 
                        COUNT(*) as tong_nhan_vien,
                        SUM(muc_thuong) as tong_thuong,
                        AVG(muc_thuong) as trung_binh_thuong,
                        COUNT(CASE WHEN trang_thai = 'Đã duyệt' THEN 1 END) as da_duyet,
                        COUNT(CASE WHEN trang_thai = 'Đã thanh toán' THEN 1 END) as da_thanh_toan
                    FROM thuong_tet 
                    WHERE nam = :nam";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nam', $nam, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getThongKeThuongTet: " . $e->getMessage());
            return false;
        }
    }
}
?>
