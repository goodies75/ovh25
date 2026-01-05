<?php
/**
 * Script de migration des données JSON vers MySQL
 * Lit le fichier fiches-data.json et insère les données dans la table MySQL
 */

require_once 'db-config.php';

$jsonFile = __DIR__ . '/fiches-data.json';

echo "🔄 Début de la migration JSON vers MySQL\n";
echo str_repeat("=", 50) . "\n\n";

try {
    $pdo = getDbConnection();

    // Vérifier si le fichier JSON existe
    if (!file_exists($jsonFile)) {
        echo "⚠️ Le fichier $jsonFile n'existe pas.\n";
        echo "Aucune donnée à migrer.\n";
        exit(0);
    }

    // Lire le fichier JSON
    $jsonContent = file_get_contents($jsonFile);
    $fiches = json_decode($jsonContent, true);

    if (!$fiches || !is_array($fiches)) {
        echo "❌ Erreur : Le fichier JSON est vide ou invalide.\n";
        exit(1);
    }

    echo "📂 Fichier JSON trouvé : " . count($fiches) . " fiche(s) à migrer\n\n";

    // Vérifier si la table existe
    $checkTable = $pdo->query("SHOW TABLES LIKE 'fiches'");
    if ($checkTable->rowCount() === 0) {
        echo "❌ Erreur : La table 'fiches' n'existe pas.\n";
        echo "Veuillez exécuter create-table.php d'abord.\n";
        exit(1);
    }

    // Compter les enregistrements existants
    $countStmt = $pdo->query("SELECT COUNT(*) as count FROM fiches");
    $existingCount = $countStmt->fetch()['count'];

    if ($existingCount > 0) {
        echo "⚠️ La table contient déjà $existingCount enregistrement(s).\n";
        echo "Les doublons seront ignorés.\n\n";
    }

    // Préparer la requête d'insertion
    $sql = "INSERT INTO fiches (
        nom_serie, titre, numero, annee, numero_edition, editeur,
        auteur_couverture, autres_auteurs, titre_secondaire, etat,
        isbn, description, image_url, created_at
    ) VALUES (
        :nom_serie, :titre, :numero, :annee, :numero_edition, :editeur,
        :auteur_couverture, :autres_auteurs, :titre_secondaire, :etat,
        :isbn, :description, :image_url, :created_at
    )";

    $stmt = $pdo->prepare($sql);

    $successCount = 0;
    $errorCount = 0;

    // Migrer chaque fiche
    foreach ($fiches as $index => $fiche) {
        try {
            $params = [
                ':nom_serie' => $fiche['nom_serie'] ?? $fiche['titre'] ?? 'Sans titre',
                ':titre' => $fiche['titre'] ?? $fiche['nom_serie'] ?? null,
                ':numero' => $fiche['numero'] ?? '',
                ':annee' => $fiche['annee'] ?? '',
                ':numero_edition' => $fiche['numero_edition'] ?? '',
                ':editeur' => $fiche['editeur'] ?? '',
                ':auteur_couverture' => $fiche['auteur_couverture'] ?? '',
                ':autres_auteurs' => isset($fiche['autres_auteurs']) && is_array($fiche['autres_auteurs'])
                    ? json_encode($fiche['autres_auteurs'], JSON_UNESCAPED_UNICODE)
                    : (isset($fiche['autres_auteurs']) ? $fiche['autres_auteurs'] : '[]'),
                ':titre_secondaire' => $fiche['titre_secondaire'] ?? '',
                ':etat' => $fiche['etat'] ?? 'Très bon',
                ':isbn' => $fiche['isbn'] ?? '',
                ':description' => $fiche['description'] ?? '',
                ':image_url' => $fiche['image_url'] ?? '',
                ':created_at' => $fiche['created_at'] ?? date('Y-m-d H:i:s'),
            ];

            $stmt->execute($params);
            $successCount++;

            echo "✅ Fiche " . ($index + 1) . " : " . ($params[':nom_serie']) . " - Migrée\n";

        } catch (PDOException $e) {
            $errorCount++;
            echo "❌ Fiche " . ($index + 1) . " : Erreur - " . $e->getMessage() . "\n";
        }
    }

    echo "\n" . str_repeat("=", 50) . "\n";
    echo "📊 Résultat de la migration :\n";
    echo "   ✅ Succès : $successCount fiche(s)\n";
    echo "   ❌ Erreurs : $errorCount fiche(s)\n";
    echo "   📁 Total : " . count($fiches) . " fiche(s)\n\n";

    // Afficher les statistiques de la table
    $countStmt = $pdo->query("SELECT COUNT(*) as count FROM fiches");
    $finalCount = $countStmt->fetch()['count'];

    echo "📋 Total dans la base de données : $finalCount fiche(s)\n\n";

    if ($successCount > 0) {
        echo "✅ Migration terminée avec succès !\n\n";

        // Créer une sauvegarde du fichier JSON
        $backupFile = $jsonFile . '.backup-' . date('Y-m-d-His');
        if (copy($jsonFile, $backupFile)) {
            echo "💾 Sauvegarde créée : " . basename($backupFile) . "\n";
            echo "   Vous pouvez maintenant supprimer le fichier JSON original si vous le souhaitez.\n";
        }
    }

} catch (PDOException $e) {
    echo "\n❌ Erreur de connexion : " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "\n❌ Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
?>
