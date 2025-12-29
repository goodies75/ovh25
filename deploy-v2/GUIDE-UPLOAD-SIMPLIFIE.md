# FICHIERS À UPLOADER SUR OVH - VERSION SIMPLIFIÉE

## 📋 LISTE DES FICHIERS ESSENTIELS :

### 🎯 APIs Principales (OBLIGATOIRES) :
- ✅ get-fiches.php (simplifié)
- ✅ update-fiche.php (nettoyé)
- ✅ post-fiche-simple.php (nouveau - pour ajouter)
- ✅ delete-fiche-simple.php (nouveau - pour supprimer)
- ✅ upload-simple.php (nouveau - pour images)

### 📁 Données :
- ✅ data/fiches-data.json (vos vraies données)

### 🌐 Frontend :
- ✅ index.html (votre app React)
- ✅ assets/ (dossier complet avec JS/CSS)

### ⚙️ Configuration :
- ✅ .htaccess-simple (à renommer en .htaccess)

### 📂 Dossiers à créer sur le serveur :
- uploads/ (sera créé automatiquement par upload-simple.php)

## 🚀 ORDRE D'UPLOAD :

1. Uploadez les fichiers PHP
2. Uploadez data/fiches-data.json 
3. Uploadez index.html et assets/
4. Renommez .htaccess-simple en .htaccess
5. Testez http://o-petit.com/get-fiches.php

## ✅ TESTS À FAIRE :

1. get-fiches.php → doit afficher vos données
2. Votre app React → doit se charger
3. Ajout/modification/suppression → doivent fonctionner

Cette version est ULTRA-SIMPLIFIÉE mais garde TOUS vos champs !
