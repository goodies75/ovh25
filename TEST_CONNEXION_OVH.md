# 🔌 Test de Connexion OVH - Guide Pratique

**Date:** 29 décembre 2025

---

## ⚠️ Note Importante

SSH n'est pas disponible dans l'environnement Claude Code actuel, donc je ne peux pas tester directement la connexion. Voici comment vous pouvez la tester depuis votre machine.

---

## 📋 Informations de Connexion

```
Serveur:  ssh.cluster023.hosting.ovh.net
Utilisateur: opetitcorq
Port: 22
Chemin distant: /home/opetitcorq/www
```

---

## 🧪 Test 1 : Connexion SSH de Base

### Sur Linux / Mac

Ouvrez un terminal et exécutez :

```bash
ssh opetitcorq@ssh.cluster023.hosting.ovh.net
```

**Résultat attendu :**
- Si vous avez une clé SSH configurée : Connexion directe ✅
- Si pas de clé : Demande de mot de passe
- Si erreur : Problème de configuration

### Sur Windows

**Option 1 : PowerShell (Windows 10+)**
```powershell
ssh opetitcorq@ssh.cluster023.hosting.ovh.net
```

**Option 2 : PuTTY**
1. Télécharger PuTTY : https://www.putty.org/
2. Host Name : `ssh.cluster023.hosting.ovh.net`
3. Port : `22`
4. Username : `opetitcorq`
5. Cliquer "Open"

---

## 🧪 Test 2 : Vérifier le Dossier Web

Une fois connecté en SSH, exécutez ces commandes :

```bash
# Aller dans le dossier web
cd /home/opetitcorq/www

# Lister les fichiers
ls -la

# Voir l'espace disque utilisé
du -sh .

# Vérifier si Git est initialisé
ls -la .git 2>/dev/null && echo "Git initialisé ✅" || echo "Git non initialisé ❌"
```

**Ce que vous devriez voir :**
- Liste des fichiers déployés (index.html, assets/, etc.)
- Taille du dossier
- État de Git

---

## 🧪 Test 3 : Tester le Site Web

### Dans votre Navigateur

Essayez ces URLs :

1. **Page principale**
   ```
   https://o-petit.com/
   ```
   ou
   ```
   http://o-petit.com/
   ```

2. **Test si le site fonctionne**
   - Vous devriez voir l'application Opet Comics
   - Navigation fonctionnelle
   - Pas d'erreurs 404

### Avec curl (en ligne de commande)

```bash
# Test simple
curl -I https://o-petit.com/

# Test avec suivi des redirections
curl -L https://o-petit.com/ | head -50
```

---

## 🧪 Test 4 : Vérifier la Configuration Git OVH

### Dans l'Espace Client OVH

1. Connectez-vous à https://www.ovh.com/manager/
2. Allez dans **Web Cloud** > **Hébergements**
3. Sélectionnez votre hébergement
4. Cherchez la section **Git** ou **Déploiement**

**Vérifiez :**
- ✅ Repository configuré : `https://github.com/goodies75/ovh25.git`
- ✅ Branche : `main`
- ✅ Répertoire source : `deploy/`
- ✅ Répertoire destination : `www/`
- ✅ Déploiement automatique : Activé

---

## 🧪 Test 5 : Test Complet du Déploiement

### Créer un Fichier de Test

```bash
# Sur votre machine (dans le dossier du projet)
cd /home/user/ovh25
echo "Test de connexion - $(date)" > deploy/test-connexion.txt
git add deploy/test-connexion.txt
git commit -m "🧪 Test de connexion OVH"
git push origin main
```

### Attendre 2-5 Minutes

Le déploiement automatique OVH devrait se déclencher.

### Vérifier sur le Serveur

```bash
# En SSH sur OVH
cd /home/opetitcorq/www
cat test-connexion.txt
```

**Si le fichier est là :** ✅ Déploiement automatique fonctionne !

**Si le fichier n'est pas là :** ❌ Le déploiement automatique n'est pas configuré ou a échoué

---

## 📊 Résultats Attendus

### ✅ Connexion Réussie

Vous devriez pouvoir :
- [x] Se connecter en SSH
- [x] Accéder au dossier `/home/opetitcorq/www`
- [x] Lister les fichiers
- [x] Voir le site sur https://o-petit.com/
- [x] Déployer automatiquement avec Git

### ❌ Problèmes Possibles

| Problème | Solution |
|----------|----------|
| **Permission denied (publickey)** | Clé SSH non configurée → Ajouter votre clé publique dans l'espace client OVH |
| **Connection refused** | Mauvais serveur ou port → Vérifier les infos dans l'espace client OVH |
| **Mot de passe demandé** | Pas de clé SSH → Configurer une clé SSH ou utiliser le mot de passe |
| **Site 404 / non trouvé** | Fichiers non déployés → Vérifier le contenu du dossier `www/` |
| **Git non configuré** | Configuration manquante → Suivre le GUIDE-DEPLOIEMENT-OVH.md |

---

## 🔑 Configuration de la Clé SSH (Si Nécessaire)

### Générer une Clé SSH

**Linux / Mac:**
```bash
ssh-keygen -t rsa -b 4096 -C "votre.email@example.com"
cat ~/.ssh/id_rsa.pub
```

**Windows (PowerShell):**
```powershell
ssh-keygen -t rsa -b 4096 -C "votre.email@example.com"
type $env:USERPROFILE\.ssh\id_rsa.pub
```

### Ajouter la Clé dans OVH

1. Copiez le contenu de votre clé publique (id_rsa.pub)
2. Espace client OVH > Hébergement > SSH
3. Ajouter une clé SSH
4. Collez votre clé publique
5. Validez

**Attendez 5-10 minutes** pour que la clé soit active.

---

## 🛠️ Commandes Utiles SSH

### Une Fois Connecté

```bash
# Voir l'espace disque
df -h

# Voir les processus
top

# Voir les logs Apache (si accessible)
tail -f ~/logs/*.log

# Tester PHP
php -v

# Voir la configuration Git locale
cd ~/www
git remote -v
git status
git log --oneline -5
```

---

## 📞 Aide et Support

### Documentation OVH
- https://docs.ovh.com/fr/hosting/
- https://docs.ovh.com/fr/hosting/mutualise-le-ssh-sur-les-hebergements-mutualises/
- https://docs.ovh.com/fr/hosting/deploiement-git/

### Problèmes Courants

**Je ne peux pas me connecter en SSH**
- Vérifiez que SSH est activé sur votre hébergement OVH
- Certains plans n'incluent pas SSH
- Contactez le support OVH si nécessaire

**Le site ne s'affiche pas**
- Vérifiez que les fichiers sont dans `www/`
- Vérifiez le fichier `.htaccess`
- Consultez les logs d'erreur OVH

**Le déploiement Git ne fonctionne pas**
- Vérifiez la configuration dans l'espace client
- Consultez les logs de déploiement
- Vérifiez que la clé SSH déployée a accès en lecture au repository GitHub

---

## 🎯 Checklist de Test

Cochez au fur et à mesure :

- [ ] SSH fonctionne (connexion réussie)
- [ ] Accès au dossier `/home/opetitcorq/www`
- [ ] Fichiers visibles dans `www/`
- [ ] Site accessible sur https://o-petit.com/
- [ ] Git configuré dans l'espace client OVH
- [ ] Déploiement automatique testé et fonctionnel
- [ ] .htaccess présent (pour React Router)
- [ ] Application Opet Comics fonctionnelle

---

## 📝 Résultat du Test

Après avoir effectué ces tests, notez vos résultats :

**Date du test :** _________________

**Connexion SSH :** ☐ OK  ☐ Erreur : _________________

**Site accessible :** ☐ OK  ☐ Erreur : _________________

**Déploiement Git :** ☐ OK  ☐ Non configuré  ☐ Erreur : _________________

**Fichiers présents :** ☐ OK  ☐ Manquants

**Application fonctionne :** ☐ OK  ☐ Erreur : _________________

---

## 🚀 Prochaines Étapes

### Si Tout Fonctionne ✅
- Votre connexion OVH est opérationnelle
- Le déploiement automatique fonctionne
- Vous pouvez commencer à utiliser l'application

### Si Problèmes ❌
1. Notez les messages d'erreur exacts
2. Consultez la documentation OVH
3. Vérifiez la configuration dans l'espace client
4. Contactez le support OVH si nécessaire
5. Utilisez le déploiement manuel FTP en attendant

---

## 💡 Alternative : Déploiement Manuel FTP

Si SSH ne fonctionne pas, vous pouvez toujours déployer via FTP :

**Voir les guides :**
- `GUIDE-FTP-DEPLOIEMENT.md`
- `GUIDE-FILEZILLA-COMPLET.md`

**Infos FTP OVH :**
- Disponibles dans l'espace client OVH
- Section "FTP-SSH" de votre hébergement

---

**Bon test ! 🚀**
