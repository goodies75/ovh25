<?php
header('Content-Type: application/json');

echo json_encode([
    'message' => 'Test de diagnostic',
    'fichier_data_existe' => file_exists('data/fiches-data.json') ? 'OUI' : 'NON',
    'fichier_racine_existe' => file_exists('fiches-data.json') ? 'OUI' : 'NON',
    'contenu_data' => file_exists('data/fiches-data.json') ? json_decode(file_get_contents('data/fiches-data.json'), true) : 'AUCUN',
    'contenu_racine' => file_exists('fiches-data.json') ? json_decode(file_get_contents('fiches-data.json'), true) : 'AUCUN'
]);
?>
