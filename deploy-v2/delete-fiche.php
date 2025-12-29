<?php
/*
 * ==================== API DE SUPPRESSION DE COMICS ====================
 * 
 * OBJECTIF: Supprimer définitivement un comic de la collection
 * 
 * UTILISATION: 
 * - React envoie une requête DELETE avec l'ID du comic à supprimer
 * - L'API trouve le comic, le supprime et réorganise le fichier
 * - Confirmation de suppression renvoyée à React
 * 
 * MÉTHODE HTTP: DELETE uniquement
 * PARAMÈTRES: ID du comic (via URL ?id=123 ou dans le body JSON)
 * FICHIER DE DONNÉES: fiches-data.json (lecture/écriture)
 */

// ========== ÉTAPE 1: CONFIGURATION DES EN-TÊTES CORS ==========
/*
 * Mêmes en-têtes que les autres APIs pour assurer la compatibilité
 * avec React depuis un autre domaine
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');           // Autoriser React
header('Access-Control-Allow-Methods: DELETE, OPTIONS'); // Méthodes autorisées
header('Access-Control-Allow-Headers: Content-Type');     // En-têtes autorisés

// ========== ÉTAPE 2: GESTION DU CORS PREFLIGHT ==========
// Gérer les requêtes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(); // Pas de traitement supplémentaire nécessaire
}

// ========== ÉTAPE 3: VÉRIFICATION DE LA MÉTHODE ==========
// Cette API ne traite QUE les requêtes DELETE (suppression)
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405); // Méthode non autorisée
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit();
}

// ========== ÉTAPE 4: RÉCUPÉRATION DE L'ID DU COMIC ==========
/*
 * Deux méthodes possibles pour récupérer l'ID:
 * 1. Via URL: ?id=123 (pratique pour les tests)
 * 2. Via body JSON: {"id": 123} (standard REST)
 */
$id = null;

// Méthode 1: Récupérer l'ID depuis l'URL (?id=...)
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    // Méthode 2: Récupérer l'ID depuis le body JSON
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['id'])) {
        $id = $input['id'];
    }
}

// Vérifier qu'on a bien un ID
if (!$id) {
    http_response_code(400); // Mauvaise requête
    echo json_encode(['error' => 'ID manquant']);
    exit();
}

// ========== ÉTAPE 5: SUPPRESSION DU COMIC ==========
try {
    // Même fichier de données que les autres APIs
    $fichier = 'data/fiches-data.json';
    
    // ========== ÉTAPE 5.1: CHARGEMENT DE LA COLLECTION ==========
    $fiches = [];
    if (file_exists($fichier)) {
        $data = file_get_contents($fichier);
        $fiches = json_decode($data, true) ?: []; // [] si le fichier est vide
    }
    
    // ========== ÉTAPE 5.2: RECHERCHE ET SUPPRESSION ==========
    $ficheFound = false;        // Le comic a-t-il été trouvé ?
    $ficheSuppressee = null;    // Données du comic supprimé (pour confirmation)
    
    /*
     * Parcourir tous les comics pour trouver celui à supprimer
     * On utilise foreach avec $key pour pouvoir faire unset()
     */
    foreach ($fiches as $key => $fiche) {
        if ($fiche['id'] == $id) { // Comparaison souple (123 == "123")
            $ficheSuppressee = $fiche; // Sauvegarder les données pour la réponse
            unset($fiches[$key]);      // Supprimer du tableau
            $ficheFound = true;
            break; // Arrêter la recherche, on a trouvé
        }
    }
    
    // ========== ÉTAPE 5.3: VÉRIFICATION ==========
    if (!$ficheFound) {
        http_response_code(404); // Non trouvé
        echo json_encode(['error' => 'Comic non trouvé avec ID: ' . $id]);
        exit();
    }
    
    // ========== ÉTAPE 6: RÉORGANISATION DU TABLEAU ==========
    /*
     * Après unset(), le tableau a des "trous" dans les indices
     * Exemple: [0, 1, 3, 4] au lieu de [0, 1, 2, 3]
     * array_values() remet les indices en ordre
     */
    $fiches = array_values($fiches);
    
    // ========== ÉTAPE 7: SAUVEGARDE DU FICHIER ==========
    // Réenregistrer la collection sans le comic supprimé
    $result = file_put_contents(
        $fichier, 
        json_encode($fiches, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
    
    if ($result === false) {
        throw new Exception('Impossible de sauvegarder les modifications');
    }
    
    // ========== ÉTAPE 8: CONFIRMATION DE SUCCÈS ==========
    /*
     * Informer React que la suppression a réussi
     * Inclure les détails du comic supprimé et le nouveau total
     */
    echo json_encode([
        'success' => true,
        'message' => 'Comic supprimé avec succès',
        'deleted_fiche' => $ficheSuppressee,      // Données du comic supprimé
        'remaining_count' => count($fiches)        // Nombre de comics restants
    ]);
    
} catch (Exception $e) {
    // ========== GESTION DES ERREURS ==========
    /*
     * En cas de problème technique (fichier non accessible, etc.)
     * HTTP 500 = erreur serveur interne
     */
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur: ' . $e->getMessage()]);
}
?>
