<?php
/**
 * Decryption Module - FIXED VERSION
 * Data Encryption System
 */

require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Decrypt Data';
requireLogin();

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];

$result = ['success' => false, 'message' => '', 'plaintext' => '', 'data' => null];
$dataId = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['data_id']) ? (int)$_POST['data_id'] : 0);

// If data_id is provided, show decryption form
if ($dataId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM encrypted_data WHERE data_id = ? AND user_id = ?");
    $stmt->execute([$dataId, $userId]);
    $data = $stmt->fetch();
    
    if ($data) {
        $result['data'] = $data;
    } else {
        $result['message'] = 'Data not found or you do not have permission to access it.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['decrypt'])) {
    $dataId = (int)$_POST['data_id'];
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Verify CSRF
    if (!verifyCSRFToken($csrf_token)) {
        $result['message'] = 'Security token validation failed.';
    } else {
        // Verify user password
        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!password_verify($password, $user['password_hash'])) {
            $result['message'] = 'Incorrect password. Please enter your password to decrypt.';
        } else {
            // Get encrypted data
            $stmt = $pdo->prepare("SELECT * FROM encrypted_data WHERE data_id = ? AND user_id = ?");
            $stmt->execute([$dataId, $userId]);
            $data = $stmt->fetch();
            
            if (!$data) {
                $result['message'] = 'Data not found or you do not have permission to access it.';
            } else {
                try {
                    $startTime = microtime(true);
                    
                    // Get user's encryption key for the specific version
                    $stmt = $pdo->prepare("SELECT encrypted_key, key_salt, key_iv FROM encryption_keys 
                                          WHERE user_id = ? AND key_version = ?");
                    $stmt->execute([$userId, $data['key_version']]);
                    $keyData = $stmt->fetch();
                    
                    if (!$keyData) {
                        throw new Exception('Encryption key not found. Please contact support.');
                    }
                    
                    // DEBUG: Log key data
                    error_log("Key Data: " . print_r($keyData, true));
                    
                    // Decode stored values
                    $salt = base64_decode($keyData['key_salt']);
                    $iv = base64_decode($keyData['key_iv']);
                    $wrappedKey = base64_decode($keyData['encrypted_key']);
                    
                    // Check if decoding worked
                    if ($salt === false || $iv === false || $wrappedKey === false) {
                        throw new Exception('Failed to decode key data. Please contact support.');
                    }
                    
                    // Derive key from password
                    $passwordKey = hash_pbkdf2('sha256', $password, $salt, 100000, 32, true);
                    
                    if ($passwordKey === false) {
                        throw new Exception('Failed to derive encryption key.');
                    }
                    
                    // Unwrap the encryption key
                    $key = openssl_decrypt($wrappedKey, 'aes-256-cbc', $passwordKey, OPENSSL_RAW_DATA, $iv);
                    
                    if ($key === false) {
                        // Get OpenSSL error
                        $opensslError = openssl_error_string();
                        error_log("OpenSSL Decrypt Error: " . $opensslError);
                        throw new Exception('Failed to decrypt the encryption key. Please check your password.');
                    }
                    
                    // Decrypt the data
                    $plaintext = decryptData($data['ciphertext'], $key, $data['iv']);
                    
                    if ($plaintext === false) {
                        throw new Exception('Failed to decrypt the data. The data may be corrupted.');
                    }
                    
                    // Update decryption time
                    $stmt = $pdo->prepare("UPDATE encrypted_data SET decryption_time = ? WHERE data_id = ?");
                    $stmt->execute([microtime(true) - $startTime, $dataId]);
                    
                    logActivity($pdo, $userId, 'decrypt', "Data decrypted: " . ($data['file_name'] ?? $dataId));
                    
                    $result['success'] = true;
                    $result['message'] = 'Data decrypted successfully!';
                    $result['plaintext'] = $plaintext;
                    $result['data'] = $data;
                    
                    // Update key last used
                    $stmt = $pdo->prepare("UPDATE encryption_keys SET last_used = NOW() WHERE user_id = ? AND key_version = ?");
                    $stmt->execute([$userId, $data['key_version']]);
                    
                } catch (Exception $e) {
                    $result['message'] = $e->getMessage();
                    error_log("Decryption Error: " . $e->getMessage());
                }
            }
        }
    }
}

// Generate CSRF token
$csrfToken = generateCSRFToken();

include '../../includes/header.php';
?>

<div style="max-width: 800px; margin: 0 auto;">
    <h1 style="font-size: 26px; color: var(--paul-blue); margin-bottom: 8px;">
        <i class="fas fa-unlock" style="color: var(--paul-gold);"></i> Decrypt Data
    </h1>
    <p style="color: var(--paul-gray); margin-bottom: 30px;">Access and view your encrypted files and text</p>

    <?php if ($result['success']): ?>
        <div class="alert alert-success">
            <span class="alert-icon"><i class="fas fa-check-circle"></i></span>
            <div>
                <strong>Success!</strong>
                <p style="margin-top: 5px;"><?php echo htmlspecialchars($result['message']); ?></p>
                <?php if ($result['data'] && $result['data']['file_name']): ?>
                    <p style="font-size: 13px; margin-top: 5px;">
                        <i class="fas fa-file"></i> <?php echo htmlspecialchars($result['data']['file_name']); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if ($result['data']['data_type'] === 'text'): ?>
            <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-top: 20px;">
                <h3 style="font-size: 16px; color: var(--paul-blue); margin-bottom: 10px;">Decrypted Text</h3>
                <div style="background: var(--paul-light); border-radius: 8px; padding: 16px; min-height: 100px; font-family: monospace; white-space: pre-wrap; word-break: break-word;">
                    <?php echo htmlspecialchars($result['plaintext']); ?>
                </div>
            </div>
        <?php else: ?>
            <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-top: 20px;">
                <h3 style="font-size: 16px; color: var(--paul-blue); margin-bottom: 10px;">File Content</h3>
                <div style="background: var(--paul-light); border-radius: 8px; padding: 16px; min-height: 100px; font-family: monospace; white-space: pre-wrap; word-break: break-word; max-height: 400px; overflow-y: auto;">
                    <?php 
                    $fileType = $result['data']['file_type'] ?? '';
                    if (strpos($fileType, 'image/') === 0) {
                        echo '<div style="text-align: center;">';
                        echo '<img src="data:' . $fileType . ';base64,' . base64_encode($result['plaintext']) . 
                             '" style="max-width: 100%; max-height: 400px; border-radius: 8px;" alt="Decrypted image">';
                        echo '</div>';
                    } elseif (strpos($fileType, 'text/') === 0 || $fileType === 'application/json') {
                        echo htmlspecialchars($result['plaintext']);
                    } else {
                        echo '<div style="text-align: center; padding: 20px; color: var(--paul-gray);">';
                        echo '<i class="fas fa-file" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>';
                        echo '<p>File of type: ' . htmlspecialchars($fileType) . ' (' . formatFileSize($result['data']['file_size'] ?? 0) . ')</p>';
                        echo '<p style="font-size: 13px;">Preview not available for this file type.</p>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 20px; text-align: center;">
            <a href="<?php echo SITE_URL; ?>modules/dashboard/" style="color: var(--paul-blue); text-decoration: none;">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
        
    <?php elseif ($result['data']): ?>
        <!-- Decryption Form -->
        <div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <div style="margin-bottom: 20px; padding: 16px; background: var(--paul-light); border-radius: 8px;">
                <h3 style="font-size: 15px; color: var(--paul-blue);">File Information</h3>
                <p style="margin-top: 5px;">
                    <i class="fas fa-file" style="color: var(--paul-blue);"></i> 
                    <?php echo htmlspecialchars($result['data']['file_name'] ?? 'Unnamed'); ?>
                    <?php if ($result['data']['file_size']): ?>
                        <span style="margin-left: 15px;">
                            <i class="fas fa-weight-hanging"></i> <?php echo formatFileSize($result['data']['file_size']); ?>
                        </span>
                    <?php endif; ?>
                    <?php if ($result['data']['data_type']): ?>
                        <span style="margin-left: 15px;">
                            <i class="fas fa-tag"></i> <?php echo htmlspecialchars($result['data']['data_type']); ?>
                        </span>
                    <?php endif; ?>
                </p>
                <p style="font-size: 13px; color: var(--paul-gray);">
                    <i class="fas fa-calendar"></i> Created: <?php echo date('M j, Y g:i A', strtotime($result['data']['created_at'])); ?>
                </p>
                <p style="font-size: 13px; color: var(--paul-gray);">
                    <i class="fas fa-key"></i> Key Version: <?php echo $result['data']['key_version']; ?>
                </p>
            </div>
            
            <?php if (!empty($result['message'])): ?>
                <div class="alert alert-error">
                    <span class="alert-icon"><i class="fas fa-exclamation-circle"></i></span>
                    <div><?php echo htmlspecialchars($result['message']); ?></div>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                <input type="hidden" name="data_id" value="<?php echo $dataId; ?>">
                <input type="hidden" name="decrypt" value="1">
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 5px; color: var(--paul-dark);">
                        <i class="fas fa-key" style="color: var(--paul-blue); width: 20px;"></i> Confirm Your Password
                    </label>
                    <input type="password" name="password" required 
                           placeholder="Enter your account password to decrypt" 
                           style="width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;">
                    <small style="color: var(--paul-gray); font-size: 12px;">Your password is required to access your encryption key</small>
                </div>
                
                <button type="submit" style="background: var(--paul-gold); color: var(--paul-blue); border: none; padding: 14px 30px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                    <i class="fas fa-unlock"></i> Decrypt Data
                </button>
                <a href="<?php echo SITE_URL; ?>modules/dashboard/" style="margin-left: 10px; color: var(--paul-gray); text-decoration: none; padding: 14px 20px; border: 2px solid #e0e0e0; border-radius: 8px; display: inline-block;">
                    Cancel
                </a>
            </form>
        </div>
        
    <?php else: ?>
        <!-- Browse for data to decrypt -->
        <div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <h3 style="font-size: 16px; color: var(--paul-blue); margin-bottom: 15px;">Select Data to Decrypt</h3>
            
            <?php 
            $recentData = getUserEncryptedData($pdo, $userId, 20);
            if (empty($recentData)):
            ?>
                <p style="color: var(--paul-gray); text-align: center; padding: 30px 0;">
                    <i class="fas fa-inbox" style="font-size: 40px; display: block; margin-bottom: 10px; opacity: 0.3;"></i>
                    No encrypted data found. <a href="<?php echo SITE_URL; ?>modules/encryption/encrypt.php" style="color: var(--paul-blue);">Encrypt your first file</a>
                </p>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead>
                            <tr style="background: var(--paul-light);">
                                <th style="padding: 12px 16px; text-align: left;">Name</th>
                                <th style="padding: 12px 16px; text-align: left;">Type</th>
                                <th style="padding: 12px 16px; text-align: left;">Size</th>
                                <th style="padding: 12px 16px; text-align: left;">Date</th>
                                <th style="padding: 12px 16px; text-align: left;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentData as $item): ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 12px 16px;">
                                        <i class="fas fa-file" style="color: var(--paul-blue); margin-right: 8px;"></i>
                                        <?php echo htmlspecialchars($item['file_name'] ?? 'Untitled'); ?>
                                    </td>
                                    <td style="padding: 12px 16px;">
                                        <span style="background: var(--paul-light); padding: 2px 10px; border-radius: 12px; font-size: 12px;">
                                            <?php echo htmlspecialchars($item['data_type'] ?? 'text'); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 12px 16px;"><?php echo formatFileSize($item['file_size'] ?? 0); ?></td>
                                    <td style="padding: 12px 16px;"><?php echo date('M j, Y', strtotime($item['created_at'])); ?></td>
                                    <td style="padding: 12px 16px;">
                                        <a href="decrypt.php?id=<?php echo $item['data_id']; ?>" 
                                           style="background: var(--paul-blue); color: white; padding: 6px 16px; border-radius: 6px; text-decoration: none; font-size: 13px;">
                                            <i class="fas fa-unlock"></i> Decrypt
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>