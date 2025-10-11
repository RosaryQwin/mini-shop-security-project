<?php
session_start();

// Simple registration processor for testing
// In production, this would connect to a database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Basic input validation
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $full_name = htmlspecialchars($_POST['full_name']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate inputs
    if (!$email) {
        $error = "Invalid email address";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        
        // Validate reCAPTCHA v2
        if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
            $error = "Please complete the reCAPTCHA verification";
        } else {
            
            // For testing purposes, we'll just simulate success
            // In production, verify reCAPTCHA with Google's API
            
            // Store user preferences in session (simulate database)
            $_SESSION['user_registered'] = true;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $full_name;
            $_SESSION['user_2fa_enabled'] = isset($_POST['enable_2fa']);
            $_SESSION['user_email_otp_enabled'] = isset($_POST['enable_email_otp']);
            $_SESSION['user_security_notifications'] = isset($_POST['security_notifications']);
            
            // Hash password (in production, use password_hash())
            $_SESSION['user_password_hash'] = password_hash($password, PASSWORD_DEFAULT);
            
            $success = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Result - Mini Shop</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .result-container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
        }
        .success {
            color: #28a745;
            border: 2px solid #28a745;
            background: #f8fff9;
        }
        .error {
            color: #dc3545;
            border: 2px solid #dc3545;
            background: #fff8f8;
        }
        .security-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
        }
        .btn {
            display: inline-block;
            background: #007BFF;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 8px;
            margin: 10px;
        }
        .btn:hover { background: #0056b3; }
        .feature-enabled { color: #28a745; font-weight: bold; }
        .feature-disabled { color: #6c757d; }
    </style>
</head>
<body>

<header>
    🛍️ Mini Shop - Registration Result
    <a href="../index.php">← Back to Shop</a>
</header>

<div class="result-container <?= isset($success) ? 'success' : 'error' ?>">
    
    <?php if (isset($success)): ?>
        
        <h2>✅ Account Created Successfully!</h2>
        
        <p>Welcome to Mini Shop, <strong><?= $full_name ?></strong>!</p>
        
        <div class="security-summary">
            <h4>🔒 Your Security Settings:</h4>
            <ul style="text-align: left;">
                <li class="<?= $_SESSION['user_2fa_enabled'] ? 'feature-enabled' : 'feature-disabled' ?>">
                    🔐 Google Authenticator 2FA: <?= $_SESSION['user_2fa_enabled'] ? 'ENABLED' : 'Disabled' ?>
                </li>
                <li class="<?= $_SESSION['user_email_otp_enabled'] ? 'feature-enabled' : 'feature-disabled' ?>">
                    📧 Email OTP for High-Value Purchases: <?= $_SESSION['user_email_otp_enabled'] ? 'ENABLED' : 'Disabled' ?>
                </li>
                <li class="<?= $_SESSION['user_security_notifications'] ? 'feature-enabled' : 'feature-disabled' ?>">
                    🔔 Security Notifications: <?= $_SESSION['user_security_notifications'] ? 'ENABLED' : 'Disabled' ?>
                </li>
            </ul>
        </div>
        
        <?php if ($_SESSION['user_2fa_enabled']): ?>
            <div style="background: #e3f2fd; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <strong>⚠️ Next Step: Set up Google Authenticator</strong><br>
                <small>You'll need to scan a QR code to complete 2FA setup on your next login.</small>
            </div>
        <?php endif; ?>
        
        <p>Your account is ready! You can now:</p>
        
        <a href="../index.php" class="btn">🛍️ Start Shopping</a>
        <a href="../secure_checkout.php" class="btn">🔒 Try Secure Checkout</a>
        
    <?php else: ?>
        
        <h2>❌ Registration Failed</h2>
        
        <p><strong>Error:</strong> <?= $error ?></p>
        
        <a href="register.php" class="btn">← Try Again</a>
        
    <?php endif; ?>
    
</div>

<script>
// Auto-redirect to shopping after successful registration
<?php if (isset($success)): ?>
setTimeout(function() {
    if (confirm("Registration complete! Would you like to start shopping now?")) {
        window.location.href = "../index.php";
    }
}, 3000);
<?php endif; ?>
</script>

</body>
</html>