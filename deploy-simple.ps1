# DEPLOIEMENT OVH - OPET COMICS
# ==============================

Write-Host "DEPLOIEMENT OPET COMICS v1.0" -ForegroundColor Green
Write-Host "=============================" -ForegroundColor Green

# Configuration
$sourceDir = "d:\opetit\Perso\Creations\react-ovh\ovh25"
$deployDir = "d:\opetit\Perso\Creations\react-ovh\ovh25\deploy-final"

Write-Host "Preparation du repertoire..." -ForegroundColor Yellow

# Nettoyer et creer le repertoire
if (Test-Path $deployDir) {
    Remove-Item $deployDir -Recurse -Force
}
New-Item -ItemType Directory -Path $deployDir -Force | Out-Null

Write-Host "Repertoire pret: $deployDir" -ForegroundColor Green

# ETAPE 1: Application React
Write-Host "Copie application React..." -ForegroundColor Yellow
Copy-Item "$sourceDir\index.html" -Destination $deployDir
Copy-Item "$sourceDir\assets" -Destination $deployDir -Recurse
Copy-Item "$sourceDir\vite.svg" -Destination $deployDir
Write-Host "Application React copiee" -ForegroundColor Green

# ETAPE 2: APIs PHP
Write-Host "Copie APIs PHP..." -ForegroundColor Yellow
Copy-Item "$sourceDir\get-fiches.php" -Destination $deployDir
Copy-Item "$sourceDir\post-fiche.php" -Destination $deployDir
Copy-Item "$sourceDir\update-fiche.php" -Destination $deployDir  
Copy-Item "$sourceDir\delete-fiche.php" -Destination $deployDir
Copy-Item "$sourceDir\upload-image.php" -Destination $deployDir

# Créer dossier api avec versions corrigées
New-Item -ItemType Directory -Path "$deployDir\api" -Force | Out-Null
Copy-Item "$sourceDir\get-fiches.php" -Destination "$deployDir\api\"
Copy-Item "$sourceDir\post-fiche.php" -Destination "$deployDir\api\"
Copy-Item "$sourceDir\update-fiche.php" -Destination "$deployDir\api\"
Copy-Item "$sourceDir\delete-fiche.php" -Destination "$deployDir\api\"
Copy-Item "$sourceDir\upload-image.php" -Destination "$deployDir\api\"

# Copier données d'exemple
if (Test-Path "$sourceDir\api\fiches.json") {
    Copy-Item "$sourceDir\api\fiches.json" -Destination "$deployDir\api\"
}

Write-Host "APIs PHP copiees (racine + api/)" -ForegroundColor Green

# ETAPE 3: Donnees
Write-Host "Copie donnees..." -ForegroundColor Yellow
Copy-Item "$sourceDir\fiches-data.json" -Destination $deployDir
Copy-Item "$sourceDir\uploads" -Destination $deployDir -Recurse
Write-Host "Donnees copiees" -ForegroundColor Green

# ETAPE 4: Configuration
Write-Host "Copie configuration..." -ForegroundColor Yellow
if (Test-Path "$sourceDir\.htaccess") {
    Copy-Item "$sourceDir\.htaccess" -Destination $deployDir
}
Write-Host "Configuration copiee" -ForegroundColor Green

# VERIFICATION
Write-Host "Verification..." -ForegroundColor Yellow
$files = @(
    "index.html",
    "assets\index-Cp6XDBd2.js", 
    "assets\index-BEYPpTHx.css",
    "get-fiches.php",
    "post-fiche.php",
    "update-fiche.php", 
    "delete-fiche.php",
    "upload-image.php",
    "api\get-fiches.php",
    "api\post-fiche.php",
    "api\fiches.json",
    "fiches-data.json",
    "uploads\.htaccess"
)

$allGood = $true
foreach ($file in $files) {
    $filePath = Join-Path $deployDir $file
    if (Test-Path $filePath) {
        Write-Host "OK: $file" -ForegroundColor Green
    } else {
        Write-Host "MANQUANT: $file" -ForegroundColor Red
        $allGood = $false
    }
}

Write-Host ""
if ($allGood) {
    Write-Host "DEPLOIEMENT PRET !" -ForegroundColor Green
    Write-Host "==================" -ForegroundColor Green
    Write-Host ""
    Write-Host "Fichiers dans: $deployDir" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "ETAPES SUIVANTES:" -ForegroundColor Yellow
    Write-Host "1. Connectez-vous a votre FTP OVH"
    Write-Host "2. Transferez tout le contenu de deploy-final/ vers www/"
    Write-Host "3. Verifiez les permissions"
    Write-Host "4. Testez votre application"
    Write-Host ""
} else {
    Write-Host "PROBLEME DETECTE" -ForegroundColor Red
}

Write-Host "Bon deploiement !" -ForegroundColor Green
