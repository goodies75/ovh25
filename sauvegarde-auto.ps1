# Sauvegarde automatique de fiches.json avant deploiement
# Telecharge automatiquement depuis le serveur
# Date: 11 aout 2025

Write-Host "=== SAUVEGARDE AUTOMATIQUE FICHES.JSON ===" -ForegroundColor Cyan

# Configuration
$backupDir = "d:\opetit\Perso\Creations\react-ovh\ovh25\backups"
$apiDir = "d:\opetit\Perso\Creations\react-ovh\ovh25\api"
$serverUrl = "https://o-petit.com/fiches.json"

# Creation du dossier backup
if (-not (Test-Path $backupDir)) {
    New-Item -ItemType Directory -Path $backupDir -Force | Out-Null
    Write-Host "Dossier backups cree" -ForegroundColor Green
}

# Date pour le backup
$timestamp = Get-Date -Format "yyyy-MM-dd_HH-mm-ss"
$backupFile = "$backupDir\fiches_backup_$timestamp.json"

Write-Host "Telechargement de fiches.json depuis le serveur..." -ForegroundColor Yellow

try {
    # Telecharge le fichier depuis le serveur
    Invoke-WebRequest -Uri $serverUrl -OutFile $backupFile
    Write-Host "Sauvegarde creee: $backupFile" -ForegroundColor Green
    
    # Copie vers le projet local pour synchronisation
    Copy-Item $backupFile -Destination "$apiDir\fiches.json" -Force
    Write-Host "Projet local synchronise avec le serveur" -ForegroundColor Green
    
    # Affiche le contenu pour verification
    Write-Host "`nContenu recupere du serveur:" -ForegroundColor Cyan
    $content = Get-Content $backupFile | ConvertFrom-Json
    foreach ($comic in $content) {
        Write-Host "  - $($comic.titre)" -ForegroundColor White
    }
    
    Write-Host "`nSAUVEGARDE REUSSIE !" -ForegroundColor Green
    Write-Host "Vous pouvez maintenant deployer en toute securite" -ForegroundColor Yellow
    
} catch {
    Write-Error "Erreur lors du telechargement: $($_.Exception.Message)"
    Write-Host "`nSOLUTION MANUELLE:" -ForegroundColor Yellow
    Write-Host "1. Connectez-vous a votre FTP" -ForegroundColor White
    Write-Host "2. Telechargez manuellement fiches.json" -ForegroundColor White
    Write-Host "3. Sauvegardez-le comme: $backupFile" -ForegroundColor White
}
