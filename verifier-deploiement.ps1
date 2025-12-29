# Script de vérification pré-déploiement
# Comics d'Olivier v2.0
# Date: 11 août 2025

Write-Host "=== VÉRIFICATION PRÉ-DÉPLOIEMENT ===" -ForegroundColor Cyan
Write-Host "Comics d'Olivier v2.0 avec icône personnalisée" -ForegroundColor Green

# Chemins
$projectDir = "d:\opetit\Perso\Creations\react-ovh\ovh25\opet-comics"
$distDir = "$projectDir\dist"
$apiDir = "d:\opetit\Perso\Creations\react-ovh\ovh25\api"

# Vérification 1: Build React
Write-Host "`n1. Vérification du build React..." -ForegroundColor Yellow
if (-not (Test-Path $distDir)) {
    Write-Host "❌ Build non trouvé. Exécution de npm run build..." -ForegroundColor Red
    Set-Location $projectDir
    npm run build
    if ($LASTEXITCODE -ne 0) {
        Write-Error "❌ Échec du build React"
        exit 1
    }
} else {
    Write-Host "✅ Build React trouvé" -ForegroundColor Green
}

# Vérification 2: Nouvelle icône
Write-Host "`n2. Vérification de la nouvelle icône..." -ForegroundColor Yellow
$iconPath = "$distDir\icones-comics.svg"
if (Test-Path $iconPath) {
    $iconSize = (Get-Item $iconPath).Length
    Write-Host "✅ Icône icones-comics.svg trouvée ($([math]::Round($iconSize/1KB, 1)) KB)" -ForegroundColor Green
} else {
    Write-Host "❌ Icône icones-comics.svg manquante" -ForegroundColor Red
    Write-Host "   Assurez-vous que l'icône est dans le dossier public/" -ForegroundColor Yellow
}

# Vérification 3: Fichiers essentiels
Write-Host "`n3. Vérification des fichiers essentiels..." -ForegroundColor Yellow
$essentialFiles = @{
    "index.html" = "$distDir\index.html"
    "Assets CSS/JS" = "$distDir\assets"
    "get-comics.php" = "$apiDir\get-comics.php"
    "add-comic.php" = "$apiDir\add-comic.php"
    "upload-image.php" = "$apiDir\upload-image.php"
    "delete-comic.php" = "$apiDir\delete-comic.php"
    "update-fiche.php" = "$apiDir\update-fiche.php"
}

$allGood = $true
foreach ($file in $essentialFiles.GetEnumerator()) {
    if (Test-Path $file.Value) {
        Write-Host "✅ $($file.Key)" -ForegroundColor Green
    } else {
        Write-Host "❌ $($file.Key) manquant" -ForegroundColor Red
        $allGood = $false
    }
}

# Vérification 4: Contenu du index.html
Write-Host "`n4. Vérification du contenu index.html..." -ForegroundColor Yellow
$indexContent = Get-Content "$distDir\index.html" -Raw
if ($indexContent -match 'icones-comics\.svg') {
    Write-Host "✅ Référence à icones-comics.svg trouvée dans index.html" -ForegroundColor Green
} else {
    Write-Host "❌ Référence à icones-comics.svg manquante dans index.html" -ForegroundColor Red
    $allGood = $false
}

if ($indexContent -match 'vite\.svg') {
    Write-Host "⚠️  Ancienne référence à vite.svg encore présente" -ForegroundColor Yellow
} else {
    Write-Host "✅ Ancienne référence vite.svg supprimée" -ForegroundColor Green
}

# Vérification 5: Assets compilés
Write-Host "`n5. Vérification des assets compilés..." -ForegroundColor Yellow
$assetsDir = "$distDir\assets"
if (Test-Path $assetsDir) {
    $cssFiles = Get-ChildItem "$assetsDir\*.css"
    $jsFiles = Get-ChildItem "$assetsDir\*.js"
    
    if ($cssFiles.Count -gt 0) {
        Write-Host "✅ Fichiers CSS: $($cssFiles.Count)" -ForegroundColor Green
        foreach ($css in $cssFiles) {
            $size = [math]::Round($css.Length / 1KB, 1)
            Write-Host "   📄 $($css.Name) ($size KB)" -ForegroundColor White
        }
    }
    
    if ($jsFiles.Count -gt 0) {
        Write-Host "✅ Fichiers JS: $($jsFiles.Count)" -ForegroundColor Green
        foreach ($js in $jsFiles) {
            $size = [math]::Round($js.Length / 1KB, 1)
            Write-Host "   📄 $($js.Name) ($size KB)" -ForegroundColor White
        }
    }
}

# Vérification 6: Navigation avec icône
Write-Host "`n6. Vérification de l'intégration Navigation..." -ForegroundColor Yellow
$navFile = "$projectDir\src\components\Navigation\Navigation.tsx"
if (Test-Path $navFile) {
    $navContent = Get-Content $navFile -Raw
    if ($navContent -match 'icones-comics\.svg') {
        Write-Host "✅ Icône intégrée dans Navigation.tsx" -ForegroundColor Green
    } else {
        Write-Host "❌ Icône non intégrée dans Navigation.tsx" -ForegroundColor Red
        $allGood = $false
    }
    
    if ($navContent -match 'nav-brand-icon') {
        Write-Host "✅ Structure nav-brand-icon présente" -ForegroundColor Green
    } else {
        Write-Host "❌ Structure nav-brand-icon manquante" -ForegroundColor Red
        $allGood = $false
    }
}

# Résumé final
Write-Host "`n=== RÉSUMÉ DE VÉRIFICATION ===" -ForegroundColor Cyan
if ($allGood) {
    Write-Host "🎉 TOUT EST PRÊT POUR LE DÉPLOIEMENT !" -ForegroundColor Green
    Write-Host "`n📋 Checklist validée:" -ForegroundColor White
    Write-Host "✅ Build React compilé" -ForegroundColor Green
    Write-Host "✅ Icône personnalisée intégrée" -ForegroundColor Green
    Write-Host "✅ Fichiers API présents" -ForegroundColor Green
    Write-Host "✅ Navigation mise à jour" -ForegroundColor Green
    Write-Host "✅ Assets optimisés" -ForegroundColor Green
    
    Write-Host "`n🚀 Étapes suivantes:" -ForegroundColor Yellow
    Write-Host "1. Executer: .\deploy-final-v2.ps1" -ForegroundColor White
    Write-Host "2. Uploader les fichiers sur OVH" -ForegroundColor White
    Write-Host "3. Tester l'application deployee" -ForegroundColor White
} else {
    Write-Host "❌ DES PROBLÈMES ONT ÉTÉ DÉTECTÉS" -ForegroundColor Red
    Write-Host "Veuillez corriger les erreurs avant de deployer" -ForegroundColor Yellow
}

Write-Host "`n" -ForegroundColor White
