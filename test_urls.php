<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>URL Testing - Mini Shop</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .test-container {
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .url-test {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #007bff;
        }
        .url-link {
            font-family: monospace;
            background: #e9ecef;
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-block;
            margin: 5px 0;
        }
        .test-btn {
            background: #007bff;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 6px;
            margin: 5px;
            display: inline-block;
        }
        .test-btn:hover { background: #0056b3; }
    </style>
</head>
<body>

<header>
    🛍️ Mini Shop - URL Testing
    <a href="index.php">← Back to Shop</a>
</header>

<div class="test-container">
    <h2>🧪 URL Testing Page</h2>
    
    <div class="url-test">
        <h4>📋 Project URLs:</h4>
        <div class="url-link">Base URL: http://localhost/AS2/s3927657_MINI_shop_SecureComs/</div>
        <div class="url-link">Success URL: http://localhost/AS2/s3927657_MINI_shop_SecureComs/Payments/success.php</div>
    </div>
    
    <div class="url-test">
        <h4>🚀 Quick Tests:</h4>
        <a href="index.php" class="test-btn">🏠 Main Shop</a>
        <a href="demo_checkout.php" class="test-btn">🧪 Demo Checkout</a>
        <a href="Payments/success.php?method=test&st=Completed&tx=TEST123&amt=99.99&cc=AUD" class="test-btn">✅ Test Success Page</a>
        <a href="auth/register.php" class="test-btn">🔒 Registration</a>
        <a href="auth/login.php" class="test-btn">🔐 Login</a>
    </div>
    
    <div class="url-test">
        <h4>🛒 Current Cart Status:</h4>
        <?php if (!empty($_SESSION['cart'])): ?>
            <p>✅ Cart has items</p>
            <?php 
            $total = 0;
            foreach ($_SESSION['cart'] as $item) {
                $total += $item['price'] * $item['qty'];
            }
            ?>
            <p><strong>Total: <?= number_format($total, 2) ?> AUD</strong></p>
            <?php if ($total >= 100): ?>
                <p style="color: #dc3545;"><strong>🚨 High-value cart detected! Email OTP will trigger.</strong></p>
            <?php endif; ?>
        <?php else: ?>
            <p>❌ Cart is empty</p>
            <p><a href="index.php">Add some items first</a></p>
        <?php endif; ?>
    </div>
    
    <div class="url-test">
        <h4>👤 User Status:</h4>
        <?php if (isset($_SESSION['user_registered']) && $_SESSION['user_registered']): ?>
            <p>✅ Logged in as: <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong></p>
            <p>📧 Email OTP: <?= $_SESSION['user_email_otp_enabled'] ? 'Enabled' : 'Disabled' ?></p>
            <p>🔐 2FA: <?= $_SESSION['user_2fa_enabled'] ? 'Enabled' : 'Disabled' ?></p>
        <?php else: ?>
            <p>👤 Not logged in (Guest mode)</p>
            <p><a href="auth/login.php">Login</a> or <a href="auth/register.php">Register</a></p>
        <?php endif; ?>
    </div>
    
    <div style="background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0;">
        <h4>🎯 Recommended Test Flow:</h4>
        <ol>
            <li>Click "Test Success Page" to verify URLs work</li>
            <li>Go to "Main Shop" and add items to cart ($100+ for Email OTP)</li>
            <li>Register/Login to enable security features</li>
            <li>Use "Demo Checkout" for easy testing</li>
            <li>Test Email OTP flow with real verification</li>
        </ol>
    </div>
    
</div>

</body>
</html>