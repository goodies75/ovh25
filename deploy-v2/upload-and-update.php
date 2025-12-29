<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

// Récupérer l'ID de la fiche
$ficheId = $_POST['id'] ?? null;
if (!$ficheId) {
    echo json_encode(['error' => 'ID de fiche manquant']);
    exit;
}

$imageUrl = null;

// Si une image est uploadée
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    // Créer le dossier uploads
    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
    }
    
    $file = $_FILES['image'];
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = 'comic_' . time() . '.' . $extension;
    $destination = 'uploads/' . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        $imageUrl = 'http://o-petit.com/' . $destination;
    } else {
        echo json_encode(['error' => 'Erreur upload image']);
        exit;
    }
}

// Mettre à jour la fiche
$jsonFile = 'data/fiches-data.json';
if (!file_exists($jsonFile)) {
    $jsonFile = 'fiches-data.json';
}

$fiches = json_decode(file_get_contents($jsonFile), true);
$found = false;

foreach ($fiches as $index => $fiche) {
    if ($fiche['id'] == $ficheId) {
        // Mettre à jour l'URL de l'image si uploadée
        if ($imageUrl) {
            $fiches[$index]['image_url'] = $imageUrl;
        }
        $found = true;
        break;
    }
}

if (!$found) {
    echo json_encode(['error' => 'Fiche non trouvée']);
    exit;
}

// Sauvegarder
if (file_put_contents($jsonFile, json_encode($fiches, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    echo json_encode(['success' => true, 'image_url' => $imageUrl]);
} else {
    echo json_encode(['error' => 'Erreur sauvegarde']);
}
?>
