<?php
include_once(__DIR__ . '/../config/Database.php');

class UserModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllUsers() {
        $query = "SELECT * FROM NHAN_VIEN NV
INNER JOIN PHONG_BAN PB ON NV.id_phong_ban = PB.id_phong_ban
INNER JOIN CHUC_VU CV ON NV.id_chuc_vu = CV.id_chuc_vu";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $users;
    }
}
?>
