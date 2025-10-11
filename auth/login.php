<?php
session_start();

// Simple login processor (for demo purposes)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];
    
    // For demo: simulate login (in production, check against database)
    if ($email && strlen($password) >= 6) {
        // Simulate successful login
        $_SESSION['user_registered'] = true;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = 'Demo User';
        $_SESSION['user_2fa_enabled'] = true; // Demo with 2FA enabled
        $_SESSION['user_email_otp_enabled'] = true; // Demo with email OTP enabled
        $_SESSION['user_security_notifications'] = true;
        
        header("Location: ../index.php");
        exit;
    } else {
        $error = "Invalid email or password (use any email + 6+ char password for demo)";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mini Shop</title>
    <link rel="stylesheet" href="../css/style.css">
    
    <style>
        .auth-container {
            max-width: 400px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .btn-primary {
            background: #007BFF;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 10px;
        }
        .btn-primary:hover { background: #0056b3; }
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .demo-notice {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>

<header>
    🛍️ Mini Shop - Login
    <a href="../index.php">← Back to Shop</a>
</header>

<div class="auth-container">
    <h2>🔐 Secure Login</h2>
    
    <div class="demo-notice">
        <strong>🧪 Demo Mode:</strong><br>
        Use any valid email and password (6+ characters) to test the secure features!
    </div>
    
    <?php if (isset($error)): ?>
        <div class="error-message">
            ⚠️ <?= $error ?>
        </div>
    <?php endif; ?>
    
    <form method="post">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required placeholder="test@example.com">
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="6+ characters">
        </div>
        
        <button type="submit" class="btn-primary">🔐 Login with Security Features</button>
        
        <div style="text-align: center; margin-top: 20px;">
            Don't have an account? <a href="register.php">Create secure account</a>
        </div>
        
        <div style="text-align: center; margin-top: 15px; color: #666; font-size: 14px;">
            <strong>Demo Features After Login:</strong><br>
            ✅ 2FA Enabled &nbsp; ✅ Email OTP &nbsp; ✅ Enhanced Security
        </div>
    </form>
</div>

</body>
</html>