# 📦 DÉPLOIEMENT DU DESIGN MODERNE

## 📍 Contenu de ce dossier

Ce dossier contient **UNIQUEMENT** les fichiers du nouveau design moderne.

### ✅ Fichiers présents (11 fichiers au total)

**Frontend (3 fichiers):**
- `index.html` - Page principale
- `assets/index-kYGoWSkS.js` (248 KB) - JavaScript moderne
- `assets/index-CTogUrvR.css` (16 KB) - CSS moderne avec Tailwind
- `vite.svg` - Favicon

**Backend PHP (4 fichiers):**
- `get-fiches.php` - Récupérer les comics
- `post-fiche.php` - Ajouter un comic
- `update-fiche.php` - Modifier un comic
- `delete-fiche.php` - Supprimer un comic

**Sécurité (2 fichiers):**
- `api/validate-pin.php` - Validation du code PIN (@0149@)
- `api/generate-pin-hash.php` - Utilitaire

**Configuration (1 fichier):**
- `.htaccess` - Configuration Apache pour React Router

---

## 🚀 COMMENT UPLOADER SUR OVH

### Méthode : FileZilla

1. **Ouvrir FileZilla**

2. **Se connecter à OVH**
   - Hôte : `ssh.cluster023.hosting.ovh.net`
   - Utilisateur : `opetitcorq`
   - Mot de passe : [votre mot de passe OVH]
   - Port : `22`

3. **Uploader les fichiers**
   - À gauche : Naviguer vers `/home/user/ovh25/deploy-moderne/`
   - À droite : Naviguer vers `/home/opetitcorq/www/`
   - **Sélectionner TOUS les fichiers de deploy-moderne/**
   - **Glisser-déposer** vers le dossier `www/`
   - **Confirmer le remplacement** des fichiers existants

4. **Vider le cache du navigateur**
   - Appuyer sur `Ctrl+Shift+R` (Windows/Linux)
   - Ou `Cmd+Shift+R` (Mac)

5. **Recharger https://o-petit.com/**

---

## 🎨 Nouveautés du design

- Dégradé de fond indigo/blanc/teal
- Navigation sticky avec effet blur
- Composants UI modernisés (boutons, cards, inputs)
- Animations fluides (fade-in, slide-up, scale)
- Polices Google Fonts (Inter + Lexend)
- Glassmorphism sur certaines cards

---

## ⚠️ Important

- Le code PIN reste : **@0149@**
- La base de données `fiches.json` n'est PAS dans ce dossier (elle reste sur le serveur)
- Les anciens fichiers seront écrasés par les nouveaux

---

**Date de création :** 30 décembre 2025  
**Version :** Design Moderne v2.0
