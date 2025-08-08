<?php
/*
 * ==================== API DE VALIDATION DU PIN DE SÉCURITÉ ====================
 * 
 * OBJECTIF: Authentifier l'administrateur avec un code PIN sécurisé
 * 
 * FONCTIONNALITÉS SÉCURISÉES:
 * - Validation du PIN avec hash bcrypt (impossible de découvrir le PIN original)
 * - Protection contre les attaques par force brute (rate limiting)
 * - Journalisation des tentatives suspectes (security.log)
 * - Blocage temporaire des IP après échecs répétés
 * - Nettoyage automatique des tentatives anciennes
 * 
 * UTILISATION:
 * - React envoie le PIN saisi par l'utilisateur
 * - L'API vérifie le PIN, applique les restrictions de sécurité
 * - Retourne autorisation + expiration ou refus + message d'erreur
 * 
 * MÉTHODE HTTP: POST uniquement
 * PARAMÈTRES: {"pin": "code_secret"}
 * SÉCURITÉ: Hash bcrypt, rate limiting, logging, blocage IP
 */

// ========== ÉTAPE 1: CONFIGURATION DES EN-TÊTES CORS ==========
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');     // Autoriser React
header('Access-Control-Allow-Methods: POST'); // Seule méthode autorisée
header('Access-Control-Allow-Headers: Content-Type');

// ========== ÉTAPE 2: PARAMÈTRES DE SÉCURITÉ ==========
/*
 * Configuration de sécurité stricte pour protéger l'accès administrateur
 */

// Hash bcrypt du PIN secret (généré avec password_hash())
// IMPORTANT: Remplacez ce hash par celui de votre PIN personnel !
// Pour générer: echo password_hash('votre_pin_secret', PASSWORD_DEFAULT);
define('ADMIN_PIN_HASH', '$2y$10$kVf8gybHnjifwWKIuDe2VudTB9JOZZTu.IHQ/A/Rg9EvSIQY6nvWG');

// Protection contre les attaques par force brute
define('MAX_ATTEMPTS', 5);     // Maximum 5 tentatives par IP
define('LOCKOUT_TIME', 300);   // Blocage de 5 minutes (300 secondes)

// ========== ÉTAPE 3: FONCTION DE JOURNALISATION ==========
/*
 * Enregistrer tous les événements de sécurité dans un fichier log
 * Utile pour détecter les tentatives d'intrusion
 */
function logSecurityEvent($event, $ip) {
    $logFile = 'security.log';
    $timestamp = date('Y-m-d H:i:s');              // Format: 2024-01-15 14:30:25
    $logEntry = "[$timestamp] IP: $ip - $event" . PHP_EOL;
    
    // FILE_APPEND = ajouter à la fin, LOCK_EX = verrou exclusif
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

// ========== ÉTAPE 4: FONCTION DE VÉRIFICATION DU RATE LIMITING ==========
/*
 * Vérifier si une IP a dépassé le nombre maximum de tentatives
 * Principe: bloquer temporairement les IP suspectes
 */
function checkRateLimit($ip) {
    $attemptsFile = 'failed_attempts.json';
    $attempts = [];
    
    // Charger les tentatives précédentes (si le fichier existe)
    if (file_exists($attemptsFile)) {
        $attempts = json_decode(file_get_contents($attemptsFile), true) ?: [];
    }
    
    // ========== NETTOYAGE DES TENTATIVES ANCIENNES ==========
    /*
     * Supprimer les tentatives de plus de LOCKOUT_TIME secondes
     * Exemple: après 5 minutes, les tentatives sont "oubliées"
     */
    $now = time(); // Timestamp actuel
    $attempts = array_filter($attempts, function($attempt) use ($now) {
        return ($now - $attempt['time']) < LOCKOUT_TIME;
    });
    
    // ========== COMPTAGE DES TENTATIVES POUR CETTE IP ==========
    $ipAttempts = array_filter($attempts, function($attempt) use ($ip) {
        return $attempt['ip'] === $ip;
    });
    
    // Si cette IP a déjà fait trop de tentatives, la bloquer
    if (count($ipAttempts) >= MAX_ATTEMPTS) {
        return false; // IP bloquée
    }
    
    return true; // IP autorisée à essayer
}

// ========== ÉTAPE 5: FONCTION D'ENREGISTREMENT DES ÉCHECS ==========
/*
 * Enregistrer une tentative d'authentification échouée
 * Chaque échec est horodaté et associé à l'IP source
 */
function recordFailedAttempt($ip) {
    $attemptsFile = 'failed_attempts.json';
    $attempts = [];
    
    // Charger les échecs précédents
    if (file_exists($attemptsFile)) {
        $attempts = json_decode(file_get_contents($attemptsFile), true) ?: [];
    }
    
    // Ajouter cette nouvelle tentative échouée
    $attempts[] = [
        'ip' => $ip,           // Adresse IP de l'attaquant
        'time' => time()       // Timestamp de la tentative
    ];
    
    // Sauvegarder avec verrou pour éviter les corruptions
    file_put_contents($attemptsFile, json_encode($attempts), LOCK_EX);
}

// ========== ÉTAPE 6: FONCTION DE NETTOYAGE APRÈS SUCCÈS ==========
/*
 * Effacer toutes les tentatives échouées d'une IP après un succès
 * Principe: l'authentification réussie "pardonne" les échecs précédents
 */
function clearFailedAttempts($ip) {
    $attemptsFile = 'failed_attempts.json';
    if (!file_exists($attemptsFile)) return; // Rien à nettoyer
    
    // Charger toutes les tentatives
    $attempts = json_decode(file_get_contents($attemptsFile), true) ?: [];
    
    // Garder seulement les tentatives des autres IPs
    $attempts = array_filter($attempts, function($attempt) use ($ip) {
        return $attempt['ip'] !== $ip; // Exclure cette IP
    });
    
    // Réindexer et sauvegarder
    file_put_contents($attemptsFile, json_encode(array_values($attempts)), LOCK_EX);
}

// ========== ÉTAPE 7: TRAITEMENT DE LA REQUÊTE D'AUTHENTIFICATION ==========
try {
    // ========== ÉTAPE 7.1: VÉRIFICATION DE LA MÉTHODE ==========
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405); // Méthode non autorisée
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
        exit;
    }
    
    // ========== ÉTAPE 7.2: RÉCUPÉRATION DES DONNÉES ==========
    $input = json_decode(file_get_contents('php://input'), true);
    $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown'; // IP de l'utilisateur
    
    // Validation des données reçues
    if (!$input || !isset($input['pin'])) {
        logSecurityEvent('Tentative avec données invalides', $clientIp);
        echo json_encode(['success' => false, 'message' => 'Données invalides']);
        exit;
    }
    
    // ========== ÉTAPE 7.3: VÉRIFICATION DU RATE LIMITING ==========
    /*
     * Avant même de vérifier le PIN, s'assurer que cette IP
     * n'a pas déjà fait trop de tentatives récemment
     */
    if (!checkRateLimit($clientIp)) {
        logSecurityEvent('Rate limit dépassé', $clientIp);
        http_response_code(429); // Too Many Requests
        echo json_encode([
            'success' => false, 
            'message' => 'Trop de tentatives. Réessayez dans 5 minutes.'
        ]);
        exit;
    }
    
    $pin = trim($input['pin']); // Nettoyer le PIN (espaces, etc.)
    // ========== ÉTAPE 7.4: VALIDATION DU PIN ==========
    /*
     * Utilisation de password_verify() pour comparer de façon sécurisée
     * Le PIN original ne peut jamais être récupéré depuis le hash
     * 
     * Comment générer un hash:
     * echo password_hash('votre_pin_secret', PASSWORD_DEFAULT);
     */
    if (password_verify($pin, ADMIN_PIN_HASH)) {
        // ========== SUCCÈS: PIN CORRECT ==========
        
        // 1. Nettoyer l'historique des échecs pour cette IP
        clearFailedAttempts($clientIp);
        
        // 2. Enregistrer le succès dans les logs
        logSecurityEvent('Authentification réussie', $clientIp);
        
        // 3. Répondre à React avec autorisation + expiration
        echo json_encode([
            'success' => true,
            'message' => 'Autorisation accordée',
            'expires' => time() + 1800 // Expire dans 30 minutes (1800 secondes)
        ]);
        
    } else {
        // ========== ÉCHEC: PIN INCORRECT ==========
        
        // 1. Enregistrer cette tentative échouée
        recordFailedAttempt($clientIp);
        
        // 2. Logger l'événement suspect
        logSecurityEvent('Échec d\'authentification', $clientIp);
        
        // 3. Répondre avec un message d'erreur générique
        // (Ne pas donner d'indices sur la cause de l'échec)
        echo json_encode([
            'success' => false,
            'message' => 'Code PIN incorrect'
        ]);
    }
    
} catch (Exception $e) {
    // ========== GESTION DES ERREURS TECHNIQUES ==========
    /*
     * En cas de problème serveur (fichier non accessible, etc.)
     * Logger l'erreur et répondre de façon sécurisée
     */
    logSecurityEvent('Erreur serveur: ' . $e->getMessage(), $clientIp);
    http_response_code(500); // Erreur interne du serveur
    echo json_encode([
        'success' => false,
        'message' => 'Erreur interne du serveur'
    ]);
}
?>
