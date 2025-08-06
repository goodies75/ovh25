# 🔄 MISE À JOUR - Correction du problème d'image

Write-Host "🔄 MISE À JOUR des fichiers sur o-petit.com" -ForegroundColor Green
Write-Host "=============================================" -ForegroundColor Green

Write-Host "`n📁 Fichiers à re-uploader (correction d'image) :" -ForegroundColor Yellow

# Fichiers principaux à mettre à jour
$filesToUpdate = @(
    "deploy\index.html",
    "deploy\assets\index-Bee7Hb03.js"
)

foreach ($file in $filesToUpdate) {
    if (Test-Path $file) {
        $size = [math]::Round((Get-Item $file).Length / 1KB, 2)
        Write-Host "   ✅ $file ($size KB)" -ForegroundColor Green
    } else {
        Write-Host "   ❌ $file (manquant)" -ForegroundColor Red
    }
}

Write-Host "`n🎯 Action à faire :" -ForegroundColor Cyan
Write-Host "1. Retournez dans votre espace client OVH"
Write-Host "2. Cliquez sur 'FTP Explorer'"
Write-Host "3. Remplacez ces 2 fichiers :"
Write-Host "   - index.html (racine)"
Write-Host "   - assets/index-Bee7Hb03.js (dans le dossier assets)"

Write-Host "`n🔧 Correction apportée :" -ForegroundColor Magenta
Write-Host "   - Les images ne seront plus cachées automatiquement"
Write-Host "   - Une image par défaut s'affichera si l'URL ne fonctionne pas"

Write-Host "`n🧪 Test après mise à jour :" -ForegroundColor Green
Write-Host "   - Ajoutez une fiche avec une URL d'image"
Write-Host "   - L'image devrait s'afficher ou montrer un placeholder"

$openExplorer = Read-Host "`nOuvrir le dossier deploy pour voir les fichiers ? (o/n)"
if ($openExplorer -eq "o" -or $openExplorer -eq "O") {
    Start-Process explorer.exe "deploy\"
    Write-Host "📁 Dossier ouvert !" -ForegroundColor Green
}

Write-Host "`n✨ Prêt pour la mise à jour !" -ForegroundColor Green
