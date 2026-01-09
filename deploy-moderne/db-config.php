<?php
/**
 * Configuration de la base de données MySQL
 * Credentials OVH
 */

define('DB_HOST', 'opetitcorqreact.mysql.db');
define('DB_NAME', 'opetitcorqreact');
define('DB_USER', 'opetitcorqreact');
define('DB_PASS', 'Lapin0tOVH');
define('DB_CHARSET', 'utf8mb4');

/**
 * Créer une connexion PDO à la base de données
 * @return PDO Instance PDO configurée
 * @throws PDOException En cas d'erreur de connexion
 */
function getDbConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";port=3306;dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Erreur de connexion à la base de données: " . $e->getMessage());
        throw $e;
    }
}
?>
