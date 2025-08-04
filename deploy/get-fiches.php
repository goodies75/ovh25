<?php
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

// Fichier pour stocker les fiches
$fichesFile = __DIR__ . '/fiches-data.json';

// Vérifier si le fichier existe
if (!file_exists($fichesFile)) {
    // Créer des données par défaut si le fichier n'existe pas
    $defaultFiches = [
        [
            'id' => 1722787200000,
            'titre' => 'Spider-Man: Into the Spider-Verse',
            'description' => 'Une aventure révolutionnaire dans le multivers avec Miles Morales',
            'image_url' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=300&h=400&fit=crop',
            'created_at' => '2024-08-04T12:00:00+00:00'
        ],
        [
            'id' => 1722873600000,
            'titre' => 'Batman: The Dark Knight Returns',
            'description' => 'Le retour épique de Batman dans une Gotham dystopique',
            'image_url' => 'https://images.unsplash.com/photo-1543832923-44667a44c804?w=300&h=400&fit=crop',
            'created_at' => '2024-08-05T12:00:00+00:00'
        ]
    ];
    file_put_contents($fichesFile, json_encode($defaultFiches, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo json_encode($defaultFiches);
    exit;
}

// Lire les fiches existantes
$fiches = json_decode(file_get_contents($fichesFile), true);

if (!$fiches) {
    echo json_encode([]);
    exit;
}

// Trier par date de création (plus récent en premier)
usort($fiches, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

echo json_encode($fiches);
?>
