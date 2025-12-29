<?php
/*
 * =====================================================
 * API UPDATE-FICHE.PHP - Modification d'un Comic
 * =====================================================
 * 
 * Cette API modifie un comic existant dans votre collection
 * stockée dans un fichier JSON. Elle met à jour les informations
 * d'un comic spécifique identifié par son ID.
 */

// ========== ÉTAPE 1: CONFIGURATION DES HEADERS ==========
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// ========== ÉTAPE 2: GESTION DU CORS PREFLIGHT ==========
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

// ========== ÉTAPE 3: VÉRIFICATION DE LA MÉTHODE ==========
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

try {
    // ========== ÉTAPE 4: LECTURE DES DONNÉES ENVOYÉES ==========
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        throw new Exception('Données JSON invalides');
    }
    
    // ========== ÉTAPE 5: VÉRIFICATION DE L'ID ==========
    if (!isset($data['id']) || empty($data['id'])) {
        throw new Exception('ID manquant');
    }
    
    $id = $data['id']; // Garder le type original (peut être string ou number)
    
    // ========== ÉTAPE 6: DÉFINITION DU FICHIER DE DONNÉES ==========
    $fichesFile = __DIR__ . '/fiches-data.json';
    
    // ========== ÉTAPE 7: CHARGEMENT DES DONNÉES EXISTANTES ==========
    if (!file_exists($fichesFile)) {
        throw new Exception('Fichier de données non trouvé');
    }
    
    $existingData = file_get_contents($fichesFile);
    if ($existingData === false) {
        throw new Exception('Impossible de lire le fichier de données');
    }
    
    $fiches = json_decode($existingData, true);
    if (!is_array($fiches)) {
        throw new Exception('Format de données invalide');
    }
    
    // ========== ÉTAPE 8: RECHERCHE DE LA FICHE À MODIFIER ==========
    $ficheIndex = -1;
    foreach ($fiches as $index => $fiche) {
        if (isset($fiche['id']) && $fiche['id'] == $id) {
            $ficheIndex = $index;
            break;
        }
    }
    
    if ($ficheIndex === -1) {
        throw new Exception('Comic non trouvé avec ID: ' . $id);
    }
    
    // ========== ÉTAPE 9: MISE À JOUR DES CHAMPS ==========
    $originalFiche = $fiches[$ficheIndex];
    $updatedFiche = $originalFiche; // Copie pour modification
    
    // Si les données viennent du formulaire React avec structure comics,
    // les convertir vers structure simple
    if (isset($data['nom_serie'])) {
        // Construire le nouveau titre
        $titre = $data['nom_serie'];
        if (!empty($data['numero'])) {
            $titre .= ' #' . $data['numero'];
        }
        if (!empty($data['titre_secondaire'])) {
            $titre .= ' - ' . $data['titre_secondaire'];
        }
        $updatedFiche['titre'] = $titre;
        
        // Construire la nouvelle description
        $description = '';
        if (!empty($data['auteur_couverture'])) {
            $description .= 'Auteur : ' . $data['auteur_couverture'] . "\n";
        }
        if (!empty($data['editeur'])) {
            $description .= 'Éditeur : ' . $data['editeur'] . "\n";
        }
        if (!empty($data['annee'])) {
            $description .= 'Année : ' . $data['annee'] . "\n";
        }
        if (!empty($data['etat'])) {
            $description .= 'État : ' . $data['etat'] . "\n";
        }
        if (!empty($data['description'])) {
            $description .= "\n" . $data['description'];
        }
        $updatedFiche['description'] = trim($description);
        
        // Image
        if (isset($data['image_url'])) {
            $updatedFiche['image_url'] = $data['image_url'];
        }
    } else {
        // Structure simple directe
        if (isset($data['titre'])) {
            $updatedFiche['titre'] = $data['titre'];
        }
        if (isset($data['description'])) {
            $updatedFiche['description'] = $data['description'];
        }
        if (isset($data['image_url'])) {
            $updatedFiche['image_url'] = $data['image_url'];
        }
    }
    
    $hasChanges = true;
    
    // ========== ÉTAPE 10: SAUVEGARDE DES MODIFICATIONS ==========
    $fiches[$ficheIndex] = $updatedFiche;
    
    $jsonOutput = json_encode($fiches, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($jsonOutput === false) {
        throw new Exception('Erreur lors de l\'encodage JSON');
    }
    
    if (file_put_contents($fichesFile, $jsonOutput) === false) {
        throw new Exception('Impossible de sauvegarder les modifications');
    }
    
    // ========== ÉTAPE 11: RÉPONSE DE SUCCÈS ==========
    echo json_encode([
        'success' => true, 
        'message' => 'Comic mis à jour avec succès',
        'updated_id' => $id,
        'updated_data' => $updatedFiche
    ]);
    
} catch (Exception $e) {
    // ========== GESTION DES ERREURS ==========
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
}
?>
