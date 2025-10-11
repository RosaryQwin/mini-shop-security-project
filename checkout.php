<?php
session_start();
include_once "Payments/paypal/config.php";

// ==========================
// 1. Calculate Cart Total
// ==========================
$total = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['qty'];
    }
}

// ==========================
// 2. Handle PayPal Flow (Form POST)
// Save address + redirect to PayPal
// ==========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_with']) && $_POST['pay_with'] === 'paypal') {
    $_SESSION['address_name']    = $_POST['address_name'];
    $_SESSION['address_street']  = $_POST['address_street'];
    $_SESSION['address_city']    = $_POST['address_city'];
    $_SESSION['address_state']   = $_POST['address_state'];
    $_SESSION['address_zip']     = $_POST['address_zip'];
    $_SESSION['address_country'] = $_POST['address_country'];

    // Auto-submit PayPal Classic Checkout Form
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
  <title>Checkout</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f9fafc; padding: 20px; }
    .checkout-container { background: #fff; max-width: 700px; margin: auto; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    h2 { margin-bottom: 20px; }
    .checkout-form { display: grid; grid-template-columns: 1fr 1fr; gap: 12px 16px; margin-top: 15px; }
    .checkout-form label { font-weight: bold; margin-bottom: 5px; font-size: 0.9rem; }
    .checkout-form input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 0.95rem; }
    .checkout-form .full-width { grid-column: span 2; }
    /* Button Styles */
    .btn-paypal { background: #ffc439; color: #111; font-weight: bold; padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer; font-size: 1rem; width: 100%; margin-top: 20px; }
    .btn-paypal:hover { background: #ffb347; }
    .btn-stripe { background: #635BFF; color: #fff; font-weight: bold; padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer; font-size: 1rem; width: 100%; margin-top: 20px; }
    .btn-stripe:hover { background: #5145CC; }
    #gpay-button { margin-top: 20px; }
  </style>
</head>
<body>
  <div class="checkout-container">
    <h2>💳 Checkout</h2>

    <?php if (!empty($_SESSION['cart'])): ?>
      <p><b>Total:</b> <?= number_format($total, 2, '.', ',') ?> AUD</p>

      <!-- ==========================
           3. Checkout Form: Collect Address
      =========================== -->
      <form method="post" id="checkout-form">
        <div class="checkout-form">
          <div class="full-width">
            <label for="fullname">Full Name</label>
            <input type="text" id="fullname" name="address_name" required>
          </div>
          <div class="full-width">
            <label for="street">Street</label>
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

        <!-- PayPal Button -->
        <button type="submit" name="pay_with" value="paypal" class="btn-paypal">💳 Pay with PayPal</button>
      </form>

      <!-- ==========================
           4. Google Pay Button
      =========================== -->
      <input type="hidden" id="cart-total" value="<?= number_format($total, 2, '.', '') ?>">
      <div id="gpay-button"></div>

        <!-- Google Pay -->
      <script src="Payments/Googlepay/googlepay.js"></script>
      <script async src="https://pay.google.com/gp/p/js/pay.js" onload="onGooglePayLoaded()"></script>
  
      <!-- ==========================
           5. Stripe Checkout
           (Hidden form auto-fills address fields before submit)
      =========================== -->
      <form id="stripe-form" action="Payments/Stripe/checkout.php" method="POST">
        <input type="hidden" name="address_name" id="stripe_address_name">
        <input type="hidden" name="address_street" id="stripe_address_street">
        <input type="hidden" name="address_city" id="stripe_address_city">
        <input type="hidden" name="address_state" id="stripe_address_state">
        <input type="hidden" name="address_zip" id="stripe_address_zip">
        <input type="hidden" name="address_country" id="stripe_address_country">
        <button type="submit" class="btn-stripe">💳 Pay with Stripe</button>
      </form>

      <!-- ==========================
           6. Midtrans Snap Checkout
      =========================== -->
      <button type="button" id="pay-midtrans" class="btn-paypal" style="background:#ff5722; color:#fff;">
        🏦 Pay with Midtrans
      </button>

    <?php else: ?>
      <p>Your cart is empty. <a href="index.php">Go Shopping</a></p>
    <?php endif; ?>
  </div>

  <!-- ==========================
       Scripts
  =========================== -->

  <!-- Stripe: Copy address from form into hidden fields -->
  <script>
    document.getElementById("stripe-form").addEventListener("submit", function () {
      document.getElementById("stripe_address_name").value = document.getElementById("fullname").value;
      document.getElementById("stripe_address_street").value = document.getElementById("street").value;
      document.getElementById("stripe_address_city").value = document.getElementById("city").value;
      document.getElementById("stripe_address_state").value = document.getElementById("state").value;
      document.getElementById("stripe_address_zip").value = document.getElementById("zip").value;
      document.getElementById("stripe_address_country").value = document.getElementById("country").value;
    });
  </script>

  
  <!-- Midtrans Snap.js -->
  <script type="text/javascript"
    src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="Mid-client-TAKGIlPIVbnhZDzS">
  </script>

  <!-- Midtrans Checkout -->
  <script>
  document.getElementById('pay-midtrans').onclick = function () {
    fetch('Payments/midtrans/checkout.php', { method: 'POST' })
      .then(res => res.json())
      .then(data => {
        console.log("Midtrans response:", data); // debug
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
        } else {
          alert("Failed to get Snap Token: " + JSON.stringify(data));
        }
      });
  };
  </script>

</body>
</html>
