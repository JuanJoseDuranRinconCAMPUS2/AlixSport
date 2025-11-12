<?php

    require_once __DIR__ . '/../../vendor/autoload.php';

    use Dotenv\Dotenv;

    class Database {
        private $conn;

    public function __construct() {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
    }

    public function getConnection() {
        $this->conn = null;

        try {
            $host = $_ENV['DB_HOST'];
            $port = $_ENV['DB_PORT'];
            $db_name = $_ENV['DB_NAME'];
            $username = $_ENV['DB_USER'];
            $password = $_ENV['DB_PASS'];

            $dsn = "mysql:host=$host;port=$port;dbname=$db_name;charset=utf8mb4";

            $this->conn = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);

        } catch (PDOException $exception) {
            echo "❌ Error de conexión: " . $exception->getMessage();
        }

        return $this->conn;
    }
    }
?>