<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gérer les requêtes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Vérifier que c'est une requête DELETE
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit();
}

// Récupérer l'ID depuis l'URL ou le body
$id = null;

// Essayer de récupérer l'ID depuis l'URL (?id=...)
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    // Essayer de récupérer l'ID depuis le body JSON
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['id'])) {
        $id = $input['id'];
    }
}

if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'ID manquant']);
    exit();
}

try {
    $fichier = 'fiches-data.json';
    
    // Charger les données existantes
    $fiches = [];
    if (file_exists($fichier)) {
        $data = file_get_contents($fichier);
        $fiches = json_decode($data, true) ?: [];
    }
    
    // Chercher et supprimer la fiche
    $ficheFound = false;
    $ficheSuppressee = null;
    
    foreach ($fiches as $key => $fiche) {
        if ($fiche['id'] == $id) {
            $ficheSuppressee = $fiche;
            unset($fiches[$key]);
            $ficheFound = true;
            break;
        }
    }
    
    if (!$ficheFound) {
        http_response_code(404);
        echo json_encode(['error' => 'Fiche non trouvée']);
        exit();
    }
    
    // Réindexer le tableau
    $fiches = array_values($fiches);
    
    // Sauvegarder les données mises à jour
    $result = file_put_contents($fichier, json_encode($fiches, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    if ($result === false) {
        throw new Exception('Erreur lors de la sauvegarde');
    }
    
    // Réponse de succès
    echo json_encode([
        'success' => true,
        'message' => 'Fiche supprimée avec succès',
        'deleted_fiche' => $ficheSuppressee,
        'remaining_count' => count($fiches)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur: ' . $e->getMessage()]);
}
?>
