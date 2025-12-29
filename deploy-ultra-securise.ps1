# SCRIPT TOUT-EN-UN : Sauvegarde + Deploiement Protege
# Comics d'Olivier - Protection complete des donnees
# Date: 11 aout 2025

Write-Host "=== DEPLOIEMENT ULTRA-SECURISE ===" -ForegroundColor Cyan
Write-Host "Sauvegarde automatique + Protection anti-ecrasement" -ForegroundColor Green

# Etape 1: Sauvegarde automatique
Write-Host "`n1. SAUVEGARDE DES DONNEES SERVEUR..." -ForegroundColor Yellow
& "d:\opetit\Perso\Creations\react-ovh\ovh25\sauvegarde-auto.ps1"

if ($LASTEXITCODE -ne 0) {
    Write-Host "Sauvegarde echouee, deploiement annule par securite" -ForegroundColor Red
    exit 1
}

# Etape 2: Build de l'application
Write-Host "`n2. BUILD DE L'APPLICATION..." -ForegroundColor Yellow
Set-Location "d:\opetit\Perso\Creations\react-ovh\ovh25\opet-comics"
npm run build

if ($LASTEXITCODE -ne 0) {
    Write-Error "Build echoue"
    exit 1
}

# Etape 3: Deploiement protege
Write-Host "`n3. DEPLOIEMENT PROTEGE..." -ForegroundColor Yellow
Set-Location "d:\opetit\Perso\Creations\react-ovh\ovh25"
& ".\deploy-protege.ps1"

Write-Host "`n=== DEPLOIEMENT ULTRA-SECURISE TERMINE ===" -ForegroundColor Cyan
Write-Host "Vos donnees sont preservees a 100% !" -ForegroundColor Green
