<?php
session_start();

// ==========================
// Demo Checkout - Simplified for Testing
// ==========================
$total = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['qty'];
    }
}

$is_high_value = ($total >= 100);
$user_logged_in = isset($_SESSION['user_registered']) && $_SESSION['user_registered'];
$has_email_otp = $user_logged_in && isset($_SESSION['user_email_otp_enabled']) && $_SESSION['user_email_otp_enabled'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['demo_action'])) {
    
    // Store address data for demo
    $_SESSION['demo_address'] = [
        'name' => $_POST['address_name'] ?? '',
        'street' => $_POST['address_street'] ?? '',
        'city' => $_POST['address_city'] ?? '',
        'state' => $_POST['address_state'] ?? '',
        'zip' => $_POST['address_zip'] ?? '',
        'country' => $_POST['address_country'] ?? ''
    ];
    
    $action = $_POST['demo_action'];
    
    if ($action === 'demo_otp' && $is_high_value && $has_email_otp) {
        // Demo Email OTP Flow
        $_SESSION['pending_payment_method'] = 'demo_paypal';
        $_SESSION['pending_payment_total'] = $total;
        header("Location: auth/send_otp.php");
        exit;
        
    } elseif ($action === 'demo_success') {
        // Direct to success for demo
        header("Location: Payments/success.php?method=demo&st=Completed&tx=" . uniqid() . "&amt=" . $total . "&cc=AUD");
        exit;
        
    } elseif ($action === 'demo_paypal') {
        // Simulate PayPal redirect
        $_SESSION['demo_payment_method'] = 'PayPal';
        header("Location: demo_payment_success.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Demo Checkout - Mini Shop</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .demo-container {
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .demo-notice {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: center;
        }
        .demo-high-value {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .status-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .status-active {
            background: #e3f2fd;
            border: 2px solid #007bff;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }
        .form-grid input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        .form-grid .full-width {
            grid-column: span 2;
        }
        .demo-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 30px 0;
        }
        .demo-btn {
            padding: 15px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            font-weight: bold;
            text-align: center;
        }
        .btn-otp { background: #28a745; color: white; }
        .btn-simple { background: #007bff; color: white; }
        .btn-paypal { background: #ffc439; color: #000; }
        .demo-btn:hover { opacity: 0.9; }
        .feature-list {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>

<header>
    🛍️ Mini Shop - Demo Checkout
    <a href="index.php">← Back to Shop</a>
</header>

<div class="demo-container">
    
    <div class="demo-notice <?= $is_high_value ? 'demo-high-value' : '' ?>">
        🧪 <strong>DEMO CHECKOUT</strong><br>
        <?php if ($is_high_value): ?>
            High-Value Purchase: Enhanced Security Features Available
        <?php else: ?>
            Standard Purchase: Basic Security Features
        <?php endif; ?>
    </div>
    
    <?php if (!empty($_SESSION['cart'])): ?>
        
        <!-- Order Summary -->
        <div class="feature-list">
            <h3>📋 Order Summary</h3>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span><strong>Total Amount:</strong></span>
                <span style="font-size: 1.5em; color: <?= $is_high_value ? '#dc3545' : '#007bff' ?>; font-weight: bold;">
                    <?= number_format($total, 2) ?> AUD
                </span>
            </div>
            <?php if ($is_high_value): ?>
                <div style="color: #dc3545; font-weight: bold; margin-top: 10px;">
                    ⚠️ High-value purchase detected! Enhanced security available.
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Current Status -->
        <div class="status-grid">
            <div class="status-item <?= $user_logged_in ? 'status-active' : '' ?>">
                👤 <strong>User Status</strong><br>
                <small><?= $user_logged_in ? 'Logged In' : 'Guest Mode' ?></small>
            </div>
            <div class="status-item <?= $has_email_otp ? 'status-active' : '' ?>">
                📧 <strong>Email OTP</strong><br>
                <small><?= $has_email_otp ? 'Enabled' : 'Disabled' ?></small>
            </div>
            <div class="status-item <?= $is_high_value ? 'status-active' : '' ?>">
                💰 <strong>Security Level</strong><br>
                <small><?= $is_high_value ? 'High Security' : 'Standard' ?></small>
            </div>
            <div class="status-item status-active">
                🛡️ <strong>Demo Mode</strong><br>
                <small>Testing Active</small>
            </div>
        </div>
        
        <!-- Address Form -->
        <form method="post" id="demo-form">
            <h3>📍 Delivery Address</h3>
            <div class="form-grid">
                <input type="text" name="address_name" placeholder="Full Name" value="Demo User" required>
                <input type="text" name="address_street" placeholder="Street Address" value="123 Demo Street" required>
                <input type="text" name="address_city" placeholder="City" value="Melbourne" required>
                <input type="text" name="address_state" placeholder="State" value="VIC" required>
                <input type="text" name="address_zip" placeholder="ZIP Code" value="3000" required>
                <input type="text" name="address_country" placeholder="Country" value="Australia" required>
            </div>
            
            <!-- Demo Payment Options -->
            <h3>🧪 Demo Payment Options</h3>
            
            <div class="demo-buttons">
                
                <?php if ($is_high_value && $has_email_otp): ?>
                <button type="submit" name="demo_action" value="demo_otp" class="demo-btn btn-otp">
                    📧 Test Email OTP Flow<br>
                    <small>Real email verification ($100+)</small>
                </button>
                <?php endif; ?>
                
                <button type="submit" name="demo_action" value="demo_success" class="demo-btn btn-simple">
                    ✅ Quick Demo Success<br>
                    <small>Skip to success page</small>
                </button>
                
                <button type="submit" name="demo_action" value="demo_paypal" class="demo-btn btn-paypal">
                    💳 Demo PayPal Flow<br>
                    <small>Simulate payment process</small>
                </button>
                
            </div>
        </form>
        
        <!-- Feature Explanation -->
        <div class="feature-list">
            <h4>🎯 What Each Demo Does:</h4>
            <ul>
                <?php if ($is_high_value && $has_email_otp): ?>
                <li><strong>Email OTP Flow</strong>: Sends real verification email, full security process</li>
                <?php endif; ?>
                <li><strong>Quick Demo Success</strong>: Bypasses all verification, shows success immediately</li>
                <li><strong>Demo PayPal Flow</strong>: Simulates payment without external redirects</li>
            </ul>
            
            <?php if (!$user_logged_in): ?>
                <div style="margin-top: 15px; padding: 10px; background: #fff3cd; border-radius: 6px;">
                    💡 <strong>Tip</strong>: <a href="auth/login.php">Login</a> or <a href="auth/register.php">Register</a> to enable enhanced security features!
                </div>
            <?php endif; ?>
            
            <?php if (!$is_high_value): ?>
                <div style="margin-top: 15px; padding: 10px; background: #e3f2fd; border-radius: 6px;">
                    💡 <strong>Tip</strong>: Add more items to reach $100+ AUD to test high-value security features!
                </div>
            <?php endif; ?>
        </div>
        
    <?php else: ?>
        <p>Your cart is empty. <a href="index.php">Go Shopping</a></p>
    <?php endif; ?>
    
</div>

</body>
</html>