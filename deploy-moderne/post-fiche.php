<?php
/**
 * API : Ajouter une nouvelle fiche comic dans MySQL
 * Méthode: POST
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gestion des requêtes OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Lire les données JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

// Validation basique
if (empty($data['nom_serie'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Le nom de série est requis']);
    exit;
}

try {
    // Charger la configuration de la BDD
    require_once 'db-config.php';
    $pdo = getDbConnection();

    // Préparer les données pour l'insertion
    $sql = "INSERT INTO fiches (
        nom_serie, titre, numero, annee, numero_edition, editeur,
        auteur_couverture, autres_auteurs, titre_secondaire, etat,
        isbn, description, image_url
    ) VALUES (
        :nom_serie, :titre, :numero, :annee, :numero_edition, :editeur,
        :auteur_couverture, :autres_auteurs, :titre_secondaire, :etat,
        :isbn, :description, :image_url
    )";

    $stmt = $pdo->prepare($sql);

    // Préparer les paramètres
    $params = [
        ':nom_serie' => trim($data['nom_serie']),
        ':titre' => isset($data['titre']) ? trim($data['titre']) : trim($data['nom_serie']),
        ':numero' => isset($data['numero']) ? trim($data['numero']) : '',
        ':annee' => isset($data['annee']) ? trim($data['annee']) : '',
        ':numero_edition' => isset($data['numero_edition']) ? trim($data['numero_edition']) : '',
        ':editeur' => isset($data['editeur']) ? trim($data['editeur']) : '',
        ':auteur_couverture' => isset($data['auteur_couverture']) ? trim($data['auteur_couverture']) : '',
        ':autres_auteurs' => isset($data['autres_auteurs']) && is_array($data['autres_auteurs'])
            ? json_encode($data['autres_auteurs'], JSON_UNESCAPED_UNICODE)
            : '[]',
        ':titre_secondaire' => isset($data['titre_secondaire']) ? trim($data['titre_secondaire']) : '',
        ':etat' => isset($data['etat']) ? trim($data['etat']) : 'Très bon',
        ':isbn' => isset($data['isbn']) ? trim($data['isbn']) : '',
        ':description' => isset($data['description']) ? trim($data['description']) : '',
        ':image_url' => isset($data['image_url']) ? trim($data['image_url']) : '',
    ];

    $stmt->execute($params);

    // Récupérer l'ID de la nouvelle fiche
    $newId = $pdo->lastInsertId();

    // Récupérer la fiche complète avec created_at
    $selectStmt = $pdo->prepare("SELECT * FROM fiches WHERE id = :id");
    $selectStmt->execute([':id' => $newId]);
    $newFiche = $selectStmt->fetch();

    // Décoder autres_auteurs pour le retour JSON
    if (isset($newFiche['autres_auteurs']) && is_string($newFiche['autres_auteurs'])) {
        $newFiche['autres_auteurs'] = json_decode($newFiche['autres_auteurs'], true) ?: [];
    }

    echo json_encode([
        'success' => true,
        'message' => 'Comic ajouté avec succès',
        'data' => $newFiche
    ]);

} catch (PDOException $e) {
    error_log("Erreur post-fiche.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Erreur lors de la sauvegarde',
        'message' => $e->getMessage()
    ]);
}
?>
