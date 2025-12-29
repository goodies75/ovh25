<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

try {
    // Lire les données JSON
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        throw new Exception('Données JSON invalides');
    }
    
    // Vérifier que l'ID est présent
    if (!isset($data['id']) || empty($data['id'])) {
        throw new Exception('ID manquant');
    }
    
    $id = intval($data['id']);
    
    // Connexion à la base de données
    $host = 'localhost';
    $dbname = 'o-petit_comics';
    $username = 'o-petit_comics';
    $password = 'UQv4F2wvQSCA3wnG';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Préparer les données pour la mise à jour
    $updateData = [];
    $params = ['id' => $id];
    
    // Liste des champs autorisés à être mis à jour
    $allowedFields = [
        'nom_serie', 'titre', 'numero', 'annee', 'numero_edition', 
        'editeur', 'auteur_couverture', 'titre_secondaire', 
        'etat', 'isbn', 'description', 'image_url'
    ];
    
    foreach ($allowedFields as $field) {
        if (isset($data[$field])) {
            $updateData[] = "$field = :$field";
            $params[$field] = $data[$field];
        }
    }
    
    // Gérer le champ autres_auteurs (array)
    if (isset($data['autres_auteurs'])) {
        $updateData[] = "autres_auteurs = :autres_auteurs";
        $params['autres_auteurs'] = is_array($data['autres_auteurs']) 
            ? json_encode($data['autres_auteurs']) 
            : $data['autres_auteurs'];
    }
    
    if (empty($updateData)) {
        throw new Exception('Aucune donnée à mettre à jour');
    }
    
    // Construire et exécuter la requête
    $sql = "UPDATE fiches SET " . implode(', ', $updateData) . " WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($params);
    
    if ($result && $stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true, 
            'message' => 'Fiche mise à jour avec succès',
            'updated_id' => $id
        ]);
    } else {
        // Vérifier si l'ID existe
        $checkStmt = $pdo->prepare("SELECT id FROM fiches WHERE id = :id");
        $checkStmt->execute(['id' => $id]);
        
        if ($checkStmt->rowCount() === 0) {
            throw new Exception('Fiche non trouvée');
        } else {
            echo json_encode([
                'success' => true, 
                'message' => 'Aucune modification nécessaire',
                'updated_id' => $id
            ]);
        }
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => 'Erreur base de données: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
}
?>
