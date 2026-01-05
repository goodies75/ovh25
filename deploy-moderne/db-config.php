<?php
/**
 * Configuration de la base de données MySQL
 * Credentials OVH
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'o-petit_comics');
define('DB_USER', 'o-petit_comics');
define('DB_PASS', 'UQv4F2wvQSCA3wnG');
define('DB_CHARSET', 'utf8mb4');

/**
 * Créer une connexion PDO à la base de données
 * @return PDO Instance PDO configurée
 * @throws PDOException En cas d'erreur de connexion
 */
function getDbConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
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
