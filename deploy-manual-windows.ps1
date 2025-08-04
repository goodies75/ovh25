# Script PowerShell pour déploiement manuel sur o-petit.com
# Usage: .\deploy-manual-windows.ps1

Write-Host "🚀 Déploiement manuel Opet Comics sur o-petit.com" -ForegroundColor Green
Write-Host "===================================================" -ForegroundColor Green

$deployFolder = ".\deploy"
$serverInfo = @"

📋 INSTRUCTIONS DE DÉPLOIEMENT MANUEL

1. 🗂️  Dossier à uploader : $deployFolder\
2. 🌐 Destination : o-petit.com (dossier www ou sous-dossier)
3. 📁 Contenu à uploader :

"@

Write-Host $serverInfo -ForegroundColor Yellow

# Lister les fichiers à uploader
Write-Host "📁 Fichiers dans le dossier deploy :" -ForegroundColor Cyan
Get-ChildItem $deployFolder -Recurse | ForEach-Object {
    $relativePath = $_.FullName.Replace((Resolve-Path $deployFolder).Path, "")
    $relativePath = $relativePath.TrimStart('\')
    if ($_.PSIsContainer) {
        Write-Host "   📁 $relativePath/" -ForegroundColor Blue
    } else {
        $size = [math]::Round($_.Length / 1KB, 2)
        Write-Host "   📄 $relativePath ($size KB)" -ForegroundColor White
    }
}

Write-Host "`n🔧 Méthodes de déploiement disponibles :" -ForegroundColor Yellow
Write-Host "   1. 🌐 Interface web OVH (Espace client > Web Cloud > Hébergements)"
Write-Host "   2. 📁 Client FTP (FileZilla, WinSCP, etc.)"
Write-Host "   3. 💻 Ligne de commande avec WinSCP ou PowerShell"

Write-Host "`n📊 Statistiques :" -ForegroundColor Magenta
$totalFiles = (Get-ChildItem $deployFolder -Recurse -File).Count
$totalSize = [math]::Round((Get-ChildItem $deployFolder -Recurse -File | Measure-Object Length -Sum).Sum / 1KB, 2)
Write-Host "   📄 Total fichiers : $totalFiles"
Write-Host "   💾 Taille totale : $totalSize KB"

Write-Host "`n🎯 Après upload, testez : https://o-petit.com/votre-dossier/" -ForegroundColor Green

# Optionnel : Ouvrir le dossier dans l'explorateur
$openFolder = Read-Host "`n❓ Ouvrir le dossier deploy dans l'explorateur ? (o/n)"
if ($openFolder -eq "o" -or $openFolder -eq "O") {
    Start-Process explorer.exe $deployFolder
    Write-Host "📁 Dossier ouvert !" -ForegroundColor Green
}

Write-Host "`n✅ Script terminé. Prêt pour le déploiement manuel !" -ForegroundColor Green
