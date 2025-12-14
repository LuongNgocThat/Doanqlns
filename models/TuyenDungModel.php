<?php
include_once(__DIR__ . '/../config/Database.php');

class TuyenDungModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllData($month, $year) {
        $data = [
            'campaigns' => $this->getAllDotTuyenDung($month, $year),
            'candidates' => $this->getAllUngVien($month, $year),
            'interviews' => $this->getAllLichHenUngVien($month, $year),
            'evaluations' => $this->getAllDanhGiaUngVien($month, $year),
            'costs' => $this->getAllChiPhiTuyenDung($month, $year),
            'allocations' => $this->getAllPhanBoUngVien($month, $year),
            'plans' => $this->getAllKeHoachTuyenDung($month, $year) // Thêm plans
        ];
        return $data;
    }

    public function getAllDotTuyenDung($month, $year) {
        $query = "SELECT dt.*, pb.ten_phong_ban, nv.ho_ten AS ten_can_bo 
                  FROM dot_tuyen_dung dt
                  LEFT JOIN phong_ban pb ON dt.id_phong_ban = pb.id_phong_ban
                  LEFT JOIN nhan_vien nv ON dt.id_can_bo_tuyen_dung = nv.id_nhan_vien";
        if ($month && $year) {
            $query .= " WHERE YEAR(dt.ngay_bat_dau) = :year AND MONTH(dt.ngay_bat_dau) = :month";
        }
        $stmt = $this->conn->prepare($query);
        if ($month && $year) {
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            $stmt->bindParam(':month', $month, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getAllUngVien($month, $year) {
        $query = "SELECT uv.*, dt.ten_dot, cv.ten_chuc_vu 
                  FROM ung_vien uv
                  LEFT JOIN dot_tuyen_dung dt ON uv.id_dot_tuyen_dung = dt.id_dot_tuyen_dung
                  LEFT JOIN chuc_vu cv ON uv.id_chuc_vu = cv.id_chuc_vu";
        if ($month && $year) {
            $query .= " WHERE YEAR(dt.ngay_bat_dau) = :year AND MONTH(dt.ngay_bat_dau) = :month";
        }
        $stmt = $this->conn->prepare($query);
        if ($month && $year) {
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            $stmt->bindParam(':month', $month, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getAllChiPhiTuyenDung($month, $year) {
        $query = "SELECT cp.*, dt.ten_dot 
                  FROM chi_phi_tuyen_dung cp
                  LEFT JOIN dot_tuyen_dung dt ON cp.id_dot_tuyen_dung = dt.id_dot_tuyen_dung
                  WHERE YEAR(cp.ngay_chi) = :year AND MONTH(cp.ngay_chi) = :month";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->bindParam(':month', $month, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getAllDanhGiaUngVien($month, $year) {
        $query = "SELECT dg.*, uv.ho_ten, cv.ten_chuc_vu
                  FROM danh_gia_ung_vien dg
                  LEFT JOIN ung_vien uv ON dg.id_ung_vien = uv.id_ung_vien
                  LEFT JOIN ke_hoach_tuyen_dung kh ON dg.id_ke_hoach = kh.id_ke_hoach
                  LEFT JOIN chuc_vu cv ON kh.id_chuc_vu = cv.id_chuc_vu
                  WHERE YEAR(dg.ngay_danh_gia) = :year AND MONTH(dg.ngay_danh_gia) = :month";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->bindParam(':month', $month, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

   public function getAllKeHoachTuyenDung($month = null, $year = null) {
    $query = "SELECT kh.*, cv.ten_chuc_vu 
              FROM ke_hoach_tuyen_dung kh
              LEFT JOIN chuc_vu cv ON kh.id_chuc_vu = cv.id_chuc_vu";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

    public function getAllLichHenUngVien($month, $year) {
        $query = "SELECT lh.*, uv.ho_ten, uv.email AS candidate_email 
                  FROM lich_hen_ung_vien lh
                  LEFT JOIN ung_vien uv ON lh.id_ung_vien = uv.id_ung_vien
                  WHERE YEAR(lh.ngay_hen) = :year AND MONTH(lh.ngay_hen) = :month";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->bindParam(':month', $month, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getAllPhanBoUngVien($month, $year) {
        $query = "SELECT pb.*, uv.ho_ten, uv.email, phong.ten_phong_ban, cv.ten_chuc_vu 
                  FROM phan_bo_ung_vien pb
                  LEFT JOIN ung_vien uv ON pb.id_ung_vien = uv.id_ung_vien
                  LEFT JOIN phong_ban phong ON pb.id_phong_ban = phong.id_phong_ban
                  LEFT JOIN chuc_vu cv ON pb.id_chuc_vu = cv.id_chuc_vu
                  WHERE YEAR(pb.ngay_phan_bo) = :year AND MONTH(pb.ngay_phan_bo) = :month";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->bindParam(':month', $month, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getOptions() {
        return [
            'positions' => $this->getAllChucVu(),
            'campaigns' => $this->getAllDotTuyenDung(null, null),
            'candidates' => $this->getAllUngVien(null, null),
            'plans' => $this->getAllKeHoachTuyenDung(null, null),
            'departments' => $this->getAllPhongBan(),
            'recruiters' => $this->getAllNhanVien()
        ];
    }

    private function getAllChucVu() {
        $query = "SELECT id_chuc_vu, ten_chuc_vu FROM chuc_vu";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    private function getAllPhongBan() {
        $query = "SELECT id_phong_ban, ten_phong_ban FROM phong_ban";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    private function getAllNhanVien() {
        $query = "SELECT id_nhan_vien, ho_ten FROM nhan_vien";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getRecordById($type, $id) {
        $queryMap = [
            'campaign' => "SELECT dt.*, pb.ten_phong_ban, nv.ho_ten AS ten_can_bo 
                          FROM dot_tuyen_dung dt
                          LEFT JOIN phong_ban pb ON dt.id_phong_ban = pb.id_phong_ban
                          LEFT JOIN nhan_vien nv ON dt.id_can_bo_tuyen_dung = nv.id_nhan_vien 
                          WHERE dt.id_dot_tuyen_dung = :id",
            'candidate' => "SELECT uv.*, dt.ten_dot, cv.ten_chuc_vu 
                           FROM ung_vien uv
                           LEFT JOIN dot_tuyen_dung dt ON uv.id_dot_tuyen_dung = dt.id_dot_tuyen_dung
                           LEFT JOIN chuc_vu cv ON uv.id_chuc_vu = cv.id_chuc_vu 
                           WHERE uv.id_ung_vien = :id",
            'interview' => "SELECT lh.*, uv.ho_ten, uv.email AS candidate_email 
                           FROM lich_hen_ung_vien lh
                           LEFT JOIN ung_vien uv ON lh.id_ung_vien = uv.id_ung_vien 
                           WHERE lh.id_lich_hen = :id",
            'evaluation' => "SELECT dg.*, uv.ho_ten, cv.ten_chuc_vu 
                            FROM danh_gia_ung_vien dg
                            LEFT JOIN ung_vien uv ON dg.id_ung_vien = uv.id_ung_vien
                            LEFT JOIN ke_hoach_tuyen_dung kh ON dg.id_ke_hoach = kh.id_ke_hoach
                            LEFT JOIN chuc_vu cv ON kh.id_chuc_vu = cv.id_chuc_vu 
                            WHERE dg.id_danh_gia = :id",
            'cost' => "SELECT cp.*, dt.ten_dot 
                       FROM chi_phi_tuyen_dung cp
                       LEFT JOIN dot_tuyen_dung dt ON cp.id_dot_tuyen_dung = dt.id_dot_tuyen_dung 
                       WHERE cp.id_chi_phi = :id",
            'allocation' => "SELECT pb.*, uv.ho_ten, uv.email, phong.ten_phong_ban, cv.ten_chuc_vu 
                            FROM phan_bo_ung_vien pb
                            LEFT JOIN ung_vien uv ON pb.id_ung_vien = uv.id_ung_vien
                            LEFT JOIN phong_ban phong ON pb.id_phong_ban = phong.id_phong_ban
                            LEFT JOIN chuc_vu cv ON pb.id_chuc_vu = cv.id_chuc_vu 
                            WHERE pb.id_phan_bo = :id",
            'plan' => "SELECT kh.*, cv.ten_chuc_vu 
                       FROM ke_hoach_tuyen_dung kh
                       LEFT JOIN chuc_vu cv ON kh.id_chuc_vu = cv.id_chuc_vu 
                       WHERE kh.id_ke_hoach = :id"
        ];

        if (!isset($queryMap[$type])) {
            return [];
        }

        $stmt = $this->conn->prepare($queryMap[$type]);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function addDotTuyenDung($data) {
        $query = "INSERT INTO dot_tuyen_dung (ma_dot, ten_dot, id_phong_ban, so_luong_tuyen, id_can_bo_tuyen_dung, ngay_bat_dau, ngay_ket_thuc, yeu_cau_vi_tri, trang_thai)
                  VALUES (:ma_dot, :ten_dot, :id_phong_ban, :so_luong_tuyen, :id_can_bo_tuyen_dung, :ngay_bat_dau, :ngay_ket_thuc, :yeu_cau_vi_tri, :trang_thai)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ma_dot', $data['ma_dot']);
        $stmt->bindParam(':ten_dot', $data['ten_dot']);
        $stmt->bindParam(':id_phong_ban', $data['id_phong_ban'], PDO::PARAM_INT);
        $stmt->bindParam(':so_luong_tuyen', $data['so_luong_tuyen'], PDO::PARAM_INT);
        $stmt->bindParam(':id_can_bo_tuyen_dung', $data['id_can_bo_tuyen_dung'], PDO::PARAM_INT);
        $stmt->bindParam(':ngay_bat_dau', $data['ngay_bat_dau']);
        $stmt->bindParam(':ngay_ket_thuc', $data['ngay_ket_thuc']);
        $stmt->bindParam(':yeu_cau_vi_tri', $data['yeu_cau_vi_tri']);
        $stmt->bindParam(':trang_thai', $data['trang_thai']);
        return $stmt->execute();
    }

    public function addUngVien($data) {
        $query = "INSERT INTO ung_vien (ho_ten, email, so_dien_thoai, id_dot_tuyen_dung, id_chuc_vu, nguon_ung_tuyen, ho_so, trang_thai)
                  VALUES (:ho_ten, :email, :so_dien_thoai, :id_dot_tuyen_dung, :id_chuc_vu, :nguon_ung_tuyen, :ho_so, :trang_thai)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ho_ten', $data['ho_ten']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':so_dien_thoai', $data['so_dien_thoai']);
        $stmt->bindParam(':id_dot_tuyen_dung', $data['id_dot_tuyen_dung'], PDO::PARAM_INT);
        $stmt->bindParam(':id_chuc_vu', $data['id_chuc_vu'], PDO::PARAM_INT);
        $stmt->bindParam(':nguon_ung_tuyen', $data['nguon_ung_tuyen']);
        $stmt->bindParam(':ho_so', $data['ho_so']);
        $stmt->bindParam(':trang_thai', $data['trang_thai']);
        return $stmt->execute();
    }

    public function addLichHenUngVien($data) {
        $query = "INSERT INTO lich_hen_ung_vien (id_ung_vien, ngay_hen, gio_hen, dia_diem, ghi_chu, trang_thai)
                  VALUES (:id_ung_vien, :ngay_hen, :gio_hen, :dia_diem, :ghi_chu, :trang_thai)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_ung_vien', $data['id_ung_vien'], PDO::PARAM_INT);
        $stmt->bindParam(':ngay_hen', $data['ngay_hen']);
        $stmt->bindParam(':gio_hen', $data['gio_hen']);
        $stmt->bindParam(':dia_diem', $data['dia_diem']);
        $stmt->bindParam(':ghi_chu', $data['ghi_chu']);
        $trang_thai = 'Chờ lên lịch';
        $stmt->bindParam(':trang_thai', $trang_thai);
        return $stmt->execute();
    }

   public function addDanhGiaUngVien($data) {
    try {
        $this->conn->beginTransaction();

        // Thêm đánh giá ứng viên
        $query = "INSERT INTO danh_gia_ung_vien (id_ung_vien, id_ke_hoach, vong_thi, diem, nhan_xet, ngay_danh_gia)
                  VALUES (:id_ung_vien, :id_ke_hoach, :vong_thi, :diem, :nhan_xet, :ngay_danh_gia)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_ung_vien', $data['id_ung_vien'], PDO::PARAM_INT);
        $stmt->bindParam(':id_ke_hoach', $data['id_ke_hoach'], PDO::PARAM_INT);
        $stmt->bindParam(':vong_thi', $data['vong_thi']);
        $stmt->bindParam(':diem', $data['diem'], PDO::PARAM_STR); // FLOAT được xử lý như chuỗi
        $stmt->bindParam(':nhan_xet', $data['nhan_xet']);
        $stmt->bindParam(':ngay_danh_gia', $data['ngay_danh_gia']);
        $success = $stmt->execute();

        if (!$success) {
            throw new Exception("Không thể thêm đánh giá ứng viên");
        }

        // Kiểm tra điểm so với điểm chuẩn
        $query = "SELECT diem_chuan 
                  FROM ke_hoach_tuyen_dung 
                  WHERE id_ke_hoach = :id_ke_hoach";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_ke_hoach', $data['id_ke_hoach'], PDO::PARAM_INT);
        $stmt->execute();
        $plan = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($plan && $data['diem'] >= $plan['diem_chuan']) {
            // Lấy thông tin ứng viên và đợt tuyển dụng
            $query = "SELECT uv.id_chuc_vu, dt.id_phong_ban 
                      FROM ung_vien uv 
                      LEFT JOIN dot_tuyen_dung dt ON uv.id_dot_tuyen_dung = dt.id_dot_tuyen_dung 
                      WHERE uv.id_ung_vien = :id_ung_vien";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_ung_vien', $data['id_ung_vien'], PDO::PARAM_INT);
            $stmt->execute();
            $candidate = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($candidate) {
                // Kiểm tra xem ứng viên đã được phân bổ chưa
                $query = "SELECT id_phan_bo 
                          FROM phan_bo_ung_vien 
                          WHERE id_ung_vien = :id_ung_vien";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id_ung_vien', $data['id_ung_vien'], PDO::PARAM_INT);
                $stmt->execute();
                if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                    // Ứng viên đã được phân bổ, bỏ qua
                    $this->conn->commit();
                    return true;
                }

                // Thêm vào phan_bo_ung_vien
                $query = "INSERT INTO phan_bo_ung_vien (id_ung_vien, id_phong_ban, id_chuc_vu, trang_thai, ngay_phan_bo, hop_dong_thu_viec)
                          VALUES (:id_ung_vien, :id_phong_ban, :id_chuc_vu, :trang_thai, :ngay_phan_bo, :hop_dong_thu_viec)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id_ung_vien', $data['id_ung_vien'], PDO::PARAM_INT);
                $stmt->bindParam(':id_phong_ban', $candidate['id_phong_ban'], PDO::PARAM_INT);
                $stmt->bindParam(':id_chuc_vu', $candidate['id_chuc_vu'], PDO::PARAM_INT);
                $trang_thai = 'Chờ phân bổ';
                $stmt->bindParam(':trang_thai', $trang_thai);
                $ngay_phan_bo = date('Y-m-d');
                $stmt->bindParam(':ngay_phan_bo', $ngay_phan_bo);
                $hop_dong_thu_viec = ''; // Có thể để trống hoặc thêm logic lấy hợp đồng
                $stmt->bindParam(':hop_dong_thu_viec', $hop_dong_thu_viec);
                $success = $stmt->execute();

                if (!$success) {
                    throw new Exception("Không thể thêm bản ghi vào phan_bo_ung_vien");
                }
            }
        }

        $this->conn->commit();
        return true;
    } catch (Exception $e) {
        $this->conn->rollBack();
        error_log("Lỗi trong addDanhGiaUngVien: " . $e->getMessage(), 3, __DIR__ . '/../logs/php_errors.log');
        return false;
    }
}

    public function addChiPhiTuyenDung($data) {
        $query = "INSERT INTO chi_phi_tuyen_dung (id_dot_tuyen_dung, noi_dung_chi_phi, so_tien, ngay_chi)
                  VALUES (:id_dot_tuyen_dung, :noi_dung_chi_phi, :so_tien, :ngay_chi)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_dot_tuyen_dung', $data['id_dot_tuyen_dung'], PDO::PARAM_INT);
        $stmt->bindParam(':noi_dung_chi_phi', $data['noi_dung_chi_phi']);
        $stmt->bindParam(':so_tien', $data['so_tien']);
        $stmt->bindParam(':ngay_chi', $data['ngay_chi']);
        return $stmt->execute();
    }

    public function addPhanBoUngVien($data) {
        try {
            $this->conn->beginTransaction();

            $query = "INSERT INTO phan_bo_ung_vien (id_ung_vien, id_phong_ban, id_chuc_vu, trang_thai, ngay_phan_bo, hop_dong_thu_viec)
                      VALUES (:id_ung_vien, :id_phong_ban, :id_chuc_vu, :trang_thai, :ngay_phan_bo, :hop_dong_thu_viec)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_ung_vien', $data['id_ung_vien'], PDO::PARAM_INT);
            $stmt->bindParam(':id_phong_ban', $data['id_phong_ban'], PDO::PARAM_INT);
            $stmt->bindParam(':id_chuc_vu', $data['id_chuc_vu'], PDO::PARAM_INT);
            $stmt->bindParam(':trang_thai', $data['trang_thai']);
            $stmt->bindParam(':ngay_phan_bo', $data['ngay_phan_bo']);
            $stmt->bindParam(':hop_dong_thu_viec', $data['hop_dong_thu_viec']);
            $success = $stmt->execute();

            if (!$success) {
                throw new Exception("Không thể thêm bản ghi vào phan_bo_ung_vien");
            }

            if ($data['trang_thai'] === 'Đã duyệt') {
                $query = "SELECT ho_ten, email, so_dien_thoai, id_chuc_vu FROM ung_vien WHERE id_ung_vien = :id_ung_vien";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id_ung_vien', $data['id_ung_vien'], PDO::PARAM_INT);
                $stmt->execute();
                $candidate = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$candidate) {
                    throw new Exception("Không tìm thấy ứng viên với id_ung_vien: " . $data['id_ung_vien']);
                }

                $query = "SELECT id_nhan_vien FROM nhan_vien WHERE email = :email";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':email', $candidate['email']);
                $stmt->execute();
                if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                    throw new Exception("Ứng viên với email " . $candidate['email'] . " đã tồn tại trong bảng nhan_vien");
                }

                $query = "INSERT INTO nhan_vien (ho_ten, email, so_dien_thoai, id_phong_ban, id_chuc_vu, ngay_vao_lam, trang_thai)
                          VALUES (:ho_ten, :email, :so_dien_thoai, :id_phong_ban, :id_chuc_vu, :ngay_vao_lam, :trang_thai)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':ho_ten', $candidate['ho_ten']);
                $stmt->bindParam(':email', $candidate['email']);
                $stmt->bindParam(':so_dien_thoai', $candidate['so_dien_thoai']);
                $stmt->bindParam(':id_phong_ban', $data['id_phong_ban'], PDO::PARAM_INT);
                $stmt->bindParam(':id_chuc_vu', $data['id_chuc_vu'], PDO::PARAM_INT);
                $stmt->bindParam(':ngay_vao_lam', $data['ngay_phan_bo']);
                $trang_thai = 'Đang làm việc';
                $stmt->bindParam(':trang_thai', $trang_thai);
                $success = $stmt->execute();

                if (!$success) {
                    throw new Exception("Không thể thêm bản ghi vào nhan_vien");
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Lỗi trong addPhanBoUngVien: " . $e->getMessage(), 3, __DIR__ . '/../logs/php_errors.log');
            return false;
        }
    }

    public function addKeHoachTuyenDung($data) {
        try {
            $query = "INSERT INTO ke_hoach_tuyen_dung (id_chuc_vu, so_vong_thi, ten_vong_thi, noi_dung_thi, diem_chuan)
                      VALUES (:id_chuc_vu, :so_vong_thi, :ten_vong_thi, :noi_dung_thi, :diem_chuan)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_chuc_vu', $data['id_chuc_vu'], PDO::PARAM_INT);
            $stmt->bindParam(':so_vong_thi', $data['so_vong_thi'], PDO::PARAM_INT);
            $stmt->bindParam(':ten_vong_thi', $data['ten_vong_thi']);
            $stmt->bindParam(':noi_dung_thi', $data['noi_dung_thi']);
            $stmt->bindParam(':diem_chuan', $data['diem_chuan'], PDO::PARAM_INT);
            $success = $stmt->execute();
            if (!$success) {
                throw new Exception("Không thể thêm kế hoạch tuyển dụng");
            }
            return true;
        } catch (Exception $e) {
            error_log("Lỗi trong addKeHoachTuyenDung: " . $e->getMessage(), 3, __DIR__ . '/../logs/php_errors.log');
            return false;
        }
    }

    public function updateKeHoachTuyenDung($id, $data) {
        try {
            $query = "UPDATE ke_hoach_tuyen_dung 
                      SET id_chuc_vu = :id_chuc_vu, so_vong_thi = :so_vong_thi, ten_vong_thi = :ten_vong_thi, 
                          noi_dung_thi = :noi_dung_thi, diem_chuan = :diem_chuan
                      WHERE id_ke_hoach = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_chuc_vu', $data['id_chuc_vu'], PDO::PARAM_INT);
            $stmt->bindParam(':so_vong_thi', $data['so_vong_thi'], PDO::PARAM_INT);
            $stmt->bindParam(':ten_vong_thi', $data['ten_vong_thi']);
            $stmt->bindParam(':noi_dung_thi', $data['noi_dung_thi']);
            $stmt->bindParam(':diem_chuan', $data['diem_chuan'], PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $success = $stmt->execute();
            if (!$success) {
                throw new Exception("Không thể cập nhật kế hoạch tuyển dụng");
            }
            return true;
        } catch (Exception $e) {
            error_log("Lỗi trong updateKeHoachTuyenDung: " . $e->getMessage(), 3, __DIR__ . '/../logs/php_errors.log');
            return false;
        }
    }

    public function deleteKeHoachTuyenDung($id) {
        try {
            $this->conn->beginTransaction();

            // Xóa các đánh giá liên quan
            $query = "DELETE FROM danh_gia_ung_vien WHERE id_ke_hoach = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Xóa kế hoạch tuyển dụng
            $query = "DELETE FROM ke_hoach_tuyen_dung WHERE id_ke_hoach = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $success = $stmt->execute();

            if (!$success) {
                throw new Exception("Không thể xóa kế hoạch tuyển dụng với id: $id");
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Lỗi trong deleteKeHoachTuyenDung: " . $e->getMessage(), 3, __DIR__ . '/../logs/php_errors.log');
            return false;
        }
    }

    public function updateDotTuyenDung($id, $data) {
        $query = "UPDATE dot_tuyen_dung 
                  SET ma_dot = :ma_dot, ten_dot = :ten_dot, id_phong_ban = :id_phong_ban, 
                      so_luong_tuyen = :so_luong_tuyen, id_can_bo_tuyen_dung = :id_can_bo_tuyen_dung, 
                      ngay_bat_dau = :ngay_bat_dau, ngay_ket_thuc = :ngay_ket_thuc, 
                      yeu_cau_vi_tri = :yeu_cau_vi_tri, trang_thai = :trang_thai
                  WHERE id_dot_tuyen_dung = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ma_dot', $data['ma_dot']);
        $stmt->bindParam(':ten_dot', $data['ten_dot']);
        $stmt->bindParam(':id_phong_ban', $data['id_phong_ban'], PDO::PARAM_INT);
        $stmt->bindParam(':so_luong_tuyen', $data['so_luong_tuyen'], PDO::PARAM_INT);
        $stmt->bindParam(':id_can_bo_tuyen_dung', $data['id_can_bo_tuyen_dung'], PDO::PARAM_INT);
        $stmt->bindParam(':ngay_bat_dau', $data['ngay_bat_dau']);
        $stmt->bindParam(':ngay_ket_thuc', $data['ngay_ket_thuc']);
        $stmt->bindParam(':yeu_cau_vi_tri', $data['yeu_cau_vi_tri']);
        $stmt->bindParam(':trang_thai', $data['trang_thai']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateUngVien($id, $data) {
        $query = "UPDATE ung_vien 
                  SET ho_ten = :ho_ten, email = :email, so_dien_thoai = :so_dien_thoai, 
                      id_dot_tuyen_dung = :id_dot_tuyen_dung, id_chuc_vu = :id_chuc_vu, 
                      nguon_ung_tuyen = :nguon_ung_tuyen, ho_so = :ho_so, trang_thai = :trang_thai
                  WHERE id_ung_vien = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ho_ten', $data['ho_ten']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':so_dien_thoai', $data['so_dien_thoai']);
        $stmt->bindParam(':id_dot_tuyen_dung', $data['id_dot_tuyen_dung'], PDO::PARAM_INT);
        $stmt->bindParam(':id_chuc_vu', $data['id_chuc_vu'], PDO::PARAM_INT);
        $stmt->bindParam(':nguon_ung_tuyen', $data['nguon_ung_tuyen']);
        $stmt->bindParam(':ho_so', $data['ho_so']);
        $stmt->bindParam(':trang_thai', $data['trang_thai']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateLichHenUngVien($id, $data) {
        $query = "UPDATE lich_hen_ung_vien 
                  SET id_ung_vien = :id_ung_vien, ngay_hen = :ngay_hen, gio_hen = :gio_hen, 
                      dia_diem = :dia_diem, ghi_chu = :ghi_chu
                  WHERE id_lich_hen = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_ung_vien', $data['id_ung_vien'], PDO::PARAM_INT);
        $stmt->bindParam(':ngay_hen', $data['ngay_hen']);
        $stmt->bindParam(':gio_hen', $data['gio_hen']);
        $stmt->bindParam(':dia_diem', $data['dia_diem']);
        $stmt->bindParam(':ghi_chu', $data['ghi_chu']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

 public function updateDanhGiaUngVien($id, $data) {
    try {
        $this->conn->beginTransaction();

        // Cập nhật đánh giá
        $query = "UPDATE danh_gia_ung_vien 
                  SET id_ung_vien = :id_ung_vien, id_ke_hoach = :id_ke_hoach, vong_thi = :vong_thi, 
                      diem = :diem, nhan_xet = :nhan_xet, ngay_danh_gia = :ngay_danh_gia
                  WHERE id_danh_gia = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_ung_vien', $data['id_ung_vien'], PDO::PARAM_INT);
        $stmt->bindParam(':id_ke_hoach', $data['id_ke_hoach'], PDO::PARAM_INT);
        $stmt->bindParam(':vong_thi', $data['vong_thi']);
        $stmt->bindParam(':diem', $data['diem'], PDO::PARAM_STR); // FLOAT
        $stmt->bindParam(':nhan_xet', $data['nhan_xet']);
        $stmt->bindParam(':ngay_danh_gia', $data['ngay_danh_gia']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $success = $stmt->execute();

        if (!$success) {
            throw new Exception("Không thể cập nhật đánh giá ứng viên");
        }

        // Kiểm tra điểm so với điểm chuẩn
        $query = "SELECT diem_chuan 
                  FROM ke_hoach_tuyen_dung 
                  WHERE id_ke_hoach = :id_ke_hoach";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_ke_hoach', $data['id_ke_hoach'], PDO::PARAM_INT);
        $stmt->execute();
        $plan = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($plan && $data['diem'] >= $plan['diem_chuan']) {
            // Lấy thông tin ứng viên và đợt tuyển dụng
            $query = "SELECT uv.id_chuc_vu, dt.id_phong_ban 
                      FROM ung_vien uv 
                      LEFT JOIN dot_tuyen_dung dt ON uv.id_dot_tuyen_dung = dt.id_dot_tuyen_dung 
                      WHERE uv.id_ung_vien = :id_ung_vien";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_ung_vien', $data['id_ung_vien'], PDO::PARAM_INT);
            $stmt->execute();
            $candidate = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($candidate) {
                // Kiểm tra xem ứng viên đã được phân bổ chưa
                $query = "SELECT id_phan_bo 
                          FROM phan_bo_ung_vien 
                          WHERE id_ung_vien = :id_ung_vien";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id_ung_vien', $data['id_ung_vien'], PDO::PARAM_INT);
                $stmt->execute();
                if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                    // Ứng viên đã được phân bổ, bỏ qua
                    $this->conn->commit();
                    return true;
                }

                // Thêm vào phan_bo_ung_vien
                $query = "INSERT INTO phan_bo_ung_vien (id_ung_vien, id_phong_ban, id_chuc_vu, trang_thai, ngay_phan_bo, hop_dong_thu_viec)
                          VALUES (:id_ung_vien, :id_phong_ban, :id_chuc_vu, :trang_thai, :ngay_phan_bo, :hop_dong_thu_viec)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id_ung_vien', $data['id_ung_vien'], PDO::PARAM_INT);
                $stmt->bindParam(':id_phong_ban', $candidate['id_phong_ban'], PDO::PARAM_INT);
                $stmt->bindParam(':id_chuc_vu', $candidate['id_chuc_vu'], PDO::PARAM_INT);
                $trang_thai = 'Chờ phân bổ';
                $stmt->bindParam(':trang_thai', $trang_thai);
                $ngay_phan_bo = date('Y-m-d');
                $stmt->bindParam(':ngay_phan_bo', $ngay_phan_bo);
                $hop_dong_thu_viec = '';
                $stmt->bindParam(':hop_dong_thu_viec', $hop_dong_thu_viec);
                $success = $stmt->execute();

                if (!$success) {
                    throw new Exception("Không thể thêm bản ghi vào phan_bo_ung_vien");
                }
            }
        }

        $this->conn->commit();
        return true;
    } catch (Exception $e) {
        $this->conn->rollBack();
        error_log("Lỗi trong updateDanhGiaUngVien: " . $e->getMessage(), 3, __DIR__ . '/../logs/php_errors.log');
        return false;
    }
}
    public function updateChiPhiTuyenDung($id, $data) {
        $query = "UPDATE chi_phi_tuyen_dung 
                  SET id_dot_tuyen_dung = :id_dot_tuyen_dung, noi_dung_chi_phi = :noi_dung_chi_phi, 
                      so_tien = :so_tien, ngay_chi = :ngay_chi
                  WHERE id_chi_phi = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_dot_tuyen_dung', $data['id_dot_tuyen_dung'], PDO::PARAM_INT);
        $stmt->bindParam(':noi_dung_chi_phi', $data['noi_dung_chi_phi']);
        $stmt->bindParam(':so_tien', $data['so_tien']);
        $stmt->bindParam(':ngay_chi', $data['ngay_chi']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updatePhanBoUngVien($id, $data) {
        $query = "UPDATE phan_bo_ung_vien 
                  SET id_ung_vien = :id_ung_vien, id_phong_ban = :id_phong_ban, id_chuc_vu = :id_chuc_vu, 
                      trang_thai = :trang_thai, ngay_phan_bo = :ngay_phan_bo, 
                      hop_dong_thu_viec = :hop_dong_thu_viec
                  WHERE id_phan_bo = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_ung_vien', $data['id_ung_vien'], PDO::PARAM_INT);
        $stmt->bindParam(':id_phong_ban', $data['id_phong_ban'], PDO::PARAM_INT);
        $stmt->bindParam(':id_chuc_vu', $data['id_chuc_vu'], PDO::PARAM_INT);
        $stmt->bindParam(':trang_thai', $data['trang_thai']);
        $stmt->bindParam(':ngay_phan_bo', $data['ngay_phan_bo']);
        $stmt->bindParam(':hop_dong_thu_viec', $data['hop_dong_thu_viec']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateAllocationStatus($id_phan_bo, $trang_thai) {
        try {
            $this->conn->beginTransaction();

            $query = "UPDATE phan_bo_ung_vien SET trang_thai = :trang_thai WHERE id_phan_bo = :id_phan_bo";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':trang_thai', $trang_thai);
            $stmt->bindParam(':id_phan_bo', $id_phan_bo, PDO::PARAM_INT);
            $success = $stmt->execute();

            if (!$success) {
                throw new Exception("Không thể cập nhật trạng thái phân bổ cho id_phan_bo: $id_phan_bo");
            }

            if ($trang_thai === 'Đã duyệt') {
                $query = "SELECT pb.id_ung_vien, pb.id_phong_ban, pb.id_chuc_vu, 
                                 uv.ho_ten, uv.email, uv.so_dien_thoai
                          FROM phan_bo_ung_vien pb
                          LEFT JOIN ung_vien uv ON pb.id_ung_vien = uv.id_ung_vien
                          WHERE pb.id_phan_bo = :id_phan_bo";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id_phan_bo', $id_phan_bo, PDO::PARAM_INT);
                $stmt->execute();
                $allocation = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$allocation) {
                    throw new Exception("Không tìm thấy thông tin phân bổ hoặc ứng viên cho id_phan_bo: $id_phan_bo");
                }

                $query = "SELECT id_nhan_vien FROM nhan_vien WHERE email = :email";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':email', $allocation['email']);
                $stmt->execute();
                if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                    throw new Exception("Ứng viên với email " . $allocation['email'] . " đã tồn tại trong bảng nhan_vien");
                }

                $query = "INSERT INTO nhan_vien (ho_ten, email, so_dien_thoai, id_phong_ban, id_chuc_vu, ngay_vao_lam, trang_thai)
                          VALUES (:ho_ten, :email, :so_dien_thoai, :id_phong_ban, :id_chuc_vu, :ngay_vao_lam, :trang_thai)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':ho_ten', $allocation['ho_ten']);
                $stmt->bindParam(':email', $allocation['email']);
                $stmt->bindParam(':so_dien_thoai', $allocation['so_dien_thoai']);
                $stmt->bindParam(':id_phong_ban', $allocation['id_phong_ban'], PDO::PARAM_INT);
                $stmt->bindParam(':id_chuc_vu', $allocation['id_chuc_vu'], PDO::PARAM_INT);
                $ngay_vao_lam = date('Y-m-d');
                $stmt->bindParam(':ngay_vao_lam', $ngay_vao_lam);
                $trang_thai_nv = 'Đang làm việc';
                $stmt->bindParam(':trang_thai', $trang_thai_nv);
                $success = $stmt->execute();

                if (!$success) {
                    throw new Exception("Không thể thêm bản ghi vào nhan_vien cho ứng viên: " . $allocation['ho_ten']);
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Lỗi trong updateAllocationStatus: " . $e->getMessage(), 3, __DIR__ . '/../logs/php_errors.log');
            return false;
        }
    }

    public function updateInterviewStatus($id_lich_hen, $trang_thai) {
        try {
            $this->conn->beginTransaction();

            $validStatuses = ['Đã duyệt', 'Từ chối'];
            if (!in_array($trang_thai, $validStatuses)) {
                throw new Exception("Trạng thái không hợp lệ: $trang_thai");
            }

            $query = "UPDATE lich_hen_ung_vien SET trang_thai = :trang_thai WHERE id_lich_hen = :id_lich_hen";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':trang_thai', $trang_thai);
            $stmt->bindParam(':id_lich_hen', $id_lich_hen, PDO::PARAM_INT);
            $success = $stmt->execute();

            if (!$success) {
                throw new Exception("Không thể cập nhật trạng thái lịch hẹn cho id_lich_hen: $id_lich_hen");
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Lỗi trong updateInterviewStatus: " . $e->getMessage(), 3, __DIR__ . '/../logs/php_errors.log');
            return false;
        }
    }

    public function deleteRecord($type, $id) {
        try {
            $this->conn->beginTransaction();

            $tableMap = [
                'campaign' => ['table' => 'dot_tuyen_dung', 'id_field' => 'id_dot_tuyen_dung'],
                'candidate' => ['table' => 'ung_vien', 'id_field' => 'id_ung_vien'],
                'interview' => ['table' => 'lich_hen_ung_vien', 'id_field' => 'id_lich_hen'],
                'evaluation' => ['table' => 'danh_gia_ung_vien', 'id_field' => 'id_danh_gia'],
                'cost' => ['table' => 'chi_phi_tuyen_dung', 'id_field' => 'id_chi_phi'],
                'allocation' => ['table' => 'phan_bo_ung_vien', 'id_field' => 'id_phan_bo'],
                'plan' => ['table' => 'ke_hoach_tuyen_dung', 'id_field' => 'id_ke_hoach']
            ];

            if (!isset($tableMap[$type])) {
                throw new Exception("Loại dữ liệu không hợp lệ: $type");
            }

            $table = $tableMap[$type]['table'];
            $idField = $tableMap[$type]['id_field'];

            // Xóa các bản ghi liên quan trước
            switch ($type) {
                case 'campaign':
                    $query = "DELETE FROM chi_phi_tuyen_dung WHERE id_dot_tuyen_dung = :id";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();

                    $query = "SELECT id_ung_vien FROM ung_vien WHERE id_dot_tuyen_dung = :id";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                    $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($candidates as $candidate) {
                        $query = "DELETE FROM lich_hen_ung_vien WHERE id_ung_vien = :id";
                        $stmt = $this->conn->prepare($query);
                        $stmt->bindParam(':id', $candidate['id_ung_vien'], PDO::PARAM_INT);
                        $stmt->execute();

                        $query = "DELETE FROM danh_gia_ung_vien WHERE id_ung_vien = :id";
                        $stmt = $this->conn->prepare($query);
                        $stmt->bindParam(':id', $candidate['id_ung_vien'], PDO::PARAM_INT);
                        $stmt->execute();

                        $query = "DELETE FROM phan_bo_ung_vien WHERE id_ung_vien = :id";
                        $stmt = $this->conn->prepare($query);
                        $stmt->bindParam(':id', $candidate['id_ung_vien'], PDO::PARAM_INT);
                        $stmt->execute();

                        $query = "DELETE FROM ung_vien WHERE id_ung_vien = :id";
                        $stmt = $this->conn->prepare($query);
                        $stmt->bindParam(':id', $candidate['id_ung_vien'], PDO::PARAM_INT);
                        $stmt->execute();
                    }
                    break;

                case 'candidate':
                    $query = "DELETE FROM lich_hen_ung_vien WHERE id_ung_vien = :id";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();

                    $query = "DELETE FROM danh_gia_ung_vien WHERE id_ung_vien = :id";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();

                    $query = "DELETE FROM phan_bo_ung_vien WHERE id_ung_vien = :id";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                    break;

                case 'plan':
                    $query = "DELETE FROM danh_gia_ung_vien WHERE id_ke_hoach = :id";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                    break;
            }

            $query = "DELETE FROM $table WHERE $idField = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $success = $stmt->execute();

            if ($success) {
                $this->conn->commit();
                return true;
            } else {
                throw new Exception("Không thể xóa bản ghi từ bảng $table");
            }
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Lỗi trong deleteRecord: " . $e->getMessage(), 3, __DIR__ . '/../logs/php_errors.log');
            return false;
        }
    }

    public function __destruct() {
        $this->conn = null;
    }

    // Add new methods for chatbot
    public function get_active_campaigns() {
        $this->db->select('dt.*, cv.ten_chuc_vu, pb.ten_phong_ban')
                 ->from('dot_tuyen_dung dt')
                 ->join('chuc_vu cv', 'cv.id_chuc_vu = dt.id_chuc_vu', 'left')
                 ->join('phong_ban pb', 'pb.id_phong_ban = dt.id_phong_ban', 'left')
                 ->where('dt.trang_thai', 'Đang tiến hành')
                 ->where('dt.ngay_ket_thuc >=', date('Y-m-d'));
        return $this->db->get()->result_array();
    }

    public function add_candidate_from_chatbot($data) {
        $this->db->insert('ung_vien', [
            'ho_ten' => $data['ho_ten'],
            'email' => $data['email'],
            'so_dien_thoai' => $data['so_dien_thoai'],
            'id_dot_tuyen_dung' => $data['id_dot_tuyen_dung'],
            'id_chuc_vu' => $data['id_chuc_vu'],
            'nguon_ung_tuyen' => 'Chatbot',
            'ho_so' => $data['ho_so'] ?? null,
            'trang_thai' => 'Mới nộp'
        ]);
        return $this->db->insert_id();
    }

    public function schedule_interview_from_chatbot($data) {
        $this->db->insert('lich_hen', [
            'id_ung_vien' => $data['id_ung_vien'],
            'ngay_hen' => $data['ngay_hen'],
            'gio_hen' => $data['gio_hen'],
            'dia_diem' => $data['dia_diem'],
            'ghi_chu' => $data['ghi_chu'] ?? null,
            'trang_thai' => 'Chờ phỏng vấn'
        ]);
        return $this->db->insert_id();
    }

    public function get_candidate_by_email($email) {
        return $this->db->get_where('ung_vien', ['email' => $email])->row_array();
    }

    public function get_candidate_by_phone($phone) {
        return $this->db->get_where('ung_vien', ['so_dien_thoai' => $phone])->row_array();
    }

    public function get_available_interview_slots($id_dot_tuyen_dung, $date) {
        // Get all booked slots for the date
        $booked_slots = $this->db->select('gio_hen')
                                ->from('lich_hen')
                                ->where('ngay_hen', $date)
                                ->get()
                                ->result_array();
        
        // Convert to array of times
        $booked_times = array_column($booked_slots, 'gio_hen');
        
        // Define available slots (9:00 AM to 5:00 PM, 1-hour intervals)
        $available_slots = [];
        $start = strtotime('09:00:00');
        $end = strtotime('17:00:00');
        
        for ($time = $start; $time <= $end; $time += 3600) {
            $slot = date('H:i:s', $time);
            if (!in_array($slot, $booked_times)) {
                $available_slots[] = $slot;
            }
        }
        
        return $available_slots;
    }
}
?>