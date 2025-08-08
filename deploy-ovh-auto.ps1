# =====================================================
# SCRIPT DE DÉPLOIEMENT AUTOMATIQUE - OVH
# =====================================================
# Application: Opet Comics v1.0
# Date: $(Get-Date -Format "yyyy-MM-dd HH:mm:ss")
# 
# Ce script prépare et transfère tous les fichiers 
# nécessaires vers votre hébergement OVH
# =====================================================

Write-Host "🚀 DÉPLOIEMENT OPET COMICS v1.0" -ForegroundColor Green
Write-Host "=================================" -ForegroundColor Green
Write-Host ""

# Configuration
$sourceDir = "d:\opetit\Perso\Creations\react-ovh\ovh25"
$deployDir = "d:\opetit\Perso\Creations\react-ovh\ovh25\deploy-final"

Write-Host "📁 Préparation du répertoire de déploiement..." -ForegroundColor Yellow

# Nettoyer et créer le répertoire de déploiement
if (Test-Path $deployDir) {
    Remove-Item $deployDir -Recurse -Force
}
New-Item -ItemType Directory -Path $deployDir -Force | Out-Null

Write-Host "✅ Répertoire préparé: $deployDir" -ForegroundColor Green

# =====================================================
# ÉTAPE 1: FICHIERS ESSENTIELS APPLICATION
# =====================================================
Write-Host ""
Write-Host "📦 Copie des fichiers application..." -ForegroundColor Yellow

# Application React (build)
Copy-Item "$sourceDir\index.html" -Destination $deployDir
Copy-Item "$sourceDir\assets" -Destination $deployDir -Recurse
Copy-Item "$sourceDir\vite.svg" -Destination $deployDir

Write-Host "✅ Application React copiée" -ForegroundColor Green

# =====================================================
# ÉTAPE 2: APIs PHP CORRIGÉES
# =====================================================
Write-Host ""
Write-Host "🔧 Copie des APIs PHP corrigées..." -ForegroundColor Yellow

# APIs principales (versions corrigées de la racine)
Copy-Item "$sourceDir\get-fiches.php" -Destination $deployDir
Copy-Item "$sourceDir\post-fiche.php" -Destination $deployDir
Copy-Item "$sourceDir\update-fiche.php" -Destination $deployDir  
Copy-Item "$sourceDir\delete-fiche.php" -Destination $deployDir
Copy-Item "$sourceDir\upload-image.php" -Destination $deployDir

# Créer dossier api avec versions corrigées également
New-Item -ItemType Directory -Path "$deployDir\api" -Force | Out-Null
Copy-Item "$sourceDir\get-fiches.php" -Destination "$deployDir\api\"
Copy-Item "$sourceDir\post-fiche.php" -Destination "$deployDir\api\"
Copy-Item "$sourceDir\update-fiche.php" -Destination "$deployDir\api\"
Copy-Item "$sourceDir\delete-fiche.php" -Destination "$deployDir\api\"
Copy-Item "$sourceDir\upload-image.php" -Destination "$deployDir\api\"

# Copier données d'exemple du dossier api
if (Test-Path "$sourceDir\api\fiches.json") {
    Copy-Item "$sourceDir\api\fiches.json" -Destination "$deployDir\api\"
}

Write-Host "✅ APIs PHP copiées (racine + api/)" -ForegroundColor Green

# =====================================================
# ÉTAPE 3: DONNÉES ET UPLOADS
# =====================================================
Write-Host ""
Write-Host "Copie donnees et repertoire uploads..." -ForegroundColor Yellow

# Fichier de donnees JSON
Copy-Item "$sourceDir\fiches-data.json" -Destination $deployDir

# Repertoire uploads avec .htaccess
Copy-Item "$sourceDir\uploads" -Destination $deployDir -Recurse

Write-Host "Donnees et uploads copies" -ForegroundColor Green

# =====================================================
# ÉTAPE 4: FICHIERS DE CONFIGURATION
# =====================================================
Write-Host ""
Write-Host "⚙️ Copie fichiers de configuration..." -ForegroundColor Yellow

# .htaccess principal si existant
if (Test-Path "$sourceDir\.htaccess") {
    Copy-Item "$sourceDir\.htaccess" -Destination $deployDir
    Write-Host "✅ .htaccess principal copié" -ForegroundColor Green
}

Write-Host "✅ Configuration copiée" -ForegroundColor Green

# =====================================================
# ÉTAPE 5: VÉRIFICATION DU DÉPLOIEMENT
# =====================================================
Write-Host ""
Write-Host "🔍 Vérification du déploiement..." -ForegroundColor Yellow

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
        Write-Host "✅ $file" -ForegroundColor Green
    } else {
        Write-Host "❌ $file MANQUANT" -ForegroundColor Red
        $allGood = $false
    }
}

# =====================================================
# ÉTAPE 6: RÉSUMÉ ET INSTRUCTIONS
# =====================================================
Write-Host ""
if ($allGood) {
    Write-Host "🎉 DÉPLOIEMENT PRÉPARÉ AVEC SUCCÈS !" -ForegroundColor Green
    Write-Host "====================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "📁 Tous les fichiers sont dans: $deployDir" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "📤 ÉTAPES SUIVANTES:" -ForegroundColor Yellow
    Write-Host "1. Connectez-vous à votre FTP/SFTP OVH" -ForegroundColor White
    Write-Host "2. Transférez TOUT le contenu de deploy-final/ vers www/" -ForegroundColor White
    Write-Host "3. Vérifiez les permissions (755 pour dossiers, 644 pour fichiers)" -ForegroundColor White
    Write-Host "4. Testez votre application sur votre domaine OVH" -ForegroundColor White
    Write-Host ""
    Write-Host "🌟 FONCTIONNALITÉS INCLUSES:" -ForegroundColor Magenta
    Write-Host "• Application React moderne et responsive" -ForegroundColor White
    Write-Host "• Ajout/modification/suppression de comics" -ForegroundColor White  
    Write-Host "• Module photo avec caméra et upload" -ForegroundColor White
    Write-Host "• Système de tri et recherche" -ForegroundColor White
    Write-Host "• Stockage JSON sécurisé" -ForegroundColor White
    Write-Host ""
} else {
    Write-Host "❌ PROBLÈME DÉTECTÉ" -ForegroundColor Red
    Write-Host "Certains fichiers sont manquants. Vérifiez la structure source." -ForegroundColor Red
}

Write-Host "🎯 Bon déploiement ! 🚀" -ForegroundColor Green
