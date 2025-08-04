<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Lire le fichier des fiches
$fichesFile = __DIR__ . '/fiches.json';

if (!file_exists($fichesFile)) {
    echo json_encode([]);
    exit;
}

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
