<?php
session_start();

// Check if user is logged in and has email OTP enabled
if (!isset($_SESSION['user_registered']) || !$_SESSION['user_email_otp_enabled']) {
    header("Location: ../index.php");
    exit;
}

// Check if this is triggered by high-value purchase
if (!isset($_SESSION['pending_payment_total']) || $_SESSION['pending_payment_total'] < 100) {
    header("Location: ../checkout.php");
    exit;
}

require_once '../../../as1/Q3/PHPMailer/src/PHPMailer.php';
require_once '../../../as1/Q3/PHPMailer/src/SMTP.php';
require_once '../../../as1/Q3/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = $_SESSION['user_email'];
$total = $_SESSION['pending_payment_total'];

// Generate 6-digit OTP
$otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
$_SESSION['purchase_otp'] = $otp;
$_SESSION['otp_generated_time'] = time(); // For expiry check

$mail = new PHPMailer(true);
$email_sent = false;
$error_message = "";

try {
    // Gmail SMTP settings (using your existing config from AS1/Q3)
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'rosarysus@gmail.com'; // Your existing Gmail
    $mail->Password = 'eljwpewgxgeyyfyx'; // Your existing App Password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('rosarysus@gmail.com', 'Mini Shop Security');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = '🔐 High-Value Purchase Verification - Mini Shop';
    $mail->Body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
        <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center;'>
            <h1>🛡️ Security Verification Required</h1>
        </div>
        
        <div style='padding: 30px; background: #f9f9f9;'>
            <h2>High-Value Purchase Detected</h2>
            <p>We've detected a purchase of <strong>" . number_format($total, 2) . " AUD</strong> on your account.</p>
            
            <div style='background: #fff; padding: 20px; border-radius: 8px; border-left: 4px solid #007bff; margin: 20px 0;'>
                <h3>Your Verification Code:</h3>
                <div style='font-size: 32px; font-weight: bold; color: #007bff; text-align: center; letter-spacing: 5px; font-family: monospace;'>
                    $otp
                </div>
            </div>
            
            <p><strong>⏰ This code expires in 10 minutes.</strong></p>
            <p><strong>🔒 For your security:</strong></p>
            <ul>
                <li>Never share this code with anyone</li>
                <li>Only enter this code on the Mini Shop website</li>
                <li>Contact us if you didn't request this verification</li>
            </ul>
            
            <div style='margin-top: 30px; padding: 15px; background: #e3f2fd; border-radius: 8px;'>
                <strong>Transaction Details:</strong><br>
                Amount: " . number_format($total, 2) . " AUD<br>
                Time: " . date('Y-m-d H:i:s') . "<br>
                Email: $email
            </div>
        </div>
        
        <div style='background: #333; color: #ccc; padding: 15px; text-align: center; font-size: 12px;'>
            This is an automated security message from Mini Shop.<br>
            If you didn't initiate this transaction, please contact support immediately.
        </div>
    </div>";

    // Send the email
    $mail->send();
    $email_sent = true;

} catch (Exception $e) {
    $error_message = "Email could not be sent. Error: " . $mail->ErrorInfo;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - Mini Shop</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .otp-container {
            max-width: 500px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
        }
        .security-notice {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .success-notice {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #c3e6cb;
        }
        .error-notice {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #f5c6cb;
        }
        .otp-form {
            margin: 30px 0;
        }
        .otp-input {
            font-size: 24px;
            text-align: center;
            letter-spacing: 5px;
            font-family: monospace;
            padding: 15px;
            width: 200px;
            border: 2px solid #007bff;
            border-radius: 8px;
            margin: 10px;
        }
        .btn-verify {
            background: #28a745;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin: 10px;
        }
        .btn-verify:hover { background: #218838; }
        .btn-resend {
            background: #ffc107;
            color: #000;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin: 10px;
        }
        .transaction-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>

<header>
    🛍️ Mini Shop - Security Verification
    <a href="../index.php">← Back to Shop</a>
</header>

<div class="otp-container">
    <div class="security-notice">
        🛡️ <strong>HIGH SECURITY VERIFICATION</strong><br>
        Email OTP required for purchases over $100 AUD
    </div>
    
    <div class="transaction-summary">
        <h3>📋 Transaction Summary</h3>
        <div style="display: flex; justify-content: space-between;">
            <span><strong>Amount:</strong></span>
            <span style="font-size: 1.2em; color: #007bff;"><strong><?= number_format($total, 2) ?> AUD</strong></span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-top: 10px;">
            <span><strong>Payment Method:</strong></span>
            <span><?= ucfirst($_SESSION['pending_payment_method'] ?? 'Unknown') ?></span>
        </div>
    </div>
    
    <?php if ($email_sent): ?>
        <div class="success-notice">
            ✅ <strong>Verification Code Sent!</strong><br>
            Check your email (<strong><?= htmlspecialchars($email) ?></strong>) for the 6-digit verification code.
        </div>
        
        <form action="verify_purchase_otp.php" method="post" class="otp-form">
            <h3>📧 Enter Verification Code</h3>
            <input type="text" 
                   name="otp_code" 
                   class="otp-input" 
                   placeholder="000000" 
                   maxlength="6" 
                   pattern="[0-9]{6}" 
                   required 
                   autofocus>
            <br>
            <button type="submit" class="btn-verify">🔐 Verify & Complete Purchase</button>
        </form>
        
        <div style="color: #666; font-size: 14px; margin: 20px 0;">
            ⏰ Code expires in 10 minutes<br>
            🔒 For security, never share this code with anyone
        </div>
        
        <form action="send_otp.php" method="post" style="display: inline;">
            <button type="submit" class="btn-resend">📧 Resend Code</button>
        </form>
        
    <?php else: ?>
        <div class="error-notice">
            ❌ <strong>Email Sending Failed</strong><br>
            <?= $error_message ?>
        </div>
        
        <a href="send_otp.php" class="btn-verify">🔄 Try Again</a>
    <?php endif; ?>
    
    <div style="margin-top: 30px;">
        <a href="../index.php">🛍️ Continue Shopping</a> | 
        <a href="../cart.php">🛒 View Cart</a>
    </div>
</div>

<script>
// Auto-focus and formatting for OTP input
document.querySelector('.otp-input').addEventListener('input', function(e) {
    // Only allow numbers
    e.target.value = e.target.value.replace(/[^0-9]/g, '');
    
    // Auto-submit when 6 digits entered
    if (e.target.value.length === 6) {
        // Optional: auto-submit after short delay
        setTimeout(() => {
            if (confirm('Submit verification code: ' + e.target.value + '?')) {
                e.target.form.submit();
            }
        }, 500);
    }
});

// Countdown timer for code expiry
let timeLeft = 600; // 10 minutes
const timer = setInterval(() => {
    timeLeft--;
    if (timeLeft <= 0) {
        clearInterval(timer);
        alert('⚠️ Verification code has expired. Please request a new code.');
        window.location.reload();
    }
}, 1000);
</script>

</body>
</html>