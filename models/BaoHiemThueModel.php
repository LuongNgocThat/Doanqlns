<?php
include_once(__DIR__ . '/../config/Database.php');

class BaoHiemThueModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllBaoHiemThue() {
        $query = "SELECT 
                    bht.*, 
                    nv.ho_ten, nv.so_nguoi_phu_thuoc, nv.phu_cap_chuc_vu, nv.phu_cap_bang_cap, nv.phu_cap_khac
                  FROM bao_hiem_thue_tncn bht 
                  JOIN nhan_vien nv ON bht.id_nhan_vien = nv.id_nhan_vien";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $users ?: [];
    }

    public function deleteBaoHiemThue($id) {
        $query = "DELETE FROM BAO_HIEM_THUE_TNCN WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateBaoHiemThue($id, $data) {
        $query = "UPDATE bao_hiem_thue_tncn 
                  SET thang = :thang, 
                      bhxh_nv = :bhxh_nv, 
                      bhyt_nv = :bhyt_nv, 
                      bhtn_nv = :bhtn_nv, 
                      bhxh_cty = :bhxh_cty,
                      bhyt_cty = :bhyt_cty,
                      bhtn_cty = :bhtn_cty,
                      thue_tncn = :thue_tncn,
                      thu_nhap_truoc_thue = :thu_nhap_truoc_thue,
                      giam_tru_gia_canh = :giam_tru_gia_canh,
                      thu_nhap_chiu_thue = :thu_nhap_chiu_thue,
                      tong_khoan_tru = :tong_khoan_tru
                  WHERE id = :id";
    
        $stmt = $this->conn->prepare($query);
    
        // Tính tổng khoản trừ (chỉ tính phần nhân viên)
        $tong_khoan_tru = floatval($data['bhxh_nv']) + floatval($data['bhyt_nv']) + floatval($data['bhtn_nv']) + floatval($data['thue_tncn']);
    
        $stmt->bindParam(":thang", $data['thang']);
        $stmt->bindParam(":bhxh_nv", $data['bhxh_nv']);
        $stmt->bindParam(":bhyt_nv", $data['bhyt_nv']);
        $stmt->bindParam(":bhtn_nv", $data['bhtn_nv']);
        $stmt->bindParam(":bhxh_cty", $data['bhxh_cty']);
        $stmt->bindParam(":bhyt_cty", $data['bhyt_cty']);
        $stmt->bindParam(":bhtn_cty", $data['bhtn_cty']);
        $stmt->bindParam(":thue_tncn", $data['thue_tncn']);
        $stmt->bindParam(":thu_nhap_truoc_thue", $data['thu_nhap_truoc_thue']);
        $stmt->bindParam(":giam_tru_gia_canh", $data['giam_tru_gia_canh']);
        $stmt->bindParam(":thu_nhap_chiu_thue", $data['thu_nhap_chiu_thue']);
        $stmt->bindParam(":tong_khoan_tru", $tong_khoan_tru);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    
        return $stmt->execute();
    }

    // Phương thức thêm mới bảo hiểm và thuế TNCN
    public function addBaoHiemThue($data) {
        $query = "INSERT INTO bao_hiem_thue_tncn (
                    id_nhan_vien, thang, bhxh_nv, bhyt_nv, bhtn_nv, 
                    bhxh_cty, bhyt_cty, bhtn_cty, thue_tncn, 
                    thu_nhap_truoc_thue, giam_tru_gia_canh, thu_nhap_chiu_thue, tong_khoan_tru
                  ) VALUES (
                    :id_nhan_vien, :thang, :bhxh_nv, :bhyt_nv, :bhtn_nv, 
                    :bhxh_cty, :bhyt_cty, :bhtn_cty, :thue_tncn, 
                    :thu_nhap_truoc_thue, :giam_tru_gia_canh, :thu_nhap_chiu_thue, :tong_khoan_tru
                  )";
        
        $stmt = $this->conn->prepare($query);

        // Tính tổng khoản trừ (chỉ tính phần nhân viên)
        $tong_khoan_tru = floatval($data['bhxh_nv']) + floatval($data['bhyt_nv']) + floatval($data['bhtn_nv']) + floatval($data['thue_tncn']);

        $stmt->bindParam(":id_nhan_vien", $data['id_nhan_vien'], PDO::PARAM_INT);
        $stmt->bindParam(":thang", $data['thang']);
        $stmt->bindParam(":bhxh_nv", $data['bhxh_nv']);
        $stmt->bindParam(":bhyt_nv", $data['bhyt_nv']);
        $stmt->bindParam(":bhtn_nv", $data['bhtn_nv']);
        $stmt->bindParam(":bhxh_cty", $data['bhxh_cty']);
        $stmt->bindParam(":bhyt_cty", $data['bhyt_cty']);
        $stmt->bindParam(":bhtn_cty", $data['bhtn_cty']);
        $stmt->bindParam(":thue_tncn", $data['thue_tncn']);
        $stmt->bindParam(":thu_nhap_truoc_thue", $data['thu_nhap_truoc_thue']);
        $stmt->bindParam(":giam_tru_gia_canh", $data['giam_tru_gia_canh']);
        $stmt->bindParam(":thu_nhap_chiu_thue", $data['thu_nhap_chiu_thue']);
        $stmt->bindParam(":tong_khoan_tru", $tong_khoan_tru);

        return $stmt->execute();
    }

    public function __destruct() {
        $this->conn = null;
    }
}
?>