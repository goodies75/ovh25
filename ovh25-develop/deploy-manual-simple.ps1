# Script PowerShell pour deploiement manuel sur o-petit.com
Write-Host "Deploiement manuel Opet Comics sur o-petit.com" -ForegroundColor Green
Write-Host "================================================" -ForegroundColor Green

$deployFolder = ".\deploy"

Write-Host "Dossier a uploader : $deployFolder" -ForegroundColor Yellow
Write-Host "Destination : o-petit.com (dossier www)" -ForegroundColor Yellow

Write-Host "`nFichiers dans le dossier deploy :" -ForegroundColor Cyan
Get-ChildItem $deployFolder -Recurse | ForEach-Object {
    $relativePath = $_.FullName.Replace((Resolve-Path $deployFolder).Path, "")
    $relativePath = $relativePath.TrimStart('\')
    if ($_.PSIsContainer) {
        Write-Host "   DOSSIER $relativePath/" -ForegroundColor Blue
    } else {
        $size = [math]::Round($_.Length / 1KB, 2)
        Write-Host "   FICHIER $relativePath ($size KB)" -ForegroundColor White
    }
}

Write-Host "`nMethodes de deploiement disponibles :" -ForegroundColor Yellow
Write-Host "   1. Interface web OVH (Espace client > Web Cloud > Hebergements)"
Write-Host "   2. Client FTP (FileZilla, WinSCP, etc.)"
Write-Host "   3. Ligne de commande"

$totalFiles = (Get-ChildItem $deployFolder -Recurse -File).Count
$totalSize = [math]::Round((Get-ChildItem $deployFolder -Recurse -File | Measure-Object Length -Sum).Sum / 1KB, 2)
Write-Host "`nTotal fichiers : $totalFiles" -ForegroundColor Magenta
Write-Host "Taille totale : $totalSize KB" -ForegroundColor Magenta

Write-Host "`nApres upload, testez : https://o-petit.com/votre-dossier/" -ForegroundColor Green

$openFolder = Read-Host "`nOuvrir le dossier deploy dans l'explorateur ? (o/n)"
if ($openFolder -eq "o" -or $openFolder -eq "O") {
    Start-Process explorer.exe $deployFolder
    Write-Host "Dossier ouvert !" -ForegroundColor Green
}

Write-Host "`nPret pour le deploiement manuel !" -ForegroundColor Green
