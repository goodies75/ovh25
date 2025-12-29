<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Test simple : d'abord vérifier si le fichier existe
if (file_exists('data/fiches-data.json')) {
    echo json_encode(['status' => 'data/fiches-data.json existe']);
} elseif (file_exists('fiches-data.json')) {
    echo json_encode(['status' => 'fiches-data.json existe']);
} else {
    echo json_encode(['status' => 'aucun fichier de données trouvé']);
}
?>
