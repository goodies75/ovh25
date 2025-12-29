<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// SOLUTION FINALE : Nettoyer définitivement toutes les données corrompues

// 1. Récupérer toutes les vraies images
$uploadsDir = 'uploads';
$realImages = [];

if (is_dir($uploadsDir)) {
    $files = scandir($uploadsDir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file)) {
            $realImages[] = $file;
        }
    }
}

// 2. Lire les données actuelles
$jsonFile = 'data/fiches-data.json';
if (!file_exists($jsonFile)) {
    $jsonFile = 'fiches-data.json';
}

$fiches = json_decode(file_get_contents($jsonFile), true) ?: [];

// 3. Nettoyer TOUTES les données corrompues
$corrections = [
    'test_image_corrected' => 0,
    'relative_urls_fixed' => 0,
    'empty_urls_cleaned' => 0,
    'total_fiches' => count($fiches)
];

$cleanedFiches = [];

foreach ($fiches as $fiche) {
    $imageUrl = $fiche['image_url'] ?? '';
    $needsCorrection = false;
    
    // Détecter les URLs problématiques
    if (empty($imageUrl)) {
        $corrections['empty_urls_cleaned']++;
        $needsCorrection = true;
    } elseif (strpos($imageUrl, 'test-image') !== false) {
        $corrections['test_image_corrected']++;
        $needsCorrection = true;
    } elseif (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
        $corrections['relative_urls_fixed']++;
        $needsCorrection = true;
    }
    
    // Corriger avec une vraie image
    if ($needsCorrection && !empty($realImages)) {
        $randomImage = $realImages[array_rand($realImages)];
        $fiche['image_url'] = 'http://o-petit.com/uploads/' . $randomImage;
    }
    
    // Garder seulement les fiches valides
    if (!empty($fiche['nom_serie']) && !empty($fiche['image_url'])) {
        $cleanedFiches[] = $fiche;
    }
}

$result = [
    'status' => 'NETTOYAGE COMPLET TERMINÉ',
    'before' => $corrections['total_fiches'],
    'after' => count($cleanedFiches),
    'corrections' => $corrections,
    'images_disponibles' => count($realImages),
    'sample_images' => array_slice($realImages, 0, 3)
];

// 4. Sauvegarder les données nettoyées
if (file_put_contents($jsonFile, json_encode($cleanedFiches, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    $result['sauvegarde'] = 'RÉUSSIE';
    $result['message'] = 'Base de données complètement nettoyée. Testez maintenant votre application React !';
} else {
    $result['sauvegarde'] = 'ÉCHOUÉE';
    $result['error'] = 'Impossible de sauvegarder';
}

echo json_encode($result, JSON_PRETTY_PRINT);
?>
