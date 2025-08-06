# Configuration pour React Router (SPA routing)

## Pour serveur Apache (.htaccess)
Créer un fichier `.htaccess` dans le dossier racine :

```apache
RewriteEngine On
RewriteBase /

# Handle Angular and React Router
RewriteRule ^index\.html$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.html [L]
```

## Pour serveur Nginx (nginx.conf)
```nginx
location / {
    try_files $uri $uri/ /index.html;
}
```

## Notes importantes :
- Ces configurations permettent à React Router de fonctionner avec des URLs propres
- Toutes les routes non-existantes sont redirigées vers index.html
- React Router prend ensuite le relais pour afficher la bonne page
- Nécessaire pour les routes comme /add, /list, etc.

## Pour OVH :
1. Créer un fichier .htaccess dans le dossier public_html/
2. Y placer la configuration Apache ci-dessus
3. Vérifier que le module mod_rewrite est activé (généralement déjà fait sur OVH)
