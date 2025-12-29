<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Diagnostic complet pour identifier le problème d'affichage

$results = [
    'timestamp' => date('Y-m-d H:i:s'),
    'tests' => []
];

// Test 1: Vérifier les fichiers JSON
$jsonFiles = ['data/fiches-data.json', 'fiches-data.json'];
foreach ($jsonFiles as $file) {
    $test = [
        'name' => "JSON File: $file",
        'file' => $file,
        'exists' => file_exists($file),
        'readable' => file_exists($file) ? is_readable($file) : false,
        'size' => file_exists($file) ? filesize($file) : 0
    ];
    
    if ($test['readable']) {
        $content = file_get_contents($file);
        $data = json_decode($content, true);
        $test['valid_json'] = json_last_error() === JSON_ERROR_NONE;
        $test['count'] = is_array($data) ? count($data) : 0;
        
        if ($test['valid_json'] && $test['count'] > 0) {
            $test['sample'] = [];
            foreach (array_slice($data, 0, 2) as $i => $fiche) {
                $test['sample'][$i] = [
                    'id' => $fiche['id'] ?? 'N/A',
                    'nom_serie' => $fiche['nom_serie'] ?? 'N/A',
                    'image_url' => $fiche['image_url'] ?? 'N/A',
                    'image_url_length' => strlen($fiche['image_url'] ?? ''),
                    'image_url_starts_with' => substr($fiche['image_url'] ?? '', 0, 20)
                ];
            }
        }
    }
    
    $results['tests'][] = $test;
}

// Test 2: Vérifier le dossier uploads
$uploadsDir = 'uploads';
$test = [
    'name' => 'Uploads Directory',
    'path' => $uploadsDir,
    'exists' => is_dir($uploadsDir),
    'readable' => is_dir($uploadsDir) ? is_readable($uploadsDir) : false
];

if ($test['readable']) {
    $files = scandir($uploadsDir);
    $imageFiles = array_filter($files, function($file) {
        return preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file);
    });
    $test['image_count'] = count($imageFiles);
    $test['sample_images'] = array_slice($imageFiles, 0, 5);
}

$results['tests'][] = $test;

// Test 3: Tester l'accès aux APIs
$apis = [
    'get-fiches.php',
    'post-fiche-simple.php',
    'update-fiche.php',
    'delete-fiche-simple.php',
    'upload-simple.php'
];

foreach ($apis as $api) {
    $test = [
        'name' => "API: $api",
        'file' => $api,
        'exists' => file_exists($api),
        'readable' => file_exists($api) ? is_readable($api) : false
    ];
    $results['tests'][] = $test;
}

// Test 4: Vérifier les URLs d'images
$activeJsonFile = null;
foreach ($jsonFiles as $file) {
    if (file_exists($file)) {
        $activeJsonFile = $file;
        break;
    }
}

if ($activeJsonFile) {
    $data = json_decode(file_get_contents($activeJsonFile), true);
    if ($data) {
        $urlAnalysis = [
            'name' => 'Image URL Analysis',
            'total_fiches' => count($data),
            'fiches_with_images' => 0,
            'url_patterns' => []
        ];
        
        foreach ($data as $fiche) {
            if (!empty($fiche['image_url'])) {
                $urlAnalysis['fiches_with_images']++;
                $url = $fiche['image_url'];
                
                if (strpos($url, 'http') === 0) {
                    $urlAnalysis['url_patterns']['absolute'][] = $url;
                } elseif (strpos($url, './') === 0) {
                    $urlAnalysis['url_patterns']['relative_dot'][] = $url;
                } elseif (strpos($url, 'uploads/') === 0) {
                    $urlAnalysis['url_patterns']['relative_direct'][] = $url;
                } else {
                    $urlAnalysis['url_patterns']['other'][] = $url;
                }
            }
        }
        
        $results['tests'][] = $urlAnalysis;
    }
}

// Test 5: Configuration serveur
$serverTest = [
    'name' => 'Server Configuration',
    'php_version' => phpversion(),
    'json_functions' => function_exists('json_encode') && function_exists('json_decode'),
    'file_functions' => function_exists('file_get_contents') && function_exists('file_put_contents'),
    'current_dir' => getcwd(),
    'script_dir' => __DIR__
];

$results['tests'][] = $serverTest;

echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
