<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Diagnostic simple et robuste
$result = [
    'timestamp' => date('Y-m-d H:i:s'),
    'method' => $_SERVER['REQUEST_METHOD'],
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'max_execution_time' => ini_get('max_execution_time')
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Vérifier si le fichier est reçu
    if (!isset($_FILES['image'])) {
        $result['error'] = 'Aucun fichier image reçu';
        $result['files_received'] = $_FILES;
        echo json_encode($result);
        exit;
    }
    
    $file = $_FILES['image'];
    
    $result['file_info'] = [
        'name' => $file['name'],
        'type' => $file['type'],
        'size' => $file['size'],
        'error' => $file['error'],
        'tmp_name_exists' => file_exists($file['tmp_name'])
    ];
    
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
        
        $result['error'] = $errors[$file['error']] ?? 'Erreur upload inconnue: ' . $file['error'];
        echo json_encode($result);
        exit;
    }
    
    // Vérifier le type de fichier
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        $result['error'] = 'Type de fichier non autorisé: ' . $file['type'];
        echo json_encode($result);
        exit;
    }
    
    // Vérifier/créer le dossier uploads
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            $result['error'] = 'Impossible de créer le dossier uploads';
            echo json_encode($result);
            exit;
        }
    }
    
    if (!is_writable($uploadDir)) {
        $result['error'] = 'Dossier uploads non accessible en écriture';
        echo json_encode($result);
        exit;
    }
    
    // Générer nom de fichier unique
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = 'img_' . uniqid() . '.' . $extension;
    $filePath = $uploadDir . $fileName;
    
    // Tentative de déplacement du fichier
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        $result['success'] = true;
        $result['url'] = 'http://o-petit.com/uploads/' . $fileName;
        $result['file_path'] = $filePath;
        $result['file_size_saved'] = filesize($filePath);
    } else {
        $result['error'] = 'Échec move_uploaded_file - permissions ?';
        $result['upload_dir_permissions'] = substr(sprintf('%o', fileperms($uploadDir)), -4);
    }
    
} else {
    $result['error'] = 'Méthode non autorisée - utiliser POST';
}

echo json_encode($result);
?>
