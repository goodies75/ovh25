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

    // Vérifier les données reçues
    if (empty($_POST['imageData']) || empty($_POST['filename'])) {
        throw new Exception('Données manquantes');
    }

    $imageData = $_POST['imageData'];
    $filename = $_POST['filename'];
    
    // Nettoyer le nom de fichier
    $cleanFilename = preg_replace('/[^a-zA-Z0-9.-]/', '_', $filename);
    $extension = pathinfo($cleanFilename, PATHINFO_EXTENSION);
    $baseName = pathinfo($cleanFilename, PATHINFO_FILENAME);
    
    // Générer un nom unique
    $uniqueFilename = $baseName . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . $uniqueFilename;
    
    // Décoder et sauvegarder l'image
    if (strpos($imageData, 'data:') === 0) {
        // Base64 avec préfixe data:
        $imageData = explode(',', $imageData)[1];
    }
    
    $decodedImage = base64_decode($imageData);
    if ($decodedImage === false) {
        throw new Exception('Impossible de décoder l\'image');
    }
    
    if (file_put_contents($filepath, $decodedImage) === false) {
        throw new Exception('Impossible de sauvegarder l\'image');
    }
    
    // Générer l'URL relative
    $imageUrl = './uploads/' . $uniqueFilename;
    
    // Retourner le succès avec les URLs
    echo json_encode([
        'success' => true,
        'message' => 'Image uploadée avec succès',
        'images' => [
            'medium' => [
                'url' => $imageUrl,
                'width' => 400,
                'height' => 400
            ],
            'thumbnail' => [
                'url' => $imageUrl,
                'width' => 150,
                'height' => 150
            ],
            'full' => [
                'url' => $imageUrl
            ]
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'debug' => [
            'upload_dir' => __DIR__ . '/uploads/',
            'upload_dir_exists' => is_dir(__DIR__ . '/uploads/'),
            'upload_dir_writable' => is_writable(__DIR__ . '/uploads/')
        ]
    ]);
}
?>
