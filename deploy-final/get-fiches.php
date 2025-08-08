<?php
/*
 * =====================================================
 * API GET-FICHES.PHP - Récupération des Comics
 * =====================================================
 * 
 * Cette API récupère la liste complète de votre collection
 * de comics depuis un fichier JSON. Elle gère aussi
 * l'initialisation avec des données d'exemple.
 */

// ========== ÉTAPE 1: CONFIGURATION DES HEADERS ==========
// Indique au navigateur que la réponse sera en JSON
header('Content-Type: application/json');

// Autorise les requêtes depuis votre site React (CORS)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// ========== ÉTAPE 2: GESTION DU CORS PREFLIGHT ==========
// Les navigateurs envoient une requête OPTIONS avant GET/POST
// pour vérifier si l'API autorise les requêtes cross-origin
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); // Répond "OK, tu peux continuer"
    exit(0); // Arrête ici sans traiter de données
}

// ========== ÉTAPE 3: VÉRIFICATION DE LA MÉTHODE ==========
// Cette API ne traite QUE les requêtes GET (lecture seule)
// Si quelqu'un essaie POST/PUT/DELETE, on refuse
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Code erreur "Méthode non autorisée"
    echo json_encode(['error' => 'Method not allowed']);
    exit; // Arrêt avec message d'erreur
}

// ========== ÉTAPE 4: DÉFINITION DU FICHIER DE DONNÉES ==========
// __DIR__ = dossier actuel où se trouve ce script PHP
// Les comics sont stockés dans "fiches-data.json"
$fichesFile = __DIR__ . '/fiches-data.json';

// ========== ÉTAPE 5: GESTION DU PREMIER DÉMARRAGE ==========
// Vérifier si le fichier existe
if (!file_exists($fichesFile)) {
    /*
     * PREMIÈRE UTILISATION: Le fichier n'existe pas encore
     * On crée automatiquement des données d'exemple pour
     * que votre site affiche quelque chose dès le démarrage
     */
    
    // Données d'exemple avec 2 comics populaires
    $defaultFiches = [
        [
            'id' => 1722787200000,              // ID unique (timestamp)
            'titre' => 'Spider-Man: Into the Spider-Verse',  // Titre du comic
            'description' => 'Une aventure révolutionnaire dans le multivers avec Miles Morales',
            'image_url' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=300&h=400&fit=crop', // Image de couverture
            'created_at' => '2024-08-04T12:00:00+00:00'  // Date d'ajout
        ],
        [
            'id' => 1722873600000,
            'titre' => 'Batman: The Dark Knight Returns',
            'description' => 'Le retour épique de Batman dans une Gotham dystopique',
            'image_url' => 'https://images.unsplash.com/photo-1543832923-44667a44c804?w=300&h=400&fit=crop',
            'created_at' => '2024-08-05T12:00:00+00:00'
        ]
    ];
    
    /*
     * CRÉATION DU FICHIER JSON:
     * - JSON_PRETTY_PRINT: Format lisible (avec indentation)
     * - JSON_UNESCAPED_UNICODE: Préserve les accents français
     */
    file_put_contents($fichesFile, json_encode($defaultFiches, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    // Renvoie immédiatement les données créées à React
    echo json_encode($defaultFiches);
    exit; // Mission accomplie, on s'arrête ici
}

// ========== ÉTAPE 6: LECTURE DES DONNÉES EXISTANTES ==========
/*
 * UTILISATION NORMALE: Le fichier existe déjà
 * On lit le contenu et on le transforme en tableau PHP
 */

// Lire le contenu du fichier JSON
$fiches = json_decode(file_get_contents($fichesFile), true);
/*
 * Explication json_decode():
 * - file_get_contents($fichesFile): Lit tout le fichier en string
 * - json_decode(..., true): Transforme JSON en tableau PHP
 * - Le "true" signifie "retourne un tableau, pas un objet"
 */

// ========== ÉTAPE 7: GESTION DES ERREURS ==========
// Si le fichier JSON est corrompu ou vide
if (!$fiches) {
    echo json_encode([]); // Renvoie une liste vide []
    exit; // Évite une erreur fatale
}

// ========== ÉTAPE 8: TRI PAR DATE (PLUS RÉCENT EN PREMIER) ==========
/*
 * Fonction de tri personnalisée:
 * Compare les dates de création de 2 comics et les ordonne
 * du plus récent au plus ancien
 */
usort($fiches, function($a, $b) {
    /*
     * strtotime(): Convertit "2024-08-05T12:00:00+00:00" en timestamp
     * $b - $a: Ordre décroissant (récent en premier)
     * $a - $b: Ordre croissant (ancien en premier)
     */
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

// ========== ÉTAPE 9: ENVOI DE LA RÉPONSE ==========
/*
 * Convertit le tableau PHP en JSON et l'envoie à React
 * React recevra un tableau de tous vos comics triés
 */
echo json_encode($fiches);

/*
 * =====================================================
 * RÉSUMÉ DU WORKFLOW:
 * =====================================================
 * 
 * 1. React appelle fetch('./get-fiches.php')
 * 2. PHP vérifie si fiches-data.json existe
 * 3. Si NON: Crée données d'exemple + renvoie
 * 4. Si OUI: Lit le fichier + trie par date + renvoie
 * 5. React reçoit la liste et affiche les cartes
 * 
 * FICHIER CRÉÉ: fiches-data.json
 * FORMAT: [{"id":123,"titre":"Mon Comic",...}, {...}]
 */
?>
