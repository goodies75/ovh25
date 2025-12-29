# Script de verification pre-deploiement
# Comics d'Olivier v2.0
# Date: 11 aout 2025

Write-Host "=== VERIFICATION PRE-DEPLOIEMENT ===" -ForegroundColor Cyan
Write-Host "Comics d'Olivier v2.0 avec icone personnalisee" -ForegroundColor Green

# Chemins
$projectDir = "d:\opetit\Perso\Creations\react-ovh\ovh25\opet-comics"
$distDir = "$projectDir\dist"
$apiDir = "d:\opetit\Perso\Creations\react-ovh\ovh25\api"

# Verification 1: Build React
Write-Host "`n1. Verification du build React..." -ForegroundColor Yellow
if (-not (Test-Path $distDir)) {
    Write-Host "Build non trouve" -ForegroundColor Red
    exit 1
} else {
    Write-Host "Build React trouve" -ForegroundColor Green
}

# Verification 2: Nouvelle icone
Write-Host "`n2. Verification de la nouvelle icone..." -ForegroundColor Yellow
$iconPath = "$distDir\icones-comics.svg"
if (Test-Path $iconPath) {
    $iconSize = (Get-Item $iconPath).Length
    Write-Host "Icone icones-comics.svg trouvee" -ForegroundColor Green
} else {
    Write-Host "Icone icones-comics.svg manquante" -ForegroundColor Red
}

# Verification 3: Fichiers essentiels
Write-Host "`n3. Verification des fichiers essentiels..." -ForegroundColor Yellow
$essentialFiles = @(
    "$distDir\index.html",
    "$distDir\assets",
    "$apiDir\get-comics.php",
    "$apiDir\add-comic.php",
    "$apiDir\upload-image.php",
    "$apiDir\delete-comic.php",
    "$apiDir\update-fiche.php"
)

$allGood = $true
foreach ($file in $essentialFiles) {
    if (Test-Path $file) {
        $fileName = Split-Path $file -Leaf
        Write-Host "✅ $fileName" -ForegroundColor Green
    } else {
        $fileName = Split-Path $file -Leaf
        Write-Host "❌ $fileName manquant" -ForegroundColor Red
        $allGood = $false
    }
}

# Verification 4: Contenu du index.html
Write-Host "`n4. Verification du contenu index.html..." -ForegroundColor Yellow
$indexContent = Get-Content "$distDir\index.html" -Raw
if ($indexContent -match 'icones-comics\.svg') {
    Write-Host "Reference a icones-comics.svg trouvee" -ForegroundColor Green
} else {
    Write-Host "Reference a icones-comics.svg manquante" -ForegroundColor Red
    $allGood = $false
}

# Resume final
Write-Host "`n=== RESUME DE VERIFICATION ===" -ForegroundColor Cyan
if ($allGood) {
    Write-Host "TOUT EST PRET POUR LE DEPLOIEMENT !" -ForegroundColor Green
    Write-Host "`nEtapes suivantes:" -ForegroundColor Yellow
    Write-Host "1. Executer: .\deploy-final-v2.ps1" -ForegroundColor White
    Write-Host "2. Uploader les fichiers sur OVH" -ForegroundColor White
    Write-Host "3. Tester l'application deployee" -ForegroundColor White
} else {
    Write-Host "DES PROBLEMES ONT ETE DETECTES" -ForegroundColor Red
    Write-Host "Veuillez corriger les erreurs avant de deployer" -ForegroundColor Yellow
}
