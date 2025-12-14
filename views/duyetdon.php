
            <?php
ob_start();
require_once __DIR__ . '/../includes/check_login.php';
require("PHPMailer-master/src/PHPMailer.php");
require("PHPMailer-master/src/SMTP.php");
require("PHPMailer-master/src/Exception.php");

include_once(__DIR__ . '/../config/Database.php');

class CNghiPhep {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function getDonNghiPhep($idNghiPhep) {
        $sql = "SELECT np.*, nv.ho_ten, nv.email 
                FROM nghi_phep np
                JOIN nhan_vien nv ON np.id_nhan_vien = nv.id_nhan_vien
                WHERE np.id_nghi_phep = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $idNghiPhep);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function updateTrangThaiNghiPhep($idNghiPhep, $trangThai, $idNguoiDuyet) {
        $sql = "UPDATE nghi_phep 
                SET trang_thai1 = ?, id_nguoi_duyet = ?, ngay_duyet = NOW() 
                WHERE id_nghi_phep = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sii", $trangThai, $idNguoiDuyet, $idNghiPhep);
        return $stmt->execute();
    }
}

$p = new CNghiPhep($conn);

if (isset($_REQUEST["duyetDonNghiPhep"])) {
    $idNghiPhep = $_REQUEST["duyetDonNghiPhep"];
    $idNguoiDuyet = $_SESSION['user_id'];
    
    $thongTinDon = $p->getDonNghiPhep($idNghiPhep);

    if ($thongTinDon) {
        $emailNhanVien = $thongTinDon['email'];
        $hoTenNV = $thongTinDon['ho_ten'];
        $ngayBatDau = date("d-m-Y", strtotime($thongTinDon['ngay_bat_dau']));
        $ngayKetThuc = date("d-m-Y", strtotime($thongTinDon['ngay_ket_thuc']));
        $loaiNghi = $thongTinDon['loai_nghi'];
        $lyDo = $thongTinDon['ly_do'];
        $trangThai = "Đã duyệt";
        
        $updateResult = $p->updateTrangThaiNghiPhep($idNghiPhep, $trangThai, $idNguoiDuyet);
        
        if ($updateResult) {
            if (empty($emailNhanVien)) {
                echo "<script>alert('Cập nhật trạng thái thành công nhưng email nhân viên rỗng!'); window.location.href = 'quanly_nghiphep.php';</script>";
                exit();
            }

            $mail = new PHPMailer\PHPMailer\PHPMailer();
            $mail->IsSMTP();
            $mail->SMTPDebug = 2; // Bật debug để ghi log
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'ssl';
            $mail->Host = "smtp.gmail.com";
            $mail->Port = 465;
            $mail->IsHTML(true);
            $mail->Username = "rya07661@gmail.com";
            $mail->Password = "tcnkzzujjdvjsoel"; // Mật khẩu ứng dụng của bạn
            $mail->SetFrom("rya07661@gmail.com");
            $mail->Subject = "THÔNG BÁO DUYỆT ĐƠN NGHỈ PHÉP";
            $mail->CharSet = 'UTF-8';
            
            $mail->Body = "
            <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto;'>
                <div style='background-color: #4CAF50; color: white; padding: 15px; text-align: center;'>
                    <h2>THÔNG BÁO DUYỆT ĐƠN NGHỈ PHÉP</h2>
                </div>
                <div style='padding: 20px;'>
                    <p>Xin chào <strong>$hoTenNV</strong>,</p>
                    <p>Đơn xin nghỉ phép của bạn đã được <span style='color: #4CAF50; font-weight: bold;'>DUYỆT</span> với thông tin sau:</p>
                    <table style='width: 100%; border-collapse: collapse; margin: 15px 0;'>
                        <tr>
                            <td style='padding: 8px; border: 1px solid #ddd;'><strong>Loại nghỉ:</strong></td>
                            <td style='padding: 8px; border: 1px solid #ddd;'>$loaiNghi</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px; border: 1px solid #ddd;'><strong>Ngày bắt đầu:</strong></td>
                            <td style='padding: 8px; border: 1px solid #ddd;'>$ngayBatDau</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px; border: 1px solid #ddd;'><strong>Ngày kết thúc:</strong></td>
                            <td style='padding: 8px; border: 1px solid #ddd;'>$ngayKetThuc</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px; border: 1px solid #ddd;'><strong>Lý do:</strong></td>
                            <td style='padding: 8px; border: 1px solid #ddd;'>$lyDo</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px; border: 1px solid #ddd;'><strong>Trạng thái:</strong></td>
                            <td style='padding: 8px; border: 1px solid #ddd; color: #4CAF50; font-weight: bold;'>$trangThai</td>
                        </tr>
                    </table>
                    <p>Vui lòng sắp xếp công việc phù hợp trong thời gian nghỉ phép.</p>
                    <p>Nếu có bất kỳ thắc mắc nào, vui lòng liên hệ với quản lý trực tiếp.</p>
                    <div style='margin-top: 20px; padding: 10px; background-color: #f9f9f9; border-left: 4px solid #4CAF50;'>
                        <p><strong>Lưu ý:</strong> Đây là email tự động, vui lòng không trả lời.</p>
                    </div>
                </div>
                <div style='text-align: center; padding: 15px; background-color: #f1f1f1; font-size: 12px;'>
                    <p>HRM Pro - Hệ thống quản lý nhân sự chuyên nghiệp</p>
                </div>
            </div>";
            
            $mail->AddAddress($emailNhanVien);
            
            if (!$mail->Send()) {
                echo "<script>alert('Cập nhật trạng thái thành công nhưng gửi email thất bại: " . $mail->ErrorInfo . "'); window.location.href = 'quanly_nghiphep.php';</script>";
            } else {
                echo "<script>alert('Cập nhật trạng thái và gửi email thông báo thành công!'); window.location.href = 'quanly_nghiphep.php';</script>";
            }
            exit();
        } else {
            echo "<script>alert('Có lỗi khi cập nhật trạng thái đơn nghỉ phép!'); window.history.back();</script>";
            exit();
        }
    } else {
        echo "<script>alert('Không tìm thấy thông tin đơn nghỉ phép!'); window.history.back();</script>";
        exit();
    }
}

ob_end_flush();
?>