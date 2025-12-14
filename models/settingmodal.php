<?php
require_once __DIR__ . '/../config/database.php';

class SettingModal {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    private function isEmailUnique($email) {
        try {
            // Kiểm tra email trong bảng nguoi_dung
            $stmt = $this->conn->prepare("SELECT id FROM nguoi_dung WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                return false;
            }
            return true;
        } catch (PDOException $e) {
            error_log("Database Error in isEmailUnique: " . $e->getMessage());
            return false;
        }
    }

    public function registerRegularUser($ten_dang_nhap, $mat_khau, $email, $role) {
        try {
            // Kiểm tra tên đăng nhập đã tồn tại
            $stmt = $this->conn->prepare("SELECT id FROM nguoi_dung WHERE ten_dang_nhap = ?");
            $stmt->execute([$ten_dang_nhap]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Tên đăng nhập đã tồn tại'];
            }

            // Kiểm tra email đã tồn tại
            if (!$this->isEmailUnique($email)) {
                return ['success' => false, 'message' => 'Email đã được đăng ký'];
            }

            // Kiểm tra email có tồn tại trong bảng nhan_vien
            $stmt = $this->conn->prepare("SELECT id_nhan_vien FROM nhan_vien WHERE email = ?");
            $stmt->execute([$email]);
            $nhanVien = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$nhanVien) {
                return ['success' => false, 'message' => 'Email không tồn tại trong danh sách nhân viên'];
            }

            // Set quyền dựa trên role
            $quyen_them = $quyen_sua = $quyen_xoa = 0;
            switch ($role) {
                case 'admin':
                    $quyen_them = $quyen_sua = $quyen_xoa = 1;
                    break;
                case 'manager':
                    $quyen_them = $quyen_sua = 1;
                    break;
                case 'employee':
                    // Employee không có quyền gì, giữ mặc định là 0
                    break;
            }

            // Thêm người dùng mới
            $stmt = $this->conn->prepare("
                INSERT INTO nguoi_dung (id_nhan_vien, ten_dang_nhap, mat_khau, email, quyen_them, quyen_sua, quyen_xoa, trang_thai) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'Hoạt động')
            ");
            
            $result = $stmt->execute([
                $nhanVien['id_nhan_vien'],
                $ten_dang_nhap,
                $mat_khau,
                $email,
                $quyen_them,
                $quyen_sua,
                $quyen_xoa
            ]);

            if ($result) {
                return ['success' => true, 'message' => 'Đăng ký tài khoản thành công'];
            } else {
                return ['success' => false, 'message' => 'Lỗi khi đăng ký tài khoản'];
            }
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống, vui lòng thử lại sau'];
        }
    }

    public function registerGoogleUser($email, $mat_khau_email, $role) {
        try {
            // Kiểm tra email đã tồn tại
            if (!$this->isEmailUnique($email)) {
                return ['success' => false, 'message' => 'Email đã được đăng ký'];
            }

            // Set quyền dựa trên role
            $quyen_them = $quyen_sua = $quyen_xoa = 0;
            switch ($role) {
                case 'admin':
                    $quyen_them = $quyen_sua = $quyen_xoa = 1;
                    break;
                case 'manager':
                    $quyen_them = $quyen_sua = 1;
                    break;
                case 'employee':
                    // Employee không có quyền gì
                    break;
            }

            // Thêm người dùng mới
            $stmt = $this->conn->prepare("
                INSERT INTO nguoi_dung (email, mat_khau, quyen_them, quyen_sua, quyen_xoa) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $email,
                $mat_khau_email,
                $quyen_them,
                $quyen_sua,
                $quyen_xoa
            ]);

            if ($result) {
                return ['success' => true, 'message' => 'Đăng ký tài khoản Google thành công'];
            } else {
                return ['success' => false, 'message' => 'Lỗi khi đăng ký tài khoản Google'];
            }
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống, vui lòng thử lại sau'];
        }
    }

    public function getAllUsers() {
        try {
            $stmt = $this->conn->prepare("
                SELECT n.id, n.id_nhan_vien, n.ten_dang_nhap, n.mat_khau, n.email, 
                       n.quyen_them, n.quyen_sua, n.quyen_xoa, n.trang_thai
                FROM nguoi_dung n
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteUser($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM nguoi_dung WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return false;
        }
    }

    public function updateUserPermissions($userId, $data) {
        try {
            $updateFields = [];
            $params = [];

            if (isset($data['quyen_them'])) {
                $updateFields[] = "quyen_them = :quyen_them";
                $params[':quyen_them'] = $data['quyen_them'];
            }
            if (isset($data['quyen_sua'])) {
                $updateFields[] = "quyen_sua = :quyen_sua";
                $params[':quyen_sua'] = $data['quyen_sua'];
            }
            if (isset($data['quyen_xoa'])) {
                $updateFields[] = "quyen_xoa = :quyen_xoa";
                $params[':quyen_xoa'] = $data['quyen_xoa'];
            }

            if (empty($updateFields)) {
                return false;
            }

            $sql = "UPDATE nguoi_dung SET " . implode(", ", $updateFields) . " WHERE id = :id";
            $params[':id'] = $userId;

            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return false;
        }
    }

    public function toggleUserStatus($userId) {
        try {
            // First get current status
            $stmt = $this->conn->prepare("SELECT trang_thai FROM nguoi_dung WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return false;
            }

            // Toggle status
            $newStatus = $user['trang_thai'] === 'Hoạt động' ? 'Không hoạt động' : 'Hoạt động';

            $stmt = $this->conn->prepare("UPDATE nguoi_dung SET trang_thai = ? WHERE id = ?");
            return $stmt->execute([$newStatus, $userId]);
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return false;
        }
    }

    public function updateUserStatus($id, $newStatus) {
        try {
            // Kiểm tra giá trị trạng thái hợp lệ
            if (!in_array($newStatus, ['Hoạt động', 'Không hoạt động'])) {
                return [
                    'success' => false,
                    'message' => 'Trạng thái không hợp lệ'
                ];
            }

            // Cập nhật trạng thái
            $stmt = $this->conn->prepare("UPDATE nguoi_dung SET trang_thai = ? WHERE id = ?");
            $success = $stmt->execute([$newStatus, $id]);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Cập nhật trạng thái thành công',
                    'new_status' => $newStatus
                ];
            }

            return [
                'success' => false,
                'message' => 'Không thể cập nhật trạng thái'
            ];

        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Lỗi database: ' . $e->getMessage()
            ];
        }
    }
} 