# 🛡️ GUIDE PRESERVATION DES DONNEES - Comics d'Olivier

## 🚨 PROBLEME IDENTIFIE
- Vos comics ajoutés en ligne sont stockés dans `fiches.json` (pas une vraie BDD)
- Le déploiement écrase ce fichier avec la version locale
- ❌ **Résultat** : Perte des comics ajoutés en ligne

## ✅ SOLUTION 1 : Script de déploiement sécurisé

### **Utilisez `deploy-preserve-data.ps1`**
Ce script copie TOUT sauf `fiches.json`

```powershell
# Commande à utiliser à l'avenir
.\deploy-preserve-data.ps1
```

**Avantages :**
- ✅ Préserve vos données existantes
- ✅ Met à jour l'interface et l'icône
- ✅ Évite l'écrasement accidentel

## ✅ SOLUTION 2 : Sauvegarde avant déploiement

### **1. Avant chaque déploiement :**
1. Connectez-vous à votre FTP OVH
2. Téléchargez `fiches.json` depuis `/www/`
3. Sauvegardez-le dans `backups/fiches_YYYY-MM-DD.json`
4. Copiez-le vers `api/fiches.json` local
5. Puis déployez normalement

### **2. Structure de sauvegarde :**
```
ovh25/
├── backups/                    # 🆕 Dossier sauvegardes
│   ├── fiches_2025-08-11.json # Backup d'aujourd'hui
│   ├── fiches_2025-08-10.json # Backup d'hier
│   └── ...                    # Historique
├── api/
│   └── fiches.json            # ⚠️ Synchronisé avec serveur
└── deploy-preserve/           # Déploiement sécurisé
```

## 🔄 WORKFLOW RECOMMANDE

### **Pour mettre à jour le design/fonctionnalités :**
```bash
1. Télécharger fiches.json du serveur
2. .\deploy-preserve-data.ps1
3. Upload SANS fiches.json
```

### **Pour ajouter des comics :**
```bash
1. Utilisez directement l'interface web
2. Les données sont sauvées sur le serveur
3. Pas besoin de déploiement
```

## 🎯 ETAT ACTUEL

### **Données nettoyées :**
- ❌ Supprimé : Comics de test (Spider-Man, Batman, Watchmen)
- ❌ Supprimé : Comic invalide ("okok")
- ✅ Conservé : "Zap 0" de R. Crumb

### **Prêt pour déploiement :**
- 📁 Dossier : `deploy-preserve/`
- 🎨 Icône : `icones-comics.svg` intégrée
- 🛡️ Données : Préservées sur le serveur

## 📋 INSTRUCTIONS FINALES

### **Upload FTP :**
1. Connectez-vous à o-petit.com via FTP
2. Allez dans `/www/`
3. Uploadez TOUS les fichiers de `deploy-preserve/` **SAUF fiches.json**
4. Vos comics actuels resteront intacts

### **Test :**
1. Vérifiez https://o-petit.com/
2. Contrôlez que votre nouvelle icône apparaît
3. Vérifiez que vos comics sont toujours là
4. Testez l'ajout d'un nouveau comic

## 🚀 RESULTAT ATTENDU

✅ **Interface mise à jour** avec nouvelle icône
✅ **Navigation améliorée** avec design responsive  
✅ **Données préservées** : Vos comics restent intacts
✅ **Fonctionnalités** : Ajout/modification/suppression opérationnels

---
*Guide créé le 11 août 2025 - Comics d'Olivier v2.0*
*Système anti-perte de données activé ! 🛡️*
