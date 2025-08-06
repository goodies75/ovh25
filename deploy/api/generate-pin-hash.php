<?php
// Script pour générer le hash de votre PIN
// Utilisez ce script UNE SEULE FOIS pour générer votre hash, puis supprimez-le !

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pin'])) {
    $pin = $_POST['pin'];
    $hash = password_hash($pin, PASSWORD_DEFAULT);
    
    echo "<h2>Hash généré pour votre PIN :</h2>";
    echo "<code style='background: #f5f5f5; padding: 10px; display: block; word-break: break-all;'>$hash</code>";
    echo "<p><strong>IMPORTANT :</strong> Copiez ce hash dans validate-pin.php et supprimez ce fichier !</p>";
    echo "<p>Remplacez la valeur de ADMIN_PIN_HASH par ce hash.</p>";
} else {
?>
<!DOCTYPE html>
<html>
<head>
    <title>Générateur de Hash PIN</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 500px; margin: 50px auto; padding: 20px; }
        input, button { padding: 10px; margin: 10px 0; width: 100%; box-sizing: border-box; }
        .warning { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Générateur de Hash PIN</h1>
    <p class="warning">⚠️ Utilisez ce script UNE SEULE FOIS puis supprimez-le !</p>
    
    <form method="post">
        <label for="pin">Entrez votre PIN secret :</label>
        <input type="password" id="pin" name="pin" required>
        <button type="submit">Générer le Hash</button>
    </form>
    
    <h3>Instructions :</h3>
    <ol>
        <li>Entrez votre PIN secret (ex: MonPinSecret123)</li>
        <li>Copiez le hash généré</li>
        <li>Remplacez ADMIN_PIN_HASH dans validate-pin.php</li>
        <li><strong>Supprimez ce fichier après utilisation !</strong></li>
    </ol>
</body>
</html>
<?php
}
?>
