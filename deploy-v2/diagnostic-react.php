<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Diagnostic complet de l'état actuel

$result = [
    'timestamp' => date('Y-m-d H:i:s'),
    'diagnostics' => []
];

// 1. Vérifier l'API get-fiches.php
$getFichesTest = [
    'name' => 'Test API get-fiches.php',
    'status' => 'unknown'
];

try {
    $jsonFile = 'data/fiches-data.json';
    if (!file_exists($jsonFile)) {
        $jsonFile = 'fiches-data.json';
    }
    
    if (file_exists($jsonFile)) {
        $content = file_get_contents($jsonFile);
        $fiches = json_decode($content, true);
        
        if ($fiches) {
            $getFichesTest['status'] = 'success';
            $getFichesTest['total_fiches'] = count($fiches);
            $getFichesTest['sample_fiches'] = array_slice($fiches, 0, 2);
        } else {
            $getFichesTest['status'] = 'json_error';
            $getFichesTest['json_error'] = json_last_error_msg();
        }
    } else {
        $getFichesTest['status'] = 'file_not_found';
    }
} catch (Exception $e) {
    $getFichesTest['status'] = 'exception';
    $getFichesTest['error'] = $e->getMessage();
}

$result['diagnostics'][] = $getFichesTest;

// 2. Tester directement l'API get-fiches.php
$apiTest = [
    'name' => 'Test direct API get-fiches.php',
    'status' => 'unknown'
];

if (file_exists('get-fiches.php')) {
    ob_start();
    try {
        include 'get-fiches.php';
        $apiOutput = ob_get_contents();
        ob_end_clean();
        
        $apiJson = json_decode($apiOutput, true);
        if ($apiJson) {
            $apiTest['status'] = 'success';
            $apiTest['api_response'] = $apiJson;
        } else {
            $apiTest['status'] = 'invalid_json';
            $apiTest['raw_output'] = substr($apiOutput, 0, 500);
        }
    } catch (Exception $e) {
        ob_end_clean();
        $apiTest['status'] = 'exception';
        $apiTest['error'] = $e->getMessage();
    }
} else {
    $apiTest['status'] = 'file_not_found';
}

$result['diagnostics'][] = $apiTest;

// 3. Vérifier les fichiers essentiels
$filesTest = [
    'name' => 'Vérification fichiers essentiels',
    'files' => []
];

$essentialFiles = [
    'get-fiches.php',
    'post-fiche-simple.php', 
    'update-fiche.php',
    'delete-fiche-simple.php',
    'upload-simple.php',
    'data/fiches-data.json',
    'fiches-data.json'
];

foreach ($essentialFiles as $file) {
    $filesTest['files'][$file] = [
        'exists' => file_exists($file),
        'readable' => file_exists($file) ? is_readable($file) : false,
        'size' => file_exists($file) ? filesize($file) : 0
    ];
}

$result['diagnostics'][] = $filesTest;

// 4. Test CORS
$corsTest = [
    'name' => 'Test CORS Headers',
    'headers' => [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
        'Access-Control-Allow-Headers' => 'Content-Type'
    ]
];

$result['diagnostics'][] = $corsTest;

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
