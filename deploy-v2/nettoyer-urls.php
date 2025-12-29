<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Nettoyer toutes les URLs relatives restantes

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

$corrections = 0;
$fichesProblematiques = [];

foreach ($fiches as $index => $fiche) {
    $imageUrl = $fiche['image_url'] ?? '';
    
    // Identifier les URLs problématiques
    if (!empty($imageUrl) && !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
        $fichesProblematiques[] = [
            'index' => $index,
            'id' => $fiche['id'],
            'nom_serie' => $fiche['nom_serie'] ?? 'N/A',
            'old_url' => $imageUrl
        ];
        
        // Corriger les URLs relatives
        if (strpos($imageUrl, './uploads/') === 0) {
            $nouveauNom = str_replace('./uploads/', '', $imageUrl);
            $fiches[$index]['image_url'] = 'http://o-petit.com/uploads/' . $nouveauNom;
            $corrections++;
        } elseif (strpos($imageUrl, 'uploads/') === 0) {
            $nouveauNom = str_replace('uploads/', '', $imageUrl);
            $fiches[$index]['image_url'] = 'http://o-petit.com/uploads/' . $nouveauNom;
            $corrections++;
        }
    }
}

$result = [
    'status' => 'Nettoyage URLs',
    'fiches_problematiques_trouvees' => count($fichesProblematiques),
    'corrections_effectuees' => $corrections,
    'details' => $fichesProblematiques
];

// Sauvegarder si des corrections ont été faites
if ($corrections > 0) {
    if (file_put_contents($jsonFile, json_encode($fiches, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
        $result['sauvegarde'] = 'Réussie';
    } else {
        $result['sauvegarde'] = 'Échouée';
    }
}

echo json_encode($result, JSON_PRETTY_PRINT);
?>
