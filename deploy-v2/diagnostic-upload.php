<?php
header('Content-Type: application/json');

// Test des permissions et de l'environnement d'upload
echo json_encode([
    'message' => 'Diagnostic upload',
    'upload_dir_exists' => is_dir(__DIR__ . '/uploads/') ? 'OUI' : 'NON',
    'upload_dir_writable' => is_writable(__DIR__ . '/uploads/') ? 'OUI' : 'NON',
    'upload_dir_path' => __DIR__ . '/uploads/',
    'php_version' => phpversion(),
    'gd_extension' => extension_loaded('gd') ? 'OUI' : 'NON',
    'max_upload_size' => ini_get('upload_max_filesize'),
    'max_post_size' => ini_get('post_max_size'),
    'memory_limit' => ini_get('memory_limit')
]);
?>
