# 🔐 GUIDE DE SÉCURITÉ - OPET COMICS

## Vue d'ensemble

Ce guide explique comment sécuriser votre site Opet Comics pour que seuls les utilisateurs autorisés puissent modifier ou supprimer les comics, tout en gardant la consultation publique.

## 🚀 Solution Implémentée

### Caractéristiques principales

- ✅ **Consultation publique** : Tout le monde peut voir les comics
- 🔒 **Édition/Suppression protégée** : Nécessite un code PIN
- ⏰ **Session temporaire** : L'autorisation expire après 30 minutes
- 🛡️ **Protection côté serveur** : Validation sécurisée du PIN
- 🚫 **Rate limiting** : Protection contre les attaques par force brute
- 📝 **Journal de sécurité** : Traçabilité des tentatives d'accès

## 📋 Configuration Initiale

### 1. Générer votre hash PIN

1. Uploadez `generate-pin-hash.php` sur votre serveur
2. Visitez `votre-site.com/api/generate-pin-hash.php`
3. Entrez votre PIN secret (ex: "MonPinSecret123")
4. Copiez le hash généré
5. **SUPPRIMEZ le fichier** après utilisation

### 2. Configurer le serveur

Dans `validate-pin.php`, remplacez la ligne :
```php
define('ADMIN_PIN_HASH', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
```

Par votre hash généré :
```php
define('ADMIN_PIN_HASH', 'VOTRE_HASH_GENERE_ICI');
```

## 🔄 Fonctionnement

### Workflow de sécurité

1. **Utilisateur normal** :
   - Peut voir tous les comics
   - Peut ouvrir les détails
   - Voit les boutons "Modifier" et "Supprimer"

2. **Première action admin** :
   - Clic sur "Modifier" ou "Supprimer"
   - Modal PIN s'affiche
   - Demande le code administrateur

3. **Validation** :
   - Envoi sécurisé au serveur
   - Vérification du hash
   - En cas de succès : autorisation pour 30 minutes

4. **Session active** :
   - Indicateur vert en haut à droite
   - Actions directes pendant 30 minutes
   - Bouton de déconnexion manuel

## 🛡️ Mesures de Sécurité

### Côté Serveur
- **Hash sécurisé** : PIN jamais stocké en clair
- **Rate limiting** : Max 5 tentatives par IP/5 minutes
- **Logs de sécurité** : Traçabilité dans `security.log`
- **Validation stricte** : Contrôle des entrées
- **Headers sécurisés** : CORS et méthodes HTTP

### Côté Client
- **Session temporaire** : Expiration automatique
- **Stockage sécurisé** : sessionStorage (non persistant)
- **Interface claire** : Indicateurs visuels d'état
- **UX optimisée** : Workflows fluides

## 📁 Fichiers de Sécurité

```
/src/components/security/
├── PinProtection.tsx          # Modal de saisie PIN
├── PinProtection.css          # Styles de la modal
└── AdminStatus.tsx            # Indicateur d'état admin

/src/hooks/
└── useAdminAuth.ts           # Hook de gestion auth

/api/
├── validate-pin.php          # Validation serveur
├── generate-pin-hash.php     # Générateur de hash (à supprimer)
├── security.log              # Journal automatique
└── failed_attempts.json      # Suivi tentatives
```

## 🎨 Interface Utilisateur

### Indicateurs visuels
- 🔓 **Badge vert** : Mode administrateur actif
- 🔒 **Modal PIN** : Demande d'autorisation
- ⏰ **Timer** : "Autorisation valide 30 minutes"
- ⚠️ **Erreurs** : Messages clairs d'échec

### Expérience utilisateur
- **Consultation libre** : Navigation fluide
- **Actions protégées** : Une seule demande PIN/session
- **Feedback immédiat** : États et erreurs clairs
- **Déconnexion facile** : Bouton toujours visible

## 🔧 Personnalisation

### Modifier la durée de session
Dans `PinProtection.tsx` :
```typescript
const expirationTime = Date.now() + (30 * 60 * 1000); // 30 minutes
```

### Changer les tentatives autorisées
Dans `validate-pin.php` :
```php
define('MAX_ATTEMPTS', 5);      // Tentatives max
define('LOCKOUT_TIME', 300);    // Délai en secondes
```

### Personnaliser les messages
Modifiez les textes dans `PinProtection.tsx` et `AdminStatus.tsx`

## 📊 Monitoring

### Fichiers de logs
- `security.log` : Toutes les tentatives d'auth
- `failed_attempts.json` : Suivi des échecs par IP

### Format des logs
```
[2025-01-15 14:30:25] IP: 192.168.1.100 - Authentification réussie
[2025-01-15 14:31:10] IP: 192.168.1.105 - Échec d'authentification
[2025-01-15 14:32:00] IP: 192.168.1.105 - Rate limit dépassé
```

## 🚨 Sécurité Avancée (Optionnel)

### Protection supplémentaire
1. **HTTPS obligatoire** en production
2. **Firewall** : Bloquer IPs suspectes
3. **Backup régulier** des logs
4. **Rotation des PINs** périodique

### Monitoring avancé
1. **Alertes email** sur tentatives multiples
2. **Dashboard admin** avec statistiques
3. **Géolocalisation** des connexions
4. **Audit trail** complet

## 🔄 Déploiement

### Upload des fichiers
1. **Côté client** : Build React avec nouvelles fonctionnalités
2. **Côté serveur** : Upload des APIs PHP
3. **Configuration** : Générer et configurer le PIN
4. **Test** : Vérifier toutes les fonctionnalités
5. **Nettoyage** : Supprimer `generate-pin-hash.php`

### Vérification post-déploiement
- ✅ Consultation publique fonctionne
- ✅ Modal PIN s'affiche lors d'édition/suppression
- ✅ Validation PIN correcte
- ✅ Session temporaire active
- ✅ Indicateur admin visible
- ✅ Déconnexion manuelle possible
- ✅ Rate limiting opérationnel

## 🆘 Dépannage

### Problèmes courants

**PIN ne fonctionne pas :**
- Vérifier le hash dans `validate-pin.php`
- Contrôler les logs dans `security.log`
- Tester avec `generate-pin-hash.php`

**Rate limiting trop strict :**
- Supprimer `failed_attempts.json`
- Ajuster `MAX_ATTEMPTS` et `LOCKOUT_TIME`

**Session ne persiste pas :**
- Vérifier sessionStorage dans navigateur
- Contrôler l'expiration dans `useAdminAuth.ts`

**Erreurs serveur :**
- Vérifier permissions PHP sur fichiers
- Contrôler les logs d'erreur du serveur
- Tester les endpoints API individuellement

## 📞 Support

En cas de problème, vérifiez :
1. Les logs navigateur (F12 → Console)
2. Le fichier `security.log` sur le serveur
3. Les permissions des fichiers PHP
4. La configuration du hash PIN

---

*Guide créé pour Opet Comics - Version sécurisée 2025*
