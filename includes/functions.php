<?php
/**
 * Core Functions
 * Data Encryption System
 */

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Check if user is logged in
 */

function isLoggedIn() {
    // Start session if not started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if session variables exist
    return isset($_SESSION['user_id']) && isset($_SESSION['username']) && $_SESSION['user_id'] > 0;
}
/**
 * Redirect to login if not authenticated
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['error'] = 'Please login to access this page';
        header('Location: ' . SITE_URL . 'modules/auth/login.php');
        exit();
    }
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    // For debugging - accept any token when debug mode is on
    if (isset($_GET['debug']) && $_GET['debug'] === '1') {
        return true;
    }
    
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Log activity to audit trail
 */
function logActivity($pdo, $userId, $action, $details = null) {
    try {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        $stmt = $pdo->prepare("INSERT INTO audit_log (user_id, action, details, ip_address, user_agent) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $action, $details, $ip, $userAgent]);
    } catch (PDOException $e) {
        // Silent fail for logging
    }
}

/**
 * Get user by ID
 */
function getUserById($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT user_id, username, email, created_at, last_login FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch();
}

/**
 * Generate a random encryption key (32 bytes for AES-256)
 */
function generateEncryptionKey() {
    return random_bytes(32);
}

/**
 * Derive a key from user password
 */
function deriveKeyFromPassword($password, $salt) {
    return hash_pbkdf2('sha256', $password, $salt, 100000, 32, true);
}

/**
 * Encrypt data using AES-256-CBC
 */
function encryptData($data, $key) {
    $iv = random_bytes(16);
    $ciphertext = openssl_encrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    return [
        'ciphertext' => base64_encode($ciphertext),
        'iv' => base64_encode($iv)
    ];
}

/**
 * Decrypt data using AES-256-CBC
 */
function decryptData($ciphertext, $key, $iv) {
    $ciphertext = base64_decode($ciphertext);
    $iv = base64_decode($iv);
    return openssl_decrypt($ciphertext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
}

/**
 * Store user encryption key
 */
function storeUserKey($pdo, $userId, $key, $password) {
    $salt = random_bytes(16);
    $iv = random_bytes(16);
    
    // Derive key from password for wrapping
    $passwordKey = deriveKeyFromPassword($password, $salt);
    
    // Wrap the encryption key with password-derived key
    $wrappedKey = openssl_encrypt($key, 'aes-256-cbc', $passwordKey, OPENSSL_RAW_DATA, $iv);
    
    // Get current max version
    $stmt = $pdo->prepare("SELECT COALESCE(MAX(key_version), 0) + 1 as next_version FROM encryption_keys WHERE user_id = ?");
    $stmt->execute([$userId]);
    $version = $stmt->fetch()['next_version'];
    
    $stmt = $pdo->prepare("INSERT INTO encryption_keys (user_id, key_version, encrypted_key, key_salt, key_iv, expires_at) 
                           VALUES (?, ?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 1 YEAR))");
    $stmt->execute([
        $userId,
        $version,
        base64_encode($wrappedKey),
        base64_encode($salt),
        base64_encode($iv)
    ]);
    
    return $version;
}

/**
 * Get user's active encryption key
 */
function getUserActiveKey($pdo, $userId, $password) {
    $stmt = $pdo->prepare("SELECT * FROM encryption_keys WHERE user_id = ? AND is_active = 1 ORDER BY key_version DESC LIMIT 1");
    $stmt->execute([$userId]);
    $keyData = $stmt->fetch();
    
    if (!$keyData) {
        // Generate new key
        $newKey = generateEncryptionKey();
        storeUserKey($pdo, $userId, $newKey, $password);
        
        // Fetch again
        $stmt = $pdo->prepare("SELECT * FROM encryption_keys WHERE user_id = ? AND is_active = 1 ORDER BY key_version DESC LIMIT 1");
        $stmt->execute([$userId]);
        $keyData = $stmt->fetch();
    }
    
    // Derive password key to unwrap
    $salt = base64_decode($keyData['key_salt']);
    $iv = base64_decode($keyData['key_iv']);
    $passwordKey = deriveKeyFromPassword($password, $salt);
    
    // Unwrap the encryption key
    $wrappedKey = base64_decode($keyData['encrypted_key']);
    $key = openssl_decrypt($wrappedKey, 'aes-256-cbc', $passwordKey, OPENSSL_RAW_DATA, $iv);
    
    return [
        'key' => $key,
        'version' => $keyData['key_version']
    ];
}

/**
 * Format file size
 */
function formatFileSize($bytes) {
    if ($bytes === 0) return '0 B';
    $k = 1024;
    $sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}

/**
 * Get user's encrypted data
 */
function getUserEncryptedData($pdo, $userId, $limit = 50) {
    $stmt = $pdo->prepare("SELECT data_id, file_name, file_size, file_type, data_type, 
                                  encryption_time, decryption_time, created_at 
                           FROM encrypted_data 
                           WHERE user_id = ? 
                           ORDER BY created_at DESC 
                           LIMIT ?");
    $stmt->execute([$userId, $limit]);
    return $stmt->fetchAll();
}

/**
 * Get total count of user's encrypted files
 */
function getUserDataCount($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM encrypted_data WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch()['count'];
}
?>
