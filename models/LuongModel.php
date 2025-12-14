<?php
include_once(__DIR__ . '/../config/Database.php');

class LuongModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Cập nhật phụ cấp chức vụ cho tất cả bản ghi lương
    public function updatePhuCapChucVu() {
        try {
            $query = "UPDATE luong l 
                     LEFT JOIN nhan_vien nv ON l.id_nhan_vien = nv.id_nhan_vien 
                     SET l.phu_cap_chuc_vu = COALESCE(nv.phu_cap_chuc_vu, 0),
                         l.phu_cap_bang_cap = COALESCE(nv.phu_cap_bang_cap, 0),
                         l.phu_cap_khac = COALESCE(nv.phu_cap_khac, 0),
                         l.luong_co_ban = COALESCE(nv.luong_co_ban, 0)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return ['success' => true, 'message' => 'Đã cập nhật phụ cấp chức vụ cho tất cả bản ghi lương'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi khi cập nhật phụ cấp chức vụ: ' . $e->getMessage()];
        }
    }

    // Hàm tính số ngày công từ bảng cham_cong
    private function calculateAttendanceStats($id_nhan_vien, $month, $year) {
        try {
            $query = "SELECT ngay_lam_viec, trang_thai 
                      FROM cham_cong 
                      WHERE id_nhan_vien = :id_nhan_vien 
                      AND ngay_lam_viec LIKE :thang";
            $stmt = $this->conn->prepare($query);
            $thang = sprintf('%04d-%02d%%', $year, $month);
            $stmt->bindParam(':id_nhan_vien', $id_nhan_vien, PDO::PARAM_INT);
            $stmt->bindParam(':thang', $thang, PDO::PARAM_STR);
            $stmt->execute();
            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $startDate = new DateTime("$year-$month-01");
            $endDate = new DateTime("$year-$month-" . $startDate->format('t'));
            $daysInMonth = $startDate->format('t');

            $diemDanhDays = 0;
            $nghiDays = 0;
            $khongPhepCount = 0;

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = new DateTime("$year-$month-$day");
                $dateStr = $date->format('Y-m-d');
                $isSunday = $date->format('w') == 0;
                $record = array_filter($records, function($r) use ($dateStr) {
                    return $r['ngay_lam_viec'] === $dateStr;
                });
                $record = reset($record);

                if ($isSunday) {
                    $diemDanhDays += 1;
                } elseif ($record) {
                    $trang_thai = $record['trang_thai'];
                    switch ($trang_thai) {
                        case 'Đúng giờ':
                            $diemDanhDays += 1;
                            break;
                        case 'Đi trễ':
                            $diemDanhDays += 0.75;
                            break;
                        case 'Có phép':
                            $diemDanhDays += 1;
                            $nghiDays -= 0.5;
                            $khongPhepCount += 0.5;
                            break;
                        case 'Không phép':
                            $diemDanhDays += 1;
                            $nghiDays -= 1;
                            $khongPhepCount += 1;
                            break;
                        case 'Phép Năm':
                        case 'Nghỉ Lễ':
                            $diemDanhDays += 1; // Tính là ngày công bình thường
                            break;
                    }
                }
            }

            $totalWorkDays = $diemDanhDays - $khongPhepCount;
            return $totalWorkDays;
        } catch (PDOException $e) {
            error_log("Lỗi khi tính số ngày công: " . $e->getMessage());
            return 0;
        }
    }

    public function getAllLuong($month, $year) {
        try {
            // Bước 1: Lấy tất cả nhân viên để đảm bảo mỗi nhân viên có bản ghi lương
            $queryNhanVien = "SELECT id_nhan_vien FROM nhan_vien";
            $stmtNhanVien = $this->conn->prepare($queryNhanVien);
            $stmtNhanVien->execute();
            $nhanViens = $stmtNhanVien->fetchAll(PDO::FETCH_ASSOC);

            $thang = sprintf('%04d-%02d', $year, $month);

            // Bước 2: Kiểm tra và tạo bản ghi lương nếu chưa tồn tại
            foreach ($nhanViens as $nhanVien) {
                $id_nhan_vien = $nhanVien['id_nhan_vien'];
                $queryCheck = "SELECT COUNT(*) FROM luong WHERE id_nhan_vien = :id_nhan_vien AND thang = :thang";
                $stmtCheck = $this->conn->prepare($queryCheck);
                $stmtCheck->bindParam(':id_nhan_vien', $id_nhan_vien, PDO::PARAM_INT);
                $stmtCheck->bindParam(':thang', $thang, PDO::PARAM_STR);
                $stmtCheck->execute();
                $count = $stmtCheck->fetchColumn();

                if ($count == 0) {
                    // Tính số ngày công từ bảng cham_cong
                    $so_ngay_cong = $this->calculateAttendanceStats($id_nhan_vien, $month, $year);

                    // Lấy thông tin nhân viên để cập nhật phụ cấp
                    $queryNhanVien = "SELECT luong_co_ban, phu_cap_chuc_vu, phu_cap_bang_cap, phu_cap_khac FROM nhan_vien WHERE id_nhan_vien = :id_nhan_vien";
                    $stmtNhanVien = $this->conn->prepare($queryNhanVien);
                    $stmtNhanVien->bindParam(':id_nhan_vien', $id_nhan_vien, PDO::PARAM_INT);
                    $stmtNhanVien->execute();
                    $nhanVienInfo = $stmtNhanVien->fetch(PDO::FETCH_ASSOC);

                    // Tạo bản ghi lương mới với phụ cấp từ nhan_vien
                    $queryInsert = "INSERT INTO luong (id_nhan_vien, thang, trang_thai, ngay_cham_cong, so_ngay_cong, luong_co_ban, phu_cap_chuc_vu, phu_cap_bang_cap, phu_cap_khac) 
                                    VALUES (:id_nhan_vien, :thang, 'Tạm tính', :ngay_cham_cong, :so_ngay_cong, :luong_co_ban, :phu_cap_chuc_vu, :phu_cap_bang_cap, :phu_cap_khac)";
                    $stmtInsert = $this->conn->prepare($queryInsert);
                    $stmtInsert->bindParam(':id_nhan_vien', $id_nhan_vien, PDO::PARAM_INT);
                    $stmtInsert->bindParam(':thang', $thang, PDO::PARAM_STR);
                    $ngay_cham_cong = date('Y-m-d');
                    $stmtInsert->bindParam(':ngay_cham_cong', $ngay_cham_cong, PDO::PARAM_STR);
                    $stmtInsert->bindParam(':so_ngay_cong', $so_ngay_cong, PDO::PARAM_STR);
                    $stmtInsert->bindParam(':luong_co_ban', $nhanVienInfo['luong_co_ban'], PDO::PARAM_STR);
                    $stmtInsert->bindParam(':phu_cap_chuc_vu', $nhanVienInfo['phu_cap_chuc_vu'], PDO::PARAM_STR);
                    $stmtInsert->bindParam(':phu_cap_bang_cap', $nhanVienInfo['phu_cap_bang_cap'], PDO::PARAM_STR);
                    $stmtInsert->bindParam(':phu_cap_khac', $nhanVienInfo['phu_cap_khac'], PDO::PARAM_STR);
                    $stmtInsert->execute();
                }
            }

            // Bước 3: Lấy dữ liệu lương và tính toán các thông số
            $query = "SELECT 
                        l.id_luong,
                        l.id_nhan_vien,
                        nv.ho_ten,
                        l.thang,
                        l.so_ngay_cong,
                        l.trang_thai,
                        l.luong_co_ban,
                        l.phu_cap_chuc_vu,
                        l.phu_cap_bang_cap,
                        l.phu_cap_khac,
                        l.tien_thuong,
                        l.cac_khoan_tru_khac,
                        l.luong_thuc_nhan,
                        l.so_ngay_nghi_phep,
                        l.so_ngay_nghi_khong_phep,
                        l.thu_nhap_truoc_thue,
                        l.thue_tncn,
                        l.bhxh_nv,
                        l.bhyt_nv,
                        l.bhtn_nv,
                        nv.luong_co_ban AS base_salary,
                        nv.phu_cap_chuc_vu AS nv_phu_cap_chuc_vu,
                        nv.phu_cap_bang_cap AS nv_phu_cap_bang_cap,
                        nv.phu_cap_khac AS nv_phu_cap_khac,
                        nv.so_nguoi_phu_thuoc,
                        ROUND((nv.luong_co_ban / DAY(LAST_DAY(STR_TO_DATE(CONCAT(:thang, '-01'), '%Y-%m-%d')))) * COALESCE(l.so_ngay_cong, 0), 0) AS calculated_luong_co_ban,
                        COALESCE(SUM(t.tien_thuong), 0) AS tien_thuong,
                        COALESCE(bt.tong_khoan_tru, 0) AS cac_khoan_tru
                    FROM luong l
                    INNER JOIN nhan_vien nv ON l.id_nhan_vien = nv.id_nhan_vien
                    LEFT JOIN thuong t ON l.id_nhan_vien = t.id_nhan_vien 
                        AND DATE_FORMAT(t.ngay, '%Y-%m') = :thang
                    LEFT JOIN bao_hiem_thue_tncn bt ON l.id_nhan_vien = bt.id_nhan_vien 
                        AND bt.thang = :thang
                    WHERE l.thang = :thang
                    GROUP BY l.id_luong, l.id_nhan_vien, nv.ho_ten, l.thang, l.so_ngay_cong, l.trang_thai,
                             l.luong_co_ban, l.phu_cap_chuc_vu, l.phu_cap_bang_cap, l.phu_cap_khac, l.tien_thuong, l.cac_khoan_tru_khac, l.luong_thuc_nhan,
                             l.so_ngay_nghi_phep, l.so_ngay_nghi_khong_phep, l.thu_nhap_truoc_thue, l.thue_tncn, l.bhxh_nv, l.bhyt_nv, l.bhtn_nv,
                             nv.luong_co_ban, nv.phu_cap_chuc_vu, nv.phu_cap_bang_cap, nv.phu_cap_khac, nv.so_nguoi_phu_thuoc, bt.tong_khoan_tru";

            $stmt = $this->conn->prepare($query);
            $stmt->execute(['thang' => $thang, 'thang' => $thang, 'thang' => $thang]);

            $luongData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Bước 4: Cập nhật số ngày công và các thông số lương
            foreach ($luongData as &$record) {
                // Tính lại số ngày công từ bảng cham_cong
                $so_ngay_cong = $this->calculateAttendanceStats($record['id_nhan_vien'], $month, $year);
                $luong_co_ban = $record['calculated_luong_co_ban'];
                $phu_cap_chuc_vu = $record['nv_phu_cap_chuc_vu'] ?? 0;
                $phu_cap_bang_cap = $record['nv_phu_cap_bang_cap'] ?? 0;
                $phu_cap_khac = $record['nv_phu_cap_khac'] ?? 0;
                $tien_thuong = $record['tien_thuong'];
                $cac_khoan_tru = $record['cac_khoan_tru']; // Sử dụng từ query AS cac_khoan_tru
                
                // Tính lương gross (trước thuế)
                $luong_gross = $luong_co_ban + $phu_cap_chuc_vu + $phu_cap_bang_cap + $phu_cap_khac + $tien_thuong;
                
                // Tính giảm trừ gia cảnh (11 triệu + 4.4 triệu/người phụ thuộc)
                $so_nguoi_phu_thuoc = $record['so_nguoi_phu_thuoc'] ?? 0;
                $giam_tru_gia_canh = 11000000 + ($so_nguoi_phu_thuoc * 4400000);
                
                // Thu nhập chịu thuế
                $thu_nhap_chiu_thue = max(0, $luong_gross - $giam_tru_gia_canh);
                
                // Tính thuế TNCN (theo bậc thuế)
                $thue_tncn = $this->calculateThueTNCN($thu_nhap_chiu_thue);
                
                // Tính bảo hiểm (8% BHXH + 1.5% BHYT + 1% BHTN)
                $bhxh_nv = $luong_gross * 0.08;
                $bhyt_nv = $luong_gross * 0.015;
                $bhtn_nv = $luong_gross * 0.01;
                
                // Tổng khoản trừ
                $tong_khoan_tru = $bhxh_nv + $bhyt_nv + $bhtn_nv + $thue_tncn;
                
                // Lương thực nhận
                $luong_thuc_nhan = $luong_gross - $tong_khoan_tru;

                // Cập nhật bản ghi trong bảng luong
                $queryUpdate = "UPDATE luong 
                                SET so_ngay_cong = :so_ngay_cong,
                                    luong_co_ban = :luong_co_ban,
                                    phu_cap_chuc_vu = :phu_cap_chuc_vu,
                                    phu_cap_bang_cap = :phu_cap_bang_cap,
                                    phu_cap_khac = :phu_cap_khac,
                                    tien_thuong = :tien_thuong,
                                    cac_khoan_tru = :cac_khoan_tru,
                                    luong_thuc_nhan = :luong_thuc_nhan,
                                    thu_nhap_truoc_thue = :thu_nhap_truoc_thue,
                                    giam_tru_gia_canh = :giam_tru_gia_canh,
                                    thu_nhap_chiu_thue = :thu_nhap_chiu_thue,
                                    thue_tncn = :thue_tncn,
                                    bhxh_nv = :bhxh_nv,
                                    bhyt_nv = :bhyt_nv,
                                    bhtn_nv = :bhtn_nv
                                WHERE id_luong = :id_luong";
                $stmtUpdate = $this->conn->prepare($queryUpdate);
                $stmtUpdate->bindParam(':so_ngay_cong', $so_ngay_cong, PDO::PARAM_STR);
                $stmtUpdate->bindParam(':luong_co_ban', $luong_co_ban, PDO::PARAM_STR);
                $stmtUpdate->bindParam(':phu_cap_chuc_vu', $phu_cap_chuc_vu, PDO::PARAM_STR);
                $stmtUpdate->bindParam(':phu_cap_bang_cap', $phu_cap_bang_cap, PDO::PARAM_STR);
                $stmtUpdate->bindParam(':phu_cap_khac', $phu_cap_khac, PDO::PARAM_STR);
                $stmtUpdate->bindParam(':tien_thuong', $tien_thuong, PDO::PARAM_STR);
                $stmtUpdate->bindParam(':cac_khoan_tru', $tong_khoan_tru, PDO::PARAM_STR);
                $stmtUpdate->bindParam(':luong_thuc_nhan', $luong_thuc_nhan, PDO::PARAM_STR);
                $stmtUpdate->bindParam(':thu_nhap_truoc_thue', $luong_gross, PDO::PARAM_STR);
                $stmtUpdate->bindParam(':giam_tru_gia_canh', $giam_tru_gia_canh, PDO::PARAM_STR);
                $stmtUpdate->bindParam(':thu_nhap_chiu_thue', $thu_nhap_chiu_thue, PDO::PARAM_STR);
                $stmtUpdate->bindParam(':thue_tncn', $thue_tncn, PDO::PARAM_STR);
                $stmtUpdate->bindParam(':bhxh_nv', $bhxh_nv, PDO::PARAM_STR);
                $stmtUpdate->bindParam(':bhyt_nv', $bhyt_nv, PDO::PARAM_STR);
                $stmtUpdate->bindParam(':bhtn_nv', $bhtn_nv, PDO::PARAM_STR);
                $stmtUpdate->bindParam(':id_luong', $record['id_luong'], PDO::PARAM_INT);
                $stmtUpdate->execute();

                // Cập nhật lại dữ liệu trả về
                $record['so_ngay_cong'] = $so_ngay_cong;
                $record['luong_co_ban'] = $luong_co_ban;
                $record['phu_cap_chuc_vu'] = $phu_cap_chuc_vu;
                $record['phu_cap_bang_cap'] = $phu_cap_bang_cap;
                $record['phu_cap_khac'] = $phu_cap_khac;
                $record['tien_thuong'] = $tien_thuong;
                $record['cac_khoan_tru'] = $tong_khoan_tru;
                $record['luong_thuc_nhan'] = $luong_thuc_nhan;
                $record['thu_nhap_truoc_thue'] = $luong_gross;
                $record['giam_tru_gia_canh'] = $giam_tru_gia_canh;
                $record['thu_nhap_chiu_thue'] = $thu_nhap_chiu_thue;
                $record['thue_tncn'] = $thue_tncn;
                $record['bhxh_nv'] = $bhxh_nv;
                $record['bhyt_nv'] = $bhyt_nv;
                $record['bhtn_nv'] = $bhtn_nv;
                $record['so_nguoi_phu_thuoc'] = $so_nguoi_phu_thuoc;
            }

            return $luongData;
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy dữ liệu lương: " . $e->getMessage());
            return [];
        }
    }

    public function updateLuongStatus($id_luong, $trang_thai) {
        try {
            if (!in_array($trang_thai, ['Tạm tính', 'Đã thanh toán'])) {
                return ['success' => false, 'message' => 'Trạng thái không hợp lệ'];
            }

            $query = "UPDATE luong SET trang_thai = :trang_thai WHERE id_luong = :id_luong";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':trang_thai', $trang_thai, PDO::PARAM_STR);
            $stmt->bindParam(':id_luong', $id_luong, PDO::PARAM_INT);
            $success = $stmt->execute();

            if ($success && $stmt->rowCount() > 0) {
                return ['success' => true];
            } else {
                return ['success' => false, 'message' => 'Không tìm thấy bản ghi lương hoặc không có thay đổi'];
            }
        } catch (PDOException $e) {
            error_log("Lỗi khi cập nhật trạng thái lương: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()];
        }
    }

    public function updateLuongDetails($id_luong, $data) {
        try {
            if ($data['so_ngay_cong'] < 0 || $data['luong_co_ban'] < 0 || 
                $data['phu_cap_chuc_vu'] < 0 || $data['tien_thuong'] < 0 || 
                $data['cac_khoan_tru'] < 0) {
                return ['success' => false, 'message' => 'Giá trị không được âm'];
            }

            $luong_thuc_nhan = $data['luong_co_ban'] + $data['phu_cap_chuc_vu'] + 
                              $data['tien_thuong'] - $data['cac_khoan_tru'];

            $query = "UPDATE luong 
                      SET so_ngay_cong = :so_ngay_cong,
                          luong_co_ban = :luong_co_ban,
                          phu_cap_chuc_vu = :phu_cap_chuc_vu,
                          tien_thuong = :tien_thuong,
                          cac_khoan_tru = :cac_khoan_tru,
                          luong_thuc_nhan = :luong_thuc_nhan
                      WHERE id_luong = :id_luong";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':so_ngay_cong', $data['so_ngay_cong'], PDO::PARAM_STR);
            $stmt->bindParam(':luong_co_ban', $data['luong_co_ban'], PDO::PARAM_STR);
            $stmt->bindParam(':phu_cap_chuc_vu', $data['phu_cap_chuc_vu'], PDO::PARAM_STR);
            $stmt->bindParam(':tien_thuong', $data['tien_thuong'], PDO::PARAM_STR);
            $stmt->bindParam(':cac_khoan_tru', $data['cac_khoan_tru'], PDO::PARAM_STR);
            $stmt->bindParam(':luong_thuc_nhan', $luong_thuc_nhan, PDO::PARAM_STR);
            $stmt->bindParam(':id_luong', $id_luong, PDO::PARAM_INT);
            $success = $stmt->execute();

            if ($success && $stmt->rowCount() > 0) {
                return ['success' => true];
            } else {
                return ['success' => false, 'message' => 'Không tìm thấy bản ghi lương hoặc không có thay đổi'];
            }
        } catch (PDOException $e) {
            error_log("Lỗi khi cập nhật chi tiết lương: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()];
        }
    }

    // Hàm tính thuế TNCN theo bậc thuế
    private function calculateThueTNCN($thuNhapChiuThue) {
        if ($thuNhapChiuThue <= 0) {
            return 0;
        }
        
        $thue = 0;
        
        // Bậc 1: 0 - 5 triệu: 5%
        if ($thuNhapChiuThue > 5000000) {
            $thue += 5000000 * 0.05;
            $thuNhapChiuThue -= 5000000;
        } else {
            $thue += $thuNhapChiuThue * 0.05;
            return $thue;
        }
        
        // Bậc 2: 5 - 10 triệu: 10%
        if ($thuNhapChiuThue > 5000000) {
            $thue += 5000000 * 0.10;
            $thuNhapChiuThue -= 5000000;
        } else {
            $thue += $thuNhapChiuThue * 0.10;
            return $thue;
        }
        
        // Bậc 3: 10 - 18 triệu: 15%
        if ($thuNhapChiuThue > 8000000) {
            $thue += 8000000 * 0.15;
            $thuNhapChiuThue -= 8000000;
        } else {
            $thue += $thuNhapChiuThue * 0.15;
            return $thue;
        }
        
        // Bậc 4: 18 - 32 triệu: 20%
        if ($thuNhapChiuThue > 14000000) {
            $thue += 14000000 * 0.20;
            $thuNhapChiuThue -= 14000000;
        } else {
            $thue += $thuNhapChiuThue * 0.20;
            return $thue;
        }
        
        // Bậc 5: 32 - 52 triệu: 25%
        if ($thuNhapChiuThue > 20000000) {
            $thue += 20000000 * 0.25;
            $thuNhapChiuThue -= 20000000;
        } else {
            $thue += $thuNhapChiuThue * 0.25;
            return $thue;
        }
        
        // Bậc 6: 52 - 80 triệu: 30%
        if ($thuNhapChiuThue > 28000000) {
            $thue += 28000000 * 0.30;
            $thuNhapChiuThue -= 28000000;
        } else {
            $thue += $thuNhapChiuThue * 0.30;
            return $thue;
        }
        
        // Bậc 7: Trên 80 triệu: 35%
        $thue += $thuNhapChiuThue * 0.35;
        
        return $thue;
    }

    public function __destruct() {
        $this->conn = null;
    }
}
?>