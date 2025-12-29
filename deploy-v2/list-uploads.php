<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Lister tous les fichiers dans le dossier uploads
$uploadsDir = 'uploads';
$result = [
    'uploads_exists' => is_dir($uploadsDir),
    'files' => []
];

if (is_dir($uploadsDir)) {
    $files = scandir($uploadsDir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $filePath = $uploadsDir . '/' . $file;
            $result['files'][] = [
                'name' => $file,
                'size' => filesize($filePath),
                'is_image' => preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file),
                'url' => 'http://o-petit.com/uploads/' . $file
            ];
        }
    }
}

$result['total_files'] = count($result['files']);
$result['image_files'] = array_filter($result['files'], function($f) { return $f['is_image']; });
$result['total_images'] = count($result['image_files']);

echo json_encode($result, JSON_PRETTY_PRINT);
?>
