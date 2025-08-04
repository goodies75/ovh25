# 🚀 Déploiement Opet Comics sur o-petit.com

**🔄 Déploiement automatique configuré !** - Dernière mise à jour : 4 août 2025

## 📁 Fichiers à télécharger sur votre serveur

Voici tous les fichiers que vous devez télécharger dans un dossier sur votre serveur o-petit.com :

### 1. Fichiers de l'application React (générés)
- `index.html` - Page principale
- `assets/` - Dossier contenant les fichiers CSS et JS
  - `index-CJf1j6UJ.css` - Styles de l'application
  - `index-Chx8y_sq.js` - Code JavaScript de l'application
- `vite.svg` - Icône

### 2. Fichiers PHP de l'API
- `get-fiches.php` - API pour récupérer les fiches
- `post-fiche.php` - API pour ajouter une fiche

## 📤 Instructions de déploiement

1. **Créer un dossier** sur votre serveur (par exemple `/comics/` ou `/opet-comics/`)

2. **Télécharger tous les fichiers** de ce dossier `deploy/` vers votre serveur

3. **Vérifier les permissions** : Assurez-vous que PHP peut écrire dans le dossier (pour créer `fiches-data.json`)

4. **Tester l'application** en visitant : `https://o-petit.com/votre-dossier/`

## 🔧 Structure sur le serveur

```
votre-dossier/
├── index.html
├── get-fiches.php
├── post-fiche.php
├── vite.svg
├── fiches-data.json (sera créé automatiquement)
└── assets/
    ├── index-CJf1j6UJ.css
    └── index-Chx8y_sq.js
```

## 🎯 Points importants

- Les données sont stockées dans `fiches-data.json` qui sera créé automatiquement
- L'application fonctionne entièrement côté client avec des appels AJAX vers les fichiers PHP
- Aucune base de données n'est nécessaire
- Compatible avec tous les hébergements PHP

## 🐛 En cas de problème

1. Vérifiez que tous les fichiers sont bien téléchargés
2. Vérifiez les permissions d'écriture du dossier
3. Consultez les logs d'erreur PHP de votre hébergeur
4. Testez les URLs directement :
   - `https://o-petit.com/votre-dossier/get-fiches.php`
   - `https://o-petit.com/votre-dossier/post-fiche.php` (avec une requête POST)

## ✨ Fonctionnalités

- ✅ Interface moderne et responsive
- ✅ Ajout de fiches avec titre, description et image
- ✅ Affichage en cartes avec animations
- ✅ Gestion d'erreurs et états de chargement
- ✅ Stockage persistant des données
