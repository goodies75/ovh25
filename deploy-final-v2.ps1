# Script de déploiement OVH - Comics d'Olivier v2.0
# Avec nouvelle icône intégrée
# Date: 11 août 2025

Write-Host "=== DÉPLOIEMENT COMICS D'OLIVIER v2.0 ===" -ForegroundColor Cyan
Write-Host "Nouvelle version avec icône personnalisée" -ForegroundColor Green

# Configuration
$sourceDir = "d:\opetit\Perso\Creations\react-ovh\ovh25\opet-comics\dist"
$apiDir = "d:\opetit\Perso\Creations\react-ovh\ovh25\api"
$deployDir = "d:\opetit\Perso\Creations\react-ovh\ovh25\deploy-final-v2"

# Vérification des répertoires source
if (-not (Test-Path $sourceDir)) {
    Write-Error "Répertoire source introuvable: $sourceDir"
    Write-Host "Veuillez d'abord exécuter 'npm run build' dans opet-comics/" -ForegroundColor Yellow
    exit 1
}

if (-not (Test-Path $apiDir)) {
    Write-Error "Répertoire API introuvable: $apiDir"
    exit 1
}

# Création du répertoire de déploiement
Write-Host "Création du répertoire de déploiement..." -ForegroundColor Yellow
if (Test-Path $deployDir) {
    Remove-Item $deployDir -Recurse -Force
}
New-Item -ItemType Directory -Path $deployDir -Force | Out-Null

# Copie des fichiers frontend (build React)
Write-Host "Copie des fichiers frontend..." -ForegroundColor Yellow
Copy-Item "$sourceDir\*" -Destination $deployDir -Recurse -Force

# Copie des fichiers API PHP
Write-Host "Copie des fichiers API..." -ForegroundColor Yellow
Copy-Item "$apiDir\*.php" -Destination $deployDir -Force

# Copie du dossier data s'il existe
if (Test-Path "$apiDir\data") {
    Copy-Item "$apiDir\data" -Destination $deployDir -Recurse -Force
    Write-Host "Dossier data copié" -ForegroundColor Green
}

# Création du dossier uploads s'il n'existe pas
$uploadsDir = "$deployDir\uploads"
if (-not (Test-Path $uploadsDir)) {
    New-Item -ItemType Directory -Path $uploadsDir -Force | Out-Null
    Write-Host "Dossier uploads créé" -ForegroundColor Green
}

# Suppression des fichiers de développement
Write-Host "Nettoyage des fichiers de développement..." -ForegroundColor Yellow
Remove-Item "$deployDir\mock-*.js" -Force -ErrorAction SilentlyContinue
Remove-Item "$deployDir\vite.svg" -Force -ErrorAction SilentlyContinue

# Vérification des fichiers essentiels
Write-Host "Vérification des fichiers essentiels..." -ForegroundColor Yellow

$essentialFiles = @(
    "index.html",
    "icones-comics.svg",
    "get-comics.php",
    "add-comic.php",
    "upload-image.php",
    "delete-comic.php",
    "update-fiche.php"
)

$missingFiles = @()
foreach ($file in $essentialFiles) {
    if (-not (Test-Path "$deployDir\$file")) {
        $missingFiles += $file
    }
}

if ($missingFiles.Count -gt 0) {
    Write-Error "Fichiers manquants: $($missingFiles -join ', ')"
    exit 1
}

# Affichage du résumé
Write-Host ""
Write-Host "=== RÉSUMÉ DU DÉPLOIEMENT ===" -ForegroundColor Cyan
Write-Host "Version: Comics d'Olivier v2.0 avec icône personnalisée" -ForegroundColor Green
Write-Host "Répertoire: $deployDir" -ForegroundColor White
Write-Host "Fichiers copiés:" -ForegroundColor White

Get-ChildItem $deployDir -Recurse | ForEach-Object {
    $relativePath = $_.FullName.Replace($deployDir, "").TrimStart("\")
    if ($_.PSIsContainer) {
        Write-Host "  📁 $relativePath/" -ForegroundColor Cyan
    } else {
        $size = [math]::Round($_.Length / 1KB, 1)
        Write-Host "  📄 $relativePath ($size KB)" -ForegroundColor White
    }
}

Write-Host ""
Write-Host "✅ DÉPLOIEMENT PRÊT !" -ForegroundColor Green
Write-Host "📁 Dossier: $deployDir" -ForegroundColor White
Write-Host "🚀 Vous pouvez maintenant uploader ces fichiers sur OVH" -ForegroundColor Yellow
Write-Host ""
Write-Host "=== INSTRUCTIONS D'UPLOAD ===" -ForegroundColor Cyan
Write-Host "1. Connectez-vous à votre FTP OVH" -ForegroundColor White
Write-Host "2. Naviguez vers le dossier www/ (ou public_html/)" -ForegroundColor White
Write-Host "3. Uploadez TOUS les fichiers du dossier deploy-final-v2/" -ForegroundColor White
Write-Host "4. Assurez-vous que les permissions PHP sont correctes (755)" -ForegroundColor White
Write-Host "5. Testez l'application sur votre domaine OVH" -ForegroundColor White
Write-Host ""
Write-Host "🎨 Nouvelle icône: icones-comics.svg intégrée !" -ForegroundColor Magenta
