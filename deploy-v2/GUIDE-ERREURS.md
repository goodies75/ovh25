# GUIDE DE RÉSOLUTION DES ERREURS

## Erreurs observées dans le navigateur :

1. **Erreur SQL "SQLSTATE[HY000] [2002] No such file or directory"**
   - Cette erreur indique qu'il y a encore du code MySQL dans vos fichiers PHP sur le serveur de production
   - MySQL n'est PAS disponible sur l'hébergement mutualisé OVH

2. **Erreur JSON.parse: unexpected character at line 1 column 1**
   - Cette erreur indique que l'API PHP retourne du HTML/texte au lieu de JSON
   - Souvent causée par une erreur PHP qui génère un message d'erreur HTML

## SOLUTIONS À APPLIQUER :

### 1. Vérifier que vous avez bien déployé les bons fichiers
   - Assurez-vous que tous les fichiers de `deploy-v2/` sont bien sur votre serveur
   - Vérifiez spécialement que `update-fiche.php` ne contient AUCUN code MySQL

### 2. Tester l'API PHP
   - Allez sur `https://o-petit.com/test.php` pour vérifier que PHP fonctionne
   - Si cela affiche `{"status":"ok","message":"PHP fonctionne","time":"..."}`, PHP fonctionne

### 3. Vérifier les permissions des fichiers
   - `fiches-data.json` doit être en lecture/écriture (644 ou 666)
   - Les fichiers PHP doivent être en exécution (644)

### 4. Vérifier le contenu des fichiers API
   - `get-fiches.php` doit retourner uniquement du JSON
   - `post-fiche.php` doit retourner uniquement du JSON
   - `update-fiche.php` doit retourner uniquement du JSON
   - Aucun `echo`, `print_r` ou `var_dump` en dehors du JSON final

### 5. Vérifier la configuration .htaccess
   - Assurez-vous que le fichier `.htaccess` est bien déployé
   - Il gère la redirection des URLs React

## COMMANDES DE DIAGNOSTIC :

1. Testez chaque API individuellement :
   - GET: `https://o-petit.com/get-fiches.php`
   - POST: (avec un outil comme Postman)

2. Vérifiez le fichier JSON :
   - `https://o-petit.com/fiches-data.json` (si accessible)

## FICHIERS À REDÉPLOYER EN PRIORITÉ :
- `update-fiche.php` (version JSON uniquement)
- `get-fiches.php` 
- `post-fiche.php`
- `fiches-data.json`
- `index.html` (version corrigée)

Le problème principal semble être que vous avez encore l'ancienne version de `update-fiche.php` avec du code MySQL sur votre serveur de production.
