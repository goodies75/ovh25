<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Fichier de données
$jsonFile = 'data/fiches-data.json';
if (!file_exists($jsonFile)) {
    $jsonFile = 'fiches-data.json';
}

if (!file_exists($jsonFile)) {
    http_response_code(404);
    echo json_encode(['error' => 'Fichier de données introuvable']);
    exit;
}

// Lire le contenu JSON existant
$fiches = json_decode(file_get_contents($jsonFile), true);
if ($fiches === null) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur de lecture JSON']);
    exit;
}

// Récupérer la fiche modifiée depuis le POST ou PUT
$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Données invalides ou id manquant']);
    exit;
}

// Mettre à jour la fiche correspondante
$updated = false;
foreach ($fiches as &$fiche) {
    if (isset($fiche['id']) && $fiche['id'] == $input['id']) {
        $fiche = array_merge($fiche, $input);
        $updated = true;
        break;
    }
}

if (!$updated) {
    http_response_code(404);
    echo json_encode(['error' => 'Fiche non trouvée']);
    exit;
}

// Sauvegarder le JSON modifié
if (file_put_contents($jsonFile, json_encode($fiches, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la sauvegarde']);
    exit;
}

echo json_encode(['success' => true, 'fiche' => $input]);