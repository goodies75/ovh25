<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

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

if ($fiches === null) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur de lecture JSON']);
    exit;
}

echo json_encode($fiches);
?>
