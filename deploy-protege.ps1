# Script de deploiement PROTEGE - Comics d'Olivier
# fiches.json est EXCLU automatiquement pour eviter l'ecrasement
# Date: 11 aout 2025

Write-Host "=== DEPLOIEMENT PROTEGE ANTI-ECRASEMENT ===" -ForegroundColor Cyan
Write-Host "Le fichier fiches.json sera PRESERVE sur le serveur" -ForegroundColor Green

# Configuration
$sourceDir = "d:\opetit\Perso\Creations\react-ovh\ovh25\opet-comics\dist"
$apiDir = "d:\opetit\Perso\Creations\react-ovh\ovh25\api"
$deployDir = "d:\opetit\Perso\Creations\react-ovh\ovh25\deploy-protege"

# Verification des repertoires source
if (-not (Test-Path $sourceDir)) {
    Write-Error "Repertoire source introuvable: $sourceDir"
    Write-Host "Veuillez d'abord executer 'npm run build' dans opet-comics/" -ForegroundColor Yellow
    exit 1
}

# Creation du repertoire de deploiement
Write-Host "Creation du repertoire de deploiement protege..." -ForegroundColor Yellow
if (Test-Path $deployDir) {
    Remove-Item $deployDir -Recurse -Force
}
New-Item -ItemType Directory -Path $deployDir -Force | Out-Null

# Copie des fichiers frontend (build React)
Write-Host "Copie des fichiers frontend..." -ForegroundColor Yellow
Copy-Item "$sourceDir\*" -Destination $deployDir -Recurse -Force

# Copie SELECTIVE des fichiers API (SANS fiches.json)
Write-Host "Copie selective des fichiers API..." -ForegroundColor Yellow
$apiFiles = Get-ChildItem "$apiDir\*.php"
foreach ($file in $apiFiles) {
    Copy-Item $file.FullName -Destination $deployDir -Force
    Write-Host "  Copie: $($file.Name)" -ForegroundColor Green
}

# SUPPRESSION EXPLICITE de fiches.json s'il a ete copie accidentellement
if (Test-Path "$deployDir\fiches.json") {
    Remove-Item "$deployDir\fiches.json" -Force
    Write-Host "  SUPPRIME: fiches.json (protection anti-ecrasement)" -ForegroundColor Red
}

# Creation du dossier uploads
$uploadsDir = "$deployDir\uploads"
if (-not (Test-Path $uploadsDir)) {
    New-Item -ItemType Directory -Path $uploadsDir -Force | Out-Null
}

# Nettoyage des fichiers de developpement
Write-Host "Nettoyage des fichiers de developpement..." -ForegroundColor Yellow
Remove-Item "$deployDir\mock-*.js" -Force -ErrorAction SilentlyContinue
Remove-Item "$deployDir\vite.svg" -Force -ErrorAction SilentlyContinue

# Verification qu'aucun fiches.json n'est present
Write-Host "Verification de protection..." -ForegroundColor Yellow
if (Test-Path "$deployDir\fiches.json") {
    Write-Error "ERREUR: fiches.json detecte dans le deploiement! Arret de securite."
    exit 1
} else {
    Write-Host "  PROTECTION CONFIRMEE: Aucun fiches.json dans le deploiement" -ForegroundColor Green
}

# Creation d'un fichier d'avertissement
$warningContent = @"
# ATTENTION - PROTECTION ANTI-ECRASEMENT
# 
# Ce deploiement ne contient PAS le fichier fiches.json
# pour proteger vos donnees existantes sur le serveur.
# 
# Vos comics sont preserves automatiquement !
# 
# Date: $(Get-Date -Format "yyyy-MM-dd HH:mm:ss")
"@
Set-Content -Path "$deployDir\PROTECTION-DONNEES.txt" -Value $warningContent

# Affichage du resume
Write-Host ""
Write-Host "=== RESUME DU DEPLOIEMENT PROTEGE ===" -ForegroundColor Cyan
Write-Host "Version: Comics d'Olivier v2.0 - PROTECTION ACTIVEE" -ForegroundColor Green
Write-Host "Repertoire: $deployDir" -ForegroundColor White

Write-Host "`nFichiers inclus:" -ForegroundColor Green
Get-ChildItem $deployDir -Name | ForEach-Object {
    Write-Host "  + $_" -ForegroundColor White
}

Write-Host "`nFichiers PROTEGES (non inclus):" -ForegroundColor Red
Write-Host "  - fiches.json (preserve sur le serveur)" -ForegroundColor Red

Write-Host ""
Write-Host "DEPLOIEMENT SECURISE PRET !" -ForegroundColor Green
Write-Host "Vos donnees sont automatiquement protegees" -ForegroundColor Yellow

Write-Host ""
Write-Host "=== INSTRUCTIONS D'UPLOAD ===" -ForegroundColor Cyan
Write-Host "1. Connectez-vous a votre FTP OVH" -ForegroundColor White
Write-Host "2. Uploadez TOUS les fichiers du dossier deploy-protege/" -ForegroundColor White
Write-Host "3. fiches.json sur le serveur restera intact" -ForegroundColor Green
Write-Host "4. Vos comics actuels seront preserves" -ForegroundColor Green
