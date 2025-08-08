<?php
// Test simple pour vérifier les permissions d'upload
header('Content-Type: application/json');

$uploadDir = __DIR__ . '/uploads/';
$testFile = $uploadDir . 'test.txt';

try {
    // Vérifier si le dossier uploads existe
    if (!is_dir($uploadDir)) {
        echo json_encode(['error' => 'Dossier uploads inexistant', 'path' => $uploadDir]);
        exit;
    }
    
    // Vérifier les permissions
    if (!is_writable($uploadDir)) {
        echo json_encode(['error' => 'Dossier uploads non accessible en écriture', 'permissions' => substr(sprintf('%o', fileperms($uploadDir)), -4)]);
        exit;
    }
    
    // Tenter de créer un fichier test
    if (file_put_contents($testFile, 'test') === false) {
        echo json_encode(['error' => 'Impossible de créer un fichier dans uploads']);
        exit;
    }
    
    // Nettoyer
    unlink($testFile);
    
    echo json_encode(['success' => true, 'message' => 'Upload fonctionnel !']);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
