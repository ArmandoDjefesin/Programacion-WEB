<?php
require_once 'config.php';

class DBManager {
    private $conn;
    private static $instance = null;


    private function __construct() {
        
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        
        try {
            $this->conn = new PDO($dsn, DB_USER_APP, DB_PASS_APP, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
           
            echo json_encode(['status' => 'error', 'message' => 'Error de conexión a la base de datos.']);
            exit;
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new DBManager();
        }
        return self::$instance;
    }


    public function getConn() {
        return $this->conn;
    }

    private function __clone() {}
}
?>