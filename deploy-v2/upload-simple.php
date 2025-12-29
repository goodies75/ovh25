<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

// Vérifier si le fichier est reçu
if (!isset($_FILES['image'])) {
    echo json_encode(['error' => 'Aucun fichier image reçu']);
    exit;
}

$file = $_FILES['image'];

// Vérifier l'erreur d'upload
if ($file['error'] !== UPLOAD_ERR_OK) {
    $errors = [
        UPLOAD_ERR_INI_SIZE => 'Fichier trop volumineux (limite serveur)',
        UPLOAD_ERR_FORM_SIZE => 'Fichier trop volumineux (limite formulaire)', 
        UPLOAD_ERR_PARTIAL => 'Upload partiel seulement',
        UPLOAD_ERR_NO_FILE => 'Aucun fichier uploadé',
        UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant',
        UPLOAD_ERR_CANT_WRITE => 'Impossible écrire sur disque',
        UPLOAD_ERR_EXTENSION => 'Extension PHP bloque upload'
    ];
    
    $errorMsg = $errors[$file['error']] ?? 'Erreur upload inconnue: ' . $file['error'];
    echo json_encode(['error' => $errorMsg]);
    exit;
}

// Vérifier le type de fichier
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['error' => 'Type de fichier non autorisé: ' . $file['type']]);
    exit;
}

// Vérifier/créer le dossier uploads
$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        echo json_encode(['error' => 'Impossible de créer le dossier uploads']);
        exit;
    }
}

if (!is_writable($uploadDir)) {
    echo json_encode(['error' => 'Dossier uploads non accessible en écriture']);
    exit;
}

// Générer nom de fichier unique
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$fileName = 'img_' . uniqid() . '.' . $extension;
$filePath = $uploadDir . $fileName;

// Tentative de déplacement du fichier
if (move_uploaded_file($file['tmp_name'], $filePath)) {
    echo json_encode([
        'success' => true,
        'url' => 'http://o-petit.com/uploads/' . $fileName
    ]);
} else {
    echo json_encode(['error' => 'Échec move_uploaded_file - vérifiez les permissions']);
}
?>
