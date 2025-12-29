<?php
/*
 * =====================================================
 * API UPDATE-FICHE.PHP - Modification de Comics JSON
 * =====================================================
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

// Lecture des données JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Données JSON invalides']);
    exit;
}

if (!isset($data['id']) || empty($data['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID manquant']);
    exit;
}

$id = intval($data['id']);

// Localisation du fichier JSON
$jsonFile = 'data/fiches-data.json';
if (!file_exists($jsonFile)) {
    $jsonFile = 'fiches-data.json';
}
$fichesFile = __DIR__ . '/' . $jsonFile;

if (!file_exists($fichesFile)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Fichier de données introuvable']);
    exit;
}

// Lecture des données existantes
$fiches = json_decode(file_get_contents($fichesFile), true);

if (!$fiches) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erreur de lecture des données']);
    exit;
}

// Recherche de la fiche
$ficheIndex = -1;
foreach ($fiches as $index => $fiche) {
    if ($fiche['id'] == $id) {
        $ficheIndex = $index;
        break;
    }
}

if ($ficheIndex === -1) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Fiche non trouvée']);
    exit;
}

// Mise à jour des champs
$fieldsToUpdate = [
    'nom_serie', 'titre', 'numero', 'annee', 'numero_edition', 
    'editeur', 'auteur_couverture', 'titre_secondaire', 
    'etat', 'isbn', 'description', 'image_url', 'autres_auteurs'
];

$updated = false;
foreach ($fieldsToUpdate as $field) {
    if (isset($data[$field])) {
        $fiches[$ficheIndex][$field] = $data[$field];
        $updated = true;
    }
}

if (!$updated) {
    echo json_encode(['success' => true, 'message' => 'Aucune modification nécessaire']);
    exit;
}

// Sauvegarde
$result = file_put_contents($fichesFile, json_encode($fiches, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

if ($result === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erreur lors de la sauvegarde']);
    exit;
}

echo json_encode([
    'success' => true, 
    'message' => 'Fiche mise à jour avec succès',
    'updated_id' => $id
]);
?>
