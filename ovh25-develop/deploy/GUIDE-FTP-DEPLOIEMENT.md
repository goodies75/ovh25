# 📤 Guide de Déploiement FTP - Opet Comics avec React Router

## 🎯 Fichiers à uploader sur OVH (public_html/)

### 📁 **FICHIERS OBLIGATOIRES À REMPLACER/AJOUTER :**

#### **1. Application React (NOUVEAUX fichiers)** ⚠️ **REMPLACER**
```
index.html                    ← REMPLACER (nouvelle version avec React Router)
vite.svg                     ← Conserver (icône)
```

#### **2. Assets compilés (NOUVEAU build)** ⚠️ **REMPLACER DOSSIER COMPLET**
```
assets/
  ├── index-DN6j97dk.js      ← JavaScript principal avec React Router
  ├── index-DxSfgins.css     ← CSS principal avec tous les styles
  └── (autres fichiers...)    ← Assets générés automatiquement
```

#### **3. Configuration serveur (NOUVEAU)** ⚠️ **AJOUTER**
```
.htaccess                    ← NOUVEAU - Configuration Apache pour React Router
```

#### **4. API PHP (CONSERVER)** ✅ **Garder tels quels**
```
get-fiches.php              ← API pour récupérer les comics
post-fiche.php              ← API pour ajouter un comic  
delete-fiche.php            ← API pour supprimer un comic
test-php.php                ← Test de connection DB
```

---

## 🔄 **Action par action sur le FTP :**

### **Étape 1 : Sauvegarder l'existant**
1. Se connecter au FTP OVH
2. Aller dans `/public_html/`
3. **Sauvegarder les fichiers PHP** (au cas où)

### **Étape 2 : Remplacer les fichiers React**
1. **SUPPRIMER** l'ancien dossier `assets/` (s'il existe)
2. **UPLOADER** le nouveau dossier `assets/` depuis `/deploy/assets/`
3. **REMPLACER** `index.html` par le nouveau depuis `/deploy/index.html`

### **Étape 3 : Ajouter la configuration serveur**
4. **UPLOADER** `.htaccess` depuis `/deploy/.htaccess`

### **Étape 4 : Vérifier les API PHP**
5. **CONSERVER** tous les fichiers `*.php` existants
6. Ou les remplacer par ceux de `/deploy/` s'ils ont été modifiés

---

## 📋 **Checklist FTP complète :**

```
public_html/
├── .htaccess                 ← ⚠️ NOUVEAU (React Router config)
├── index.html                ← ⚠️ REMPLACER (build React Router)
├── vite.svg                  ← ✅ Conserver
├── assets/                   ← ⚠️ REMPLACER DOSSIER COMPLET
│   ├── index-DN6j97dk.js    ← Nouveau build React Router
│   ├── index-DxSfgins.css   ← Nouveaux styles
│   └── ...                   ← Autres assets générés
├── get-fiches.php           ← ✅ API existante (conserver)
├── post-fiche.php           ← ✅ API existante (conserver)
├── delete-fiche.php         ← ✅ API existante (conserver)
└── test-php.php             ← ✅ Test DB (conserver)
```

---

## ⚠️ **Points critiques :**

### **OBLIGATOIRE pour React Router :**
- ✅ `.htaccess` doit être présent (URLs /add, /list)
- ✅ `index.html` doit être la nouvelle version
- ✅ Dossier `assets/` complet avec nouveau build

### **NE PAS TOUCHER :**
- ✅ Base de données existante
- ✅ Configuration PHP
- ✅ Autres fichiers du site

---

## 🚀 **Après upload :**

### **Tester ces URLs :**
- `https://votresite.com/` → Page d'accueil
- `https://votresite.com/add` → Page d'ajout
- `https://votresite.com/list` → Page de liste
- `https://votresite.com/api-test` → Test API PHP

### **Si problème :**
1. Vérifier que `.htaccess` est bien uploadé
2. Vérifier les permissions (644 pour .htaccess)
3. Vérifier que mod_rewrite est activé (normalement OK sur OVH)

---

## 📁 **Résumé : Fichiers depuis /deploy/ vers FTP**

**COPIER TOUT LE CONTENU DE `/deploy/` VERS `/public_html/`**

Les fichiers importants :
- `.htaccess` (nouveau)
- `index.html` (remplacer)  
- `assets/` (remplacer dossier complet)
- `*.php` (conserver ou remplacer si modifiés)
