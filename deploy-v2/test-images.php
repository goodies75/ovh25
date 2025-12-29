<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Test pour voir les images uploadées
$uploadDir = 'uploads/';
if (is_dir($uploadDir)) {
    $files = scandir($uploadDir);
    $images = array_filter($files, function($file) {
        return !in_array($file, ['.', '..']);
    });
    
    echo json_encode([
        'status' => 'dossier uploads existe',
        'images' => array_values($images),
        'count' => count($images)
    ]);
} else {
    echo json_encode(['status' => 'dossier uploads inexistant']);
}
?>
