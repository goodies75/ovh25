# Script de deploiement SANS ecraser fiches.json
# Preserve les donnees existantes sur le serveur

Write-Host "=== DEPLOIEMENT SANS ECRASEMENT DONNEES ===" -ForegroundColor Cyan

# Configuration
$sourceDir = "d:\opetit\Perso\Creations\react-ovh\ovh25\opet-comics\dist"
$apiDir = "d:\opetit\Perso\Creations\react-ovh\ovh25\api"
$deployDir = "d:\opetit\Perso\Creations\react-ovh\ovh25\deploy-preserve"

Write-Host "Creation du repertoire de deploiement..." -ForegroundColor Yellow
if (Test-Path $deployDir) {
    Remove-Item $deployDir -Recurse -Force
}
New-Item -ItemType Directory -Path $deployDir -Force | Out-Null

# Copie des fichiers frontend
Write-Host "Copie des fichiers frontend..." -ForegroundColor Yellow
Copy-Item "$sourceDir\*" -Destination $deployDir -Recurse -Force

# Copie des fichiers API (SAUF fiches.json)
Write-Host "Copie des fichiers API (sans fiches.json)..." -ForegroundColor Yellow
Get-ChildItem "$apiDir\*.php" | ForEach-Object {
    Copy-Item $_.FullName -Destination $deployDir -Force
    Write-Host "  Copie: $($_.Name)" -ForegroundColor Green
}

# Creation du dossier uploads
$uploadsDir = "$deployDir\uploads"
if (-not (Test-Path $uploadsDir)) {
    New-Item -ItemType Directory -Path $uploadsDir -Force | Out-Null
}

# Nettoyage
Remove-Item "$deployDir\mock-*.js" -Force -ErrorAction SilentlyContinue
Remove-Item "$deployDir\vite.svg" -Force -ErrorAction SilentlyContinue

Write-Host "`n=== INSTRUCTIONS SPECIALES ===" -ForegroundColor Cyan
Write-Host "IMPORTANT: Ce deploiement PRESERVE vos donnees existantes" -ForegroundColor Green
Write-Host ""
Write-Host "1. Uploadez TOUS les fichiers SAUF fiches.json" -ForegroundColor Yellow
Write-Host "2. Ne touchez PAS au fichier fiches.json existant sur le serveur" -ForegroundColor Red
Write-Host "3. Vos comics actuels seront preserves" -ForegroundColor Green
Write-Host ""
Write-Host "Fichiers a uploader:" -ForegroundColor White
Get-ChildItem $deployDir -Name | Where-Object { $_ -ne "fiches.json" } | ForEach-Object {
    Write-Host "  - $_" -ForegroundColor White
}

Write-Host "`nDEPLOIEMENT PRET (donnees preservees) !" -ForegroundColor Green
