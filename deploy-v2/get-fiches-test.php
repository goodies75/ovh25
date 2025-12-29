<?php
// API GET ultra-simple avec TOUS vos champs
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$fichesFile = __DIR__ . '/fiches-test.json';
$fiches = json_decode(file_get_contents($fichesFile), true);

echo json_encode($fiches);
?>
