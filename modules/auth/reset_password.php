<?php
/**
 * Password Reset Page - UPDATED WORKING VERSION
 * Data Encryption System
 * Paul University Awka
 * 
 * This version uses a simple email-based reset with direct token validation
 */

require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Reset Password';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: ' . SITE_URL . 'modules/dashboard/');
    exit();
}

$step = isset($_GET['step']) ? $_GET['step'] : 'request';
$error = '';
$success = '';
$email = '';
$token = '';
$user_id = '';

// Function to generate reset token
function generateResetToken($pdo, $userId) {
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+15 minutes')); // Shorter expiry for security
    
    $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE user_id = ?");
    $stmt->execute([$token, $expires, $userId]);
    
    return $token;
}

// Function to verify reset token
function verifyResetToken($pdo, $userId, $token) {
    $stmt = $pdo->prepare("SELECT user_id, username FROM users 
                           WHERE user_id = ? AND reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$userId, $token]);
    return $stmt->fetch();
}

// Step 1: Request password reset
if ($step === 'request' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Validate CSRF
    if (!verifyCSRFToken($csrf_token)) {
        $error = 'Security token validation failed. Please try again.';
    } elseif (empty($email)) {
        $error = 'Please enter your email address.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Check if user exists with this email
        $stmt = $pdo->prepare("SELECT user_id, username FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Generate reset token
            $token = generateResetToken($pdo, $user['user_id']);
            
            // Build reset link
            $resetLink = SITE_URL . 'modules/auth/reset_password.php?step=reset&token=' . $token . '&user=' . $user['user_id'];
            
            $success = 'A password reset link has been generated.';
            $success .= '<br><br><strong>Reset Link (click to reset):</strong><br>';
            $success .= '<a href="' . $resetLink . '" style="color: var(--paul-blue); word-break: break-all; font-weight: 600; text-decoration: underline;">' . $resetLink . '</a>';
            $success .= '<br><br><em style="font-size: 13px; color: var(--paul-gray);">This link will expire in 15 minutes.</em>';
            
            // Log activity
            logActivity($pdo, $user['user_id'], 'password_reset_request', 'Password reset requested for: ' . $email);
        } else {
            // Don't reveal if email exists or not for security
            $success = 'If an account exists with this email, a reset link has been sent.';
        }
    }
}

// Step 2: Verify token and show reset form
if ($step === 'reset' && isset($_GET['token']) && isset($_GET['user'])) {
    $token = $_GET['token'];
    $user_id = (int)$_GET['user'];
    
    // Verify token
    $user = verifyResetToken($pdo, $user_id, $token);
    
    if (!$user) {
        $error = 'Invalid or expired reset token. Please request a new password reset.';
        $step = 'request';
    }
}

// Step 3: Process new password
if ($step === 'reset' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $token = $_POST['token'] ?? '';
    $user_id = (int)$_POST['user_id'] ?? 0;
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Validate CSRF
    if (!verifyCSRFToken($csrf_token)) {
        $error = 'Security token validation failed. Please try again.';
    } elseif (empty($new_password) || empty($confirm_password)) {
        $error = 'Please enter and confirm your new password.';
    } elseif (strlen($new_password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif (!preg_match('/[A-Z]/', $new_password)) {
        $error = 'Password must contain at least one uppercase letter.';
    } elseif (!preg_match('/[a-z]/', $new_password)) {
        $error = 'Password must contain at least one lowercase letter.';
    } elseif (!preg_match('/[0-9]/', $new_password)) {
        $error = 'Password must contain at least one number.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        // Verify token again
        $user = verifyResetToken($pdo, $user_id, $token);
        
        if (!$user) {
            $error = 'Invalid or expired reset token. Please request a new password reset.';
            $step = 'request';
        } else {
            try {
                // Update password
                $passwordHash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);
                
                $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, reset_expires = NULL 
                                       WHERE user_id = ?");
                $stmt->execute([$passwordHash, $user_id]);
                
                // Log activity
                logActivity($pdo, $user_id, 'password_reset_success', 'Password reset successful for: ' . $user['username']);
                
                $success = 'Password reset successful! You can now login with your new password.';
                $step = 'done';
            } catch (Exception $e) {
                $error = 'Error updating password: ' . $e->getMessage();
            }
        }
    }
}

// Generate CSRF token
$csrfToken = generateCSRFToken();

include '../../includes/header.php';
?>

<div class="auth-container" style="max-width: 480px; margin: 40px auto; background: white; border-radius: 12px; padding: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
    <div style="text-align: center; margin-bottom: 30px;">
        <div style="border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 15px;">
            <img src="../../logo-main.webp" height="80px" width="80px" alt="Paul University Logo" style="border-radius: 50%;">
        </div>
        <h1 style="font-size: 24px; color: var(--paul-blue);">Reset Password</h1>
        <p style="color: var(--paul-gray); font-size: 14px;">
            <?php if ($step === 'request'): ?>
                Enter your email to receive a reset link
            <?php elseif ($step === 'reset'): ?>
                Create a new password for your account
            <?php elseif ($step === 'done'): ?>
                Password updated successfully
            <?php endif; ?>
        </p>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-error">
            <span class="alert-icon"><i class="fas fa-exclamation-circle"></i></span>
            <div><?php echo htmlspecialchars($error); ?></div>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success">
            <span class="alert-icon"><i class="fas fa-check-circle"></i></span>
            <div><?php echo $success; ?></div>
        </div>
    <?php endif; ?>
    
    <?php if ($step === 'done'): ?>
        <div style="text-align: center; margin-top: 20px;">
            <a href="login.php" style="background: var(--paul-blue); color: white; padding: 14px 40px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block;">
                <i class="fas fa-sign-in-alt"></i> Go to Login
            </a>
        </div>
    <?php elseif ($step === 'request'): ?>
        <form method="POST" action="" style="display: flex; flex-direction: column; gap: 18px;">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            
            <div>
                <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 5px; color: var(--paul-dark);">
                    <i class="fas fa-envelope" style="color: var(--paul-blue); width: 20px;"></i> Email Address
                </label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" 
                       required placeholder="Enter your registered email"
                       style="width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; transition: border-color 0.3s;">
                <small style="color: var(--paul-gray); font-size: 12px;">Enter the email you used to register</small>
            </div>
            
            <button type="submit" style="background: var(--paul-blue); color: white; border: none; padding: 14px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; margin-top: 5px;">
                <i class="fas fa-paper-plane"></i> Send Reset Link
            </button>
        </form>
        
        <p style="text-align: center; margin-top: 20px; font-size: 14px; color: var(--paul-gray);">
            Remember your password? <a href="login.php" style="color: var(--paul-blue); font-weight: 600; text-decoration: none;">Back to Login</a>
        </p>
        
    <?php elseif ($step === 'reset' && isset($user)): ?>
        <form method="POST" action="" style="display: flex; flex-direction: column; gap: 18px;">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
            
            <div style="background: var(--paul-light); border-radius: 8px; padding: 12px 16px; margin-bottom: 5px;">
                <p style="font-size: 14px; color: var(--paul-gray);">
                    <i class="fas fa-user" style="color: var(--paul-blue);"></i> 
                    Resetting password for: <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                </p>
            </div>
            
            <div>
                <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 5px; color: var(--paul-dark);">
                    <i class="fas fa-lock" style="color: var(--paul-blue); width: 20px;"></i> New Password
                </label>
                <input type="password" name="new_password" required placeholder="Enter new password"
                       style="width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;">
                <small style="color: var(--paul-gray); font-size: 12px;">Min 8 chars, with uppercase, lowercase, and number</small>
            </div>
            
            <div>
                <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 5px; color: var(--paul-dark);">
                    <i class="fas fa-check-circle" style="color: var(--paul-blue); width: 20px;"></i> Confirm New Password
                </label>
                <input type="password" name="confirm_password" required placeholder="Confirm new password"
                       style="width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;">
            </div>
            
            <div style="background: #fff3cd; border-radius: 8px; padding: 12px 16px; border-left: 4px solid #ffc107;">
                <p style="font-size: 13px; color: #856404; margin: 0;">
                    <i class="fas fa-info-circle"></i> 
                    Password requirements: At least 8 characters, one uppercase letter, one lowercase letter, and one number.
                </p>
            </div>
            
            <button type="submit" style="background: var(--paul-gold); color: var(--paul-blue); border: none; padding: 14px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; margin-top: 5px;">
                <i class="fas fa-key"></i> Reset Password
            </button>
        </form>
        
        <p style="text-align: center; margin-top: 20px; font-size: 14px; color: var(--paul-gray);">
            <a href="login.php" style="color: var(--paul-blue); font-weight: 600; text-decoration: none;">Back to Login</a>
        </p>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>