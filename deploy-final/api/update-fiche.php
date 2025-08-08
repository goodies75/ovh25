<?php
/*
 * =====================================================
 * API UPDATE-FICHE.PHP - Modification des Comics
 * =====================================================
 * 
 * Cette API modifie un comic existant dans la collection JSON.
 */

// Configuration des headers CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gestion du CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Vérification de la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

// Lecture des données JSON envoyées
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validation des données
if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Données JSON invalides']);
    exit;
}

// Vérification de l'ID
if (!isset($data['id']) || empty($data['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID manquant']);
    exit;
}

$id = $data['id'];

// Chemin du fichier de données
$fichesFile = __DIR__ . '/fiches-data.json';

// Vérification de l'existence du fichier
if (!file_exists($fichesFile)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Fichier de données non trouvé']);
    exit;
}

// Lecture des fiches existantes
$existingData = file_get_contents($fichesFile);
$fiches = json_decode($existingData, true);

if (!is_array($fiches)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erreur de lecture des données']);
    exit;
}
// Recherche de la fiche à modifier
$ficheIndex = -1;
for ($i = 0; $i < count($fiches); $i++) {
    if ($fiches[$i]['id'] == $id) {
        $ficheIndex = $i;
        break;
    }
}

// Vérification si la fiche existe
if ($ficheIndex === -1) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Fiche non trouvée']);
    exit;
}

// Mise à jour des champs
$updatedFiche = $fiches[$ficheIndex];

// Liste des champs autorisés
$allowedFields = [
    'nom_serie', 'titre', 'numero', 'annee', 'numero_edition',
    'editeur', 'auteur_couverture', 'titre_secondaire',
    'etat', 'isbn', 'description', 'image_url'
];

// Mettre à jour les champs simples
foreach ($allowedFields as $field) {
    if (isset($data[$field])) {
        $updatedFiche[$field] = trim($data[$field]);
    }
}

// Traitement spécial pour autres_auteurs (tableau)
if (isset($data['autres_auteurs'])) {
    $updatedFiche['autres_auteurs'] = is_array($data['autres_auteurs']) 
        ? $data['autres_auteurs'] 
        : [];
}

// Mise à jour de la date de modification
$updatedFiche['updated_at'] = date('c');

// Remplacement dans le tableau
$fiches[$ficheIndex] = $updatedFiche;

// Sauvegarde dans le fichier
$success = file_put_contents($fichesFile, json_encode($fiches, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

if ($success) {
    echo json_encode([
        'success' => true,
        'message' => 'Fiche mise à jour avec succès',
        'fiche' => $updatedFiche
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erreur lors de la sauvegarde']);
}
?>
