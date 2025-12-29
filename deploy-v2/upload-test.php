<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Test simple pour voir si le script fonctionne
echo json_encode([
    'status' => 'upload-test.php fonctionne',
    'server_info' => [
        'php_version' => phpversion(),
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'tmp_upload_dir' => sys_get_temp_dir()
    ]
]);
?>
