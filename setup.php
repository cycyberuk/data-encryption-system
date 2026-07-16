<?php
/**
 * Database Setup Script
 * Run this once to initialize the database
 * DELETE THIS FILE AFTER RUNNING!
 */

require_once 'config/database.php';

echo "<h1>Data Encryption System - Database Setup</h1>";

try {
    // Create tables if they don't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            user_id INT PRIMARY KEY AUTO_INCREMENT,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_login TIMESTAMP NULL,
            is_active BOOLEAN DEFAULT TRUE,
            INDEX idx_username (username),
            INDEX idx_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "✓ Users table created<br>";

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS encryption_keys (
            key_id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            key_version INT NOT NULL DEFAULT 1,
            encrypted_key TEXT NOT NULL,
            key_salt VARCHAR(255) NOT NULL,
            key_iv VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            expires_at TIMESTAMP NULL,
            is_active BOOLEAN DEFAULT TRUE,
            last_used TIMESTAMP NULL,
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
            INDEX idx_user_keys (user_id, is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "✓ encryption_keys table created<br>";

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS encrypted_data (
            data_id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            ciphertext LONGTEXT NOT NULL,
            iv VARCHAR(255) NOT NULL,
            key_version INT NOT NULL,
            file_name VARCHAR(255) NULL,
            file_size INT NULL,
            file_type VARCHAR(100) NULL,
            data_type ENUM('text', 'file') DEFAULT 'text',
            encryption_time FLOAT NULL,
            decryption_time FLOAT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
            INDEX idx_user_data (user_id, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "✓ encrypted_data table created<br>";

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS audit_log (
            log_id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NULL,
            action VARCHAR(50) NOT NULL,
            details TEXT NULL,
            ip_address VARCHAR(45) NULL,
            user_agent VARCHAR(255) NULL,
            timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
            INDEX idx_audit_user (user_id),
            INDEX idx_audit_timestamp (timestamp)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "✓ audit_log table created<br>";

    // Create admin user
    $adminUsername = 'admin';
    $adminEmail = 'admin@pauluniversity.edu.ng';
    $adminPassword = 'Admin@2026';
    
    // Check if admin exists
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ?");
    $stmt->execute([$adminUsername]);
    
    if (!$stmt->fetch()) {
        // Hash password with bcrypt
        $passwordHash = password_hash($adminPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        
        // Insert admin user
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$adminUsername, $adminEmail, $passwordHash]);
        $userId = $pdo->lastInsertId();
        
        // Generate and store encryption key
        function generateEncryptionKey() { return random_bytes(32); }
        function deriveKeyFromPassword($password, $salt) { 
            return hash_pbkdf2('sha256', $password, $salt, 100000, 32, true); 
        }
        
        $encryptionKey = random_bytes(32);
        $salt = random_bytes(16);
        $iv = random_bytes(16);
        $passwordKey = deriveKeyFromPassword($adminPassword, $salt);
        $wrappedKey = openssl_encrypt($encryptionKey, 'aes-256-cbc', $passwordKey, OPENSSL_RAW_DATA, $iv);
        
        $stmt = $pdo->prepare("INSERT INTO encryption_keys (user_id, key_version, encrypted_key, key_salt, key_iv, expires_at) 
                               VALUES (?, 1, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 1 YEAR))");
        $stmt->execute([$userId, base64_encode($wrappedKey), base64_encode($salt), base64_encode($iv)]);
        
        echo "✓ Admin user created!<br>";
        echo "<strong>Username:</strong> admin<br>";
        echo "<strong>Password:</strong> Admin@2026<br>";
    } else {
        echo "✓ Admin user already exists<br>";
    }

    echo "<br><strong style='color: green;'>Setup completed successfully!</strong><br>";
    echo "<p>You can now <a href='modules/auth/login.php'>login</a> with username: <strong>admin</strong> and password: <strong>Admin@2026</strong></p>";
    echo "<p style='color: red;'><strong>IMPORTANT:</strong> Delete this setup.php file after running!</p>";

} catch (PDOException $e) {
    echo "<strong style='color: red;'>Error:</strong> " . $e->getMessage() . "<br>";
    echo "Please check your database configuration in config/database.php";
}
?>