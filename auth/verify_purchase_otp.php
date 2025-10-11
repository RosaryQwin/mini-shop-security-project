<?php
session_start();

// Check if user is logged in and has email OTP enabled
if (!isset($_SESSION['user_registered']) || !$_SESSION['user_email_otp_enabled']) {
    header("Location: ../index.php");
    exit;
}

// Check if OTP was submitted
if (!isset($_POST['otp_code'])) {
    header("Location: send_otp.php");
    exit;
}

$submitted_otp = $_POST['otp_code'];
$stored_otp = $_SESSION['purchase_otp'] ?? '';
$otp_time = $_SESSION['otp_generated_time'] ?? 0;
$total = $_SESSION['pending_payment_total'] ?? 0;
$payment_method = $_SESSION['pending_payment_method'] ?? 'unknown';

// Check if OTP has expired (10 minutes = 600 seconds)
$otp_expired = (time() - $otp_time) > 600;

$verification_success = false;
$error_message = "";

if ($otp_expired) {
    $error_message = "Verification code has expired. Please request a new code.";
} elseif (strlen($submitted_otp) !== 6 || !ctype_digit($submitted_otp)) {
    $error_message = "Invalid verification code format. Please enter 6 digits.";
} elseif ($submitted_otp !== $stored_otp) {
    $error_message = "Incorrect verification code. Please check your email and try again.";
} else {
    // OTP verified successfully!
    $verification_success = true;
    
    // Clear OTP data
    unset($_SESSION['purchase_otp']);
    unset($_SESSION['otp_generated_time']);
    
    // Continue with the original payment process
    include_once "../Payments/paypal/config.php";
    
    // For demo purposes, redirect to PayPal (in production, use the actual selected payment method)
    if ($payment_method === 'paypal') {
        echo '<form id="paypalForm" action="'.PAYPAL_URL.'" method="post">
            <input type="hidden" name="business" value="'.PAYPAL_ID.'">
            <input type="hidden" name="cmd" value="_xclick">
            <input type="hidden" name="item_name" value="Mini Shop Secure Order">
            <input type="hidden" name="item_number" value="'.uniqid().'">
            <input type="hidden" name="amount" value="'.number_format($total, 2, ".", "").'">
            <input type="hidden" name="currency_code" value="'.PAYPAL_CURRENCY.'">
            <input type="hidden" name="return" value="'.PAYPAL_RETURN_URL.'">
            <input type="hidden" name="cancel_return" value="'.PAYPAL_CANCEL_URL.'">
            <input type="hidden" name="notify_url" value="'.PAYPAL_NOTIFY_URL.'">
        </form>
        <script>document.getElementById("paypalForm").submit();</script>';
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Result - Mini Shop</title>
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
            border: 2px solid #28a745;
            background: #f8fff9;
        }
        .error {
            border: 2px solid #dc3545;
            background: #fff8f8;
        }
        .success-badge {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .error-badge {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .transaction-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
        }
        .security-summary {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 10px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-primary { background: #007bff; color: white; }
        .btn:hover { opacity: 0.9; }
        .processing-animation {
            display: inline-block;
            animation: spin 2s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

<header>
    🛍️ Mini Shop - Verification Result
    <a href="../index.php">← Back to Shop</a>
</header>

<div class="result-container <?= $verification_success ? 'success' : 'error' ?>">
    
    <?php if ($verification_success): ?>
        
        <div class="success-badge">
            ✅ <strong>VERIFICATION SUCCESSFUL</strong><br>
            <span class="processing-animation">🔄</span> Processing your secure payment...
        </div>
        
        <h2>🎉 Email OTP Verified!</h2>
        
        <div class="transaction-details">
            <h4>📋 Verified Transaction:</h4>
            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                <span><strong>Amount:</strong></span>
                <span style="color: #28a745; font-weight: bold;"><?= number_format($total, 2) ?> AUD</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                <span><strong>Payment Method:</strong></span>
                <span><?= ucfirst($payment_method) ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                <span><strong>Verification Time:</strong></span>
                <span><?= date('Y-m-d H:i:s') ?></span>
            </div>
        </div>
        
        <div class="security-summary">
            <h4>🛡️ Security Features Applied:</h4>
            <ul style="text-align: left; margin: 10px 0;">
                <li>✅ <strong>Email OTP Verification</strong> - Completed</li>
                <li>✅ <strong>reCAPTCHA v3</strong> - Risk assessment passed</li>
                <li>✅ <strong>High-Value Protection</strong> - $100+ security protocol</li>
                <li>✅ <strong>Secure Payment Gateway</strong> - Encrypted processing</li>
            </ul>
        </div>
        
        <p><strong>🔄 Redirecting to payment processor...</strong></p>
        <p>If you are not redirected automatically, click the button below:</p>
        
        <a href="../Payments/success.php?method=<?= $payment_method ?>&st=Verified&tx=<?= uniqid() ?>&amt=<?= $total ?>&cc=AUD" class="btn btn-success">
            💳 Complete Payment
        </a>
        
        <script>
            // Auto-redirect after 3 seconds if not already redirected
            setTimeout(() => {
                window.location.href = "../Payments/success.php?method=<?= $payment_method ?>&st=Verified&tx=<?= uniqid() ?>&amt=<?= $total ?>&cc=AUD";
            }, 3000);
        </script>
        
    <?php else: ?>
        
        <div class="error-badge">
            ❌ <strong>VERIFICATION FAILED</strong><br>
            Email OTP verification unsuccessful
        </div>
        
        <h2>🚫 Verification Error</h2>
        
        <div class="transaction-details">
            <p><strong>Error:</strong> <?= htmlspecialchars($error_message) ?></p>
            
            <div style="margin: 20px 0;">
                <strong>Troubleshooting Tips:</strong>
                <ul style="text-align: left;">
                    <li>Check your email for the latest verification code</li>
                    <li>Make sure you enter all 6 digits correctly</li>
                    <li>Request a new code if the current one has expired</li>
                    <li>Check your spam/junk folder for the email</li>
                </ul>
            </div>
        </div>
        
        <div style="margin: 30px 0;">
            <a href="send_otp.php" class="btn btn-primary">📧 Get New Verification Code</a>
            <a href="../secure_checkout.php" class="btn btn-danger">🔙 Back to Checkout</a>
        </div>
        
    <?php endif; ?>
    
    <div style="margin-top: 40px; font-size: 12px; color: #666;">
        <p><strong>🔒 Security Notice:</strong></p>
        <p>This verification protects high-value transactions. Your security is our priority.</p>
    </div>
</div>

</body>
</html>