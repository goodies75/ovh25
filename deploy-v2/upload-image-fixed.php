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
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit();
}

try {
    // Créer le dossier uploads s'il n'existe pas
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception('Impossible de créer le dossier uploads');
        }
    }

    // Lire les données JSON
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data || !isset($data['imageData'])) {
        throw new Exception('Données d\'image manquantes');
    }

    $imageData = $data['imageData'];
    $filename = isset($data['filename']) ? $data['filename'] : 'upload.jpg';
    
    // Nettoyer le nom de fichier
    $cleanFilename = preg_replace('/[^a-zA-Z0-9.-]/', '_', $filename);
    $extension = pathinfo($cleanFilename, PATHINFO_EXTENSION);
    if (empty($extension)) {
        $extension = 'jpg';
    }
    $baseName = pathinfo($cleanFilename, PATHINFO_FILENAME);
    if (empty($baseName)) {
        $baseName = 'upload';
    }
    
    // Générer un nom unique
    $uniqueFilename = $baseName . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . $uniqueFilename;
    
    // Décoder l'image
    if (strpos($imageData, 'data:') === 0) {
        // Enlever le préfixe data:image/xxx;base64,
        $parts = explode(',', $imageData);
        if (count($parts) >= 2) {
            $imageData = $parts[1];
        }
    }
    
    $decodedImage = base64_decode($imageData);
    if ($decodedImage === false) {
        throw new Exception('Impossible de décoder l\'image base64');
    }
    
    // Vérifier que c'est bien une image
    $imageInfo = @getimagesizefromstring($decodedImage);
    if ($imageInfo === false) {
        throw new Exception('Les données ne correspondent pas à une image valide');
    }
    
    // Sauvegarder l'image
    if (file_put_contents($filepath, $decodedImage) === false) {
        throw new Exception('Impossible de sauvegarder l\'image sur le serveur');
    }
    
    // Générer l'URL relative
    $imageUrl = './uploads/' . $uniqueFilename;
    
    // Retourner le succès
    echo json_encode([
        'success' => true,
        'message' => 'Image uploadée avec succès',
        'url' => $imageUrl,
        'filename' => $uniqueFilename,
        'size' => strlen($decodedImage),
        'dimensions' => [
            'width' => $imageInfo[0],
            'height' => $imageInfo[1]
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'debug' => [
            'method' => $_SERVER['REQUEST_METHOD'],
            'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'N/A',
            'input_length' => strlen(file_get_contents('php://input')),
            'upload_dir' => __DIR__ . '/uploads/',
            'upload_dir_exists' => is_dir(__DIR__ . '/uploads/'),
            'upload_dir_writable' => is_writable(__DIR__ . '/uploads/')
        ]
    ]);
}
?>
