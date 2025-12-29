<?php
// Upload ultra-simple sans vérifications
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Debug : afficher tout ce qui est reçu
    $debug = [
        'FILES' => $_FILES,
        'POST' => $_POST,
        'server_method' => $_SERVER['REQUEST_METHOD']
    ];
    
    // Si pas de fichier, afficher le debug
    if (empty($_FILES)) {
        echo json_encode(['error' => 'Aucun fichier reçu', 'debug' => $debug]);
        exit;
    }
    
    // Créer le dossier
    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
    }
    
    $file = $_FILES['image'];
    $fileName = 'test_' . time() . '.jpg';
    $destination = 'uploads/' . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        echo json_encode(['success' => true, 'url' => $destination, 'debug' => $debug]);
    } else {
        echo json_encode(['error' => 'Échec move_uploaded_file', 'debug' => $debug]);
    }
    
} else {
    echo json_encode(['error' => 'Méthode: ' . $_SERVER['REQUEST_METHOD']]);
}
?>
