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

// Ici vous pourriez sauvegarder en base de données
// Pour l'instant, on simule juste une réponse de succès

// Simuler un ID généré
$newId = time();

// Sauvegarder dans un fichier JSON simple (pour le développement)
$fichesFile = __DIR__ . '/fiches.json';
$fiches = [];

if (file_exists($fichesFile)) {
    $existingData = file_get_contents($fichesFile);
    $fiches = json_decode($existingData, true) ?: [];
}

$newFiche = [
    'id' => $newId,
    'titre' => $data['titre'],
    'description' => $data['description'],
    'image_url' => $data['image_url'] ?? '',
    'created_at' => date('Y-m-d H:i:s')
];

$fiches[] = $newFiche;

file_put_contents($fichesFile, json_encode($fiches, JSON_PRETTY_PRINT));

echo json_encode([
    'success' => true,
    'message' => 'Fiche ajoutée avec succès',
    'data' => $newFiche
]);
?>
