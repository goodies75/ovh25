<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$jsonFile = 'data/fiches-data.json';

// Test de lecture du fichier
$content = file_get_contents($jsonFile);

if ($content === false) {
    echo json_encode(['error' => 'Impossible de lire le fichier']);
    exit;
}

// Test de décodage JSON
$data = json_decode($content, true);

if ($data === null) {
    echo json_encode(['error' => 'JSON invalide', 'json_error' => json_last_error_msg()]);
    exit;
}

// Succès - afficher les données
echo json_encode($data);
?>
