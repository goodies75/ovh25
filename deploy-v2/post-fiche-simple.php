<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Données JSON invalides']);
    exit;
}

// Localisation du fichier JSON
$jsonFile = 'data/fiches-data.json';
if (!file_exists($jsonFile)) {
    $jsonFile = 'fiches-data.json';
}

// Lecture des données existantes
$fiches = [];
if (file_exists($jsonFile)) {
    $fiches = json_decode(file_get_contents($jsonFile), true) ?: [];
}

// Nouvelle fiche avec ID unique
$newFiche = $input;
$newFiche['id'] = time() * 1000 + rand(100, 999); // ID unique
$newFiche['created_at'] = date('c'); // Date ISO

$fiches[] = $newFiche;

if (file_put_contents($jsonFile, json_encode($fiches, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la sauvegarde']);
    exit;
}

echo json_encode(['success' => true, 'id' => $newFiche['id']]);
?>
