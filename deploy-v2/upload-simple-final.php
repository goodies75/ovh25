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

// Créer le dossier uploads s'il n'existe pas
if (!is_dir('uploads')) {
    mkdir('uploads', 0777, true);
}

// Vérifier si un fichier a été envoyé
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['error' => 'Aucun fichier reçu ou erreur upload']);
    exit;
}

$file = $_FILES['image'];

// Vérifier le type de fichier
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['error' => 'Type de fichier non autorisé']);
    exit;
}

// Générer un nom unique
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$fileName = 'comic_' . time() . '.' . $extension;
$destination = 'uploads/' . $fileName;

if (move_uploaded_file($file['tmp_name'], $destination)) {
    echo json_encode([
        'success' => true,
        'url' => 'http://o-petit.com/' . $destination
    ]);
} else {
    echo json_encode(['error' => 'Échec de sauvegarde']);
}
?>
