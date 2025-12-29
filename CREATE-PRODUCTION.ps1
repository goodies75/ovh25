# Script de création du package de production
# Crée un dossier "production" avec SEULEMENT les fichiers nécessaires

$sourceDir = "d:\opetit\Perso\Creations\react-ovh\ovh25\deploy-v2"
$productionDir = "d:\opetit\Perso\Creations\react-ovh\ovh25\production"

# Créer le dossier production
if (Test-Path $productionDir) {
    Remove-Item $productionDir -Recurse -Force
}
New-Item -ItemType Directory -Path $productionDir

# Créer le dossier uploads
New-Item -ItemType Directory -Path "$productionDir\uploads"

# Créer le dossier assets
New-Item -ItemType Directory -Path "$productionDir\assets"

# Copier les fichiers essentiels
$essentialFiles = @(
    "index.html",
    "vite.svg",
    "fiches-data.json",
    "get-fiches.php",
    "post-fiche-simple.php", 
    "update-fiche.php",
    "delete-fiche-simple.php",
    "upload-image.php",
    "validate-pin.php",
    ".htaccess"
)

foreach ($file in $essentialFiles) {
    $sourcePath = Join-Path $sourceDir $file
    if (Test-Path $sourcePath) {
        Copy-Item $sourcePath $productionDir
        Write-Host "Copie: $file"
    } else {
        Write-Host "Manquant: $file" -ForegroundColor Yellow
    }
}

# Copier tout le dossier assets
Copy-Item "$sourceDir\assets\*" "$productionDir\assets\" -Recurse -Force
Write-Host "Copie: dossier assets"

Write-Host ""
Write-Host "Package de production cree dans: $productionDir"
Write-Host "Contenu a uploader sur votre FTP:"
Get-ChildItem $productionDir | Format-Table Name, Length -AutoSize
