<?php
header('Content-Type: application/json');

echo "=== TEST CONNEXION MYSQL OVH ===\n\n";

try {
    // Paramètres de connexion (mêmes que dans ovh25-develop)
    $host = 'localhost';
    $dbname = 'o-petit_comics';
    $username = 'o-petit_comics';
    $password = 'UQv4F2wvQSCA3wnG';
    
    echo "1. Tentative de connexion à la base...\n";
    echo "   Host: $host\n";
    echo "   Database: $dbname\n";
    echo "   User: $username\n\n";
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ CONNEXION RÉUSSIE !\n\n";
    
    // Vérifier si la table 'fiches' existe
    echo "2. Vérification de la table 'fiches'...\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'fiches'");
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        echo "✅ Table 'fiches' trouvée !\n\n";
        
        // Compter les enregistrements
        echo "3. Comptage des enregistrements...\n";
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM fiches");
        $count = $stmt->fetch()['total'];
        echo "📊 Nombre total de comics: $count\n\n";
        
        if ($count > 0) {
            // Afficher quelques exemples
            echo "4. Exemples d'enregistrements:\n";
            $stmt = $pdo->query("SELECT id, titre, nom_serie, created_at FROM fiches ORDER BY created_at DESC LIMIT 5");
            $fiches = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($fiches as $fiche) {
                $titre = $fiche['titre'] ?: $fiche['nom_serie'] ?: 'Sans titre';
                $date = $fiche['created_at'];
                echo "   - ID: {$fiche['id']} | Titre: $titre | Date: $date\n";
            }
            
            echo "\n✅ VOTRE BASE MYSQL FONCTIONNE ET CONTIENT VOS DONNÉES !\n";
            echo "🎯 Vous pouvez restaurer les APIs MySQL.\n";
        } else {
            echo "⚠️  Table vide - aucun comic trouvé.\n";
        }
        
    } else {
        echo "❌ Table 'fiches' non trouvée.\n";
        echo "📋 Tables disponibles:\n";
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            echo "   - $table\n";
        }
    }
    
} catch (PDOException $e) {
    echo "❌ ERREUR DE CONNEXION:\n";
    echo "Code: " . $e->getCode() . "\n";
    echo "Message: " . $e->getMessage() . "\n\n";
    
    if (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "🔐 Problème d'authentification - vérifiez vos identifiants.\n";
    } elseif (strpos($e->getMessage(), 'Unknown database') !== false) {
        echo "🗄️  Base de données inexistante.\n";
    } elseif (strpos($e->getMessage(), 'No such file') !== false) {
        echo "🚫 MySQL non disponible sur ce serveur.\n";
    }
    
    echo "\n💡 Recommandation: Restez avec le système JSON actuel.\n";
}

echo "\n=== FIN DU TEST ===\n";
?>
