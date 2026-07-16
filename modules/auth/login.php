<?php
/**
 * User Login - SUPER DEBUG VERSION
 * Data Encryption System
 */

require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Login';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: ' . SITE_URL . 'modules/dashboard/');
    exit();
}

$error = '';
$debugInfo = [];
$showDebug = isset($_GET['debug']) && $_GET['debug'] === '1';

// Debug mode
if ($showDebug) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

// Special test mode - bypass all security for testing
if (isset($_GET['test']) && $_GET['test'] === '1') {
    // Direct login without password check
    $testUser = $_GET['user'] ?? 'admin';
    $stmt = $pdo->prepare("SELECT user_id, username FROM users WHERE username = ?");
    $stmt->execute([$testUser]);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['login_time'] = time();
        
        logActivity($pdo, $user['user_id'], 'login', 'User logged in via test mode');
        
        header('Location: ' . SITE_URL . 'modules/dashboard/');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    $remember = isset($_POST['remember']);
    
    $debugInfo['post'] = [
        'username' => $username,
        'password_length' => strlen($password),
        'csrf_received' => $csrf_token ? substr($csrf_token, 0, 20) . '...' : 'none'
    ];
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        try {
            // Find user
            $stmt = $pdo->prepare("SELECT user_id, username, password_hash, is_active FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            $debugInfo['user'] = $user ? [
                'id' => $user['user_id'],
                'username' => $user['username'],
                'hash' => substr($user['password_hash'], 0, 30) . '...',
                'hash_length' => strlen($user['password_hash']),
                'is_active' => $user['is_active']
            ] : 'NOT FOUND';
            
            if ($user) {
                // Test password verification
                $passwordValid = password_verify($password, $user['password_hash']);
                $debugInfo['password_verify'] = $passwordValid ? '✅ TRUE' : '❌ FALSE';
                
                // Try alternative verification
                $debugInfo['hash_info'] = password_get_info($user['password_hash']);
                
                // Try rehashing the same password to compare
                $testHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                $debugInfo['test_hash'] = substr($testHash, 0, 40) . '...';
                
                // Check if the hash starts with $2y$ (bcrypt)
                $debugInfo['hash_algo'] = substr($user['password_hash'], 0, 4);
                
                if ($passwordValid) {
                    if ($user['is_active'] == 0) {
                        $error = 'Your account has been deactivated. Please contact support.';
                    } else {
                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['login_time'] = time();
                        
                        logActivity($pdo, $user['user_id'], 'login', 'User logged in successfully');
                        
                        header('Location: ' . SITE_URL . 'modules/dashboard/');
                        exit();
                    }
                } else {
                    $error = 'Invalid username or password.';
                    logActivity($pdo, null, 'login_failed', 'Failed login attempt for: ' . $username);
                }
            } else {
                $error = 'Invalid username or password.';
                logActivity($pdo, null, 'login_failed', 'Failed login attempt for: ' . $username . ' - User not found');
            }
        } catch (Exception $e) {
            $error = 'System error: ' . $e->getMessage();
            $debugInfo['error'] = $e->getMessage();
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
        <h1 style="font-size: 24px; color: var(--paul-blue);">Welcome Back</h1>
        <p style="color: var(--paul-gray); font-size: 14px;">Secure access to your encrypted data</p>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-error">
            <span class="alert-icon"><i class="fas fa-exclamation-circle"></i></span>
            <div><?php echo htmlspecialchars($error); ?></div>
        </div>
    <?php endif; ?>
    
    <?php if ($showDebug): ?>
        <div class="alert alert-info" style="overflow: auto; max-height: 600px;">
            <span class="alert-icon"><i class="fas fa-bug"></i></span>
            <div>
                <strong>🔍 Debug Information</strong>
                <pre style="margin-top: 10px; font-size: 12px; background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto; white-space: pre-wrap; word-wrap: break-word;">
<?php
echo "=== SYSTEM INFO ===\n";
echo "PHP Version: " . phpversion() . "\n";
echo "OpenSSL: " . (extension_loaded('openssl') ? '✅ Enabled' : '❌ Disabled') . "\n";
echo "PDO MySQL: " . (extension_loaded('pdo_mysql') ? '✅ Enabled' : '❌ Disabled') . "\n";
echo "Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? '✅ Active' : '❌ Inactive') . "\n\n";

echo "=== DATABASE USERS ===\n";
$stmt = $pdo->query("SELECT user_id, username, email, is_active, 
                     LEFT(password_hash, 30) as hash_preview, 
                     LENGTH(password_hash) as hash_length 
                     FROM users");
$users = $stmt->fetchAll();
foreach ($users as $u) {
    echo "ID: {$u['user_id']}\n";
    echo "  Username: {$u['username']}\n";
    echo "  Email: {$u['email']}\n";
    echo "  Active: " . ($u['is_active'] ? 'Yes' : 'No') . "\n";
    echo "  Hash: {$u['hash_preview']}...\n";
    echo "  Hash Length: {$u['hash_length']}\n";
    echo "  Hash Algo: " . substr($u['hash_preview'], 0, 4) . "\n";
    echo "---\n";
}
echo "\n";

echo "=== SESSION DATA ===\n";
print_r($_SESSION);
echo "\n";

echo "=== POST DATA ===\n";
print_r($debugInfo);
echo "\n";

echo "=== TEST LOGIN ===\n";
echo "To bypass login and go directly to dashboard:\n";
echo "  ?test=1&user=admin\n";
echo "  ?test=1&user=cyrus\n";
?>
                </pre>
            </div>
        </div>
        
        <!-- Test Login Buttons -->
        <div style="display: flex; gap: 10px; margin-top: 15px; flex-wrap: wrap; justify-content: center;">
            <a href="?test=1&user=admin" style="background: #28a745; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 14px;">
                🚀 Test Login as Admin
            </a>
            <a href="?test=1&user=cyrus" style="background: #17a2b8; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 14px;">
                🚀 Test Login as Cyrus
            </a>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="" style="display: flex; flex-direction: column; gap: 18px; margin-top: 20px;">
        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
        
        <div>
            <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 5px; color: var(--paul-dark);">
                <i class="fas fa-user" style="color: var(--paul-blue); width: 20px;"></i> Username or Email
            </label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                   required placeholder="Enter your username"
                   style="width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; transition: border-color 0.3s;">
        </div>
        
        <div>
            <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 5px; color: var(--paul-dark);">
                <i class="fas fa-lock" style="color: var(--paul-blue); width: 20px;"></i> Password
            </label>
            <input type="password" name="password" required placeholder="Enter your password"
                   style="width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;">
        </div>
        
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <label style="font-size: 14px; color: var(--paul-gray); cursor: pointer;">
                <input type="checkbox" name="remember" style="margin-right: 8px;"> Remember me
            </label>
            <a href="reset_password.php" style="color: var(--paul-blue); font-size: 14px; text-decoration: none;">Forgot password?</a>
        </div>
        
        <button type="submit" style="background: var(--paul-blue); color: white; border: none; padding: 14px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; margin-top: 5px;">
            <i class="fas fa-sign-in-alt"></i> Login
        </button>
    </form>
    
    <p style="text-align: center; margin-top: 20px; font-size: 14px; color: var(--paul-gray);">
        Don't have an account? <a href="register.php" style="color: var(--paul-blue); font-weight: 600; text-decoration: none;">Create one</a>
    </p>
</div>

<?php include '../../includes/footer.php'; ?>
