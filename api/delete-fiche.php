<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gestion des requêtes OPTIONS pour CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Accepter DELETE et POST pour la suppression
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit();
}

try {
    // Lecture de l'ID à supprimer
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data || !isset($data['id'])) {
        echo json_encode(['success' => false, 'error' => 'ID manquant']);
        exit();
    }
    
    $idToDelete = $data['id'];
    
    // Charger les fiches existantes
    $fichesFile = __DIR__ . '/fiches.json';
    
    if (!file_exists($fichesFile)) {
        echo json_encode(['success' => false, 'error' => 'Fichier de données non trouvé']);
        exit();
    }
    
    $existingData = file_get_contents($fichesFile);
    $fiches = json_decode($existingData, true);
    
    if (!is_array($fiches)) {
        echo json_encode(['success' => false, 'error' => 'Données corrompues']);
        exit();
    }
    
    // Rechercher et supprimer la fiche
    $ficheToDelete = null;
    $newFiches = [];
    
    foreach ($fiches as $fiche) {
        if ($fiche['id'] == $idToDelete) {
            $ficheToDelete = $fiche;
        } else {
            $newFiches[] = $fiche;
        }
    }
    
    if ($ficheToDelete === null) {
        echo json_encode(['success' => false, 'error' => 'Comic non trouvé']);
        exit();
    }
    
    // Sauvegarder les données mises à jour
    $result = file_put_contents($fichesFile, json_encode($newFiches, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    if ($result === false) {
        echo json_encode(['success' => false, 'error' => 'Erreur lors de la sauvegarde']);
        exit();
    }
    
    // Suppression éventuelle de l'image associée
    if (isset($ficheToDelete['image']) && !empty($ficheToDelete['image'])) {
        $imagePath = dirname(__FILE__) . '/../uploads/' . basename($ficheToDelete['image']);
        if (file_exists($imagePath)) {
            @unlink($imagePath);
        }
        
        // Suppression de la miniature également
        $thumbPath = dirname(__FILE__) . '/../uploads/thumb_' . basename($ficheToDelete['image']);
        if (file_exists($thumbPath)) {
            @unlink($thumbPath);
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Comic supprimé avec succès',
        'deletedId' => $idToDelete,
        'title' => $ficheToDelete['titre'] ?? 'Sans titre'
    ]);
    
} catch (Exception $e) {
    error_log("Erreur suppression comic: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur lors de la suppression'
    ]);
}
?>
