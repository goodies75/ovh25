<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Diagnostic complet de l'upload
$diagnostic = [
    'timestamp' => date('Y-m-d H:i:s'),
    'method' => $_SERVER['REQUEST_METHOD'],
    'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'N/A',
    'files_received' => !empty($_FILES),
    'post_data' => !empty($_POST)
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $diagnostic['files_details'] = $_FILES;
    $diagnostic['post_details'] = $_POST;
    
    // Tentative d'upload réel
    if (isset($_FILES['image'])) {
        $file = $_FILES['image'];
        $diagnostic['file_analysis'] = [
            'name' => $file['name'],
            'type' => $file['type'],
            'size' => $file['size'],
            'error' => $file['error'],
            'tmp_name' => $file['tmp_name']
        ];
        
        // Vérifications
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
            $diagnostic['upload_dir_created'] = true;
        }
        
        $diagnostic['upload_dir_writable'] = is_writable($uploadDir);
        
        // Vérifier les erreurs d'upload
        $uploadErrors = [
            UPLOAD_ERR_OK => 'Aucune erreur',
            UPLOAD_ERR_INI_SIZE => 'Fichier trop volumineux (php.ini)',
            UPLOAD_ERR_FORM_SIZE => 'Fichier trop volumineux (formulaire)',
            UPLOAD_ERR_PARTIAL => 'Upload partiel',
            UPLOAD_ERR_NO_FILE => 'Aucun fichier uploadé',
            UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant',
            UPLOAD_ERR_CANT_WRITE => 'Erreur écriture disque',
            UPLOAD_ERR_EXTENSION => 'Extension PHP a stoppé l\'upload'
        ];
        
        $diagnostic['upload_error_meaning'] = $uploadErrors[$file['error']] ?? 'Erreur inconnue';
        
        if ($file['error'] === UPLOAD_ERR_OK) {
            // Tentative d'upload
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = 'test_' . uniqid() . '.' . $extension;
            $filePath = $uploadDir . $fileName;
            
            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                $diagnostic['upload_test'] = 'SUCCÈS';
                $diagnostic['file_created'] = $filePath;
                $diagnostic['full_url'] = 'http://o-petit.com/uploads/' . $fileName;
                
                echo json_encode([
                    'success' => true,
                    'url' => 'http://o-petit.com/uploads/' . $fileName,
                    'diagnostic' => $diagnostic
                ]);
            } else {
                $diagnostic['upload_test'] = 'ÉCHEC move_uploaded_file';
                echo json_encode([
                    'success' => false,
                    'error' => 'Erreur lors du déplacement du fichier',
                    'diagnostic' => $diagnostic
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Erreur upload: ' . $diagnostic['upload_error_meaning'],
                'diagnostic' => $diagnostic
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Aucun fichier reçu',
            'diagnostic' => $diagnostic
        ]);
    }
} else {
    // Affichage du formulaire de test
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Test Upload Diagnostic</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .result { background: #f0f0f0; padding: 10px; margin: 10px 0; }
            button { padding: 10px 20px; margin: 5px; }
        </style>
    </head>
    <body>
        <h1>Diagnostic Upload d'Images</h1>
        
        <h2>Test 1: Upload Direct</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="image" accept="image/*" required>
            <button type="submit">Tester Upload Direct</button>
        </form>
        
        <h2>Test 2: Upload via JavaScript (comme React)</h2>
        <input type="file" id="jsFile" accept="image/*">
        <button onclick="testJSUpload()">Tester Upload JS</button>
        
        <h2>Résultats</h2>
        <div id="results" class="result">
            Diagnostic initial: <?php echo json_encode($diagnostic, JSON_PRETTY_PRINT); ?>
        </div>
        
        <script>
        function testJSUpload() {
            const file = document.getElementById('jsFile').files[0];
            if (!file) {
                alert('Sélectionnez un fichier');
                return;
            }
            
            const formData = new FormData();
            formData.append('image', file);
            
            fetch('upload-diagnostic.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('results').innerHTML = 
                    '<h3>Résultat JS Upload:</h3><pre>' + JSON.stringify(data, null, 2) + '</pre>';
            })
            .catch(error => {
                document.getElementById('results').innerHTML = 
                    '<h3>Erreur:</h3><pre>' + error + '</pre>';
            });
        }
        </script>
    </body>
    </html>
    <?php
}
?>
