<?php
session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mini Shop</title>
  <!-- Link to external CSS -->
  <link rel="stylesheet" href="css/style.css">
  <style>
    .header-auth {
      position: absolute;
      right: 20px;
      top: 50%;
      transform: translateY(-50%);
    }
    .btn-sm {
      padding: 8px 12px;
      font-size: 0.9rem;
      margin-left: 5px;
    }
    .welcome-user {
      color: #fff;
      margin-right: 10px;
      font-weight: bold;
    }
    .security-badge {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 15px;
      border-radius: 8px;
      margin: 20px auto;
      max-width: 1100px;
      text-align: center;
    }
    .security-features {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-top: 10px;
      flex-wrap: wrap;
    }
    .security-feature {
      background: rgba(255,255,255,0.2);
      padding: 8px 12px;
      border-radius: 15px;
      font-size: 0.9rem;
    }
    .guest-notice {
      background: #fff3cd;
      color: #856404;
      padding: 15px;
      border-radius: 8px;
      margin: 20px auto;
      max-width: 1100px;
      text-align: center;
      border: 1px solid #ffeaa7;
    }
  </style>
</head>
<body>
  <header>
    🛍️ Mini Shop
    <div class="header-auth">
      <?php if (isset($_SESSION['user_registered']) && $_SESSION['user_registered']): ?>
        <span class="welcome-user">👋 Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>!</span>
        <a href="auth/logout.php" class="btn btn-sm">Logout</a>
      <?php else: ?>
        <a href="auth/register.php" class="btn btn-sm">🔒 Create Secure Account</a>
        <a href="auth/login.php" class="btn btn-sm" style="background:#28a745;">Login</a>
      <?php endif; ?>
    </div>
  </header>

  <?php if (isset($_SESSION['user_registered']) && $_SESSION['user_registered']): ?>
    <!-- Security Status for Registered Users -->
    <div class="security-badge">
      🛡️ <strong>Secure Shopping Mode Active</strong>
      <div class="security-features">
        <?php if ($_SESSION['user_2fa_enabled']): ?>
          <span class="security-feature">🔐 2FA Enabled</span>
        <?php endif; ?>
        <?php if ($_SESSION['user_email_otp_enabled']): ?>
          <span class="security-feature">📧 Email OTP Active</span>
        <?php endif; ?>
        <span class="security-feature">🛡️ reCAPTCHA Protected</span>
        <span class="security-feature">💳 Secure Payments</span>
      </div>
    </div>
  <?php else: ?>
    <!-- Guest Shopping Notice -->
    <div class="guest-notice">
      👤 <strong>Shopping as Guest</strong> - <a href="auth/register.php">Create a secure account</a> for enhanced security features like 2FA and email OTP verification!
    </div>
  <?php endif; ?>

  <div class="container">
    <!-- Item 1 -->
    <div class="card">
      <img src="images/Kari_Ayam_tabona.png" alt="Kari Ayam Tabona Medan">
      <div class="card-body">
        <h3>Kari Ayam Tabona Medan</h3>
        <p class="price">20 AUD</p>
        <a href="add_to_cart.php?product_id=1" class="btn">Add to Cart</a>
      </div>
    </div>
    <!-- Item 2 -->
    <div class="card">
      <img src="images/kwetiau_ateng.jpg" alt="Kwetiau Ateng Medan">
      <div class="card-body">
        <h3>Kwetiau Ateng Medan</h3>
        <p class="price">18 AUD</p>
        <a href="add_to_cart.php?product_id=2" class="btn">Add to Cart</a>
      </div>
    </div>
    <!-- Item 3 -->
    <div class="card">
      <img src="images/kerak_telor.jpg" alt="Kerak Telor Jakarta">
      <div class="card-body">
        <h3>Kerak Telor Jakarta</h3>
        <p class="price">13 AUD</p>
        <a href="add_to_cart.php?product_id=3" class="btn">Add to Cart</a>
      </div>
    </div>
  </div>

  <!-- Cart Preview -->
  <div class="cart-preview">
    <h2>🛒 What’s in Your Cart</h2>
    <?php if (!empty($_SESSION['cart'])): ?>
      <?php $total = 0; ?>
      <?php foreach ($_SESSION['cart'] as $item): 
        $subtotal = $item['price'] * $item['qty'];
        $total += $subtotal;
      ?>
        <div class="cart-item">
          <div style="display:flex; align-items:center;">
            <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
            <span><?= htmlspecialchars($item['name']) ?> (x<?= $item['qty'] ?>)</span>
          </div>
          <span><?= number_format($subtotal, 0, '.', ',') ?> AUD</span>
          
        </div>
      <?php endforeach; ?>
      <div class="cart-actions">
        <strong>Total: <?= number_format($total, 0, '.', ',') ?> AUD </strong><br>
        <a href="cart.php" class="btn">View Full Cart</a>
        <?php if (isset($_SESSION['user_registered']) && $_SESSION['user_registered']): ?>
          <a href="secure_checkout.php" class="btn" style="background:#28a745;">🔒 Secure Checkout</a>
        <?php else: ?>
          <a href="checkout.php" class="btn" style="background:#ffc107; color:#000;">Guest Checkout</a>
        <?php endif; ?>
        <a href="reset.php" class="btn" style="background:#ff4d4d;">Clear Cart</a>
      </div>
    <?php else: ?>
      <p>Your cart is empty.</p>
    <?php endif; ?>
  </div>

</body>
</html>
