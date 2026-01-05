<?php
/**
 * Script de test de connexion à la base de données
 * À exécuter pour vérifier que la connexion fonctionne
 */

header('Content-Type: text/plain; charset=utf-8');

echo "🔍 Test de connexion à la base de données\n";
echo str_repeat("=", 50) . "\n\n";

// Test 1: Vérifier que le fichier de config existe
echo "1️⃣ Vérification du fichier de configuration...\n";
if (!file_exists(__DIR__ . '/db-config.php')) {
    echo "❌ ERREUR : Le fichier db-config.php n'existe pas\n";
    exit(1);
}
echo "✅ Fichier db-config.php trouvé\n\n";

// Test 2: Charger la configuration
echo "2️⃣ Chargement de la configuration...\n";
try {
    require_once 'db-config.php';
    echo "✅ Configuration chargée\n";
    echo "   - Host: " . DB_HOST . "\n";
    echo "   - Database: " . DB_NAME . "\n";
    echo "   - User: " . DB_USER . "\n";
    echo "   - Charset: " . DB_CHARSET . "\n\n";
} catch (Exception $e) {
    echo "❌ ERREUR : " . $e->getMessage() . "\n";
    exit(1);
}

// Test 3: Connexion à la base de données
echo "3️⃣ Tentative de connexion à MySQL...\n";
try {
    $pdo = getDbConnection();
    echo "✅ Connexion réussie !\n\n";
} catch (PDOException $e) {
    echo "❌ ERREUR de connexion : " . $e->getMessage() . "\n";
    echo "\nVérifiez :\n";
    echo "- Les credentials sont corrects\n";
    echo "- Le serveur MySQL est accessible\n";
    echo "- La base de données existe\n";
    exit(1);
}

// Test 4: Vérifier la version de MySQL
echo "4️⃣ Version de MySQL...\n";
try {
    $version = $pdo->query("SELECT VERSION()")->fetchColumn();
    echo "✅ MySQL version : $version\n\n";
} catch (PDOException $e) {
    echo "❌ ERREUR : " . $e->getMessage() . "\n";
}

// Test 5: Lister les tables
echo "5️⃣ Tables existantes dans la base...\n";
try {
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    if (empty($tables)) {
        echo "⚠️ Aucune table trouvée\n\n";
    } else {
        echo "✅ " . count($tables) . " table(s) trouvée(s) :\n";
        foreach ($tables as $table) {
            echo "   - $table\n";

            // Si c'est la table fiches, afficher le nombre d'enregistrements
            if ($table === 'fiches') {
                $count = $pdo->query("SELECT COUNT(*) FROM fiches")->fetchColumn();
                echo "     → $count enregistrement(s)\n";
            }
        }
        echo "\n";
    }
} catch (PDOException $e) {
    echo "❌ ERREUR : " . $e->getMessage() . "\n";
}

// Test 6: Vérifier si la table fiches existe
echo "6️⃣ Vérification de la table 'fiches'...\n";
try {
    $checkTable = $pdo->query("SHOW TABLES LIKE 'fiches'");
    if ($checkTable->rowCount() > 0) {
        echo "✅ La table 'fiches' existe\n";

        // Afficher la structure
        echo "\n📋 Structure de la table 'fiches' :\n";
        $columns = $pdo->query("DESCRIBE fiches")->fetchAll();
        foreach ($columns as $col) {
            echo "   - " . $col['Field'] . " : " . $col['Type'];
            if ($col['Key'] === 'PRI') echo " [PRIMARY KEY]";
            echo "\n";
        }
    } else {
        echo "⚠️ La table 'fiches' n'existe pas encore\n";
        echo "   → Exécutez create-table.php pour la créer\n";
    }
} catch (PDOException $e) {
    echo "❌ ERREUR : " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "✅ Tests terminés avec succès !\n";
?>
