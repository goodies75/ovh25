<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Test complet du processus de création de fiche

echo "=== TEST UPLOAD ===\n";

// 1. Tester l'upload d'image
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_image'])) {
    $uploadDir = 'uploads/';
    $file = $_FILES['test_image'];
    
    echo "Fichier reçu: " . $file['name'] . "\n";
    echo "Type: " . $file['type'] . "\n";
    echo "Taille: " . $file['size'] . "\n";
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = "test_" . uniqid() . '.' . $extension;
    $filePath = $uploadDir . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        $fullUrl = 'http://o-petit.com/uploads/' . $fileName;
        echo "Upload réussi!\n";
        echo "Nom fichier: $fileName\n";
        echo "URL complète: $fullUrl\n";
        
        // Créer une fiche de test
        $jsonFile = 'data/fiches-data.json';
        if (!file_exists($jsonFile)) {
            $jsonFile = 'fiches-data.json';
        }
        
        $fiches = [];
        if (file_exists($jsonFile)) {
            $fiches = json_decode(file_get_contents($jsonFile), true) ?: [];
        }
        
        $nouvelleFiche = [
            'id' => time(),
            'nom_serie' => 'TEST DIAGNOSTIC',
            'numero' => '1',
            'annee' => '2025',
            'numero_edition' => '1',
            'editeur' => 'Test Editor',
            'auteur_couverture' => 'Test Author',
            'autres_auteurs' => [],
            'titre_secondaire' => 'Test diagnostic upload',
            'etat' => 'Neuf',
            'isbn' => '123456789',
            'description' => 'Fiche créée pour tester le diagnostic',
            'image_url' => $fullUrl,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $fiches[] = $nouvelleFiche;
        
        if (file_put_contents($jsonFile, json_encode($fiches, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
            echo "Fiche créée avec succès!\n";
            echo "Image URL stockée: " . $nouvelleFiche['image_url'] . "\n";
        } else {
            echo "Erreur sauvegarde fiche\n";
        }
        
    } else {
        echo "Erreur upload\n";
    }
    exit;
}

// 2. Afficher les dernières fiches créées
$jsonFile = 'data/fiches-data.json';
if (!file_exists($jsonFile)) {
    $jsonFile = 'fiches-data.json';
}

$result = ['status' => 'Diagnostic des fiches récentes'];

if (file_exists($jsonFile)) {
    $fiches = json_decode(file_get_contents($jsonFile), true);
    if ($fiches) {
        // Prendre les 3 dernières fiches
        $dernieresFiches = array_slice($fiches, -3);
        
        foreach ($dernieresFiches as $i => $fiche) {
            $result["fiche_$i"] = [
                'id' => $fiche['id'],
                'nom_serie' => $fiche['nom_serie'] ?? 'N/A',
                'image_url' => $fiche['image_url'] ?? 'N/A',
                'created_at' => $fiche['created_at'] ?? 'N/A'
            ];
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Diagnostic Upload</title>
</head>
<body>
    <h1>Test Diagnostic Upload</h1>
    
    <h2>1. Tester Upload</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="test_image" accept="image/*" required>
        <button type="submit">Tester Upload + Création Fiche</button>
    </form>
    
    <h2>2. Dernières fiches</h2>
    <pre><?php echo json_encode($result, JSON_PRETTY_PRINT); ?></pre>
    
    <h2>3. Test API Upload Simple</h2>
    <script>
    function testUploadAPI() {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.onchange = function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const formData = new FormData();
            formData.append('image', file);
            
            fetch('upload-simple.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Réponse upload-simple.php:', data);
                document.getElementById('api-result').innerHTML = JSON.stringify(data, null, 2);
            })
            .catch(error => {
                console.error('Erreur:', error);
                document.getElementById('api-result').innerHTML = 'Erreur: ' + error;
            });
        };
        input.click();
    }
    </script>
    
    <button onclick="testUploadAPI()">Tester API upload-simple.php</button>
    <pre id="api-result"></pre>
</body>
</html>
