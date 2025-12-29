<?php
// Configuration base de données OVH
define('DB_HOST', 'localhost');
define('DB_NAME', 'o-petit_comics');
define('DB_USER', 'o-petit_comics');
define('DB_PASS', 'UQv4F2wvQSCA3wnG');

function getDbConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        error_log("Erreur connexion DB: " . $e->getMessage());
        throw new Exception("Erreur de connexion à la base de données");
    }
}
?>
