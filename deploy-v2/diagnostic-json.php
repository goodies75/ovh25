<?php
/*
 * ========================================
 * DIAGNOSTIC COMPLET DES ERREURS JSON
 * ========================================
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

$results = [];

// ========== TEST 1: VÉRIFICATION DU FICHIER JSON ==========
$jsonFile = 'data/fiches-data.json';
if (!file_exists($jsonFile)) {
    $jsonFile = 'fiches-data.json';
}
$fichesFile = __DIR__ . '/' . $jsonFile;

$results['file_info'] = [
    'file_path' => $fichesFile,
    'file_exists' => file_exists($fichesFile),
    'file_readable' => is_readable($fichesFile),
    'file_writable' => is_writable($fichesFile),
    'file_size' => file_exists($fichesFile) ? filesize($fichesFile) : 0
];

// ========== TEST 2: LECTURE BRUTE DU FICHIER ==========
if (file_exists($fichesFile)) {
    $rawContent = file_get_contents($fichesFile);
    $results['raw_content'] = [
        'length' => strlen($rawContent),
        'first_100_chars' => substr($rawContent, 0, 100),
        'last_100_chars' => substr($rawContent, -100),
        'has_bom' => substr($rawContent, 0, 3) === "\xEF\xBB\xBF",
        'encoding' => mb_detect_encoding($rawContent)
    ];
    
    // Test de validation JSON
    $jsonData = json_decode($rawContent, true);
    $results['json_validation'] = [
        'is_valid' => $jsonData !== null,
        'json_error' => json_last_error_msg(),
        'json_error_code' => json_last_error(),
        'decoded_count' => is_array($jsonData) ? count($jsonData) : 'N/A'
    ];
    
    // Recherche de caractères problématiques
    $problematicChars = [];
    for ($i = 0; $i < strlen($rawContent); $i++) {
        $char = $rawContent[$i];
        $ord = ord($char);
        if ($ord < 32 && !in_array($ord, [9, 10, 13])) { // Caractères de contrôle sauf tab, LF, CR
            $problematicChars[] = [
                'position' => $i,
                'char' => $char,
                'ord' => $ord,
                'hex' => dechex($ord)
            ];
        }
    }
    $results['problematic_chars'] = $problematicChars;
} else {
    $results['file_error'] = 'Fichier introuvable';
}

// ========== TEST 3: TEST DE RÉÉCRITURE ==========
try {
    $testData = [
        [
            'id' => time(),
            'test' => 'diagnostic',
            'created_at' => date('c')
        ]
    ];
    
    $testJson = json_encode($testData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $testFile = __DIR__ . '/test-write.json';
    
    $writeResult = file_put_contents($testFile, $testJson);
    
    if ($writeResult !== false) {
        $readBack = file_get_contents($testFile);
        $parseBack = json_decode($readBack, true);
        
        $results['write_test'] = [
            'write_success' => true,
            'bytes_written' => $writeResult,
            'read_back_success' => $readBack !== false,
            'parse_back_success' => $parseBack !== null,
            'json_matches' => $testData === $parseBack
        ];
        
        // Nettoyer le fichier test
        @unlink($testFile);
    } else {
        $results['write_test'] = [
            'write_success' => false,
            'error' => 'Impossible d\'écrire le fichier test'
        ];
    }
} catch (Exception $e) {
    $results['write_test'] = [
        'error' => $e->getMessage()
    ];
}

// ========== TEST 4: PERMISSIONS DU DOSSIER ==========
$dataDir = __DIR__ . '/data';
$results['permissions'] = [
    'data_dir_exists' => is_dir($dataDir),
    'data_dir_writable' => is_writable($dataDir),
    'current_dir_writable' => is_writable(__DIR__),
    'php_user' => get_current_user(),
    'umask' => sprintf('%04o', umask())
];

// ========== RÉPONSE ==========
echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
