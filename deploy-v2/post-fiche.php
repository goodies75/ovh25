<?php
/*
 * =====================================================
 * API POST-FICHE.PHP - Ajout de Nouveaux Comics
 * =====================================================
 * 
 * Cette API reçoit les données d'un nouveau comic depuis
 * React et l'ajoute à votre collection dans le fichier JSON.
 */

// ========== ÉTAPE 1: CONFIGURATION DES HEADERS ==========
// Indique que la réponse sera en JSON
header('Content-Type: application/json');

// Configuration CORS pour autoriser React à envoyer des données
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS'); // Autorise POST (création)
header('Access-Control-Allow-Headers: Content-Type');

// ========== ÉTAPE 2: GESTION DU CORS PREFLIGHT ==========
// Même principe que get-fiches.php
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); // "OK, tu peux envoyer tes données"
    exit(0);
}

// ========== ÉTAPE 3: VÉRIFICATION DE LA MÉTHODE ==========
// Cette API ne traite QUE les requêtes POST (création)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Erreur "Méthode non autorisée"
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// ========== ÉTAPE 4: LECTURE DES DONNÉES ENVOYÉES ==========
/*
 * React envoie les données du formulaire en JSON dans le body
 * php://input contient les données brutes de la requête
 */
$input = file_get_contents('php://input');
$data = json_decode($input, true); // Convertit JSON → tableau PHP

// ========== ÉTAPE 5: VALIDATION DES DONNÉES ==========
// Vérifier que le JSON est valide
if (!$data) {
    http_response_code(400); // Erreur "Mauvaise requête"
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

// Vérifier que le champ obligatoire est présent
if (empty($data['nom_serie'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Le nom de série est requis']);
    exit;
}

// ========== ÉTAPE 6: PRÉPARATION DU FICHIER DE DONNÉES ==========
// Même logique que les autres APIs
$jsonFile = 'data/fiches-data.json';
if (!file_exists($jsonFile)) {
    $jsonFile = 'fiches-data.json';
}
$fichesFile = __DIR__ . '/' . $jsonFile;
$fiches = []; // Tableau vide par défaut

// ========== ÉTAPE 7: CHARGEMENT DES COMICS EXISTANTS ==========
// Lire les fiches existantes pour les préserver
if (file_exists($fichesFile)) {
    $existingData = file_get_contents($fichesFile); // Lit le fichier complet
    $fiches = json_decode($existingData, true) ?: []; // Convertit en tableau PHP
    /*
     * L'opérateur ?: signifie "ou sinon"
     * Si json_decode échoue → utilise [] (tableau vide)
     */
}

// ========== ÉTAPE 8: CRÉATION DE LA NOUVELLE FICHE ==========
/*
 * On construit un objet comic complet avec tous les champs
 * en utilisant les données envoyées par React
 */
$newFiche = [
    // ID unique: timestamp en millisecondes + nombre aléatoire
    'id' => time() * 1000 + rand(0, 999),
    
    // Champs obligatoires
    'nom_serie' => trim($data['nom_serie']), // trim() enlève les espaces
    
    // Champs optionnels avec valeurs par défaut
    'numero' => isset($data['numero']) ? trim($data['numero']) : '',
    'annee' => isset($data['annee']) ? trim($data['annee']) : '',
    'numero_edition' => isset($data['numero_edition']) ? trim($data['numero_edition']) : '',
    'editeur' => isset($data['editeur']) ? trim($data['editeur']) : '',
    'auteur_couverture' => isset($data['auteur_couverture']) ? trim($data['auteur_couverture']) : '',
    
    // Autres auteurs: doit être un tableau
    'autres_auteurs' => isset($data['autres_auteurs']) && is_array($data['autres_auteurs']) ? $data['autres_auteurs'] : [],
    
    'titre_secondaire' => isset($data['titre_secondaire']) ? trim($data['titre_secondaire']) : '',
    'etat' => isset($data['etat']) ? trim($data['etat']) : 'Très bon', // Valeur par défaut
    'isbn' => isset($data['isbn']) ? trim($data['isbn']) : '',
    'description' => isset($data['description']) ? trim($data['description']) : '',
    'image_url' => isset($data['image_url']) ? trim($data['image_url']) : '',
    
    // Date de création automatique au format ISO 8601
    'created_at' => date('c') // Ex: "2025-08-07T14:30:00+02:00"
];

// ========== ÉTAPE 9: AJOUT À LA COLLECTION ==========
/*
 * Ajouter le nouveau comic au début du tableau
 * array_unshift() insert au début (plus récent en premier)
 */
array_unshift($fiches, $newFiche);

// ========== ÉTAPE 10: SAUVEGARDE DANS LE FICHIER ==========
/*
 * Écrire tout le tableau mis à jour dans le fichier JSON
 * Même options que get-fiches.php pour la lisibilité
 */
$success = file_put_contents($fichesFile, json_encode($fiches, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// ========== ÉTAPE 11: RÉPONSE À REACT ==========
if ($success) {
    // Succès: Renvoie la fiche créée avec son ID
    echo json_encode([
        'success' => true,
        'fiche' => $newFiche,
        'message' => 'Comic ajouté avec succès!'
    ]);
} else {
    // Échec: Erreur d'écriture fichier
    http_response_code(500); // Erreur serveur interne
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la sauvegarde'
    ]);
}

/*
 * =====================================================
 * RÉSUMÉ DU WORKFLOW:
 * =====================================================
 * 
 * 1. React envoie POST avec données formulaire
 * 2. PHP valide les données reçues
 * 3. PHP charge la collection existante
 * 4. PHP crée nouvel objet comic avec ID unique
 * 5. PHP ajoute au début du tableau (plus récent)
 * 6. PHP sauvegarde tout dans fiches-data.json
 * 7. PHP renvoie succès/échec à React
 * 8. React rafraîchit la liste ou affiche erreur
 */
?>
