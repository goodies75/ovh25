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
if (empty($data['nom_serie'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Le nom de série est requis']);
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

// Créer la nouvelle fiche avec tous les champs
$newFiche = [
    'id' => time() * 1000 + rand(0, 999),
    'nom_serie' => trim($data['nom_serie']),
    'numero' => isset($data['numero']) ? trim($data['numero']) : '',
    'annee' => isset($data['annee']) ? trim($data['annee']) : '',
    'numero_edition' => isset($data['numero_edition']) ? trim($data['numero_edition']) : '',
    'editeur' => isset($data['editeur']) ? trim($data['editeur']) : '',
    'auteur_couverture' => isset($data['auteur_couverture']) ? trim($data['auteur_couverture']) : '',
    'autres_auteurs' => isset($data['autres_auteurs']) && is_array($data['autres_auteurs']) ? $data['autres_auteurs'] : [],
    'titre_secondaire' => isset($data['titre_secondaire']) ? trim($data['titre_secondaire']) : '',
    'etat' => isset($data['etat']) ? trim($data['etat']) : 'Très bon',
    'isbn' => isset($data['isbn']) ? trim($data['isbn']) : '',
    'description' => isset($data['description']) ? trim($data['description']) : '',
    'image_url' => isset($data['image_url']) ? trim($data['image_url']) : '',
    'created_at' => date('c') // Format ISO 8601
];

// Ajouter la nouvelle fiche
$fiches[] = $newFiche;

// Sauvegarder
if (file_put_contents($fichesFile, json_encode($fiches, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    echo json_encode([
        'success' => true,
        'message' => 'Comic ajouté avec succès',
        'data' => $newFiche
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la sauvegarde']);
}
?>
