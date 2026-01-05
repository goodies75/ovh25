# Migration vers MySQL - Guide d'installation

Ce guide vous accompagne étape par étape pour migrer votre application Comics de JSON vers MySQL.

## 📋 Table des matières

1. [Prérequis](#prérequis)
2. [Fichiers de migration](#fichiers-de-migration)
3. [Instructions étape par étape](#instructions-étape-par-étape)
4. [Vérification du fonctionnement](#vérification-du-fonctionnement)
5. [Rollback (en cas de problème)](#rollback)

---

## ✅ Prérequis

- Accès SSH ou FTP à votre serveur OVH
- Base de données MySQL configurée (déjà fait : `o-petit_comics`)
- PHP 7.4 ou supérieur avec extension PDO MySQL

---

## 📦 Fichiers de migration

Les fichiers suivants ont été créés/modifiés :

### Nouveaux fichiers :
- `db-config.php` - Configuration centralisée de la BDD
- `create-table.php` - Script de création de la table `fiches`
- `migrate-json-to-mysql.php` - Script de migration des données JSON → MySQL
- `test-connection.php` - Script de test de connexion

### Fichiers modifiés :
- `get-fiches.php` - Lecture depuis MySQL
- `post-fiche.php` - Insertion dans MySQL
- `update-fiche.php` - Mise à jour dans MySQL
- `delete-fiche.php` - Suppression depuis MySQL

---

## 🚀 Instructions étape par étape

### Étape 1 : Tester la connexion à la base de données

Depuis votre navigateur, accédez à :
```
https://votre-domaine.com/test-connection.php
```

**Résultat attendu** :
```
✅ Connexion réussie !
✅ MySQL version : 8.x.x
```

**Si erreur** : Vérifiez les credentials dans `db-config.php`

---

### Étape 2 : Créer la table MySQL

Depuis votre navigateur, accédez à :
```
https://votre-domaine.com/create-table.php
```

**Résultat attendu** :
```
✅ Table 'fiches' créée avec succès !
📋 Structure de la table : [liste des colonnes]
```

**Notes** :
- Si la table existe déjà, le script vous préviendra
- La table contient 15 champs + index pour optimiser les performances

---

### Étape 3 : Migrer les données JSON vers MySQL

**IMPORTANT** : Avant cette étape, faites une sauvegarde de votre fichier `fiches-data.json` !

Depuis votre navigateur, accédez à :
```
https://votre-domaine.com/migrate-json-to-mysql.php
```

**Résultat attendu** :
```
✅ Succès : X fiche(s)
❌ Erreurs : 0 fiche(s)
📋 Total dans la base de données : X fiche(s)
💾 Sauvegarde créée : fiches-data.json.backup-YYYY-MM-DD-HHMMSS
```

**Ce que fait ce script** :
1. Lit le fichier `fiches-data.json`
2. Insère chaque fiche dans MySQL
3. Crée une sauvegarde automatique du fichier JSON
4. Affiche un rapport détaillé

---

### Étape 4 : Vérifier le fonctionnement

#### Test 1 : Afficher la liste des comics
Ouvrez votre application et vérifiez que tous vos comics s'affichent correctement.

#### Test 2 : Ajouter un comic
Ajoutez un nouveau comic depuis l'interface. Vérifiez qu'il apparaît dans la liste.

#### Test 3 : Modifier un comic
Modifiez un comic existant. Vérifiez que les changements sont sauvegardés.

#### Test 4 : Supprimer un comic
Supprimez un comic. Vérifiez qu'il disparaît de la liste.

---

## 🔍 Vérification du fonctionnement

### Via l'interface web

Testez toutes les opérations CRUD dans votre application :
- ✅ **Create** : Ajouter un comic
- ✅ **Read** : Afficher la liste
- ✅ **Update** : Modifier un comic
- ✅ **Delete** : Supprimer un comic

### Via phpMyAdmin (recommandé)

1. Connectez-vous à phpMyAdmin sur OVH
2. Sélectionnez la base `o-petit_comics`
3. Cliquez sur la table `fiches`
4. Vérifiez que vos données sont présentes

---

## 🔄 Rollback (en cas de problème)

Si vous rencontrez des problèmes et voulez revenir à la version JSON :

### Option 1 : Via Git

```bash
git checkout HEAD -- get-fiches.php post-fiche.php delete-fiche.php
```

### Option 2 : Restaurer manuellement

1. Supprimez ou renommez les fichiers modifiés
2. Restaurez les anciens fichiers depuis votre backup
3. Restaurez `fiches-data.json` depuis `fiches-data.json.backup-YYYY-MM-DD-HHMMSS`

---

## 📊 Avantages de MySQL vs JSON

| Critère | JSON | MySQL |
|---------|------|-------|
| **Performances** | Lent (> 100 fiches) | Rapide (milliers de fiches) |
| **Sécurité** | Risque de corruption | Transactions ACID |
| **Recherche** | Lent | Index optimisés |
| **Concurrence** | Problèmes | Gestion native |
| **Backup** | Manuel | Automatique (OVH) |
| **Requêtes complexes** | Impossible | SQL puissant |

---

## 🛠️ Dépannage

### Erreur : "Table already exists"
**Solution** : La table existe déjà. Passez directement à l'étape 3 (migration).

### Erreur : "Access denied"
**Solution** : Vérifiez les credentials dans `db-config.php` (lignes 8-11).

### Erreur : "SQLSTATE[HY000] [2002] Connection refused"
**Solution** : Le serveur MySQL n'est pas accessible. Contactez le support OVH.

### Les comics ne s'affichent plus
**Solution** : Vérifiez que la migration (étape 3) a bien été effectuée.

---

## 📝 Notes importantes

- **Sauvegarde** : Le fichier `fiches-data.json` original est conservé et une copie de backup est créée
- **Sécurité** : Les credentials sont dans `db-config.php` (ne PAS commit ce fichier dans Git public)
- **Performance** : MySQL est beaucoup plus rapide que JSON pour les grosses collections
- **Évolutivité** : Vous pouvez maintenant ajouter des milliers de comics sans ralentissement

---

## 🎯 Prochaines étapes (optionnelles)

Après la migration réussie, vous pouvez :

1. ✅ Supprimer le fichier `fiches-data.json` (gardez le backup)
2. ✅ Ajouter des index supplémentaires selon vos besoins
3. ✅ Mettre en place des backups automatiques
4. ✅ Optimiser les requêtes SQL si nécessaire

---

## 💡 Support

Si vous rencontrez des problèmes :
1. Consultez la section [Dépannage](#dépannage)
2. Vérifiez les logs PHP sur votre serveur
3. Testez avec `test-connection.php`

---

**Date de création** : 2026-01-05
**Version** : 1.0
**Auteur** : Claude (Migration automatisée)
