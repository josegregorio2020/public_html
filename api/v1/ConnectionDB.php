<?php
class Database {
    private $host = "10.10.2.7";
    private $dbname = "aprendetic";
    private $username = "aprendetic";
    private $password = 'pg9$tbR8Yy!n';
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO("mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4", $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die(json_encode(["status" => "error", "message" => "Error de conexión a la base de datos", "details" => $e->getMessage()]));
        }
    }

    public function getConnection() {
        return $this->pdo;
    }
}
?>
