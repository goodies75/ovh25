<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Capturer exactement ce que upload-image.php retourne

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    // Faire l'appel à upload-image.php et capturer la réponse
    ob_start();
    include 'upload-image.php';
    $response = ob_get_contents();
    ob_end_clean();
    
    // Analyser la réponse
    $analysis = [
        'timestamp' => date('Y-m-d H:i:s'),
        'raw_response' => $response,
        'response_length' => strlen($response),
        'is_valid_json' => false,
        'json_data' => null,
        'json_error' => null
    ];
    
    // Tester si c'est du JSON valide
    $jsonData = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $analysis['is_valid_json'] = true;
        $analysis['json_data'] = $jsonData;
    } else {
        $analysis['json_error'] = json_last_error_msg();
    }
    
    echo json_encode($analysis, JSON_PRETTY_PRINT);
} else {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Test Réponse Upload</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .result { background: #f5f5f5; padding: 15px; margin: 10px 0; border-left: 4px solid #007cba; }
        </style>
    </head>
    <body>
        <h1>Test Réponse upload-image.php</h1>
        
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="image" accept="image/*" required>
            <button type="submit">Tester et Analyser la Réponse</button>
        </form>
        
        <p>Ce test va capturer exactement ce que upload-image.php retourne et analyser si c'est du JSON valide.</p>
    </body>
    </html>
    <?php
}
?>
