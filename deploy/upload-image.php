<?php
/*
 * ==================== API D'UPLOAD ET TRAITEMENT D'IMAGES ====================
 * 
 * OBJECTIF: Recevoir, traiter et sauvegarder les photos de couverture de comics
 * 
 * FONCTIONNALITÉS:
 * - Upload sécurisé d'images depuis React
 * - Génération automatique de formats multiples (thumbnail, medium, full)
 * - Compression et optimisation des images
 * - Nommage unique pour éviter les conflits
 * - Validation stricte des types et tailles
 * 
 * UTILISATION:
 * - React envoie l'image en base64 ou multipart
 * - L'API traite et génère les formats nécessaires
 * - Retourne les URLs des images générées
 * 
 * MÉTHODE HTTP: POST uniquement
 * PARAMÈTRES: image (base64 ou file), filename
 * FORMATS GÉNÉRÉS: thumbnail (150px), medium (400px), full (800px)
 */

// ========== ÉTAPE 1: CONFIGURATION DES EN-TÊTES CORS ==========
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// ========== ÉTAPE 2: GESTION DU CORS PREFLIGHT ==========
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ========== ÉTAPE 3: VÉRIFICATION DE LA MÉTHODE ==========
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit();
}

// ========== ÉTAPE 4: CONFIGURATION DU SYSTÈME ==========
/*
 * Paramètres de traitement des images
 * Qualité et tailles optimisées pour le web
 */
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB max
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/webp']);

// Formats à générer automatiquement
$imageFormats = [
    'thumbnail' => ['width' => 150, 'height' => 200, 'quality' => 85],
    'medium'    => ['width' => 400, 'height' => 533, 'quality' => 90],
    'full'      => ['width' => 800, 'height' => 1067, 'quality' => 95]
];

// ========== ÉTAPE 5: FONCTIONS DE TRAITEMENT ==========

/**
 * Créer le dossier d'upload s'il n'existe pas
 */
function ensureUploadDirectory() {
    if (!file_exists(UPLOAD_DIR)) {
        if (!mkdir(UPLOAD_DIR, 0755, true)) {
            throw new Exception('Impossible de créer le dossier d\'upload');
        }
    }
}

/**
 * Générer un nom de fichier unique et sécurisé
 */
function generateSafeFilename($originalName) {
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $baseName = pathinfo($originalName, PATHINFO_FILENAME);
    
    // Nettoyer le nom de base
    $baseName = preg_replace('/[^a-zA-Z0-9_-]/', '', $baseName);
    $baseName = substr($baseName, 0, 20); // Limiter la longueur
    
    // Ajouter timestamp pour unicité
    $timestamp = time();
    $randomId = substr(md5(uniqid()), 0, 8);
    
    return $baseName . '_' . $timestamp . '_' . $randomId . '.' . $extension;
}

/**
 * Redimensionner et optimiser une image
 */
function resizeImage($sourcePath, $destPath, $maxWidth, $maxHeight, $quality = 90) {
    // Détecter le type d'image
    $imageInfo = getimagesize($sourcePath);
    if (!$imageInfo) {
        throw new Exception('Fichier image invalide');
    }
    
    list($origWidth, $origHeight, $imageType) = $imageInfo;
    
    // Créer la ressource image source
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_WEBP:
            $sourceImage = imagecreatefromwebp($sourcePath);
            break;
        default:
            throw new Exception('Type d\'image non supporté');
    }
    
    if (!$sourceImage) {
        throw new Exception('Impossible de charger l\'image');
    }
    
    // ========== CALCUL DES PROPORTIONS ==========
    /*
     * Redimensionner en gardant les proportions
     * S'assurer que l'image tient dans les dimensions max
     */
    $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
    $newWidth = round($origWidth * $ratio);
    $newHeight = round($origHeight * $ratio);
    
    // Créer l'image de destination
    $destImage = imagecreatetruecolor($newWidth, $newHeight);
    
    // Préserver la transparence pour PNG
    if ($imageType === IMAGETYPE_PNG) {
        imagealphablending($destImage, false);
        imagesavealpha($destImage, true);
        $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
        imagefill($destImage, 0, 0, $transparent);
    }
    
    // Redimensionner avec anti-aliasing
    imagecopyresampled(
        $destImage, $sourceImage,
        0, 0, 0, 0,
        $newWidth, $newHeight,
        $origWidth, $origHeight
    );
    
    // Sauvegarder selon le format
    $success = false;
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $success = imagejpeg($destImage, $destPath, $quality);
            break;
        case IMAGETYPE_PNG:
            // PNG: qualité de 0 à 9 (inverse de JPEG)
            $pngQuality = round((100 - $quality) / 11);
            $success = imagepng($destImage, $destPath, $pngQuality);
            break;
        case IMAGETYPE_WEBP:
            $success = imagewebp($destImage, $destPath, $quality);
            break;
    }
    
    // Nettoyer la mémoire
    imagedestroy($sourceImage);
    imagedestroy($destImage);
    
    if (!$success) {
        throw new Exception('Erreur lors de la sauvegarde');
    }
    
    return ['width' => $newWidth, 'height' => $newHeight];
}

// ========== ÉTAPE 6: TRAITEMENT DE LA REQUÊTE ==========
try {
    ensureUploadDirectory();
    
    // ========== RÉCUPÉRATION DES DONNÉES ==========
    $imageData = null;
    $filename = null;
    
    // Vérifier si c'est un upload base64 ou multipart
    if (isset($_POST['imageData']) && isset($_POST['filename'])) {
        // ========== MODE BASE64 (depuis caméra) ==========
        $imageData = $_POST['imageData'];
        $filename = $_POST['filename'];
        
        // Décoder le base64
        if (strpos($imageData, ',') !== false) {
            $imageData = explode(',', $imageData)[1]; // Retirer le préfixe data:image/...
        }
        
        $imageData = base64_decode($imageData);
        if (!$imageData) {
            throw new Exception('Données image base64 invalides');
        }
        
    } elseif (isset($_FILES['image'])) {
        // ========== MODE MULTIPART (upload fichier) ==========
        $uploadedFile = $_FILES['image'];
        
        if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Erreur lors de l\'upload: ' . $uploadedFile['error']);
        }
        
        if ($uploadedFile['size'] > MAX_FILE_SIZE) {
            throw new Exception('Fichier trop volumineux (max 10MB)');
        }
        
        if (!in_array($uploadedFile['type'], ALLOWED_TYPES)) {
            throw new Exception('Type de fichier non autorisé');
        }
        
        $imageData = file_get_contents($uploadedFile['tmp_name']);
        $filename = $uploadedFile['name'];
        
    } else {
        throw new Exception('Aucune image fournie');
    }
    
    // ========== GÉNÉRATION DU NOM UNIQUE ==========
    $safeFilename = generateSafeFilename($filename);
    $tempPath = UPLOAD_DIR . 'temp_' . $safeFilename;
    
    // Sauvegarder temporairement
    if (file_put_contents($tempPath, $imageData) === false) {
        throw new Exception('Impossible de sauvegarder l\'image temporaire');
    }
    
    // ========== VALIDATION DE L'IMAGE ==========
    $imageInfo = getimagesize($tempPath);
    if (!$imageInfo) {
        unlink($tempPath);
        throw new Exception('Fichier image corrompu');
    }
    
    // ========== GÉNÉRATION DES FORMATS ==========
    $generatedImages = [];
    $baseFilename = pathinfo($safeFilename, PATHINFO_FILENAME);
    
    foreach ($imageFormats as $format => $specs) {
        $formatFilename = $baseFilename . '_' . $format . '.jpg'; // Toujours en JPEG pour optimisation
        $formatPath = UPLOAD_DIR . $formatFilename;
        
        try {
            $dimensions = resizeImage(
                $tempPath, 
                $formatPath, 
                $specs['width'], 
                $specs['height'], 
                $specs['quality']
            );
            
            $generatedImages[$format] = [
                'url' => 'uploads/' . $formatFilename, // URL relative pour React
                'filename' => $formatFilename,
                'width' => $dimensions['width'],
                'height' => $dimensions['height'],
                'size' => filesize($formatPath)
            ];
            
        } catch (Exception $e) {
            // En cas d'erreur sur un format, nettoyer et arrêter
            unlink($tempPath);
            foreach ($generatedImages as $img) {
                if (file_exists(UPLOAD_DIR . $img['filename'])) {
                    unlink(UPLOAD_DIR . $img['filename']);
                }
            }
            throw new Exception('Erreur génération format ' . $format . ': ' . $e->getMessage());
        }
    }
    
    // Supprimer le fichier temporaire
    unlink($tempPath);
    
    // ========== RÉPONSE DE SUCCÈS ==========
    echo json_encode([
        'success' => true,
        'message' => 'Image traitée avec succès',
        'images' => $generatedImages,
        'original_filename' => $filename,
        'processed_at' => date('c')
    ]);
    
} catch (Exception $e) {
    // ========== GESTION DES ERREURS ==========
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
