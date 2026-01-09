<?php
// Test de connexion MySQL - version simple
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Test de connexion MySQL</h2>";

// Test 1: avec l'adresse actuelle
echo "<h3>Test 1: opetitcorqreact.mysql.db:3306</h3>";
try {
    $pdo1 = new PDO(
        "mysql:host=opetitcorqreact.mysql.db;port=3306;dbname=opetitcorqreact;charset=utf8mb4",
        "opetitcorqreact",
        "Lapin0tOVH"
    );
    echo "✅ CONNEXION RÉUSSIE avec opetitcorqreact.mysql.db:3306<br>";
} catch (PDOException $e) {
    echo "❌ ÉCHEC: " . $e->getMessage() . "<br>";
}

// Test 2: avec localhost
echo "<h3>Test 2: localhost</h3>";
try {
    $pdo2 = new PDO(
        "mysql:host=localhost;dbname=opetitcorqreact;charset=utf8mb4",
        "opetitcorqreact",
        "Lapin0tOVH"
    );
    echo "✅ CONNEXION RÉUSSIE avec localhost<br>";
} catch (PDOException $e) {
    echo "❌ ÉCHEC: " . $e->getMessage() . "<br>";
}

// Test 3: avec 127.0.0.1
echo "<h3>Test 3: 127.0.0.1</h3>";
try {
    $pdo3 = new PDO(
        "mysql:host=127.0.0.1;dbname=opetitcorqreact;charset=utf8mb4",
        "opetitcorqreact",
        "Lapin0tOVH"
    );
    echo "✅ CONNEXION RÉUSSIE avec 127.0.0.1<br>";
} catch (PDOException $e) {
    echo "❌ ÉCHEC: " . $e->getMessage() . "<br>";
}

echo "<br><h3>Informations PHP</h3>";
echo "Version PHP: " . phpversion() . "<br>";
echo "PDO MySQL disponible: " . (extension_loaded('pdo_mysql') ? 'OUI' : 'NON') . "<br>";
?>
