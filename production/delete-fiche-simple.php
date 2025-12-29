<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['id']) || !isset($input['pin'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID et PIN requis']);
    exit;
}

// Vérification du PIN
if ($input['pin'] !== '@0149@') {
    http_response_code(403);
    echo json_encode(['error' => 'PIN incorrect']);
    exit;
}

$id = intval($input['id']);

// Localisation du fichier JSON
$jsonFile = 'data/fiches-data.json';
if (!file_exists($jsonFile)) {
    $jsonFile = 'fiches-data.json';
}

if (!file_exists($jsonFile)) {
    http_response_code(404);
    echo json_encode(['error' => 'Fichier de données introuvable']);
    exit;
}

$fiches = json_decode(file_get_contents($jsonFile), true);

if (!$fiches) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur de lecture des données']);
    exit;
}

// Recherche et suppression
$found = false;
foreach ($fiches as $index => $fiche) {
    if ($fiche['id'] == $id) {
        array_splice($fiches, $index, 1);
        $found = true;
        break;
    }
}

if (!$found) {
    http_response_code(404);
    echo json_encode(['error' => 'Fiche non trouvée']);
    exit;
}

if (file_put_contents($jsonFile, json_encode($fiches, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la sauvegarde']);
    exit;
}

echo json_encode(['success' => true, 'deleted_id' => $id]);
?>
