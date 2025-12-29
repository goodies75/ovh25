# =================================================================
# DEPLOIEMENT FINAL - COMICS D'OLIVIER V2.0
# Version avec toutes les nouveautes: PIN, Upload, Icone custom
# =================================================================

Write-Host "🚀 DEPLOIEMENT COMICS D'OLIVIER V2.0" -ForegroundColor Cyan
Write-Host "=====================================`n" -ForegroundColor Cyan

# Variables
$sourceDir = ".\opet-comics"
$apiDir = ".\api"
$deployDir = ".\deploy-final-v2"

# 1. Nettoyage du dossier de deploiement
Write-Host "🧹 Nettoyage du dossier de deploiement..." -ForegroundColor Yellow
if (Test-Path $deployDir) {
    Remove-Item $deployDir -Recurse -Force
}
New-Item -ItemType Directory -Path $deployDir -Force | Out-Null

# 2. Build du projet React
Write-Host "⚡ Build du projet React..." -ForegroundColor Yellow
Set-Location $sourceDir
npm run build
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Erreur lors du build React!" -ForegroundColor Red
    exit 1
}
Set-Location ..

# 3. Copie des fichiers build
Write-Host "📦 Copie des fichiers frontend..." -ForegroundColor Yellow
$distPath = "$sourceDir\dist"
Copy-Item "$distPath\*" $deployDir -Recurse -Force

# 4. Copie des APIs
Write-Host "🔌 Copie des APIs..." -ForegroundColor Yellow
Copy-Item "$apiDir\get-fiches.php" $deployDir -Force
Copy-Item "$apiDir\post-fiche.php" $deployDir -Force
Copy-Item "$apiDir\upload-image.php" $deployDir -Force
Copy-Item "$apiDir\validate-pin.php" $deployDir -Force
Copy-Item "$apiDir\fiches.json" $deployDir -Force

# 5. Copie de l'icone personnalisee
Write-Host "🎨 Copie de l'icone personnalisee..." -ForegroundColor Yellow
if (Test-Path ".\icones-comics.svg") {
    Copy-Item ".\icones-comics.svg" $deployDir -Force
    Write-Host "  ✅ Icone personnalisee copiee" -ForegroundColor Green
} else {
    Write-Host "  ⚠️  Icone personnalisee non trouvee" -ForegroundColor Orange
}

# 6. Creation du dossier uploads
Write-Host "📁 Creation du dossier uploads..." -ForegroundColor Yellow
New-Item -ItemType Directory -Path "$deployDir\uploads" -Force | Out-Null

# 7. Creation d'un .htaccess pour OVH
Write-Host "⚙️  Creation du fichier .htaccess..." -ForegroundColor Yellow
$htaccessContent = @"
# Configuration pour OVH
RewriteEngine On

# Gestion des fichiers statiques
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType image/svg+xml "access plus 1 month"
</IfModule>

# CORS pour les APIs
<IfModule mod_headers.c>
    Header always set Access-Control-Allow-Origin "*"
    Header always set Access-Control-Allow-Methods "GET, POST, OPTIONS"
    Header always set Access-Control-Allow-Headers "Content-Type"
</IfModule>

# Redirection des routes React vers index.html
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_URI} !^/api/
RewriteRule ^(.*)$ index.html [QSA,L]
"@
Set-Content -Path "$deployDir\.htaccess" -Value $htaccessContent -Encoding UTF8

# 8. Affichage du resume
Write-Host "`n📋 RESUME DU DEPLOIEMENT" -ForegroundColor Cyan
Write-Host "========================" -ForegroundColor Cyan
Write-Host "Dossier de deploiement: $deployDir" -ForegroundColor White

Write-Host "`n📁 Fichiers deployes:" -ForegroundColor White
Get-ChildItem $deployDir -Recurse | ForEach-Object {
    if (-not $_.PSIsContainer) {
        $relativePath = $_.FullName.Replace((Get-Item $deployDir).FullName, "")
        $size = [math]::Round($_.Length / 1KB, 1)
        Write-Host "  📄 $relativePath ($size KB)" -ForegroundColor Gray
    }
}

Write-Host "`n✅ DEPLOIEMENT PRET !" -ForegroundColor Green
Write-Host "=====================" -ForegroundColor Green
Write-Host "🎯 Nouveautes incluses:" -ForegroundColor White
Write-Host "  ✅ Icone personnalisee integree" -ForegroundColor Green
Write-Host "  ✅ Securite PIN (code: 2025)" -ForegroundColor Green
Write-Host "  ✅ Upload d'images fonctionnel" -ForegroundColor Green
Write-Host "  ✅ Navigation responsive" -ForegroundColor Green
Write-Host "  ✅ Police Fascinate" -ForegroundColor Green
Write-Host "  ✅ Icones Lucide React" -ForegroundColor Green

Write-Host "`n🚀 Prochaine etape:" -ForegroundColor Cyan
Write-Host "Uploadez le contenu de '$deployDir' vers votre serveur OVH" -ForegroundColor White
Write-Host "Cible: dossier 'www' ou 'public_html' de votre hebergement" -ForegroundColor White

Write-Host "`n🎉 Votre application est prete pour la production !" -ForegroundColor Magenta
