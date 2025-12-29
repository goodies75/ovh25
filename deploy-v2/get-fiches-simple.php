<?php
// API GET ultra-simple avec vos vraies données
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Utilise le vrai fichier de données
$jsonFile = 'data/fiches-data.json';
if (!file_exists($jsonFile)) {
    $jsonFile = 'fiches-data.json';
}
$fichesFile = __DIR__ . '/' . $jsonFile;

if (!file_exists($fichesFile)) {
    echo json_encode(['error' => 'Fichier de données introuvable: ' . $fichesFile]);
    exit;
}

$fiches = json_decode(file_get_contents($fichesFile), true);

if (!$fiches) {
    echo json_encode(['error' => 'Erreur de lecture JSON']);
    exit;
}

echo json_encode($fiches);
?>
