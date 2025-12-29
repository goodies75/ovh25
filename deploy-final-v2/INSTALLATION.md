# INSTALLATION COMICS D'OLIVIER - OVH

## ÉTAPES D'INSTALLATION

### 1. Base de données MySQL
- Aller dans phpMyAdmin sur OVH
- Sélectionner la base `o-petit_comics`
- Importer le fichier `create-database.sql`

### 2. Upload des fichiers
- Télécharger TOUS les fichiers de ce dossier via FTP
- Les placer à la racine de votre domaine

### 3. Permissions
- Créer le dossier `uploads/` s'il n'existe pas
- Donner les permissions 755 au dossier `uploads/`

### 4. Test
- Aller sur votre site
- Entrer le code PIN : @0149@
- Vérifier que l'application fonctionne

## FICHIERS IMPORTANTS

- `config-db.php` : Configuration base de données
- `validate-pin.php` : Authentification PIN (@0149@)
- `get-fiches.php` : Récupération des comics
- `post-fiche.php` : Ajout d'un comic
- `update-fiche.php` : Modification d'un comic
- `delete-fiche.php` : Suppression d'un comic
- `upload-image.php` : Upload d'images
- `.htaccess` : Configuration Apache (SIMPLIFIÉ pour OVH)

## EN CAS DE PROBLÈME

1. Vérifier les logs d'erreur OVH
2. Tester d'abord `validate-pin.php` directement
3. Puis tester `get-fiches.php`
4. S'assurer que la base de données est bien créée
