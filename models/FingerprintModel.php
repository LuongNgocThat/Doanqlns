<?php
require_once __DIR__ . '/../config/database.php';

class FingerprintModel {
    private $conn;

    public function __construct() {
        try {
            $db = new Database();
            $this->conn = $db->getConnection();
            
            if (!$this->conn) {
                error_log("Database connection failed in FingerprintModel");
                throw new Exception("Không thể kết nối cơ sở dữ liệu");
            }
        } catch (Exception $e) {
            error_log("FingerprintModel construct error: " . $e->getMessage());
            throw new Exception("Khởi tạo FingerprintModel thất bại: " . $e->getMessage());
        }
    }

    public function getAllFingerprints() {
        try {
            // Kiểm tra kết nối
            if (!$this->conn) {
                error_log("Database connection is null in getAllFingerprints");
                throw new Exception("Không thể kết nối cơ sở dữ liệu");
            }

            // Kiểm tra bảng tồn tại
            $tableExists = $this->checkTableExists();
            if (!$tableExists) {
                error_log("Table van_tay does not exist");
                return [];
            }

            $sql = "SELECT 
                        vt.id,
                        vt.id_nhan_vien,
                        vt.du_lieu_van_tay,
                        vt.trang_thai,
                        vt.ngay_tao,
                        vt.ngay_cap_nhat,
                        nv.ho_ten as ten_nhan_vien
                    FROM van_tay vt
                    LEFT JOIN nhan_vien nv ON vt.id_nhan_vien = nv.id_nhan_vien
                    ORDER BY vt.ngay_tao DESC";
            
            error_log("Executing query: " . $sql);
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!$results) {
                error_log("No fingerprint records found");
                return [];
            }

            // Format dữ liệu trước khi trả về
            foreach ($results as &$result) {
                $result['ngay_tao'] = $result['ngay_tao'] ? date('d/m/Y H:i:s', strtotime($result['ngay_tao'])) : null;
                $result['ngay_cap_nhat'] = $result['ngay_cap_nhat'] ? date('d/m/Y H:i:s', strtotime($result['ngay_cap_nhat'])) : null;
                
                // Đảm bảo các trường không null
                $result['id'] = $result['id'] ?? '';
                $result['id_nhan_vien'] = $result['id_nhan_vien'] ?? '';
                $result['ten_nhan_vien'] = $result['ten_nhan_vien'] ?? 'Không xác định';
                $result['trang_thai'] = $result['trang_thai'] ?? 'pending';
                
                // Xử lý dữ liệu vân tay nếu có
                if (!empty($result['du_lieu_van_tay'])) {
                    try {
                        $decodedData = json_decode($result['du_lieu_van_tay'], true);
                        $result['du_lieu_van_tay'] = $decodedData ?: $result['du_lieu_van_tay'];
                    } catch (Exception $e) {
                        error_log("Error decoding fingerprint data: " . $e->getMessage());
                        $result['du_lieu_van_tay'] = null;
                    }
                } else {
                    $result['du_lieu_van_tay'] = null;
                }
            }
            
            error_log("Returning " . count($results) . " fingerprint records");
            return $results;
        } catch (PDOException $e) {
            error_log("PDO Error in FingerprintModel::getAllFingerprints: " . $e->getMessage());
            throw new Exception("Không thể lấy danh sách vân tay: " . $e->getMessage());
        } catch (Exception $e) {
            error_log("Error in FingerprintModel::getAllFingerprints: " . $e->getMessage());
            throw new Exception("Lỗi khi lấy danh sách vân tay: " . $e->getMessage());
        }
    }

    public function getById($id) {
        try {
            $sql = "SELECT * FROM van_tay WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in FingerprintModel::getById: " . $e->getMessage());
            throw new Exception("Không thể lấy thông tin vân tay");
        }
    }

    public function create($data) {
        try {
            $sql = "INSERT INTO van_tay (id_nhan_vien, du_lieu_van_tay, trang_thai, ngay_tao) 
                    VALUES (?, ?, ?, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $data['id_nhan_vien'],
                $data['du_lieu_van_tay'],
                $data['trang_thai']
            ]);
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error in FingerprintModel::create: " . $e->getMessage());
            throw new Exception("Không thể tạo vân tay mới");
        }
    }

    public function update($id, $data) {
        try {
            $updateFields = [];
            $params = [];

            if (isset($data['id_nhan_vien'])) {
                $updateFields[] = "id_nhan_vien = ?";
                $params[] = $data['id_nhan_vien'];
            }
            if (isset($data['du_lieu_van_tay'])) {
                $updateFields[] = "du_lieu_van_tay = ?";
                $params[] = $data['du_lieu_van_tay'];
            }
            if (isset($data['trang_thai'])) {
                $updateFields[] = "trang_thai = ?";
                $params[] = $data['trang_thai'];
            }

            if (empty($updateFields)) {
                throw new Exception("Không có dữ liệu để cập nhật");
            }

            $updateFields[] = "ngay_cap_nhat = NOW()";
            $params[] = $id;

            $sql = "UPDATE van_tay SET " . implode(", ", $updateFields) . ", ngay_cap_nhat = NOW() WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error in FingerprintModel::update: " . $e->getMessage());
            throw new Exception("Không thể cập nhật vân tay");
        }
    }

    public function delete($id) {
        try {
            $sql = "DELETE FROM van_tay WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error in FingerprintModel::delete: " . $e->getMessage());
            throw new Exception("Không thể xóa vân tay");
        }
    }

    public function checkTableExists() {
        try {
            $result = $this->conn->query("SHOW TABLES LIKE 'van_tay'");
            return $result->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error checking van_tay table existence: " . $e->getMessage());
            return false;
        }
    }

    public function createTable() {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS van_tay (
                id INT AUTO_INCREMENT PRIMARY KEY,
                id_nhan_vien INT,
                du_lieu_van_tay LONGTEXT,
                trang_thai ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
                ngay_tao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                ngay_cap_nhat DATETIME ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (id_nhan_vien) REFERENCES nhan_vien(id_nhan_vien) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            error_log("Creating van_tay table with query: " . $sql);
            
            $this->conn->exec($sql);
            error_log("van_tay table created successfully");
            return true;
        } catch (PDOException $e) {
            error_log("Error creating van_tay table: " . $e->getMessage());
            throw new Exception("Không thể tạo bảng van_tay: " . $e->getMessage());
        }
    }

    public function checkEmployeeExists($employeeId) {
        try {
            $sql = "SELECT COUNT(*) FROM nhan_vien WHERE id_nhan_vien = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$employeeId]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error in FingerprintModel::checkEmployeeExists: " . $e->getMessage());
            throw new Exception("Không thể kiểm tra thông tin nhân viên");
        }
    }
}
?> 