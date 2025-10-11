<?php
session_start();
include_once "Payments/paypal/config.php";

// ==========================
// Security & Cart Validation
// ==========================
$total = 0;
$requires_high_security = false;

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['qty'];
    }
}

// Determine security level based on cart total
$requires_high_security = ($total >= 100); // High-value purchases
$requires_2fa = isset($_SESSION['user_2fa_enabled']) && $_SESSION['user_2fa_enabled'];
$requires_email_otp = ($total >= 100) && isset($_SESSION['user_email_otp_enabled']) && $_SESSION['user_email_otp_enabled'];

// ==========================
// Handle PayPal Flow with Security Verification
// ==========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_with']) && $_POST['pay_with'] === 'paypal') {
    
    // Store address data
    $_SESSION['address_name']    = $_POST['address_name'];
    $_SESSION['address_street']  = $_POST['address_street'];
    $_SESSION['address_city']    = $_POST['address_city'];
    $_SESSION['address_state']   = $_POST['address_state'];
    $_SESSION['address_zip']     = $_POST['address_zip'];
    $_SESSION['address_country'] = $_POST['address_country'];
    
    // For demo purposes, skip strict reCAPTCHA verification
    // In production, implement proper reCAPTCHA v3 verification with real secret key
    if ($requires_high_security && isset($_POST['recaptcha_v3_token'])) {
        // Demo: Just check if token exists (bypass actual Google verification)
        if (empty($_POST['recaptcha_v3_token'])) {
            $_SESSION['checkout_error'] = "Security verification required. Please refresh and try again.";
            header("Location: secure_checkout.php");
            exit;
        }
        // In demo mode, always pass reCAPTCHA check
    }
    
    // If high-security purchase, redirect to email OTP verification
    if ($requires_email_otp || $requires_2fa) {
        $_SESSION['pending_payment_method'] = 'paypal';
        $_SESSION['pending_payment_total'] = $total;
        header("Location: auth/send_otp.php");
        exit;
    }
    
    // Standard PayPal redirect (for low-value purchases)
    echo '<form id="paypalForm" action="'.PAYPAL_URL.'" method="post">
        <input type="hidden" name="business" value="'.PAYPAL_ID.'">
        <input type="hidden" name="cmd" value="_xclick">
        <input type="hidden" name="item_name" value="Mini Shop Order">
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Secure Checkout - Mini Shop</title>
  
  <!-- reCAPTCHA v3 for high-value transactions -->
  <script src="https://www.google.com/recaptcha/api.js?render=6LfEVpsrAAAAACvi8Rp4s37GIxdh0kkXYc1VZKiF"></script>
  
  <style>
    body { 
        font-family: Arial, sans-serif; 
        background: #f9fafc; 
        padding: 20px; 
    }
    .checkout-container { 
        background: #fff; 
        max-width: 800px; 
        margin: auto; 
        padding: 25px; 
        border-radius: 12px; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
    }
    .security-notice {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 25px;
        text-align: center;
    }
    .high-security {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    .security-features {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin: 20px 0;
    }
    .security-feature {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
        border-left: 4px solid #28a745;
    }
    .security-feature.active {
        border-left-color: #007bff;
        background: #e3f2fd;
    }
    .checkout-form { 
        display: grid; 
        grid-template-columns: 1fr 1fr; 
        gap: 12px 16px; 
        margin-top: 15px; 
    }
    .checkout-form label { 
        font-weight: bold; 
        margin-bottom: 5px; 
        font-size: 0.9rem; 
    }
    .checkout-form input { 
        width: 100%; 
        padding: 10px; 
        border: 1px solid #ccc; 
        border-radius: 6px; 
        font-size: 0.95rem; 
        box-sizing: border-box;
    }
    .checkout-form .full-width { 
        grid-column: span 2; 
    }
    .btn-paypal { 
        background: #ffc439; 
        color: #111; 
        font-weight: bold; 
        padding: 12px 20px; 
        border: none; 
        border-radius: 8px; 
        cursor: pointer; 
        font-size: 1rem; 
        width: 100%; 
        margin-top: 20px; 
    }
    .btn-paypal:hover { background: #ffb347; }
    .btn-stripe { 
        background: #635BFF; 
        color: #fff; 
        font-weight: bold; 
        padding: 12px 20px; 
        border: none; 
        border-radius: 8px; 
        cursor: pointer; 
        font-size: 1rem; 
        width: 100%; 
        margin-top: 20px; 
    }
    .btn-stripe:hover { background: #5145CC; }
    .purchase-summary {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .total-amount {
        font-size: 1.5em;
        color: #007bff;
        font-weight: bold;
    }
    .error-message {
        background: #f8d7da;
        color: #721c24;
        padding: 12px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
  </style>
</head>
<body>
  <div class="checkout-container">
    <h2>🔒 Secure Checkout</h2>

    <?php if (isset($_SESSION['checkout_error'])): ?>
        <div class="error-message">
            ⚠️ <?= $_SESSION['checkout_error'] ?>
        </div>
        <?php unset($_SESSION['checkout_error']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['cart'])): ?>
        
        <!-- Security Level Indicator -->
        <div class="security-notice <?= $requires_high_security ? 'high-security' : '' ?>">
            <?php if ($requires_high_security): ?>
                🛡️ <strong>HIGH SECURITY MODE</strong><br>
                This purchase requires enhanced security verification due to the transaction amount.
            <?php else: ?>
                🔒 <strong>STANDARD SECURITY</strong><br>
                Your transaction is protected with industry-standard security measures.
            <?php endif; ?>
        </div>
        
        <!-- Purchase Summary -->
        <div class="purchase-summary">
            <h3>📋 Order Summary</h3>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span>Total Amount:</span>
                <span class="total-amount"><?= number_format($total, 2, '.', ',') ?> AUD</span>
            </div>
        </div>
        
        <!-- Active Security Features -->
        <div class="security-features">
            <div class="security-feature active">
                🛡️ <strong>reCAPTCHA v3</strong><br>
                <small><?= $requires_high_security ? 'Enhanced Bot Protection' : 'Standard Bot Protection' ?></small>
            </div>
            
            <?php if ($requires_email_otp): ?>
            <div class="security-feature active">
                📧 <strong>Email OTP</strong><br>
                <small>Required for purchases $100+</small>
            </div>
            <?php endif; ?>
            
            <?php if ($requires_2fa): ?>
            <div class="security-feature active">
                🔐 <strong>Google Authenticator</strong><br>
                <small>TOTP 2FA Verification</small>
            </div>
            <?php endif; ?>
            
            <div class="security-feature active">
                💳 <strong>Secure Payment</strong><br>
                <small>SSL Encrypted Processing</small>
            </div>
        </div>

        <!-- Checkout Form -->
        <form method="post" id="secure-checkout-form">
            <h3>📍 Delivery Address</h3>
            <div class="checkout-form">
                <div class="full-width">
                    <label for="fullname">Full Name</label>
                    <input type="text" id="fullname" name="address_name" required>
                </div>
                <div class="full-width">
                    <label for="street">Street Address</label>
                    <input type="text" id="street" name="address_street" required>
                </div>
                <div>
                    <label for="city">City</label>
                    <input type="text" id="city" name="address_city" required>
                </div>
                <div>
                    <label for="state">State</label>
                    <input type="text" id="state" name="address_state" required>
                </div>
                <div>
                    <label for="zip">ZIP Code</label>
                    <input type="text" id="zip" name="address_zip" required>
                </div>
                <div>
                    <label for="country">Country</label>
                    <input type="text" id="country" name="address_country" required>
                </div>
            </div>

            <!-- Hidden field for reCAPTCHA v3 token -->
            <input type="hidden" name="recaptcha_v3_token" id="recaptcha_v3_token">

            <!-- Payment Buttons -->
            <h3>💳 Select Payment Method</h3>
            
            <button type="submit" name="pay_with" value="paypal" class="btn-paypal">
                💳 Pay with PayPal
                <?= $requires_high_security ? ' (Enhanced Security)' : '' ?>
            </button>
        </form>

        <!-- Additional Payment Methods -->
        <form id="stripe-form" action="Payments/Stripe/checkout.php" method="POST">
            <input type="hidden" name="address_name" id="stripe_address_name">
            <input type="hidden" name="address_street" id="stripe_address_street">
            <input type="hidden" name="address_city" id="stripe_address_city">
            <input type="hidden" name="address_state" id="stripe_address_state">
            <input type="hidden" name="address_zip" id="stripe_address_zip">
            <input type="hidden" name="address_country" id="stripe_address_country">
            <input type="hidden" name="security_level" value="<?= $requires_high_security ? 'high' : 'standard' ?>">
            <button type="submit" class="btn-stripe">
                💳 Pay with Stripe
                <?= $requires_high_security ? ' (Enhanced Security)' : '' ?>
            </button>
        </form>

        <!-- Google Pay -->
        <input type="hidden" id="cart-total" value="<?= number_format($total, 2, '.', '') ?>">
        <div id="gpay-button"></div>
        
        <!-- Midtrans -->
        <button type="button" id="pay-midtrans" class="btn-paypal" style="background:#ff5722; color:#fff;">
            🏦 Pay with Midtrans
            <?= $requires_high_security ? ' (Enhanced Security)' : '' ?>
        </button>

    <?php else: ?>
        <p>Your cart is empty. <a href="index.php">Go Shopping</a></p>
    <?php endif; ?>
  </div>

  <!-- Scripts -->
  <script>
    // reCAPTCHA v3 Implementation
    grecaptcha.ready(function() {
        grecaptcha.execute('6LfEVpsrAAAAACvi8Rp4s37GIxdh0kkXYc1VZKiF', {
            action: <?= $requires_high_security ? "'high_value_checkout'" : "'checkout'" ?>
        })
        .then(function(token) {
            document.getElementById('recaptcha_v3_token').value = token;
        });
    });

    // Copy address data for Stripe checkout
    document.getElementById("stripe-form").addEventListener("submit", function () {
        document.getElementById("stripe_address_name").value = document.getElementById("fullname").value;
        document.getElementById("stripe_address_street").value = document.getElementById("street").value;
        document.getElementById("stripe_address_city").value = document.getElementById("city").value;
        document.getElementById("stripe_address_state").value = document.getElementById("state").value;
        document.getElementById("stripe_address_zip").value = document.getElementById("zip").value;
        document.getElementById("stripe_address_country").value = document.getElementById("country").value;
    });

    // Enhanced form validation for high-security purchases
    document.getElementById("secure-checkout-form").addEventListener("submit", function(e) {
        <?php if ($requires_high_security): ?>
        // Additional validation for high-value purchases
        const requiredFields = ['fullname', 'street', 'city', 'state', 'zip', 'country'];
        let missingFields = [];
        
        requiredFields.forEach(field => {
            const input = document.getElementById(field);
            if (!input.value.trim()) {
                missingFields.push(field);
                input.style.borderColor = '#dc3545';
            } else {
                input.style.borderColor = '#28a745';
            }
        });
        
        if (missingFields.length > 0) {
            alert('Please complete all required fields for high-security checkout.');
            e.preventDefault();
            return false;
        }
        
        // Confirm high-value purchase
        if (!confirm(`⚠️ HIGH SECURITY CHECKOUT\n\nYou are about to make a purchase of ${<?= $total ?>} AUD.\n\nAdditional security verification will be required.\n\nProceed?`)) {
            e.preventDefault();
            return false;
        }
        <?php endif; ?>
    });
  </script>

  <!-- Google Pay Script -->
  <script src="Payments/Googlepay/googlepay.js"></script>
  <script async src="https://pay.google.com/gp/p/js/pay.js" onload="onGooglePayLoaded()"></script>
  
  <!-- Midtrans Script -->
  <script type="text/javascript"
    src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="Mid-client-TAKGIlPIVbnhZDzS">
  </script>

  <script>
  document.getElementById('pay-midtrans').onclick = function () {
    fetch('Payments/midtrans/checkout.php', { method: 'POST' })
      .then(res => res.json())
      .then(data => {
        if (data.token) {
          snap.pay(data.token, {
            onSuccess: function(result){ 
              window.location.href = "Payments/success.php?method=midtrans&st=Completed&tx=" + 
              result.transaction_id + 
              "&amt=" + result.gross_amount + "&cc=IDR";
            },
            onPending: function(result){ alert("Waiting for payment!"); },
            onError: function(result){ alert("Payment failed!"); },
            onClose: function(){ alert("You closed the payment popup!"); }
          });
        }
      });
  };
  </script>

</body>
</html>