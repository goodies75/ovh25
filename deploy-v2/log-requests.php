<?php
// Script pour voir tous les appels reçus
$logFile = 'all-requests.log';
$timestamp = date('Y-m-d H:i:s');

$request = [
    'timestamp' => $timestamp,
    'url' => $_SERVER['REQUEST_URI'] ?? 'N/A',
    'method' => $_SERVER['REQUEST_METHOD'] ?? 'N/A',
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'N/A',
    'referer' => $_SERVER['HTTP_REFERER'] ?? 'N/A'
];

file_put_contents($logFile, json_encode($request) . "\n", FILE_APPEND);

echo "Request logged to $logFile";
?>
