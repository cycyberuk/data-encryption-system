<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' : ''; ?> DATA ENCRYPTION AND CRYPTOGRAPHIC SOLUTIONS FOR INFORMATION SECURITY BY ORJI, CYRUS EBERE M.SC COMPUTER SCIENCE | Supervised By Prof Paul Nosike - Paul University Awka </title>
     
	 
	 <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>logo-pua.png">
    <!-- Paul University Color Scheme -->
    <style>
        :root {
            --paul-blue: #003366;
            --paul-gold: #FFD700;
            --paul-dark: #1a1a1a;
            --paul-light: #F5F5F5;
            --paul-gray: #666666;
            --paul-white: #FFFFFF;
            --paul-success: #28a745;
            --paul-danger: #dc3545;
            --paul-warning: #ffc107;
            --paul-info: #17a2b8;
        }
    </style>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* ===== BASE STYLES ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--paul-light);
            color: var(--paul-dark);
            line-height: 1.6;
            min-height: 100vh;
        }
        
        /* ===== PAUL UNIVERSITY HEADER ===== */
        .paul-header {
            background: var(--paul-blue);
            padding: 12px 0;
            border-bottom: 4px solid var(--paul-gold);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .paul-header .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .paul-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--paul-white);
        }
        
        .paul-brand .logo-icon {
            width: 45px;
            height: 45px;
            background: var(--paul-gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 20px;
            color: var(--paul-blue);
        }
        
        .paul-brand .brand-text {
            display: flex;
            flex-direction: column;
        }
        
        .paul-brand .brand-name {
            font-size: 18px;
            font-weight: 700;
            color: var(--paul-white);
            letter-spacing: 0.5px;
        }
        
        .paul-brand .brand-sub {
            font-size: 11px;
            color: rgba(255,255,255,0.8);
            font-weight: 300;
            letter-spacing: 1px;
        }
        
        .paul-nav {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .paul-nav a {
            color: var(--paul-white);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .paul-nav a:hover {
            background: rgba(255,255,255,0.15);
        }
        
        .paul-nav a.active {
            background: var(--paul-gold);
            color: var(--paul-blue);
        }
        
        .paul-nav .nav-divider {
            color: rgba(255,255,255,0.3);
            padding: 0 4px;
        }
        
        .user-dropdown {
            position: relative;
            display: inline-block;
        }
        
        .user-dropdown .dropbtn {
            background: rgba(255,255,255,0.1);
            color: var(--paul-white);
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .user-dropdown .dropbtn:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .user-dropdown .dropbtn .avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--paul-gold);
            color: var(--paul-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 12px;
        }
        
        .user-dropdown .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 45px;
            background: var(--paul-white);
            min-width: 200px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            border-radius: 8px;
            overflow: hidden;
            z-index: 1000;
        }
        
        .user-dropdown .dropdown-content.show {
            display: block;
        }
        
        .user-dropdown .dropdown-content a {
            color: var(--paul-dark);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            border-bottom: 1px solid #eee;
        }
        
        .user-dropdown .dropdown-content a:hover {
            background: var(--paul-light);
        }
        
        .user-dropdown .dropdown-content .dropdown-divider {
            height: 1px;
            background: #eee;
            margin: 4px 0;
        }
        
        .user-dropdown .dropdown-content .logout-link {
            color: var(--paul-danger);
        }
        
        /* ===== MOBILE NAV TOGGLE ===== */
        .nav-toggle {
            display: none;
            background: rgba(255,255,255,0.1);
            border: none;
            color: var(--paul-white);
            font-size: 24px;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
        }
        
        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .paul-header .container {
                flex-wrap: wrap;
            }
            
            .nav-toggle {
                display: block;
            }
            
            .paul-nav {
                display: none;
                width: 100%;
                flex-direction: column;
                padding: 15px 0 5px;
                gap: 5px;
            }
            
            .paul-nav.open {
                display: flex;
            }
            
            .paul-nav a {
                width: 100%;
                padding: 10px 16px;
                text-align: center;
            }
            
            .user-dropdown {
                width: 100%;
            }
            
            .user-dropdown .dropbtn {
                width: 100%;
                justify-content: center;
            }
            
            .user-dropdown .dropdown-content {
                right: auto;
                left: 0;
                width: 100%;
            }
        }
        
        /* ===== MAIN CONTENT ===== */
        .main-content {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        /* ===== ALERT MESSAGES ===== */
        .alert {
            padding: 14px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid transparent;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .alert-success {
            background: #d4edda;
            border-color: var(--paul-success);
            color: #155724;
        }
        
        .alert-error {
            background: #f8d7da;
            border-color: var(--paul-danger);
            color: #721c24;
        }
        
        .alert-warning {
            background: #fff3cd;
            border-color: var(--paul-warning);
            color: #856404;
        }
        
        .alert-info {
            background: #d1ecf1;
            border-color: var(--paul-info);
            color: #0c5460;
        }
        
        .alert .alert-icon {
            font-size: 20px;
        }
        
        /* ===== FOOTER ===== */
        .paul-footer {
            background: var(--paul-blue);
            color: var(--paul-white);
            padding: 25px 0;
            margin-top: 50px;
            border-top: 3px solid var(--paul-gold);
        }
        
        .paul-footer .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            text-align: center;
        }
        
        .paul-footer p {
            opacity: 0.8;
            font-size: 14px;
        }
        
        .paul-footer .footer-gold {
            color: var(--paul-gold);
            font-weight: 600;
        }
    </style>
</head>
<body>

<!-- ===== PAUL UNIVERSITY HEADER ===== -->
<header class="paul-header">
    <div class="container">
        <a href="<?php echo SITE_URL; ?>" class="paul-brand">
            <div class="logo-icon">PUA</div>
            <div class="brand-text">
                <span class="brand-name">PAUL UNIVERSITY AWKA</span>
                <span class="brand-sub">Data Encryption System - MSc Project</span>
            </div>
        </a>
        
        <button class="nav-toggle" onclick="toggleNav()" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>
        
        <nav class="paul-nav" id="mainNav">
            <?php if (isLoggedIn()): ?>
                <a href="<?php echo SITE_URL; ?>modules/dashboard/" class="active">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
                <a href="<?php echo SITE_URL; ?>modules/encryption/encrypt.php">
                    <i class="fas fa-lock"></i> Encrypt
                </a>
                <a href="<?php echo SITE_URL; ?>modules/encryption/decrypt.php">
                    <i class="fas fa-unlock"></i> Decrypt
                </a>
				
				     <a href="<?php echo SITE_URL; ?>modules/dashboard/benchmark.php">
                    <i class="fas fa-unlock"></i> Benchmark
                </a>
				
 
                <span class="nav-divider">|</span>
                <div class="user-dropdown">
                    <button class="dropbtn" onclick="toggleDropdown()">
                        <span class="avatar"><?php echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?></span>
                        <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-content" id="userDropdown">
                        <a href="<?php echo SITE_URL; ?>modules/dashboard/">
                            <i class="fas fa-user"></i> My Profile
                        </a>
                        <a href="<?php echo SITE_URL; ?>modules/dashboard/">
                            <i class="fas fa-key"></i> My Keys
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="<?php echo SITE_URL; ?>modules/auth/logout.php" class="logout-link">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?php echo SITE_URL; ?>modules/auth/login.php">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
                <a href="<?php echo SITE_URL; ?>modules/auth/register.php">
                    <i class="fas fa-user-plus"></i> Register
                </a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<!-- ===== MAIN CONTENT ===== -->
<main class="main-content" >

<script>
// Toggle mobile navigation
function toggleNav() {
    document.getElementById('mainNav').classList.toggle('open');
}

// Toggle user dropdown
function toggleDropdown() {
    document.getElementById('userDropdown').classList.toggle('show');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    var dropdown = document.getElementById('userDropdown');
    var btn = document.querySelector('.dropbtn');
    if (dropdown && !dropdown.contains(event.target) && !btn?.contains(event.target)) {
        dropdown.classList.remove('show');
    }
});
</script>