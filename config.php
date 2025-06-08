<?php
// config.php untuk AWS RDS
$host = $_SERVER['RDS_HOSTNAME'] ?? 'todolist-mysql.c7cy0c2wquov.ap-southeast-1.rds.amazonaws.com';
$port = $_SERVER['RDS_PORT'] ?? 3306;
$dbname = $_SERVER['RDS_DB_NAME'] ?? 'todolist';
$username = $_SERVER['RDS_USERNAME'] ?? 'admin';
$password = $_SERVER['RDS_PASSWORD'] ?? 'jancok';

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, $username, $password, $options);
    
    // Auto-create table jika belum ada
    $createTable = "CREATE TABLE IF NOT EXISTS tasks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        task VARCHAR(255) NOT NULL,
        completed TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($createTable);
    
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>