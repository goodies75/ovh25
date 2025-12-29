<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gestion des requêtes OPTIONS pour CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Vérification de la méthode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit();
}

try {
    // Lecture du JSON envoyé
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data || !isset($data['pin'])) {
        echo json_encode(['success' => false, 'error' => 'PIN manquant']);
        exit();
    }
    
    $pin = trim($data['pin']);
    
    // Validation du PIN
    // SÉCURITÉ : PIN personnalisé d'Olivier
    $validPin = '@0149@'; // Code PIN personnalisé
    
    if ($pin === $validPin) {
        // PIN valide
        echo json_encode([
            'success' => true,
            'message' => 'Authentification réussie'
        ]);
    } else {
        // PIN invalide
        echo json_encode([
            'success' => false,
            'error' => 'Code PIN incorrect'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Erreur validation PIN: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur'
    ]);
}
?>
