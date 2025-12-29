<?php
// Test simple de l'environnement OVH
echo "<h1>Test Serveur OVH</h1>";

echo "<h2>Informations PHP</h2>";
echo "Version PHP: " . phpversion() . "<br>";
echo "Serveur: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";

echo "<h2>Test Base de Données</h2>";
try {
    $host = 'localhost';
    $dbname = 'o-petit_comics';
    $username = 'o-petit_comics';
    $password = 'UQv4F2wvQSCA3wnG';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    echo "✅ Connexion MySQL réussie<br>";
    
    // Test simple
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    echo "Tables trouvées: " . count($tables) . "<br>";
    
} catch (Exception $e) {
    echo "❌ Erreur MySQL: " . $e->getMessage() . "<br>";
}

echo "<h2>Test Upload</h2>";
if (is_dir('./uploads')) {
    echo "✅ Dossier uploads existe<br>";
    if (is_writable('./uploads')) {
        echo "✅ Dossier uploads accessible en écriture<br>";
    } else {
        echo "❌ Dossier uploads non accessible en écriture<br>";
    }
} else {
    echo "❌ Dossier uploads manquant<br>";
}

echo "<h2>Modules Apache</h2>";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    echo "mod_rewrite: " . (in_array('mod_rewrite', $modules) ? "✅" : "❌") . "<br>";
    echo "mod_headers: " . (in_array('mod_headers', $modules) ? "✅" : "❌") . "<br>";
} else {
    echo "Impossible de vérifier les modules Apache<br>";
}

echo "<h2>Permissions</h2>";
echo "Répertoire courant: " . getcwd() . "<br>";
echo "Propriétaire: " . get_current_user() . "<br>";

?>
<style>
body { font-family: Arial; padding: 20px; }
h1 { color: #4F46E5; }
h2 { color: #EC4899; border-bottom: 1px solid #ccc; }
</style>
