<?php
/**
 * API : Upload d'images pour les comics
 * Méthode: POST
 * Accepte : multipart/form-data avec un fichier image
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gestion des requêtes OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Configuration
$uploadDir = __DIR__ . '/uploads/';
$maxFileSize = 10 * 1024 * 1024; // 10 MB
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

// Créer le dossier uploads s'il n'existe pas
if (!file_exists($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        http_response_code(500);
        echo json_encode(['error' => 'Impossible de créer le dossier uploads']);
        exit;
    }
}

// Vérifier qu'un fichier a été uploadé
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    $errorMessage = 'Aucune image uploadée';

    if (isset($_FILES['image']['error'])) {
        switch ($_FILES['image']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $errorMessage = 'Le fichier est trop volumineux';
                break;
            case UPLOAD_ERR_PARTIAL:
                $errorMessage = 'Le fichier n\'a été que partiellement uploadé';
                break;
            case UPLOAD_ERR_NO_FILE:
                $errorMessage = 'Aucun fichier n\'a été uploadé';
                break;
            default:
                $errorMessage = 'Erreur lors de l\'upload';
        }
    }

    http_response_code(400);
    echo json_encode(['error' => $errorMessage]);
    exit;
}

$file = $_FILES['image'];

// Vérifier la taille du fichier
if ($file['size'] > $maxFileSize) {
    http_response_code(400);
    echo json_encode(['error' => 'Le fichier est trop volumineux (max 10 MB)']);
    exit;
}

// Vérifier le type MIME
if (!in_array($file['type'], $allowedTypes)) {
    http_response_code(400);
    echo json_encode(['error' => 'Type de fichier non autorisé. Utilisez JPG, PNG, GIF ou WebP']);
    exit;
}

// Vérifier l'extension
$fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($fileExtension, $allowedExtensions)) {
    http_response_code(400);
    echo json_encode(['error' => 'Extension de fichier non autorisée']);
    exit;
}

// Générer un nom de fichier unique
$fileName = uniqid('comic_', true) . '.' . $fileExtension;
$filePath = $uploadDir . $fileName;

// Déplacer le fichier uploadé
if (!move_uploaded_file($file['tmp_name'], $filePath)) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la sauvegarde du fichier']);
    exit;
}

// Optimiser l'image (redimensionner si trop grande)
try {
    $imageInfo = getimagesize($filePath);
    if ($imageInfo !== false) {
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $maxDimension = 1200; // Largeur/hauteur maximale

        // Redimensionner seulement si l'image est trop grande
        if ($width > $maxDimension || $height > $maxDimension) {
            $ratio = $width / $height;

            if ($width > $height) {
                $newWidth = $maxDimension;
                $newHeight = (int)($maxDimension / $ratio);
            } else {
                $newHeight = $maxDimension;
                $newWidth = (int)($maxDimension * $ratio);
            }

            // Créer une nouvelle image
            $source = null;
            switch ($imageInfo[2]) {
                case IMAGETYPE_JPEG:
                    $source = imagecreatefromjpeg($filePath);
                    break;
                case IMAGETYPE_PNG:
                    $source = imagecreatefrompng($filePath);
                    break;
                case IMAGETYPE_GIF:
                    $source = imagecreatefromgif($filePath);
                    break;
                case IMAGETYPE_WEBP:
                    $source = imagecreatefromwebp($filePath);
                    break;
            }

            if ($source) {
                $destination = imagecreatetruecolor($newWidth, $newHeight);

                // Préserver la transparence pour PNG et GIF
                if ($imageInfo[2] == IMAGETYPE_PNG || $imageInfo[2] == IMAGETYPE_GIF) {
                    imagealphablending($destination, false);
                    imagesavealpha($destination, true);
                }

                imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                // Sauvegarder l'image redimensionnée
                switch ($imageInfo[2]) {
                    case IMAGETYPE_JPEG:
                        imagejpeg($destination, $filePath, 85);
                        break;
                    case IMAGETYPE_PNG:
                        imagepng($destination, $filePath, 8);
                        break;
                    case IMAGETYPE_GIF:
                        imagegif($destination, $filePath);
                        break;
                    case IMAGETYPE_WEBP:
                        imagewebp($destination, $filePath, 85);
                        break;
                }

                imagedestroy($source);
                imagedestroy($destination);
            }
        }
    }
} catch (Exception $e) {
    // Si l'optimisation échoue, on garde l'image originale
    error_log("Erreur lors de l'optimisation de l'image: " . $e->getMessage());
}

// Retourner l'URL de l'image uploadée
$imageUrl = './uploads/' . $fileName;

echo json_encode([
    'success' => true,
    'message' => 'Image uploadée avec succès',
    'url' => $imageUrl,
    'filename' => $fileName,
    'size' => filesize($filePath)
]);
?>
