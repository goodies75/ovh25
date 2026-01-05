<?php
/**
 * Script de création de la table 'fiches' dans MySQL
 * À exécuter une seule fois pour initialiser la structure
 */

require_once 'db-config.php';

try {
    $pdo = getDbConnection();

    // Vérifier si la table existe déjà
    $checkTable = $pdo->query("SHOW TABLES LIKE 'fiches'");

    if ($checkTable->rowCount() > 0) {
        echo "⚠️ La table 'fiches' existe déjà.\n";
        echo "Voulez-vous la recréer ? Cela supprimera toutes les données.\n";
        echo "Pour continuer, modifiez ce script et décommentez la ligne DROP TABLE.\n";

        // Décommenter pour forcer la recréation (ATTENTION: supprime les données)
        // $pdo->exec("DROP TABLE IF EXISTS fiches");
        // echo "✅ Table supprimée.\n";
    }

    // Créer la table avec la structure complète
    $sql = "CREATE TABLE IF NOT EXISTS fiches (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom_serie VARCHAR(255) NOT NULL,
        titre VARCHAR(255) DEFAULT NULL,
        numero VARCHAR(50) DEFAULT '',
        annee VARCHAR(10) DEFAULT '',
        numero_edition VARCHAR(50) DEFAULT '',
        editeur VARCHAR(255) DEFAULT '',
        auteur_couverture VARCHAR(255) DEFAULT '',
        autres_auteurs TEXT DEFAULT NULL COMMENT 'JSON array of authors',
        titre_secondaire VARCHAR(255) DEFAULT '',
        etat ENUM('Neuf', 'Très bon', 'Bon', 'Moyen', 'Abîmé') DEFAULT 'Très bon',
        isbn VARCHAR(20) DEFAULT '',
        description TEXT DEFAULT '',
        image_url VARCHAR(500) DEFAULT '',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

        INDEX idx_nom_serie (nom_serie),
        INDEX idx_editeur (editeur),
        INDEX idx_annee (annee),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $pdo->exec($sql);

    echo "✅ Table 'fiches' créée avec succès !\n\n";

    // Afficher la structure de la table
    echo "📋 Structure de la table :\n";
    $columns = $pdo->query("DESCRIBE fiches");

    printf("%-20s %-30s %-10s %-5s %-15s\n", "Field", "Type", "Null", "Key", "Default");
    echo str_repeat("-", 90) . "\n";

    foreach ($columns as $col) {
        printf("%-20s %-30s %-10s %-5s %-15s\n",
            $col['Field'],
            $col['Type'],
            $col['Null'],
            $col['Key'],
            $col['Default'] ?? 'NULL'
        );
    }

    echo "\n✅ Script terminé avec succès !\n";

} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
?>
