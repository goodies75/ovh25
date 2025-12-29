# 🚀 GUIDE DE DÉPLOIEMENT - COMICS D'OLIVIER v2.0

## 🎨 Version 2.0 - Nouveautés
- ✅ **Icône personnalisée** : `icones-comics.svg` dans la navigation
- ✅ **Navigation améliorée** : Icône à gauche du titre
- ✅ **Design responsive** : Parfait sur mobile et desktop
- ✅ **Police Fascinate** : Style comics pour les titres
- ✅ **Icônes Lucide React** : Navigation moderne

## 📁 Fichiers prêts pour OVH

Le dossier `deploy-final-v2/` contient :

```
deploy-final-v2/
├── index.html              # Page principale avec nouvelle icône
├── icones-comics.svg       # 🎨 VOTRE ICÔNE PERSONNALISÉE
├── assets/                 # CSS et JS compilés
│   ├── index-[hash].css   # Styles avec navigation mise à jour
│   └── index-[hash].js    # Application React
├── get-fiches.php         # API - Récupération des comics
├── post-fiche.php         # API - Ajout/modification des comics  
├── update-fiche.php       # API - Mise à jour des fiches
├── fiches.json           # Base de données JSON
└── uploads/              # Dossier pour images (vide)
```

## 🔧 Étapes de déploiement

### 1. ✅ Préparation (FAIT)
- ✅ Application compilée
- ✅ Icône intégrée dans la navigation
- ✅ Fichiers API préparés
- ✅ Dossier de déploiement créé

### 2. 📤 Upload FTP vers OVH

**Connexion FTP :**
- Hôte : `ftp.votre-domaine.com`
- Utilisateur : votre login OVH
- Mot de passe : votre password OVH
- Port : 21 (FTP standard)

**Upload :**
1. Connectez-vous à votre client FTP (FileZilla, WinSCP, etc.)
2. Naviguez vers `www/` ou `public_html/` sur le serveur
3. Sélectionnez TOUS les fichiers du dossier `deploy-final-v2/`
4. Uploadez en conservant la structure des dossiers
5. Mode de transfert : **Binaire** pour les images SVG

### 3. ⚙️ Configuration des permissions

```bash
# Sur votre serveur OVH (via SSH ou gestionnaire de fichiers)
chmod 755 *.php           # Scripts PHP exécutables
chmod 755 uploads/        # Dossier uploads en écriture
chmod 644 *.html *.svg *.css *.js  # Fichiers statiques
chmod 644 fiches.json     # Base de données JSON
```

### 4. 🧪 Tests post-déploiement

**À vérifier :**
- ✅ Page d'accueil : `https://votre-domaine.com`
- ✅ Nouvelle icône visible dans l'onglet du navigateur
- ✅ Icône dans la navigation (à gauche du titre)
- ✅ Navigation responsive (tester sur mobile)
- ✅ Ajout d'un comic de test
- ✅ Upload d'une image de couverture

## 🎯 Points d'attention

### Icône personnalisée
- L'icône `icones-comics.svg` doit être accessible à la racine
- Elle apparaît dans l'onglet du navigateur (favicon)
- Elle est affichée dans la navigation à gauche du titre

### Navigation responsive
- **Desktop** : Navigation horizontale avec icône + titre à gauche
- **Mobile** : Navigation verticale centrée avec icône adaptée
- **Styles** : Police Fascinate pour le titre principal

### API fonctionnelle
- `get-fiches.php` : Récupération des comics
- `post-fiche.php` : Ajout de nouveaux comics
- `update-fiche.php` : Modification des fiches existantes

## 🐛 Résolution de problèmes

### Icône ne s'affiche pas
1. Vérifiez que `icones-comics.svg` est bien uploadé à la racine
2. Contrôlez les permissions (644)
3. Videz le cache navigateur (Ctrl+F5)

### Navigation cassée
1. Vérifiez que le dossier `assets/` est complet
2. Contrôlez que les fichiers CSS sont bien uploadés
3. Testez avec les outils développeur du navigateur

### Erreurs PHP
1. Vérifiez les permissions des fichiers `.php` (755)
2. Consultez les logs d'erreur OVH
3. Testez les endpoints API individuellement

## 🎉 Félicitations !

Votre application "Comics d'Olivier" v2.0 est maintenant prête avec :
- 🎨 Votre icône personnalisée unique
- 📱 Design responsive parfait
- ⚡ Performance optimisée
- ✨ Interface moderne avec Lucide React

**URL de test :** `https://votre-domaine.com`

---
*Déploiement préparé le 11 août 2025 - Version 2.0*
*Icône personnalisée intégrée avec succès ! 🚀*
