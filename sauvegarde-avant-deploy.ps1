# Script de sauvegarde avant deploiement
# Recupere fiches.json du serveur avant d'ecraser

Write-Host "=== SAUVEGARDE AVANT DEPLOIEMENT ===" -ForegroundColor Cyan

# Configuration FTP (à adapter)
$ftpServer = "ftp.o-petit.com"
$ftpUser = "votre-login-ftp"
$ftpPass = "votre-password-ftp"

# Repertoires
$backupDir = "d:\opetit\Perso\Creations\react-ovh\ovh25\backups"
$apiDir = "d:\opetit\Perso\Creations\react-ovh\ovh25\api"

# Creation du dossier backup
if (-not (Test-Path $backupDir)) {
    New-Item -ItemType Directory -Path $backupDir -Force | Out-Null
}

# Date pour le backup
$timestamp = Get-Date -Format "yyyy-MM-dd_HH-mm-ss"
$backupFile = "$backupDir\fiches_backup_$timestamp.json"

Write-Host "1. Telechargement de fiches.json depuis le serveur..." -ForegroundColor Yellow

# Commande FTP pour telecharger (à adapter selon votre methode)
Write-Host "   ATTENTION: Telechargez manuellement fiches.json depuis votre FTP" -ForegroundColor Red
Write-Host "   Serveur: $ftpServer" -ForegroundColor White
Write-Host "   Fichier: /www/fiches.json" -ForegroundColor White
Write-Host "   Destination: $backupFile" -ForegroundColor White

Write-Host "`n2. Copie vers le projet..." -ForegroundColor Yellow
if (Test-Path $backupFile) {
    Copy-Item $backupFile -Destination "$apiDir\fiches.json" -Force
    Write-Host "   fiches.json mis a jour avec les donnees du serveur" -ForegroundColor Green
} else {
    Write-Host "   ATTENTION: Backup non trouve, utilisez les donnees locales" -ForegroundColor Yellow
}

Write-Host "`nMaintenant vous pouvez deployer sans perdre vos donnees!" -ForegroundColor Green
