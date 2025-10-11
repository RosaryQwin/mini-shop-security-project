<?php
session_start();
require_once __DIR__ . "/config.php";

// calculate total
$total = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['qty'];
    }
}

// Convert AUD to IDR (1 AUD = 10,500 IDR)
$total_idr = (int)($total * 10500);

// Midtrans API request params
$params = [
    'transaction_details' => [
        'order_id' => 'ORDER-' . uniqid(),
        'gross_amount' => $total_idr  // Amount in IDR (converted from AUD)
    ],
    'customer_details' => [
        'first_name' => $_SESSION['address_name'] ?? 'Guest',
        'last_name' => '',
        'email' => 'test@example.com',
        'phone' => '+62812345678'
    ],
    'item_details' => [[
        'id' => 'item1',
        'price' => $total_idr,
        'quantity' => 1,
        'name' => 'Mini Shop Order (AUD ' . number_format($total, 2) . ')'
    ]]
];

// Call Midtrans Snap API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://app.sandbox.midtrans.com/snap/v1/transactions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Accept: application/json",
    "Authorization: Basic " . base64_encode(MIDTRANS_SERVER_KEY . ":")
]);

$response = curl_exec($ch);
if ($response === false) {
    echo json_encode(["error" => curl_error($ch)]);
} else {
    echo $response; // should contain {"token": "...", "redirect_url": "..."}
}
curl_close($ch);
