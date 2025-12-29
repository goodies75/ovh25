<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Test méthodique de toute la chaîne de fonctionnement

$tests = [];

// TEST 1: upload-simple.php retourne-t-il les bonnes URLs ?
$tests['upload_api'] = [
    'name' => 'Test upload-simple.php',
    'description' => 'Vérifier que l\'upload retourne une URL absolue'
];

if (file_exists('upload-simple.php')) {
    $content = file_get_contents('upload-simple.php');
    if (strpos($content, 'http://o-petit.com/uploads/') !== false) {
        $tests['upload_api']['status'] = 'CORRECT - retourne URL absolue';
    } else {
        $tests['upload_api']['status'] = 'PROBLEME - retourne URL relative';
        $tests['upload_api']['fix_needed'] = true;
    }
} else {
    $tests['upload_api']['status'] = 'FICHIER MANQUANT';
}

// TEST 2: post-fiche-simple.php stocke-t-il correctement ?
$tests['post_api'] = [
    'name' => 'Test post-fiche-simple.php',
    'description' => 'Vérifier que la création stocke les URLs telles que reçues'
];

if (file_exists('post-fiche-simple.php')) {
    $content = file_get_contents('post-fiche-simple.php');
    // Chercher des manipulations d'URL suspectes
    if (strpos($content, './uploads/') !== false || strpos($content, 'test-image') !== false) {
        $tests['post_api']['status'] = 'PROBLEME - génère des URLs relatives';
        $tests['post_api']['fix_needed'] = true;
    } else {
        $tests['post_api']['status'] = 'SEMBLE CORRECT';
    }
} else {
    $tests['post_api']['status'] = 'FICHIER MANQUANT';
}

// TEST 3: get-fiches.php retourne-t-il les bonnes données ?
$tests['get_api'] = [
    'name' => 'Test get-fiches.php',
    'description' => 'Vérifier que la lecture retourne les URLs telles que stockées'
];

if (file_exists('get-fiches.php')) {
    $tests['get_api']['status'] = 'FICHIER EXISTE';
} else {
    $tests['get_api']['status'] = 'FICHIER MANQUANT';
}

// TEST 4: Analyser les données actuelles
$tests['data_analysis'] = [
    'name' => 'Analyse des données actuelles',
    'description' => 'Vérifier l\'état actuel des URLs stockées'
];

$jsonFile = 'data/fiches-data.json';
if (!file_exists($jsonFile)) {
    $jsonFile = 'fiches-data.json';
}

if (file_exists($jsonFile)) {
    $fiches = json_decode(file_get_contents($jsonFile), true);
    if ($fiches) {
        $urlAnalysis = [
            'total' => count($fiches),
            'absolute_urls' => 0,
            'relative_urls' => 0,
            'test_image_refs' => 0,
            'empty_urls' => 0
        ];
        
        foreach ($fiches as $fiche) {
            $url = $fiche['image_url'] ?? '';
            if (empty($url)) {
                $urlAnalysis['empty_urls']++;
            } elseif (strpos($url, 'http') === 0) {
                $urlAnalysis['absolute_urls']++;
            } else {
                $urlAnalysis['relative_urls']++;
            }
            
            if (strpos($url, 'test-image') !== false) {
                $urlAnalysis['test_image_refs']++;
            }
        }
        
        $tests['data_analysis']['current_state'] = $urlAnalysis;
        
        if ($urlAnalysis['test_image_refs'] > 0) {
            $tests['data_analysis']['status'] = 'PROBLEME - Références test-image détectées';
            $tests['data_analysis']['fix_needed'] = true;
        } elseif ($urlAnalysis['relative_urls'] > 0) {
            $tests['data_analysis']['status'] = 'PROBLEME - URLs relatives détectées';
            $tests['data_analysis']['fix_needed'] = true;
        } else {
            $tests['data_analysis']['status'] = 'CORRECT - Toutes URLs absolues';
        }
    }
}

// TEST 5: React peut-il charger les APIs ?
$tests['react_connectivity'] = [
    'name' => 'Test connectivité React',
    'description' => 'Vérifier les headers CORS et accessibilité'
];

$corsHeaders = [
    'Access-Control-Allow-Origin: *',
    'Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS',
    'Access-Control-Allow-Headers: Content-Type'
];

$tests['react_connectivity']['cors_headers'] = $corsHeaders;
$tests['react_connectivity']['status'] = 'À TESTER depuis React';

// RECOMMANDATIONS
$recommendations = [];

foreach ($tests as $test) {
    if (isset($test['fix_needed']) && $test['fix_needed']) {
        $recommendations[] = $test['name'];
    }
}

$result = [
    'diagnostic_complet' => $tests,
    'problemes_identifies' => $recommendations,
    'prochaine_action' => empty($recommendations) ? 
        'Tester depuis React - Backend semble OK' : 
        'Corriger: ' . implode(', ', $recommendations)
];

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
