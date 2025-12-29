<?php
// Configuration pour voir toutes les erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Headers CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gestion OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    echo json_encode(['status' => 'OPTIONS OK']);
    exit();
}

// Méthode POST uniquement
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée', 'method' => $_SERVER['REQUEST_METHOD']]);
    exit();
}

try {
    // Log de debug
    $debug = [
        'timestamp' => date('Y-m-d H:i:s'),
        'method' => $_SERVER['REQUEST_METHOD'],
        'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'non défini',
        'content_length' => $_SERVER['CONTENT_LENGTH'] ?? 'non défini'
    ];
    
    // Lecture des données
    $input = file_get_contents('php://input');
    $debug['input_size'] = strlen($input);
    
    if (empty($input)) {
        throw new Exception('Aucune donnée reçue');
    }
    
    // Parse JSON
    $data = json_decode($input, true);
    $debug['json_parse'] = ($data !== null);
    $debug['json_error'] = json_last_error_msg();
    
    if ($data === null) {
        throw new Exception('Données JSON invalides: ' . json_last_error_msg());
    }
    
    if (!isset($data['imageData'])) {
        throw new Exception('Champ imageData manquant. Reçu: ' . implode(', ', array_keys($data)));
    }
    
    $imageData = $data['imageData'];
    $filename = $data['filename'] ?? 'upload.jpg';
    
    $debug['filename'] = $filename;
    $debug['imageData_length'] = strlen($imageData);
    
    // Créer dossier uploads
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Décoder base64
    if (strpos($imageData, 'data:') === 0) {
        $parts = explode(',', $imageData);
        $imageData = end($parts);
    }
    
    $decoded = base64_decode($imageData);
    if ($decoded === false) {
        throw new Exception('Impossible de décoder base64');
    }
    
    $debug['decoded_size'] = strlen($decoded);
    
    // Sauvegarder
    $uniqueFilename = pathinfo($filename, PATHINFO_FILENAME) . '_' . time() . '.' . (pathinfo($filename, PATHINFO_EXTENSION) ?: 'jpg');
    $filepath = $uploadDir . $uniqueFilename;
    
    $written = file_put_contents($filepath, $decoded);
    if ($written === false) {
        throw new Exception('Impossible d\'écrire le fichier');
    }
    
    $debug['file_written'] = $written;
    $debug['file_path'] = $filepath;
    
    // Succès !
    echo json_encode([
        'success' => true,
        'message' => 'Upload réussi',
        'url' => './uploads/' . $uniqueFilename,
        'filename' => $uniqueFilename,
        'size' => $written,
        'debug' => $debug
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'debug' => $debug ?? []
    ]);
}
?>
