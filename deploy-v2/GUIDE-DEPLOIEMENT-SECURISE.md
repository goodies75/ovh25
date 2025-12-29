# 🚀 GUIDE DE DÉPLOIEMENT SÉCURISÉ

## 📁 Structure des fichiers

```
votre-serveur/
├── data/                    ← VOS DONNÉES (À NE JAMAIS ÉCRASER)
│   └── fiches-data.json     ← Votre vraie collection
├── uploads/                 ← VOS IMAGES (À NE JAMAIS ÉCRASER)
├── get-fiches.php           ← Code à redéployer
├── post-fiche.php           ← Code à redéployer
├── update-fiche.php         ← Code à redéployer
├── delete-fiche.php         ← Code à redéployer
├── index.html               ← Code à redéployer
├── assets/                  ← Code à redéployer
└── .htaccess                ← Code à redéployer
```

## ⚠️ IMPORTANT : Protéger vos données lors des mises à jour

### ✅ CE QU'IL FAUT FAIRE :

1. **AVANT de déployer** : Sauvegardez vos données
   ```
   - Téléchargez : https://o-petit.com/data/fiches-data.json
   - Sauvegardez le dossier uploads/ si vous avez des images
   ```

2. **Déployez SEULEMENT les fichiers de code** :
   - Tous les fichiers `.php`
   - Le dossier `assets/`
   - Le fichier `index.html`
   - Le fichier `.htaccess`

3. **N'ÉCRASEZ JAMAIS** :
   - Le dossier `data/`
   - Le dossier `uploads/`

### 🔒 Comment créer le dossier data la première fois :

1. Sur votre serveur, créez le dossier `data/`
2. Uploadez votre vraie `fiches-data.json` dans `data/`
3. Définissez les permissions : 755 pour le dossier, 644 pour le fichier

### 📝 Commandes de déploiement recommandées :

```bash
# Option 1 : Upload sélectif (RECOMMANDÉ)
- Uploadez uniquement les fichiers .php, index.html, assets/, .htaccess
- Laissez data/ et uploads/ intacts

# Option 2 : Sauvegarde puis restauration
1. Téléchargez data/fiches-data.json
2. Téléchargez le dossier uploads/
3. Uploadez tout le nouveau code
4. Restaurez data/fiches-data.json
5. Restaurez uploads/
```

## 🎯 Avantages de cette structure :

- ✅ Vos données sont protégées
- ✅ Vous pouvez mettre à jour le code sans risque
- ✅ Séparation claire entre code et données
- ✅ Sauvegardes plus faciles
