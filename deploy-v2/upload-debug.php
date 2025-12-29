<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Logger pour débugger les appels de React
$logFile = 'upload-debug.log';
$timestamp = date('Y-m-d H:i:s');

// Capturer toutes les données reçues
$logData = [
    'timestamp' => $timestamp,
    'method' => $_SERVER['REQUEST_METHOD'],
    'headers' => getallheaders(),
    'files' => $_FILES,
    'post' => $_POST,
    'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'N/A',
    'content_length' => $_SERVER['CONTENT_LENGTH'] ?? 'N/A'
];

file_put_contents($logFile, json_encode($logData, JSON_PRETTY_PRINT) . "\n\n", FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

// Vérifier si le fichier est reçu
if (!isset($_FILES['image'])) {
    $error = 'Aucun fichier image reçu';
    file_put_contents($logFile, "ERREUR: $error\n\n", FILE_APPEND);
    echo json_encode(['error' => $error]);
    exit;
}

$file = $_FILES['image'];

// Logger les détails du fichier
file_put_contents($logFile, "FICHIER REÇU: " . json_encode($file, JSON_PRETTY_PRINT) . "\n\n", FILE_APPEND);

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
    file_put_contents($logFile, "ERREUR UPLOAD: $errorMsg\n\n", FILE_APPEND);
    echo json_encode(['error' => $errorMsg]);
    exit;
}

// Vérifier le type de fichier
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($file['type'], $allowedTypes)) {
    $error = 'Type de fichier non autorisé: ' . $file['type'];
    file_put_contents($logFile, "ERREUR TYPE: $error\n\n", FILE_APPEND);
    echo json_encode(['error' => $error]);
    exit;
}

// Vérifier/créer le dossier uploads
$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        $error = 'Impossible de créer le dossier uploads';
        file_put_contents($logFile, "ERREUR DOSSIER: $error\n\n", FILE_APPEND);
        echo json_encode(['error' => $error]);
        exit;
    }
}

if (!is_writable($uploadDir)) {
    $error = 'Dossier uploads non accessible en écriture';
    file_put_contents($logFile, "ERREUR PERMISSIONS: $error\n\n", FILE_APPEND);
    echo json_encode(['error' => $error]);
    exit;
}

// Générer nom de fichier unique
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$fileName = 'debug_' . uniqid() . '.' . $extension;
$filePath = $uploadDir . $fileName;

// Tentative de déplacement du fichier
if (move_uploaded_file($file['tmp_name'], $filePath)) {
    $result = [
        'success' => true,
        'url' => 'http://o-petit.com/uploads/' . $fileName
    ];
    file_put_contents($logFile, "SUCCÈS: " . json_encode($result) . "\n\n", FILE_APPEND);
    echo json_encode($result);
} else {
    $error = 'Échec move_uploaded_file - vérifiez les permissions';
    file_put_contents($logFile, "ERREUR MOVE: $error\n\n", FILE_APPEND);
    echo json_encode(['error' => $error]);
}
?>
