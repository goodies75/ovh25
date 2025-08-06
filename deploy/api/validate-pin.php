<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Configuration de sécurité
define('ADMIN_PIN_HASH', '$2y$10$kVf8gybHnjifwWKIuDe2VudTB9JOZZTu.IHQ/A/Rg9EvSIQY6nvWG'); // Remplacez par votre hash
define('MAX_ATTEMPTS', 5);
define('LOCKOUT_TIME', 300); // 5 minutes en secondes

function logSecurityEvent($event, $ip) {
    $logFile = 'security.log';
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] IP: $ip - $event" . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

function checkRateLimit($ip) {
    $attemptsFile = 'failed_attempts.json';
    $attempts = [];
    
    if (file_exists($attemptsFile)) {
        $attempts = json_decode(file_get_contents($attemptsFile), true) ?: [];
    }
    
    // Nettoyer les tentatives anciennes
    $now = time();
    $attempts = array_filter($attempts, function($attempt) use ($now) {
        return ($now - $attempt['time']) < LOCKOUT_TIME;
    });
    
    // Compter les tentatives pour cette IP
    $ipAttempts = array_filter($attempts, function($attempt) use ($ip) {
        return $attempt['ip'] === $ip;
    });
    
    if (count($ipAttempts) >= MAX_ATTEMPTS) {
        return false;
    }
    
    return true;
}

function recordFailedAttempt($ip) {
    $attemptsFile = 'failed_attempts.json';
    $attempts = [];
    
    if (file_exists($attemptsFile)) {
        $attempts = json_decode(file_get_contents($attemptsFile), true) ?: [];
    }
    
    $attempts[] = [
        'ip' => $ip,
        'time' => time()
    ];
    
    file_put_contents($attemptsFile, json_encode($attempts), LOCK_EX);
}

function clearFailedAttempts($ip) {
    $attemptsFile = 'failed_attempts.json';
    if (!file_exists($attemptsFile)) return;
    
    $attempts = json_decode(file_get_contents($attemptsFile), true) ?: [];
    $attempts = array_filter($attempts, function($attempt) use ($ip) {
        return $attempt['ip'] !== $ip;
    });
    
    file_put_contents($attemptsFile, json_encode(array_values($attempts)), LOCK_EX);
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
        exit;
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    if (!$input || !isset($input['pin'])) {
        logSecurityEvent('Tentative avec données invalides', $clientIp);
        echo json_encode(['success' => false, 'message' => 'Données invalides']);
        exit;
    }
    
    // Vérifier le rate limiting
    if (!checkRateLimit($clientIp)) {
        logSecurityEvent('Rate limit dépassé', $clientIp);
        http_response_code(429);
        echo json_encode([
            'success' => false, 
            'message' => 'Trop de tentatives. Réessayez dans 5 minutes.'
        ]);
        exit;
    }
    
    $pin = trim($input['pin']);
    
    // Vérifier le PIN (utilisez password_hash() pour générer votre hash)
    // Exemple: password_hash('votre_pin_secret', PASSWORD_DEFAULT)
    if (password_verify($pin, ADMIN_PIN_HASH)) {
        clearFailedAttempts($clientIp);
        logSecurityEvent('Authentification réussie', $clientIp);
        
        echo json_encode([
            'success' => true,
            'message' => 'Autorisation accordée',
            'expires' => time() + 1800 // 30 minutes
        ]);
    } else {
        recordFailedAttempt($clientIp);
        logSecurityEvent('Échec d\'authentification', $clientIp);
        
        echo json_encode([
            'success' => false,
            'message' => 'Code PIN incorrect'
        ]);
    }
    
} catch (Exception $e) {
    logSecurityEvent('Erreur serveur: ' . $e->getMessage(), $clientIp);
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur interne du serveur'
    ]);
}
?>
