<?php
session_start();
$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Cart</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f9fafc; }
    h2 { margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; background: #fff; }
    table, th, td { border: 1px solid #ddd; }
    th, td { padding: 12px; text-align: center; vertical-align: middle; }
    th { background: #333; color: #fff; }
    img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; }
    .total { font-weight: bold; font-size: 1.2rem; text-align: right; }
    .btn { 
      background: #007BFF; 
      color: #fff; 
      padding: 10px 16px; 
      text-decoration: none; 
      border-radius: 8px; 
      display: inline-block;
      margin: 5px 0;
    }
    .btn:hover { background: #0056b3; }
    .btn-danger {
      background: #ff4d4d;
    }
    .btn-danger:hover {
      background: #cc0000;
    }
  </style>
</head>
<body>
  <h2>🛒 Your Shopping Cart</h2>

  <?php if (!empty($_SESSION['cart'])): ?>
    <table>
      <tr>
        <th>Image</th>
        <th>Product</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Subtotal</th>
      </tr>
      <?php foreach ($_SESSION['cart'] as $id => $item): 
          $subtotal = $item['price'] * $item['qty'];
          $total += $subtotal;
      ?>
        <tr>
          <td><img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>"></td>
          <td><?= htmlspecialchars($item['name']) ?></td>
          <td><?= number_format($item['price'], 0, '.', ',') ?> AUD</td>
          <td><?= $item['qty'] ?></td>
          <td><?= number_format($subtotal, 0, '.', ',') ?> AUD</td>
        </tr>
      <?php endforeach; ?>
      <tr>
        <td colspan="4" class="total">Total</td>
        <td class="total"><?= number_format($total, 0, '.', ',') ?> AUD </td>
      </tr>
    </table>

    <a href="checkout.php" class="btn">Proceed to Checkout</a>
    <a href="reset.php" class="btn btn-danger">Clear Cart</a>

  <?php else: ?>
    <p>Your cart is empty. <a href="index.php">Go Shopping</a></p>
  <?php endif; ?>
</body>
</html>
