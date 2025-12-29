<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Lire les données pour voir les URLs d'images
$jsonFile = 'data/fiches-data.json';
if (!file_exists($jsonFile)) {
    $jsonFile = 'fiches-data.json';
}

$fiches = json_decode(file_get_contents($jsonFile), true);

echo json_encode([
    'total_fiches' => count($fiches),
    'sample_image_urls' => array_map(function($fiche) {
        return [
            'id' => $fiche['id'],
            'titre' => $fiche['titre'] ?? 'Sans titre',
            'image_url' => $fiche['image_url'] ?? 'Pas d\'image'
        ];
    }, array_slice($fiches, -3)) // Les 3 dernières fiches
]);
?>
