# SCRIPT DE DÉPLOIEMENT V2 - COMICS D'OLIVIER
# Ce script vous aide à déployer via FTP sur OVH

Write-Host "🚀 DÉPLOIEMENT V2 - COMICS D'OLIVIER" -ForegroundColor Green
Write-Host "=====================================" -ForegroundColor Green

Write-Host "`n📦 CONTENU À DÉPLOYER :" -ForegroundColor Yellow
Write-Host "Source : deploy-v2/" -ForegroundColor White
Write-Host "Destination : public_html/ (sur OVH)" -ForegroundColor White

Write-Host "`n📋 FICHIERS INCLUS :" -ForegroundColor Yellow
$files = @(
    "index.html (Application React)",
    "assets/ (CSS/JS)",
    "*.php (APIs)",
    "fiches-data.json (VOS DONNÉES)",
    ".htaccess (Configuration)",
    "uploads/ (Images)"
)

foreach ($file in $files) {
    Write-Host "  ✅ $file" -ForegroundColor Green
}

Write-Host "`n🔐 INFORMATIONS :" -ForegroundColor Yellow
Write-Host "  PIN Admin : @0149@" -ForegroundColor White
Write-Host "  Données réelles : Zap 0 (R. Crumb)" -ForegroundColor White
Write-Host "  Structure : JSON simple (titre, description, image_url)" -ForegroundColor White

Write-Host "`n📝 ÉTAPES DE DÉPLOIEMENT :" -ForegroundColor Yellow
Write-Host "  1. Connectez-vous à votre FTP OVH" -ForegroundColor White
Write-Host "  2. Naviguez vers public_html/" -ForegroundColor White
Write-Host "  3. Supprimez les anciens fichiers" -ForegroundColor White
Write-Host "  4. Uploadez TOUT le contenu de deploy-v2/" -ForegroundColor White
Write-Host "  5. Vérifiez les permissions des dossiers uploads/" -ForegroundColor White

Write-Host "`n🌐 APRÈS DÉPLOIEMENT :" -ForegroundColor Yellow
Write-Host "  ➡️ Testez : votresite.com" -ForegroundColor White
Write-Host "  ➡️ Vérifiez que Zap 0 s'affiche" -ForegroundColor White
Write-Host "  ➡️ Testez l'ajout d'un nouveau comic" -ForegroundColor White

Write-Host "`n✅ PRÊT POUR DÉPLOIEMENT !" -ForegroundColor Green
