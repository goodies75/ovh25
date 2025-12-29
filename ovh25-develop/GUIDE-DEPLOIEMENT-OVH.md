# 🚀 Configuration du déploiement automatique OVH

## 📋 Étapes à suivre dans l'espace client OVH

### 1. 🔐 Connexion et navigation
1. Connectez-vous à votre [espace client OVH](https://www.ovh.com/manager/)
2. Allez dans **Web Cloud** > **Hébergements**
3. Sélectionnez votre hébergement **o-petit.com**

### 2. 🔧 Configuration Git
1. Dans le menu de gauche, cherchez la section **"Git"** ou **"Déploiement automatique"**
2. Cliquez sur **"Configurer un déploiement Git"** ou **"Ajouter un dépôt"**

### 3. 📝 Paramètres à renseigner

```
Repository URL: https://github.com/goodies75/ovh25.git
Branche: main
Répertoire source (dans le repo): deploy/
Répertoire de destination: www/
```

**⚠️ Important :** 
- **Répertoire source** : `deploy/` (c'est le dossier qui contient nos fichiers prêts)
- **Répertoire de destination** : `www/` (racine du site, ou créer un sous-dossier comme `www/comics/`)

### 4. 🔑 Clé SSH (déjà configurée)
- ✅ La clé SSH publique a déjà été ajoutée comme "Deploy Key" sur GitHub
- ✅ Elle devrait être automatiquement détectée par OVH

### 5. ⚡ Activation
1. Cochez **"Déploiement automatique activé"**
2. Validez la configuration
3. OVH devrait déclencher un premier déploiement

## 🎯 Résultat attendu

Après configuration :
- ✅ À chaque `git push` vers la branche `main`
- ✅ OVH télécharge automatiquement le contenu du dossier `deploy/`
- ✅ Les fichiers sont copiés vers `www/` (ou le dossier choisi)
- ✅ Le site est mis à jour automatiquement

## 🧪 Test de fonctionnement

Une fois configuré, nous ferons un commit de test :

```bash
git commit -m "🧪 Test déploiement automatique OVH"
git push origin main
```

Le site devrait être mis à jour dans les 2-5 minutes.

## 🔍 Vérifications possibles

### URLs à tester après déploiement :
- **Principal** : https://o-petit.com/
- **Sous-dossier** : https://o-petit.com/comics/ (si configuré)
- **Test PHP** : https://o-petit.com/test-php.php

### Logs OVH
- Dans l'espace client, section Git, vous pouvez consulter les logs de déploiement
- Vérifiez s'il y a des erreurs

## 🚨 En cas de problème

### Si la section Git n'existe pas :
- Votre hébergement ne supporte peut-être pas Git
- Utilisez le déploiement manuel via FTP

### Si le déploiement échoue :
1. Vérifiez les logs dans l'espace client OVH
2. Vérifiez que le repository GitHub est bien accessible
3. Vérifiez les permissions sur le dossier de destination

### Alternative immédiate - Déploiement manuel :
Si vous voulez voir le résultat tout de suite, utilisez FileZilla ou l'interface web OVH pour uploader manuellement tous les fichiers du dossier `deploy/` vers `www/`.

## 📞 Support
- [Documentation OVH Git](https://docs.ovh.com/fr/hosting/deploiement-git/)
- Support OVH si problème technique
