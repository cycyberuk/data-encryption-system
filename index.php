<?php
/**
 * Landing Page
 * Data Encryption System
 */

require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Home';

// Redirect to dashboard if logged in
if (isLoggedIn()) {
    header('Location: ' . SITE_URL . 'modules/dashboard/');
    exit();
}

include 'includes/header.php';
?>

<div style="text-align: center; padding: 60px 20px; ">
    <div style="max-width: 700px; margin: 0 auto;">
        <div style="  border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
             <img src="logo-main.webp" height="80px" width="80px" alt="Paul University Logo">
        </div>
       
        <h1 style="font-size: 40px; color: var(--paul-blue); font-weight: 800; margin-bottom: 10px;">
           M.Sc Computer Science Project
        </h1>
        <p style="font-size: 18px; color: var(--paul-gray); margin-bottom: 30px;">
          TOPIC: DATA ENCRYPTION AND CRYPTOGRAPHIC SOLUTIONS FOR INFORMATION SECURITY BY <strong>ORJI, CYRUS EBERE</strong> - JUNE 2025
		  <br> Supervised By: <strong>Prof Paul Nosike</strong>
        </p>
        
        <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
            <a href="modules/auth/login.php" 
               style="background: var(--paul-blue); color: white; padding: 16px 40px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 16px; transition: all 0.3s;">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
            <a href="modules/auth/register.php" 
               style="background: var(--paul-gold); color: var(--paul-blue); padding: 16px 40px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 16px; transition: all 0.3s;">
                <i class="fas fa-user-plus"></i> Register
            </a>
        </div>
    </div>
</div>

<!-- Features Section -->
<div style="background: white; padding: 50px 20px; margin: 30px 0;">
    <div style="max-width: 1000px; margin: 0 auto;">
        <h2 style="text-align: center; font-size: 28px; color: var(--paul-blue); margin-bottom: 40px;">Key Features</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
            <div style="text-align: center; padding: 20px;">
                <div style="width: 60px; height: 60px; background: var(--paul-blue); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                    <i class="fas fa-lock" style="color: var(--paul-gold); font-size: 24px;"></i>
                </div>
                <h3 style="font-size: 16px; color: var(--paul-blue);">AES-256 Encryption</h3>
                <p style="font-size: 14px; color: var(--paul-gray);">Military-grade encryption for your sensitive data</p>
            </div>
            
            <div style="text-align: center; padding: 20px;">
                <div style="width: 60px; height: 60px; background: var(--paul-blue); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                    <i class="fas fa-key" style="color: var(--paul-gold); font-size: 24px;"></i>
                </div>
                <h3 style="font-size: 16px; color: var(--paul-blue);">Secure Key Management</h3>
                <p style="font-size: 14px; color: var(--paul-gray);">Your keys are encrypted and securely stored</p>
            </div>
            
            <div style="text-align: center; padding: 20px;">
                <div style="width: 60px; height: 60px; background: var(--paul-blue); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                    <i class="fas fa-file" style="color: var(--paul-gold); font-size: 24px;"></i>
                </div>
                <h3 style="font-size: 16px; color: var(--paul-blue);">File & Text Encryption</h3>
                <p style="font-size: 14px; color: var(--paul-gray);">Encrypt both files and text with ease</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>