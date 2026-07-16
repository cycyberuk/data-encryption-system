<?php
/**
 * Quick Password Reset - Direct Access
 * Use this to reset any user's password immediately
 * DELETE THIS FILE AFTER USE!
 */

require_once 'config/database.php';

echo "<h1 style='color: #003366;'>Quick Password Reset</h1>";

// Get users
$stmt = $pdo->query("SELECT user_id, username, email FROM users ORDER BY user_id");
$users = $stmt->fetchAll();

if (empty($users)) {
    echo "<p style='color: red;'>No users found in database.</p>";
    echo "<p><a href='setup.php'>Run setup.php first</a></p>";
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int)$_POST['user_id'];
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($new_password) || empty($confirm_password)) {
        echo "<p style='color: red;'>Please enter and confirm your password.</p>";
    } elseif ($new_password !== $confirm_password) {
        echo "<p style='color: red;'>Passwords do not match.</p>";
    } elseif (strlen($new_password) < 6) {
        echo "<p style='color: red;'>Password must be at least 6 characters.</p>";
    } else {
        // Update password
        $hash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);
        
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, reset_expires = NULL WHERE user_id = ?");
        $stmt->execute([$hash, $user_id]);
        
        // Get username
        $stmt = $pdo->prepare("SELECT username FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        echo "<p style='color: green; font-weight: bold;'>✅ Password reset successful for: " . htmlspecialchars($user['username']) . "</p>";
        echo "<p>New password: <strong>" . htmlspecialchars($new_password) . "</strong></p>";
        echo "<p><a href='modules/auth/login.php' style='color: #003366; font-weight: 600;'>🔐 Go to Login</a></p>";
        echo "<hr>";
    }
}

// Display form
?>
<style>
    body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
    .container { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
    h1 { color: #003366; }
    .form-group { margin-bottom: 15px; }
    label { display: block; font-weight: 600; margin-bottom: 5px; color: #333; }
    select, input { width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; }
    select:focus, input:focus { border-color: #003366; outline: none; }
    button { background: #003366; color: white; padding: 12px 30px; border: none; border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer; }
    button:hover { background: #002244; }
    .user-list { margin: 10px 0; }
    .user-item { padding: 8px 12px; border-bottom: 1px solid #eee; font-size: 14px; }
    .user-item strong { color: #003366; }
    .warning { background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin-bottom: 20px; border-radius: 4px; }
</style>

<div class="container">
    <div style="text-align: center; margin-bottom: 20px;">
        <img src="logo-main.webp" height="60px" alt="Paul University Logo" style="border-radius: 50%;">
        <h1>Paul University Awka</h1>
        <p style="color: #666;">Data Encryption System - Password Reset</p>
    </div>
    
    <div class="warning">
        <strong>⚠️ SECURITY NOTICE:</strong> This page is for admin use only. 
        <a href="modules/auth/reset_password.php" style="color: #003366;">Use the standard reset page</a> for regular password resets.
    </div>
    
    <h2>Users in System</h2>
    <div class="user-list">
        <?php foreach ($users as $u): ?>
            <div class="user-item">
                <strong><?php echo htmlspecialchars($u['username']); ?></strong> 
                (<?php echo htmlspecialchars($u['email']); ?>) 
                - ID: <?php echo $u['user_id']; ?>
            </div>
        <?php endforeach; ?>
    </div>
    
    <hr>
    
    <h2>Reset Password</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="user_id">Select User:</label>
            <select name="user_id" id="user_id" required>
                <option value="">-- Select User --</option>
                <?php foreach ($users as $u): ?>
                    <option value="<?php echo $u['user_id']; ?>">
                        <?php echo htmlspecialchars($u['username']); ?> (<?php echo htmlspecialchars($u['email']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="new_password">New Password:</label>
            <input type="text" name="new_password" id="new_password" required placeholder="Enter new password">
            <small style="color: #666;">Min 6 characters</small>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm Password:</label>
            <input type="text" name="confirm_password" id="confirm_password" required placeholder="Confirm new password">
        </div>
        
        <button type="submit">Reset Password</button>
    </form>
    
    <p style="margin-top: 20px;">
        <a href="modules/auth/login.php" style="color: #003366; font-weight: 600;">← Back to Login</a>
    </p>
</div>