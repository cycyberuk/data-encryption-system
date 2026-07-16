<?php
/**
 * Dashboard
 * Data Encryption System
 */

require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Dashboard';
requireLogin();

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get user info
$user = getUserById($pdo, $userId);

// Get encrypted data count
$dataCount = getUserDataCount($pdo, $userId);

// Get recent encrypted data
$recentData = getUserEncryptedData($pdo, $userId, 10);

// Get active key info
$stmt = $pdo->prepare("SELECT key_version, created_at, expires_at FROM encryption_keys 
                       WHERE user_id = ? AND is_active = 1 ORDER BY key_version DESC LIMIT 1");
$stmt->execute([$userId]);
$activeKey = $stmt->fetch();

include '../../includes/header.php';
?>

<div style="margin-bottom: 30px;">
    <h1 style="font-size: 28px; color: var(--paul-blue);">Dashboard</h1>
    <p style="color: var(--paul-gray);">Welcome back, <strong><?php echo htmlspecialchars($username); ?></strong></p>
</div>

<!-- Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <div style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 4px solid var(--paul-blue);">
        <div style="font-size: 28px; color: var(--paul-blue); font-weight: 700;"><?php echo $dataCount; ?></div>
        <div style="color: var(--paul-gray); font-size: 14px;">Total Encrypted Items</div>
    </div>
    
    <div style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 4px solid var(--paul-gold);">
        <div style="font-size: 28px; color: var(--paul-blue); font-weight: 700;"><?php echo $activeKey ? 'v' . $activeKey['key_version'] : 'N/A'; ?></div>
        <div style="color: var(--paul-gray); font-size: 14px;">Active Key Version</div>
    </div>
    
    <div style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 4px solid var(--paul-success);">
        <div style="font-size: 28px; color: var(--paul-blue); font-weight: 700;">AES-256</div>
        <div style="color: var(--paul-gray); font-size: 14px;">Encryption Standard</div>
    </div>
    
    <div style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 4px solid var(--paul-info);">
        <div style="font-size: 28px; color: var(--paul-blue); font-weight: 700;">✓</div>
        <div style="color: var(--paul-gray); font-size: 14px;">NDPA 2023 Compliant</div>
    </div>
</div>

<!-- Quick Actions -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
    <a href="<?php echo SITE_URL; ?>modules/encryption/encrypt.php" 
       style="background: var(--paul-blue); color: white; padding: 24px; border-radius: 12px; text-decoration: none; text-align: center; transition: all 0.3s;">
        <i class="fas fa-lock" style="font-size: 32px; display: block; margin-bottom: 10px;"></i>
        <span style="font-size: 16px; font-weight: 600;">Encrypt Data</span>
        <p style="font-size: 13px; opacity: 0.8; margin-top: 5px;">Secure your files and text</p>
    </a>
    <a href="<?php echo SITE_URL; ?>modules/encryption/decrypt.php" 
       style="background: var(--paul-gold); color: var(--paul-blue); padding: 24px; border-radius: 12px; text-decoration: none; text-align: center; transition: all 0.3s;">
        <i class="fas fa-unlock" style="font-size: 32px; display: block; margin-bottom: 10px;"></i>
        <span style="font-size: 16px; font-weight: 600;">Decrypt Data</span>
        <p style="font-size: 13px; opacity: 0.8; margin-top: 5px;">Access your encrypted files</p>
    </a>
</div>

<!-- Recent Activity -->
<div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <h2 style="font-size: 18px; color: var(--paul-blue); margin-bottom: 15px;">Recent Encrypted Items</h2>
    
    <?php if (empty($recentData)): ?>
        <p style="color: var(--paul-gray); text-align: center; padding: 30px 0;">
            <i class="fas fa-inbox" style="font-size: 40px; display: block; margin-bottom: 10px; opacity: 0.3;"></i>
            No encrypted items yet. <a href="<?php echo SITE_URL; ?>modules/encryption/encrypt.php" style="color: var(--paul-blue);">Encrypt your first file</a>
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
                        <th style="padding: 12px 16px; text-align: left;">Actions</th>
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
                                <a href="<?php echo SITE_URL; ?>modules/encryption/decrypt.php?id=<?php echo $item['data_id']; ?>" 
                                   style="color: var(--paul-blue); text-decoration: none;">
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

<?php include '../../includes/footer.php'; ?>
