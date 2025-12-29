<?php
// Test simple d'upload pour vérifier les permissions
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$uploadDir = __DIR__ . '/uploads/';
$testFile = $uploadDir . 'test_' . time() . '.txt';

try {
    // Test d'écriture simple
    $result = file_put_contents($testFile, 'Test upload: ' . date('Y-m-d H:i:s'));
    
    if ($result !== false) {
        // Nettoyage
        @unlink($testFile);
        
        echo json_encode([
            'success' => true,
            'message' => 'Upload possible !',
            'upload_dir' => $uploadDir,
            'writable' => is_writable($uploadDir),
            'test_bytes' => $result
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Impossible d\'écrire dans uploads/',
            'upload_dir' => $uploadDir,
            'exists' => is_dir($uploadDir),
            'writable' => is_writable($uploadDir)
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
