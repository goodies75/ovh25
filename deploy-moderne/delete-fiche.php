<?php
/**
 * API : Supprimer une fiche comic de MySQL
 * Méthode: DELETE
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gérer les requêtes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Vérifier que c'est une requête DELETE
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit();
}

// Récupérer l'ID depuis l'URL ou le body
$id = null;

// Essayer de récupérer l'ID depuis l'URL (?id=...)
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    // Essayer de récupérer l'ID depuis le body JSON
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['id'])) {
        $id = $input['id'];
    }
}

if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'ID manquant']);
    exit();
}

try {
    // Charger la configuration de la BDD
    require_once 'db-config.php';
    $pdo = getDbConnection();

    // Récupérer la fiche avant suppression (pour le retourner)
    $selectStmt = $pdo->prepare("SELECT * FROM fiches WHERE id = :id");
    $selectStmt->execute([':id' => $id]);
    $ficheSuppressee = $selectStmt->fetch();

    if (!$ficheSuppressee) {
        http_response_code(404);
        echo json_encode(['error' => 'Fiche non trouvée']);
        exit();
    }

    // Décoder autres_auteurs pour le retour JSON
    if (isset($ficheSuppressee['autres_auteurs']) && is_string($ficheSuppressee['autres_auteurs'])) {
        $ficheSuppressee['autres_auteurs'] = json_decode($ficheSuppressee['autres_auteurs'], true) ?: [];
    }

    // Supprimer la fiche
    $deleteStmt = $pdo->prepare("DELETE FROM fiches WHERE id = :id");
    $deleteStmt->execute([':id' => $id]);

    // Compter les fiches restantes
    $countStmt = $pdo->query("SELECT COUNT(*) as count FROM fiches");
    $remainingCount = $countStmt->fetch()['count'];

    // Réponse de succès
    echo json_encode([
        'success' => true,
        'message' => 'Fiche supprimée avec succès',
        'deleted_fiche' => $ficheSuppressee,
        'remaining_count' => $remainingCount
    ]);

} catch (PDOException $e) {
    error_log("Erreur delete-fiche.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Erreur serveur',
        'message' => $e->getMessage()
    ]);
}
?>
