<?php
session_start();

// Example product list (id, name, price, image)
$products = [
    1 => ["name" => "Kari Ayam Tabona Medan", "price" => 20, "image" => "images/Kari_Ayam_tabona.png"],
    2 => ["name" => "Kwetiau Ateng Medan", "price" => 18, "image" => "images/kwetiau_ateng.jpg"],
    3 => ["name" => "Kerak Telor Jakarta", "price" => 13, "image" => "images/kerak_telor.jpg"]
];

// Get product_id from URL
if (isset($_GET['product_id'])) {
    $id = (int)$_GET['product_id'];

    if (isset($products[$id])) {
        $product = $products[$id];

        // If item already in cart, increase qty
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty'] += 1;
        } else {
            $_SESSION['cart'][$id] = [
                "name" => $product['name'],
                "price" => $product['price'],
                "image" => $product['image'],
                "qty" => 1
            ];
        }
    }
}

// Redirect to cart page
$redirect = $_SERVER['HTTP_REFERER'] ?? 'index.php';
header("Location: $redirect");
exit();

