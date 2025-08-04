<?php
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
if (empty($data['titre']) || empty($data['description'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Titre et description requis']);
    exit;
}

// Fichier pour stocker les fiches
$fichesFile = __DIR__ . '/fiches-data.json';
$fiches = [];

// Lire les fiches existantes
if (file_exists($fichesFile)) {
    $existingData = file_get_contents($fichesFile);
    $fiches = json_decode($existingData, true) ?: [];
}

// Créer la nouvelle fiche
$newFiche = [
    'id' => time() * 1000 + rand(0, 999), // Simuler Date.now() de JavaScript
    'titre' => trim($data['titre']),
    'description' => trim($data['description']),
    'image_url' => isset($data['image_url']) ? trim($data['image_url']) : '',
    'created_at' => date('c') // Format ISO 8601
];

// Ajouter la nouvelle fiche
$fiches[] = $newFiche;

// Sauvegarder
if (file_put_contents($fichesFile, json_encode($fiches, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    echo json_encode([
        'success' => true,
        'message' => 'Fiche ajoutée avec succès',
        'data' => $newFiche
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la sauvegarde']);
}
?>
