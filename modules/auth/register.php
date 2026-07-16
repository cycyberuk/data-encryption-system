<?php
/**
 * User Registration
 * Data Encryption System
 */

require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Register';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: ' . SITE_URL . 'modules/dashboard/');
    exit();
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Validate CSRF
    if (!verifyCSRFToken($csrf_token)) {
        $errors[] = 'Security token validation failed. Please try again.';
    }
    
    // Validate inputs
    if (empty($username)) {
        $errors[] = 'Username is required.';
    } elseif (strlen($username) < 3 || strlen($username) > 30) {
        $errors[] = 'Username must be between 3 and 30 characters.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'Username can only contain letters, numbers, and underscores.';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required.';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Password must contain at least one uppercase letter.';
    } elseif (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Password must contain at least one lowercase letter.';
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must contain at least one number.';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }
    
    // Check if username exists
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $errors[] = 'Username already taken.';
        }
    }
    
    // Check if email exists
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already registered.';
        }
    }
    
    // Create user
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            // Hash password
            $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            
            // Insert user
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $passwordHash]);
            $userId = $pdo->lastInsertId();
            
            // Generate and store encryption key
            $encryptionKey = generateEncryptionKey();
            storeUserKey($pdo, $userId, $encryptionKey, $password);
            
            $pdo->commit();
            
            // Log registration
            logActivity($pdo, $userId, 'register', 'New user registered: ' . $username);
            
            $success = true;
            
            // Auto-login after registration
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;
            
            // Redirect to dashboard after 2 seconds
            header('Refresh: 2; URL=' . SITE_URL . 'modules/dashboard/');
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = 'Registration failed. Please try again.';
        }
    }
}

// Generate CSRF token
$csrfToken = generateCSRFToken();

include '../../includes/header.php';
?>

<div class="auth-container" style="max-width: 480px; margin: 40px auto; background: white; border-radius: 12px; padding: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
    <div style="text-align: center; margin-bottom: 30px;">
        <div style=" border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 15px;">
             <img src="../../logo-main.webp" height="80px" width="80px" alt="Paul University Logo">
        </div>
        <h1 style="font-size: 24px; color: var(--paul-blue);">Create Account</h1>
        <p style="color: var(--paul-gray); font-size: 14px;">Join the Paul University CST Dept Data Encryption System</p>
    </div>
    
    <?php if ($success): ?>
        <div class="alert alert-success">
            <span class="alert-icon"><i class="fas fa-check-circle"></i></span>
            <div>
                <strong>Registration Successful!</strong>
                <p style="margin-top: 5px; font-size: 14px;">Welcome <?php echo htmlspecialchars($username); ?>! Redirecting to dashboard...</p>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <span class="alert-icon"><i class="fas fa-exclamation-circle"></i></span>
            <div>
                <strong>Please fix the following errors:</strong>
                <ul style="margin-top: 5px; padding-left: 20px; font-size: 14px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (!$success): ?>
        <form method="POST" action="" style="display: flex; flex-direction: column; gap: 18px;">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            
            <div>
                <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 5px; color: var(--paul-dark);">
                    <i class="fas fa-user" style="color: var(--paul-blue); width: 20px;"></i> Username
                </label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                       required style="width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; transition: border-color 0.3s;">
                <small style="color: var(--paul-gray); font-size: 12px;">3-30 characters, letters/numbers/underscores only</small>
            </div>
            
            <div>
                <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 5px; color: var(--paul-dark);">
                    <i class="fas fa-envelope" style="color: var(--paul-blue); width: 20px;"></i> Email Address
                </label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                       required style="width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;">
            </div>
            
            <div>
                <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 5px; color: var(--paul-dark);">
                    <i class="fas fa-lock" style="color: var(--paul-blue); width: 20px;"></i> Password
                </label>
                <input type="password" name="password" required 
                       style="width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;">
                <small style="color: var(--paul-gray); font-size: 12px;">Min 8 chars, with uppercase, lowercase, and number</small>
            </div>
            
            <div>
                <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 5px; color: var(--paul-dark);">
                    <i class="fas fa-check-circle" style="color: var(--paul-blue); width: 20px;"></i> Confirm Password
                </label>
                <input type="password" name="confirm_password" required 
                       style="width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;">
            </div>
            
            <button type="submit" style="background: var(--paul-blue); color: white; border: none; padding: 14px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; margin-top: 5px;">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
        </form>
        
        <p style="text-align: center; margin-top: 20px; font-size: 14px; color: var(--paul-gray);">
            Already have an account? <a href="login.php" style="color: var(--paul-blue); font-weight: 600; text-decoration: none;">Login here</a>
        </p>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>