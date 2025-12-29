# Script de deploiement OVH - Comics d'Olivier v2.0
# Avec nouvelle icone integree
# Date: 11 aout 2025

Write-Host "=== DEPLOIEMENT COMICS D'OLIVIER v2.0 ===" -ForegroundColor Cyan
Write-Host "Nouvelle version avec icone personnalisee" -ForegroundColor Green

# Configuration
$sourceDir = "d:\opetit\Perso\Creations\react-ovh\ovh25\opet-comics\dist"
$apiDir = "d:\opetit\Perso\Creations\react-ovh\ovh25\api"
$deployDir = "d:\opetit\Perso\Creations\react-ovh\ovh25\deploy-final-v2"

# Verification des repertoires source
if (-not (Test-Path $sourceDir)) {
    Write-Error "Repertoire source introuvable: $sourceDir"
    Write-Host "Veuillez d'abord executer 'npm run build' dans opet-comics/" -ForegroundColor Yellow
    exit 1
}

if (-not (Test-Path $apiDir)) {
    Write-Error "Repertoire API introuvable: $apiDir"
    exit 1
}

# Creation du repertoire de deploiement
Write-Host "Creation du repertoire de deploiement..." -ForegroundColor Yellow
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

# Copie du fichier JSON
if (Test-Path "$apiDir\fiches.json") {
    Copy-Item "$apiDir\fiches.json" -Destination $deployDir -Force
    Write-Host "Fichier fiches.json copie" -ForegroundColor Green
}

# Creation du dossier uploads s'il n'existe pas
$uploadsDir = "$deployDir\uploads"
if (-not (Test-Path $uploadsDir)) {
    New-Item -ItemType Directory -Path $uploadsDir -Force | Out-Null
    Write-Host "Dossier uploads cree" -ForegroundColor Green
}

# Suppression des fichiers de developpement
Write-Host "Nettoyage des fichiers de developpement..." -ForegroundColor Yellow
Remove-Item "$deployDir\mock-*.js" -Force -ErrorAction SilentlyContinue
Remove-Item "$deployDir\vite.svg" -Force -ErrorAction SilentlyContinue

# Verification des fichiers essentiels
Write-Host "Verification des fichiers essentiels..." -ForegroundColor Yellow

$essentialFiles = @(
    "index.html",
    "icones-comics.svg",
    "get-fiches.php",
    "post-fiche.php"
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

# Affichage du resume
Write-Host ""
Write-Host "=== RESUME DU DEPLOIEMENT ===" -ForegroundColor Cyan
Write-Host "Version: Comics d'Olivier v2.0 avec icone personnalisee" -ForegroundColor Green
Write-Host "Repertoire: $deployDir" -ForegroundColor White
Write-Host "Fichiers copies:" -ForegroundColor White

Get-ChildItem $deployDir -Name | ForEach-Object {
    Write-Host "  - $_" -ForegroundColor White
}

Write-Host ""
Write-Host "DEPLOIEMENT PRET !" -ForegroundColor Green
Write-Host "Dossier: $deployDir" -ForegroundColor White
Write-Host "Vous pouvez maintenant uploader ces fichiers sur OVH" -ForegroundColor Yellow
Write-Host ""
Write-Host "=== INSTRUCTIONS D'UPLOAD ===" -ForegroundColor Cyan
Write-Host "1. Connectez-vous a votre FTP OVH" -ForegroundColor White
Write-Host "2. Naviguez vers le dossier www/ (ou public_html/)" -ForegroundColor White
Write-Host "3. Uploadez TOUS les fichiers du dossier deploy-final-v2/" -ForegroundColor White
Write-Host "4. Assurez-vous que les permissions PHP sont correctes (755)" -ForegroundColor White
Write-Host "5. Testez l'application sur votre domaine OVH" -ForegroundColor White
Write-Host ""
Write-Host "Nouvelle icone: icones-comics.svg integree !" -ForegroundColor Magenta
