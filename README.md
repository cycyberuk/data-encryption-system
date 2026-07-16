Here's a comprehensive README.md file for your GitHub repository:

```markdown
# 🔐 Data Encryption System - CST Dept Paul University

[![PHP Version](https://img.shields.io/badge/PHP-8.3-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql&logoColor=white)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![NDPA](https://img.shields.io/badge/NDPA-2023_Compliant-003366)](https://ndpc.gov.ng)

> **A Complete Web-Based Data Encryption System using AES-256 Encryption**

A full-featured encryption system developed as part of the MSc Computer Science & Information Technology program at **Paul University Awka, Anambra State, Nigeria**. This system provides secure data encryption and decryption with enterprise-grade security features.

---

## 📋 Table of Contents

- [Overview](#-overview)
- [Features](#-features)
- [Technology Stack](#-technology-stack)
- [Installation](#-installation)
- [Database Setup](#-database-setup)
- [Project Structure](#-project-structure)
- [Usage Guide](#-usage-guide)
- [Security Features](#-security-features)
- [Performance Benchmark](#-performance-benchmark)
- [API Endpoints](#-api-endpoints)
- [Screenshots](#-screenshots)
- [Contributing](#-contributing)
- [License](#-license)
- [Contact](#-contact)

---

## 📖 Overview

The Data Encryption System is a secure web application designed to protect sensitive information through industry-standard AES-256 encryption. Built as part of an MSc research project, this system demonstrates practical implementation of cryptographic solutions for information security in the Nigerian context, fully compliant with the Nigeria Data Protection Act (NDPA) 2023.

### 🎯 Objectives
- Provide end-to-end encryption for sensitive data
- Implement secure key management with password-based wrapping
- Ensure NDPA 2023 compliance
- Deliver a user-friendly interface for encryption/decryption
- Enable performance benchmarking and monitoring

---

## ✨ Features

### Core Features
| Feature | Description |
|---------|-------------|
| 🔐 **AES-256 Encryption** | Military-grade encryption for maximum security |
| 🔑 **Secure Key Management** | Password-wrapped encryption keys stored securely |
| 📄 **Text Encryption** | Encrypt any text data instantly |
| 📁 **File Encryption** | Upload and encrypt files (up to 50MB) |
| 🔓 **Secure Decryption** | Decrypt files and text with password verification |
| 👤 **User Authentication** | Secure registration and login system |
| 📊 **Audit Logging** | Complete activity tracking for compliance |
| 📈 **Performance Benchmark** | Test and visualize encryption performance |
| 📱 **Responsive Design** | Works on desktop, tablet, and mobile |

### Security Features
- ✅ **BCrypt Password Hashing** (Cost Factor 12)
- ✅ **AES-256-CBC Encryption** 
- ✅ **CSRF Protection** on all forms
- ✅ **SQL Injection Prevention** (Prepared Statements)
- ✅ **XSS Protection** (HTML Escaping)
- ✅ **Session Security** (HTTP-Only Cookies)
- ✅ **NDPA 2023 Compliance**
- ✅ **Comprehensive Audit Logging**

---

## 🛠 Technology Stack

### Backend
| Technology | Version | Purpose |
|------------|---------|---------|
| PHP | 8.3.x | Core application logic |
| MySQL | 8.0.x | Database storage |
| OpenSSL | 3.0.x | Cryptographic operations |

### Frontend
| Technology | Version | Purpose |
|------------|---------|---------|
| HTML5 | - | Page structure |
| CSS3 | - | Styling & responsive design |
| JavaScript | ES6 | Interactive features |
| Chart.js | 4.4.x | Performance charts |

### Server Requirements
- PHP 8.3 or higher
- MySQL 8.0 or higher
- Web Server (Apache/Nginx)
- OpenSSL extension enabled
- PDO MySQL extension enabled

---

## 📦 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/cyberuk/data-encryption-system.git
cd data-encryption-system
```

### 2. Configure Web Server

#### Using XAMPP
```bash
# Move to htdocs directory
mv data-encryption-system /opt/lampp/htdocs/
# or C:\xampp\htdocs\ on Windows
```

#### Using Apache (Manual)
```apache
# Add to httpd.conf or vhosts.conf
<VirtualHost *:80>
    ServerName encryption.local
    DocumentRoot "/var/www/html/data-encryption-system"
    <Directory "/var/www/html/data-encryption-system">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### 3. Database Setup

```bash
# Create database and tables
mysql -u root -p < database/schema.sql
```

### 4. Configuration

Copy and configure the database settings:

```bash
cp config/database.example.php config/database.php
```

Edit `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'encryption_system');
define('SITE_URL', 'http://localhost/MSCPUA/');
```

### 5. Set Permissions

```bash
# Linux/macOS
chmod -R 755 .
chmod -R 777 sessions/
chmod -R 777 uploads/

# Windows - Set proper permissions through file properties
```

### 6. Run Setup Script

Visit the setup page in your browser:
```
http://localhost/MSCPUA/setup.php
```

### 7. Default Admin Account

After running setup, use these credentials:

| Field | Value |
|-------|-------|
| Username | `admin` |
| Password | `Admin@2026` |

---

## 🗄 Database Schema

```sql
-- Core Tables
users                 -- User accounts and authentication
encryption_keys       -- Encrypted encryption keys
encrypted_data        -- Encrypted files and text
audit_log             -- System activity log

-- Key Relationships
users 1──N encryption_keys
users 1──N encrypted_data
users 1──N audit_log
```

### Database Structure

| Table | Description |
|-------|-------------|
| `users` | User accounts (username, email, password_hash, login tracking) |
| `encryption_keys` | Encrypted AES-256 keys (versioned, with salt and IV) |
| `encrypted_data` | Encrypted content with metadata (file name, size, type) |
| `audit_log` | Complete activity trail (login, encryption, decryption, etc.) |

---

## 📁 Project Structure

```
data-encryption-system/
├── assets/
│   ├── css/
│   │   ├── style.css
│   │   └── responsive.css
│   ├── js/
│   │   └── main.js
│   └── images/
├── config/
│   └── database.php
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── functions.php
├── modules/
│   ├── auth/
│   │   ├── login.php
│   │   ├── register.php
│   │   ├── logout.php
│   │   └── reset_password.php
│   ├── encryption/
│   │   ├── encrypt.php
│   │   └── decrypt.php
│   └── dashboard/
│       ├── index.php
│       └── benchmark.php
├── database/
│   └── schema.sql
├── reset_now.php
├── regenerate_key.php
├── reset_password_with_key.php
├── setup.php
└── index.php
```

---

## 📱 Usage Guide

### 👤 User Registration

1. Navigate to **Register** page
2. Enter username, email, and password
3. Password requirements:
   - Minimum 8 characters
   - At least one uppercase letter
   - At least one lowercase letter
   - At least one number
4. Click **Create Account**

### 🔐 User Login

1. Navigate to **Login** page
2. Enter username or email
3. Enter password
4. Click **Login**

### 🔒 Encrypt Data

#### Encrypt Text
1. From Dashboard, click **Encrypt**
2. Select **Encrypt Text** tab
3. Enter a name (optional)
4. Type or paste text to encrypt
5. Enter your password
6. Click **Encrypt Text**

#### Encrypt File
1. From Dashboard, click **Encrypt**
2. Select **Encrypt File** tab
3. Drag and drop or browse for file
4. Enter your password
5. Click **Encrypt File**

### 🔓 Decrypt Data

1. From Dashboard, click **Decrypt**
2. Select data from the list
3. Enter your password
4. Click **Decrypt Data**
5. View decrypted content

### 📊 Performance Benchmark

1. From Dashboard, click **Performance Benchmark**
2. Select test parameters:
   - Data Size (1KB - 50MB)
   - Data Type (Text/JSON/CSV)
   - Iterations (1-5)
3. Enter your password
4. Click **Run Benchmark**
5. View performance charts and analysis

---

## 🔐 Security Features

### Password Protection
```php
// BCrypt with cost factor 12
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
```

### Key Management
```php
// AES-256 Key Wrapping
$passwordKey = hash_pbkdf2('sha256', $password, $salt, 100000, 32, true);
$wrappedKey = openssl_encrypt($key, 'aes-256-cbc', $passwordKey, OPENSSL_RAW_DATA, $iv);
```

### CSRF Protection
```php
// Token generation and verification
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
```

### SQL Injection Prevention
```php
// Prepared statements throughout
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
```

---

## 📈 Performance Benchmark

### Sample Test Results

| Data Size | Encryption (ms) | Decryption (ms) | Throughput (MB/s) |
|-----------|-----------------|-----------------|-------------------|
| 1 KB | 0.142 | 0.128 | 7.04 |
| 10 KB | 0.891 | 0.823 | 11.22 |
| 100 KB | 8.234 | 7.891 | 12.14 |
| 1 MB | 82.145 | 78.234 | 12.17 |
| 5 MB | 410.567 | 398.123 | 12.18 |
| 10 MB | 821.234 | 795.678 | 12.17 |

### Performance Metrics
- ✅ 100% Data Integrity
- ✅ Linear Scalability
- ✅ Average Throughput: 12.17 MB/s
- ✅ Minimal Memory Overhead

---

## 🔧 Troubleshooting

### Common Issues

#### "Failed to decrypt the encryption key"
```bash
# Regenerate encryption key
http://localhost/MSCPUA/regenerate_key.php
```

#### "Invalid username or password"
```bash
# Reset password
http://localhost/MSCPUA/reset_now.php
```

#### "Database Connection Failed"
```sql
-- Check database credentials in config/database.php
-- Verify MySQL service is running
sudo systemctl status mysql
```

### Debug Mode
```bash
# Enable debug mode in login
http://localhost/MSCPUA/modules/auth/login.php?debug=1

# Enable debug mode in benchmark
http://localhost/MSCPUA/modules/dashboard/benchmark.php?debug=1
```

---

## 📸 Screenshots

### Dashboard
![Dashboard](screenshots/dashboard.png)

### Encryption Page
![Encryption](screenshots/encryption.png)

### Decryption Page
![Decryption](screenshots/decryption.png)

### Benchmark Page
![Benchmark](screenshots/benchmark.png)

---

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch
   ```bash
   git checkout -b feature/amazing-feature
   ```
3. Commit your changes
   ```bash
   git commit -m 'Add amazing feature'
   ```
4. Push to the branch
   ```bash
   git push origin feature/amazing-feature
   ```
5. Open a Pull Request

### Development Guidelines
- Follow PSR-12 coding standards
- Write meaningful commit messages
- Update documentation accordingly
- Add comments for complex logic

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

```
MIT License

Copyright (c) 2026 Orji Cyrus Ebere - Paul University Awka

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:
...
```

---

## 👨‍🎓 Academic Context

This project was developed as part of the requirements for:

**Master of Science (MSc) in Computer Science & Information Technology**  
**Paul University Awka, Anambra State, Nigeria**

**Thesis Title:** *Data Encryption and Cryptographic Solutions for Information Security*

**Supervisor:** Prof. Paul Nosike, PhD  


---

## 📧 Contact

**Author:** Orji Cyrus Ebere  
**Student ID:** PUA/CPS/CST/2024/T1/00001  
**Email:** cyrus.orji@imopoly.edu.ng  
**Department:** Computer Science & Information Technology  
**Institution:** Paul University Awka, Anambra State, Nigeria

**Supervisor:**  
Prof. Paul Nosike  
Department of Computer Science & Information Technology  
Paul University Awka, Anambra State, Nigeria

---

## 🙏 Acknowledgments

- Prof. Paul Nosike - Head of Department
- Assoc. Prof. Ngozi Egejuru, PhD 
- Rev. Dr. N. U Ezeonyi, PhD - Lecturer
- Dr. Elvin Ugonna Eziama, PhD - Lecturer
- Department of Computer Science & Information Technology, Paul University Awka
- Department of Computer Science Technology, Imo State Polytechnic Omuma

---

## 📚 References

1. Stallings, W. (2021). *Cryptography and Network Security: Principles and Practice* (8th ed.). Pearson.
2. Barker, E., et al. (2020). *Recommendation for Key Management*. NIST SP 800-57.
3. Nigeria Data Protection Act (2023). *Federal Republic of Nigeria Official Gazette*.
4. Ogundoyin, I. K., et al. (2022). *Comparative Analysis of Cryptographic Algorithms*. Uniosun Journal.
5. Ayeni, J. A., et al. (2025). *Hybrid Data Encryption for Distributed Computing*. LAUTECH Journal.

---

## ⭐ Support

If you find this project useful, please consider:
- ⭐ Starring the repository
- 🐛 Reporting issues
- 💡 Suggesting features
- 🔀 Contributing code

---

**Built with ❤️ at Paul University Awka, Anambra State, Nigeria**

---

*Last Updated: July 2026*  
*Version: 1.0.0*
```

## Additional Files to Include

### 1. `.gitignore`

```gitignore
# PHP
*.log
*.sql
*.sqlite
*.sqlite3

# Configuration
config/database.php
config/config.php
.env
*.env

# OS Files
.DS_Store
Thumbs.db
desktop.ini

# IDE Files
.vscode/
.idea/
*.sublime-*

# Temp Files
tmp/
temp/
sessions/
uploads/
cache/

# Composer
vendor/
composer.lock
composer.phar

# NPM
node_modules/
package-lock.json

# Project Specific
setup.php
reset_*.php
fix_*.php
regenerate_*.php
test_*.php
debug_*.php
benchmark_*.php
```

### 2. `LICENSE`

```
MIT License

Copyright (c) 2026 Orji Cyrus Ebere - Paul University Awka

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

### 3. `composer.json` (Optional)

```json
{
    "name": "pauluniversity/encryption-system",
    "description": "Data Encryption System with AES-256 for Paul University",
    "type": "project",
    "require": {
        "php": ">=8.3"
    },
    "license": "MIT",
    "authors": [
        {
            "name": "Orji Cyrus Ebere",
            "email": "cyrus.orji@imopoly.edu.ng",
            "role": "Developer"
        }
    ],
    "config": {
        "optimize-autoloader": true,
        "preferred-install": "dist"
    }
}
```

This README provides comprehensive documentation for your project, making it easy for others to understand, install, and use your Data Encryption System.
