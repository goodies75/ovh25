<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée. Utilisez POST.']);
    exit();
}

// Log de débogage
error_log('Upload debug: Méthode = ' . $_SERVER['REQUEST_METHOD']);
error_log('Upload debug: POST = ' . json_encode($_POST));
error_log('Upload debug: FILES = ' . json_encode($_FILES));

try {
    // Test très simple : retourner succès sans traitement
    echo json_encode([
        'success' => true,
        'message' => 'Test réussi - API fonctionne',
        'debug' => [
            'post_data' => $_POST,
            'files_data' => $_FILES,
            'method' => $_SERVER['REQUEST_METHOD']
        ],
        'images' => [
            'medium' => [
                'url' => './uploads/test-image.jpg',
                'width' => 400,
                'height' => 400
            ]
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
