<?php
/**
 * Regenerate Encryption Key for a User
 * Use this to fix the "Failed to decrypt the encryption key" error
 */

require_once 'config/database.php';
require_once 'includes/functions.php';

$message = '';
$error = '';

// Get all users
$stmt = $pdo->query("SELECT user_id, username, email FROM users ORDER BY user_id");
$users = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int)$_POST['user_id'];
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($user_id)) {
        $error = 'Please select a user.';
    } elseif (empty($password) || empty($confirm_password)) {
        $error = 'Please enter and confirm your password.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        try {
            // Get user
            $stmt = $pdo->prepare("SELECT username FROM users WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
            
            if (!$user) {
                $error = 'User not found.';
            } else {
                // Start transaction
                $pdo->beginTransaction();
                
                // Update user's password
                $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
                $stmt->execute([$hash, $user_id]);
                
                // Delete old encryption keys
                $stmt = $pdo->prepare("DELETE FROM encryption_keys WHERE user_id = ?");
                $stmt->execute([$user_id]);
                
                // Generate new encryption key
                $encryptionKey = random_bytes(32);
                $salt = random_bytes(16);
                $iv = random_bytes(16);
                
                // Derive key from password
                $passwordKey = hash_pbkdf2('sha256', $password, $salt, 100000, 32, true);
                
                // Wrap the encryption key
                $wrappedKey = openssl_encrypt($encryptionKey, 'aes-256-cbc', $passwordKey, OPENSSL_RAW_DATA, $iv);
                
                // Store new key
                $stmt = $pdo->prepare("INSERT INTO encryption_keys (user_id, key_version, encrypted_key, key_salt, key_iv, expires_at) 
                                       VALUES (?, 1, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 1 YEAR))");
                $stmt->execute([
                    $user_id,
                    base64_encode($wrappedKey),
                    base64_encode($salt),
                    base64_encode($iv)
                ]);
                
                $pdo->commit();
                
                // Log activity
                logActivity($pdo, $user_id, 'key_regenerated', 'Encryption key regenerated for user: ' . $user['username']);
                
                $message = '✅ Password and encryption key successfully reset for: <strong>' . htmlspecialchars($user['username']) . '</strong>';
                $message .= '<br><br>📝 You can now login with your new password and decrypt your data.';
                $message .= '<br><br><a href="modules/auth/login.php" style="color: #003366; font-weight: 600; text-decoration: underline;">🔐 Go to Login</a>';
                
                // Clear any old session data
                session_destroy();
                session_start();
                
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Error: ' . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

<div style="max-width: 600px; margin: 40px auto;">
    <div style="background: white; border-radius: 12px; padding: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
        <div style="text-align: center; margin-bottom: 30px;">
            <img src="logo-main.webp" height="80px" alt="Paul University Logo" style="border-radius: 50%;">
            <h1 style="font-size: 24px; color: var(--paul-blue); margin-top: 10px;">Regenerate Encryption Key</h1>
            <p style="color: var(--paul-gray); font-size: 14px;">Fix the "Failed to decrypt encryption key" error</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-success">
                <span class="alert-icon"><i class="fas fa-check-circle"></i></span>
                <div><?php echo $message; ?></div>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <span class="alert-icon"><i class="fas fa-exclamation-circle"></i></span>
                <div><?php echo htmlspecialchars($error); ?></div>
            </div>
        <?php endif; ?>
        
        <?php if (!$message): ?>
            <div style="background: #fff3cd; border-radius: 8px; padding: 15px; border-left: 4px solid #ffc107; margin-bottom: 20px;">
                <p style="font-size: 14px; color: #856404; margin: 0;">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Note:</strong> This will reset your password and generate a new encryption key.
                    You will need to re-encrypt any existing data with the new key.
                </p>
            </div>
            
            <form method="POST" action="">
                <div style="margin-bottom: 18px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 5px; color: var(--paul-dark);">
                        <i class="fas fa-user" style="color: var(--paul-blue);"></i> Select User
                    </label>
                    <select name="user_id" required style="width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;">
                        <option value="">-- Select User --</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?php echo $u['user_id']; ?>">
                                <?php echo htmlspecialchars($u['username']); ?> (<?php echo htmlspecialchars($u['email']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div style="margin-bottom: 18px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 5px; color: var(--paul-dark);">
                        <i class="fas fa-lock" style="color: var(--paul-blue);"></i> New Password
                    </label>
                    <input type="password" name="password" required placeholder="Enter new password"
                           style="width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;">
                    <small style="color: var(--paul-gray); font-size: 12px;">Min 6 characters</small>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 5px; color: var(--paul-dark);">
                        <i class="fas fa-check-circle" style="color: var(--paul-blue);"></i> Confirm Password
                    </label>
                    <input type="password" name="confirm_password" required placeholder="Confirm new password"
                           style="width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;">
                </div>
                
                <button type="submit" style="background: var(--paul-blue); color: white; border: none; padding: 14px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; width: 100%; transition: all 0.3s;">
                    <i class="fas fa-sync-alt"></i> Regenerate Key & Reset Password
                </button>
            </form>
        <?php endif; ?>
        
        <p style="text-align: center; margin-top: 20px; font-size: 14px; color: var(--paul-gray);">
            <a href="modules/auth/login.php" style="color: var(--paul-blue); font-weight: 600; text-decoration: none;">Back to Login</a>
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>