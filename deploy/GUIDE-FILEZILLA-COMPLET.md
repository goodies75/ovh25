# 📂 **FILEZILLA - LISTE EXACTE DES FICHIERS À UPLOADER**

## 🎯 **SOURCE (Côté local) :**
`d:\opetit\Perso\Creations\react-ovh\ovh25\deploy\`

## 🎯 **DESTINATION (Côté distant) :**
`/public_html/` (ou dossier racine de votre site)

---

## 📁 **FICHIERS À UPLOADER :**

### **✅ OBLIGATOIRES (Remplacer/Ajouter) :**
```
.htaccess                     ← NOUVEAU (Configuration React Router)
index.html                    ← REMPLACER (Build React avec cartes compactes)
vite.svg                      ← Icône

assets/                       ← TOUT LE DOSSIER (remplacer complètement)
├── index-Dd4nfy4I.js        ← JavaScript corrigé
├── index-rf2k9tYX.css       ← CSS avec nouveaux styles
└── ... (tous les autres)     ← Assets générés
```

### **✅ API PHP (Conserver/Remplacer) :**
```
get-fiches.php               ← API récupération
post-fiche.php               ← API ajout
delete-fiche.php             ← API suppression
test-php.php                 ← Test connexion
```

---

## 🔄 **PROCÉDURE FILEZILLA :**

### **Étape 1 : Sélection**
- **Côté local :** Sélectionner TOUS les fichiers du dossier `/deploy/`
- **Ctrl+A** pour tout sélectionner

### **Étape 2 : Upload**
- **Glisser-Déposer** du côté local vers le côté distant
- OU **Clic droit** → "Envoyer vers le serveur"

### **Étape 3 : Confirmation**
- **Remplacer** les fichiers existants si demandé
- **Attendre** la fin du transfert (barre de progression)

---

## ⚠️ **POINTS IMPORTANTS :**

### **OBLIGATOIRE :**
- ✅ **`.htaccess`** doit être transféré (React Router)
- ✅ **Dossier `assets/` COMPLET** (nouveau build)
- ✅ **Mode transfert : AUTO** (Filezilla détecte le type)

### **VÉRIFICATIONS :**
- ✅ **Permissions :** 644 pour les fichiers, 755 pour les dossiers
- ✅ **Taille :** Vérifier que les fichiers ont bien la même taille
- ✅ **`.htaccess` visible** (parfois masqué)

---

## 🧪 **APRÈS UPLOAD - TEST :**

### **URLs à tester :**
1. `https://votre-site.com/` → Page d'accueil
2. `https://votre-site.com/list` → **Cartes compactes**
3. `https://votre-site.com/add` → Formulaire d'ajout

### **Test cartes compactes :**
4. **Cliquer sur une carte** → Modal avec détails
5. **Bouton "Modifier"** → Formulaire pré-rempli
6. **Bouton "Supprimer"** → Confirmation

---

## 🎉 **RÉSULTAT ATTENDU :**

✅ **Interface moderne** avec cartes cliquables  
✅ **Navigation React Router** (URLs propres)  
✅ **Modal de détails** complète  
✅ **Édition fonctionnelle**  
✅ **API PHP** opérationnelle  

**Votre site sera transformé !** 🚀
