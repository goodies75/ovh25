<?php
/**
 * API : Récupérer toutes les fiches comics depuis MySQL
 * Méthode: GET
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gestion des requêtes OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    // Charger la configuration de la BDD
    require_once 'db-config.php';
    $pdo = getDbConnection();

    // Récupérer toutes les fiches triées par date de création (plus récent en premier)
    $sql = "SELECT
        id, nom_serie, titre, numero, annee, numero_edition, editeur,
        auteur_couverture, autres_auteurs, titre_secondaire, etat,
        isbn, description, image_url, created_at
    FROM fiches
    ORDER BY created_at DESC";

    $stmt = $pdo->query($sql);
    $fiches = $stmt->fetchAll();

    // Convertir autres_auteurs de JSON string vers array
    foreach ($fiches as &$fiche) {
        if (isset($fiche['autres_auteurs']) && is_string($fiche['autres_auteurs'])) {
            $decoded = json_decode($fiche['autres_auteurs'], true);
            $fiche['autres_auteurs'] = $decoded ?: [];
        }

        // Convertir l'id en entier
        $fiche['id'] = (int) $fiche['id'];
    }

    echo json_encode($fiches);

} catch (PDOException $e) {
    error_log("Erreur get-fiches.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Erreur lors de la récupération des fiches',
        'message' => $e->getMessage()
    ]);
}
?>
