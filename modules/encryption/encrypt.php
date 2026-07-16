<?php
/**
 * Encryption Module
 * Data Encryption System
 */

require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Encrypt Data';
requireLogin();

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get user's password for key access (needed for encryption)
$result = ['success' => false, 'message' => '', 'data_id' => null];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Verify CSRF
    if (!verifyCSRFToken($csrf_token)) {
        $result['message'] = 'Security token validation failed.';
    } else {
        // Verify password
        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!password_verify($password, $user['password_hash'])) {
            $result['message'] = 'Incorrect password. Please enter your password to encrypt.';
        } else {
            try {
                // Get user's encryption key
                $keyData = getUserActiveKey($pdo, $userId, $password);
                $key = $keyData['key'];
                $version = $keyData['version'];
                
                $startTime = microtime(true);
                
                if ($action === 'encrypt_text') {
                    $plaintext = $_POST['plaintext'] ?? '';
                    $fileName = $_POST['file_name'] ?? 'Encrypted Text';
                    
                    if (empty($plaintext)) {
                        $result['message'] = 'Please enter text to encrypt.';
                        throw new Exception('Empty text');
                    }
                    
                    // Encrypt
                    $encrypted = encryptData($plaintext, $key);
                    
                    // Store
                    $stmt = $pdo->prepare("INSERT INTO encrypted_data 
                                          (user_id, ciphertext, iv, key_version, file_name, data_type, encryption_time) 
                                          VALUES (?, ?, ?, ?, ?, 'text', ?)");
                    $stmt->execute([
                        $userId,
                        $encrypted['ciphertext'],
                        $encrypted['iv'],
                        $version,
                        $fileName,
                        microtime(true) - $startTime
                    ]);
                    
                    $dataId = $pdo->lastInsertId();
                    
                    logActivity($pdo, $userId, 'encrypt_text', "Text encrypted: " . $fileName);
                    
                    $result['success'] = true;
                    $result['message'] = 'Text encrypted successfully!';
                    $result['data_id'] = $dataId;
                    $result['ciphertext'] = $encrypted['ciphertext'];
                    
                } elseif ($action === 'encrypt_file') {
                    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                        $result['message'] = 'Please select a file to encrypt.';
                        throw new Exception('File upload error');
                    }
                    
                    $file = $_FILES['file'];
                    $fileName = basename($file['name']);
                    $fileType = mime_content_type($file['tmp_name']);
                    $fileSize = $file['size'];
                    
                    if ($fileSize > 50 * 1024 * 1024) { // 50MB limit
                        $result['message'] = 'File size exceeds 50MB limit.';
                        throw new Exception('File too large');
                    }
                    
                    // Read file content
                    $plaintext = file_get_contents($file['tmp_name']);
                    
                    // Encrypt
                    $encrypted = encryptData($plaintext, $key);
                    
                    // Store
                    $stmt = $pdo->prepare("INSERT INTO encrypted_data 
                                          (user_id, ciphertext, iv, key_version, file_name, file_size, file_type, data_type, encryption_time) 
                                          VALUES (?, ?, ?, ?, ?, ?, ?, 'file', ?)");
                    $stmt->execute([
                        $userId,
                        $encrypted['ciphertext'],
                        $encrypted['iv'],
                        $version,
                        $fileName,
                        $fileSize,
                        $fileType,
                        microtime(true) - $startTime
                    ]);
                    
                    $dataId = $pdo->lastInsertId();
                    
                    logActivity($pdo, $userId, 'encrypt_file', "File encrypted: " . $fileName);
                    
                    $result['success'] = true;
                    $result['message'] = 'File encrypted successfully!';
                    $result['data_id'] = $dataId;
                    $result['file_name'] = $fileName;
                    
                    // Update key last used
                    $stmt = $pdo->prepare("UPDATE encryption_keys SET last_used = NOW() WHERE user_id = ? AND key_version = ?");
                    $stmt->execute([$userId, $version]);
                }
                
            } catch (Exception $e) {
                if (empty($result['message'])) {
                    $result['message'] = 'Encryption failed. Please try again.';
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
        <i class="fas fa-lock" style="color: var(--paul-gold);"></i> Encrypt Data
    </h1>
    <p style="color: var(--paul-gray); margin-bottom: 30px;">Secure your sensitive data with AES-256 encryption</p>
    
    <?php if ($result['success']): ?>
        <div class="alert alert-success">
            <span class="alert-icon"><i class="fas fa-check-circle"></i></span>
            <div>
                <strong>Success!</strong>
                <p style="margin-top: 5px;"><?php echo htmlspecialchars($result['message']); ?></p>
                <?php if (isset($result['data_id'])): ?>
                    <p style="font-size: 13px; margin-top: 5px;">
                        <i class="fas fa-hashtag"></i> Data ID: <?php echo $result['data_id']; ?>
                        <?php if (isset($result['file_name'])): ?>
                            | <i class="fas fa-file"></i> <?php echo htmlspecialchars($result['file_name']); ?>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
                <?php if (isset($result['ciphertext'])): ?>
                    <div style="background: #f8f9fa; border-radius: 8px; padding: 12px; margin-top: 10px; font-size: 12px; font-family: monospace; word-break: break-all; max-height: 100px; overflow-y: auto;">
                                        <?php echo htmlspecialchars($result['ciphertext']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($result['message']) && !$result['success']): ?>
                        <div class="alert alert-error">
                            <span class="alert-icon"><i class="fas fa-exclamation-circle"></i></span>
                            <div><?php echo htmlspecialchars($result['message']); ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Encryption Options -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                        <div id="textTab" onclick="switchTab('text')" style="background: var(--paul-blue); color: white; padding: 16px; border-radius: 12px; text-align: center; cursor: pointer; transition: all 0.3s;">
                            <i class="fas fa-font" style="font-size: 24px; display: block; margin-bottom: 5px;"></i>
                            Encrypt Text
                        </div>
                        <div id="fileTab" onclick="switchTab('file')" style="background: white; color: var(--paul-blue); padding: 16px; border-radius: 12px; text-align: center; cursor: pointer; transition: all 0.3s; border: 2px solid var(--paul-blue);">
                            <i class="fas fa-file-upload" style="font-size: 24px; display: block; margin-bottom: 5px;"></i>
                            Encrypt File
                        </div>
                    </div>
                    
                    <!-- Text Encryption Form -->
                    <div id="textForm" style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                        <form method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                            <input type="hidden" name="action" value="encrypt_text">
                            
                            <div style="margin-bottom: 16px;">
                                <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 5px; color: var(--paul-dark);">
                                    <i class="fas fa-tag" style="color: var(--paul-blue); width: 20px;"></i> Name (optional)
                                </label>
                                <input type="text" name="file_name" placeholder="Enter a name for this encrypted data" 
                                       style="width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;">
                            </div>
                            
                            <div style="margin-bottom: 16px;">
                                <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 5px; color: var(--paul-dark);">
                                    <i class="fas fa-align-left" style="color: var(--paul-blue); width: 20px;"></i> Text to Encrypt
                                </label>
                                <textarea name="plaintext" required rows="6" 
                                          style="width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; font-family: inherit; resize: vertical;"><?php echo htmlspecialchars($_POST['plaintext'] ?? ''); ?></textarea>
                            </div>
                            
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 5px; color: var(--paul-dark);">
                                    <i class="fas fa-key" style="color: var(--paul-blue); width: 20px;"></i> Confirm Your Password
                                </label>
                                <input type="password" name="password" required 
                                       placeholder="Enter your account password to encrypt" 
                                       style="width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;">
                                <small style="color: var(--paul-gray); font-size: 12px;">Your password is required to access your encryption key</small>
                            </div>
                            
                            <button type="submit" style="background: var(--paul-blue); color: white; border: none; padding: 14px 30px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                                <i class="fas fa-lock"></i> Encrypt Text
                            </button>
                        </form>
                    </div>
                    
                    <!-- File Encryption Form -->
                    <div id="fileForm" style="display: none; background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                            <input type="hidden" name="action" value="encrypt_file">
                            
                            <div style="border: 2px dashed #e0e0e0; border-radius: 12px; padding: 40px; text-align: center; margin-bottom: 16px; transition: border-color 0.3s;" 
                                 id="dropZone" ondragover="handleDragOver(event)" ondrop="handleDrop(event)">
                                <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: var(--paul-blue); opacity: 0.5;"></i>
                                <p style="color: var(--paul-gray); margin-top: 10px;">
                                    <strong>Drop your file here</strong> or click to browse
                                </p>
                                <input type="file" name="file" id="fileInput" required 
                                       style="display: none;" onchange="handleFileSelect(event)">
                                <button type="button" onclick="document.getElementById('fileInput').click()" 
                                        style="background: var(--paul-light); color: var(--paul-blue); border: 2px solid var(--paul-blue); padding: 10px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; margin-top: 10px;">
                                    <i class="fas fa-folder-open"></i> Browse Files
                                </button>
                                <div id="fileInfo" style="display: none; margin-top: 15px; padding: 12px; background: var(--paul-light); border-radius: 8px;">
                                    <i class="fas fa-file" style="color: var(--paul-blue);"></i>
                                    <span id="fileName"></span>
                                    <span id="fileSize" style="margin-left: 10px; color: var(--paul-gray);"></span>
                                </div>
                            </div>
                            
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 5px; color: var(--paul-dark);">
                                    <i class="fas fa-key" style="color: var(--paul-blue); width: 20px;"></i> Confirm Your Password
                                </label>
                                <input type="password" name="password" required 
                                       placeholder="Enter your account password to encrypt" 
                                       style="width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;">
                                <small style="color: var(--paul-gray); font-size: 12px;">Your password is required to access your encryption key</small>
                            </div>
                            
                            <button type="submit" style="background: var(--paul-blue); color: white; border: none; padding: 14px 30px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                                <i class="fas fa-lock"></i> Encrypt File
                            </button>
                        </form>
                    </div>
                    
                    <script>
                        let selectedFile = null;
                        
                        function switchTab(tab) {
                            const textTab = document.getElementById('textTab');
                            const fileTab = document.getElementById('fileTab');
                            const textForm = document.getElementById('textForm');
                            const fileForm = document.getElementById('fileForm');
                            
                            if (tab === 'text') {
                                textTab.style.background = 'var(--paul-blue)';
                                textTab.style.color = 'white';
                                textTab.style.border = 'none';
                                fileTab.style.background = 'white';
                                fileTab.style.color = 'var(--paul-blue)';
                                fileTab.style.border = '2px solid var(--paul-blue)';
                                textForm.style.display = 'block';
                                fileForm.style.display = 'none';
                            } else {
                                fileTab.style.background = 'var(--paul-blue)';
                                fileTab.style.color = 'white';
                                fileTab.style.border = 'none';
                                textTab.style.background = 'white';
                                textTab.style.color = 'var(--paul-blue)';
                                textTab.style.border = '2px solid var(--paul-blue)';
                                textForm.style.display = 'none';
                                fileForm.style.display = 'block';
                            }
                        }
                        
                        function handleDragOver(e) {
                            e.preventDefault();
                            document.getElementById('dropZone').style.borderColor = 'var(--paul-blue)';
                        }
                        
                        function handleDrop(e) {
                            e.preventDefault();
                            document.getElementById('dropZone').style.borderColor = '#e0e0e0';
                            const files = e.dataTransfer.files;
                            if (files.length > 0) {
                                const input = document.getElementById('fileInput');
                                input.files = files;
                                handleFileSelect({ target: input });
                            }
                        }
                        
                        function handleFileSelect(e) {
                            const file = e.target.files[0];
                            if (file) {
                                selectedFile = file;
                                document.getElementById('fileInfo').style.display = 'block';
                                document.getElementById('fileName').textContent = file.name;
                                document.getElementById('fileSize').textContent = formatSize(file.size);
                            }
                        }
                        
                        function formatSize(bytes) {
                            if (bytes === 0) return '0 B';
                            const k = 1024;
                            const sizes = ['B', 'KB', 'MB', 'GB'];
                            const i = Math.floor(Math.log(bytes) / Math.log(k));
                            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                        }
                    </script>
                    
                    <?php include '../../includes/footer.php'; ?>
