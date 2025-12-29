<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Corriger toutes les URLs d'images
$jsonFile = 'data/fiches-data.json';
if (!file_exists($jsonFile)) {
    $jsonFile = 'fiches-data.json';
}

$fiches = json_decode(file_get_contents($jsonFile), true);
$corrected = 0;

foreach ($fiches as $index => $fiche) {
    if (isset($fiche['image_url']) && !empty($fiche['image_url'])) {
        $imageUrl = $fiche['image_url'];
        
        // Si l'URL commence par ./ ou uploads/, la corriger
        if (strpos($imageUrl, './uploads/') === 0) {
            $fiches[$index]['image_url'] = 'http://o-petit.com/' . substr($imageUrl, 2);
            $corrected++;
        } elseif (strpos($imageUrl, 'uploads/') === 0) {
            $fiches[$index]['image_url'] = 'http://o-petit.com/' . $imageUrl;
            $corrected++;
        }
    }
}

// Sauvegarder les corrections
if ($corrected > 0) {
    file_put_contents($jsonFile, json_encode($fiches, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

echo json_encode([
    'status' => 'Correction terminée',
    'fiches_corrigees' => $corrected,
    'total_fiches' => count($fiches)
]);
?>
