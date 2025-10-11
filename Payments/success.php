<?php
session_start();
require_once __DIR__ . "/paypal/config.php";

// 1. Detect payment method early
$method   = $_GET['method'] ?? 'paypal';   // default PayPal
$status   = $_GET['st'] ?? 'Completed';    // PayPal & GPay simulate "Completed"
$amount   = $_GET['amt'] ?? '';
$currency = $_GET['cc'] ?? PAYPAL_CURRENCY;
$txn_id   = $_GET['tx'] ?? ($_GET['session_id'] ?? ''); // PayPal tx or Stripe session ID

// 2. If Stripe, override with real values
if ($method === 'stripe' && !empty($_GET['session_id'])) {
    require_once __DIR__ . "/Stripe/config.php";
    require_once __DIR__ . "/../stripe-php-17.6.0/init.php"; // adjust if needed

    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    try {
        $session = \Stripe\Checkout\Session::retrieve($_GET['session_id']);
        $payment_intent = \Stripe\PaymentIntent::retrieve($session->payment_intent);

        $txn_id   = $payment_intent->id;
        $amount   = $session->amount_total / 100; // Stripe returns cents
        $currency = strtoupper($session->currency);
        $status   = $payment_intent->status; // e.g. "succeeded"
    } catch (Exception $e) {
        $status = "Failed";
    }
}

// 3. Address from session
$address_name    = $_SESSION['address_name'] ?? '';
$address_street  = $_SESSION['address_street'] ?? '';
$address_city    = $_SESSION['address_city'] ?? '';
$address_state   = $_SESSION['address_state'] ?? '';
$address_zip     = $_SESSION['address_zip'] ?? '';
$address_country = $_SESSION['address_country'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment Status</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f9fafc; padding: 20px; }
    .container { max-width: 650px; margin: auto; background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    h1 { margin-bottom: 15px; }
    h1.success { color: green; }
    h1.failed { color: red; }
    .address-box {
      border: 1px solid #ddd;
      padding: 12px;
      border-radius: 8px;
      margin-top: 10px;
      background: #fafafa;
    }
    .back-link { display: inline-block; margin-top: 20px; text-decoration: none; color: #007BFF; font-weight: bold; }
  </style>
</head>
<body>
  <div class="container">
    <?php if ($status === "Completed" || $status === "succeeded"): ?>
      <h1 class="success">✅ Payment Successful</h1>
      <p><b>Payment Method:</b> 
        <?php 
          if ($method === "gpay") echo "Google Pay";
          elseif ($method === "stripe") echo "Stripe";
          elseif ($method === "midtrans") echo "Midtrans";
          else echo "PayPal"; 
        ?>
      </p>
      <p><b>Transaction ID:</b> <?= htmlspecialchars($txn_id) ?></p>
      <p><b>Amount:</b> <?= htmlspecialchars($amount) ?> <?= htmlspecialchars(strtoupper($currency)) ?></p>
      <p><b>Status:</b> <?= htmlspecialchars($status) ?></p>

      <?php if (!empty($address_name)): ?>
        <h2>📦 Shipping Address</h2>
        <div class="address-box">
          <p><?= htmlspecialchars($address_name) ?></p>
          <p><?= htmlspecialchars($address_street) ?></p>
          <p><?= htmlspecialchars($address_city) ?>, <?= htmlspecialchars($address_state) ?> <?= htmlspecialchars($address_zip) ?></p>
          <p><?= htmlspecialchars($address_country) ?></p>
        </div>
      <?php endif; ?>


    <?php else: ?>
      <h1 class="failed">❌ Payment Failed</h1>
      <p>Something went wrong. Please try again or contact support.</p>
    <?php endif; ?>

    <a href="../index.php" class="back-link">⬅ Back to Shop</a>
  </div>
</body>
</html>
