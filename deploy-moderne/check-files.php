<?php
echo "<h2>Vérification des fichiers</h2>";
echo "<p>Répertoire actuel : " . __DIR__ . "</p>";

$requiredFiles = [
    'db-config.php',
    'get-fiches.php',
    'post-fiche.php',
    'delete-fiche.php',
    'update-fiche.php',
    'upload-image.php',
    'index.html'
];

echo "<h3>Fichiers requis :</h3>";
echo "<ul>";
foreach ($requiredFiles as $file) {
    $exists = file_exists(__DIR__ . '/' . $file);
    $status = $exists ? '✅' : '❌';
    echo "<li>$status $file</li>";
}
echo "</ul>";

echo "<h3>Tous les fichiers PHP présents :</h3>";
echo "<ul>";
$phpFiles = glob(__DIR__ . '/*.php');
foreach ($phpFiles as $file) {
    echo "<li>" . basename($file) . "</li>";
}
echo "</ul>";

echo "<h3>Test du dossier uploads :</h3>";
$uploadsDir = __DIR__ . '/uploads';
echo "<p>Dossier uploads existe : " . (is_dir($uploadsDir) ? '✅ OUI' : '❌ NON') . "</p>";
if (is_dir($uploadsDir)) {
    echo "<p>Dossier uploads accessible en écriture : " . (is_writable($uploadsDir) ? '✅ OUI' : '❌ NON') . "</p>";
} else {
    echo "<p>⚠️ Le dossier uploads doit être créé</p>";
}
?>
