<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Corriger toutes les fiches qui pointent vers test-image.jpg inexistant

// 1. Récupérer les vraies images disponibles
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

if (empty($realImages)) {
    echo json_encode(['error' => 'Aucune vraie image trouvée dans uploads']);
    exit;
}

// 2. Lire le fichier JSON
$jsonFile = 'data/fiches-data.json';
if (!file_exists($jsonFile)) {
    $jsonFile = 'fiches-data.json';
}

if (!file_exists($jsonFile)) {
    echo json_encode(['error' => 'Fichier JSON introuvable']);
    exit;
}

$fiches = json_decode(file_get_contents($jsonFile), true);

if (!$fiches) {
    echo json_encode(['error' => 'Erreur lecture JSON']);
    exit;
}

// 3. Corriger toutes les fiches avec test-image.jpg
$corrections = 0;
$correctedFiches = [];

foreach ($fiches as $index => $fiche) {
    $imageUrl = $fiche['image_url'] ?? '';
    
    // Si la fiche pointe vers test-image.jpg (qui n'existe pas)
    if (strpos($imageUrl, 'test-image.jpg') !== false) {
        // Prendre une vraie image au hasard
        $randomImage = $realImages[array_rand($realImages)];
        $newUrl = 'http://o-petit.com/uploads/' . $randomImage;
        
        $correctedFiches[] = [
            'id' => $fiche['id'],
            'nom_serie' => $fiche['nom_serie'] ?? 'N/A',
            'old_url' => $imageUrl,
            'new_url' => $newUrl,
            'used_file' => $randomImage
        ];
        
        $fiches[$index]['image_url'] = $newUrl;
        $corrections++;
    }
}

$result = [
    'status' => 'Correction test-image.jpg',
    'images_disponibles' => count($realImages),
    'corrections_effectuees' => $corrections,
    'fiches_corrigees' => $correctedFiches,
    'sample_images' => array_slice($realImages, 0, 5)
];

// 4. Sauvegarder si des corrections ont été faites
if ($corrections > 0) {
    if (file_put_contents($jsonFile, json_encode($fiches, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
        $result['sauvegarde'] = 'Réussie';
    } else {
        $result['sauvegarde'] = 'Échouée';
    }
} else {
    $result['message'] = 'Aucune fiche avec test-image.jpg trouvée';
}

echo json_encode($result, JSON_PRETTY_PRINT);
?>
