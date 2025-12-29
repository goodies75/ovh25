<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Gestion des requêtes OPTIONS pour CORS
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
    // Lecture du JSON envoyé
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data || !isset($data['image'])) {
        echo json_encode(['success' => false, 'error' => 'Image manquante']);
        exit();
    }
    
    $imageData = $data['image'];
    
    // Vérification que c'est bien une image base64
    if (!preg_match('/^data:image\/(jpeg|jpg|png|gif|webp);base64,/', $imageData)) {
        echo json_encode(['success' => false, 'error' => 'Format d\'image invalide']);
        exit();
    }
    
    // Extraction des informations de l'image
    preg_match('/^data:image\/([a-zA-Z]+);base64,/', $imageData, $matches);
    $imageType = $matches[1];
    
    // Suppression du préfixe data:image
    $imageData = preg_replace('/^data:image\/[a-zA-Z]+;base64,/', '', $imageData);
    $imageData = base64_decode($imageData);
    
    if ($imageData === false) {
        echo json_encode(['success' => false, 'error' => 'Erreur de décodage de l\'image']);
        exit();
    }
    
    // Création du dossier uploads s'il n'existe pas
    $uploadsDir = './uploads';
    if (!is_dir($uploadsDir)) {
        if (!mkdir($uploadsDir, 0755, true)) {
            echo json_encode(['success' => false, 'error' => 'Impossible de créer le dossier uploads']);
            exit();
        }
    }
    
    // Génération d'un nom de fichier unique
    $fileName = 'comic_' . time() . '_' . uniqid() . '.' . $imageType;
    $filePath = $uploadsDir . '/' . $fileName;
    
    // Sauvegarde de l'image
    if (file_put_contents($filePath, $imageData) === false) {
        echo json_encode(['success' => false, 'error' => 'Erreur lors de la sauvegarde']);
        exit();
    }
    
    // Génération de l'URL publique
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    $baseUrl = $protocol . '://' . $host . $scriptDir;
    
    // Correction du chemin si nécessaire
    $baseUrl = rtrim($baseUrl, '/');
    $imageUrl = $baseUrl . '/uploads/' . $fileName;
    
    // Génération de miniatures (optionnel)
    $thumbnails = [];
    
    // Création d'une miniature 300x400 (format comic standard)
    if (extension_loaded('gd')) {
        try {
            $source = null;
            switch($imageType) {
                case 'jpeg':
                case 'jpg':
                    $source = imagecreatefromjpeg($filePath);
                    break;
                case 'png':
                    $source = imagecreatefrompng($filePath);
                    break;
                case 'gif':
                    $source = imagecreatefromgif($filePath);
                    break;
                case 'webp':
                    $source = imagecreatefromwebp($filePath);
                    break;
            }
            
            if ($source) {
                $originalWidth = imagesx($source);
                $originalHeight = imagesy($source);
                
                // Calcul des dimensions pour 300x400
                $targetWidth = 300;
                $targetHeight = 400;
                
                $ratio = min($targetWidth / $originalWidth, $targetHeight / $originalHeight);
                $newWidth = intval($originalWidth * $ratio);
                $newHeight = intval($originalHeight * $ratio);
                
                $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
                
                // Préservation de la transparence pour PNG
                if ($imageType === 'png') {
                    imagealphablending($thumbnail, false);
                    imagesavealpha($thumbnail, true);
                    $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
                    imagefill($thumbnail, 0, 0, $transparent);
                }
                
                imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
                
                $thumbFileName = 'thumb_' . $fileName;
                $thumbFilePath = $uploadsDir . '/' . $thumbFileName;
                
                switch($imageType) {
                    case 'jpeg':
                    case 'jpg':
                        imagejpeg($thumbnail, $thumbFilePath, 85);
                        break;
                    case 'png':
                        imagepng($thumbnail, $thumbFilePath, 8);
                        break;
                    case 'gif':
                        imagegif($thumbnail, $thumbFilePath);
                        break;
                    case 'webp':
                        imagewebp($thumbnail, $thumbFilePath, 85);
                        break;
                }
                
                $thumbnails['medium'] = $baseUrl . '/uploads/' . $thumbFileName;
                
                imagedestroy($thumbnail);
                imagedestroy($source);
            }
        } catch (Exception $e) {
            // En cas d'erreur, on continue sans miniature
            error_log("Erreur création miniature: " . $e->getMessage());
        }
    }
    
    echo json_encode([
        'success' => true,
        'url' => $imageUrl,
        'fileName' => $fileName,
        'thumbnails' => $thumbnails,
        'size' => filesize($filePath),
        'type' => $imageType
    ]);
    
} catch (Exception $e) {
    error_log("Erreur upload image: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur lors de l\'upload'
    ]);
}
?>
