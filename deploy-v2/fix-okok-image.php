<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Corriger la fiche "okok" avec une vraie image

// Récupérer une vraie image
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
    echo json_encode(['error' => 'Aucune image trouvée dans uploads']);
    exit;
}

// Prendre la première image disponible
$firstImage = $realImages[0];
$newImageUrl = "http://o-petit.com/uploads/" . $firstImage;

// Lire le fichier JSON
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

// Trouver et corriger la fiche "okok"
$corrected = false;
foreach ($fiches as $index => $fiche) {
    if (isset($fiche['nom_serie']) && $fiche['nom_serie'] === 'okok') {
        $oldUrl = $fiche['image_url'] ?? 'N/A';
        $fiches[$index]['image_url'] = $newImageUrl;
        $corrected = true;
        
        $result = [
            'status' => 'Fiche corrigée',
            'fiche_id' => $fiche['id'],
            'old_image_url' => $oldUrl,
            'new_image_url' => $newImageUrl,
            'used_file' => $firstImage
        ];
        break;
    }
}

if (!$corrected) {
    echo json_encode(['error' => 'Fiche "okok" non trouvée']);
    exit;
}

// Sauvegarder
if (file_put_contents($jsonFile, json_encode($fiches, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
    echo json_encode(['error' => 'Erreur sauvegarde']);
    exit;
}

echo json_encode($result);
?>
