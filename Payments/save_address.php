<?php
session_start();
header('Content-Type: application/json');  // ✅ force JSON header

$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $_SESSION['address_name']    = $data['address_name'] ?? '';
    $_SESSION['address_street']  = $data['address_street'] ?? '';
    $_SESSION['address_city']    = $data['address_city'] ?? '';
    $_SESSION['address_state']   = $data['address_state'] ?? '';
    $_SESSION['address_zip']     = $data['address_zip'] ?? '';
    $_SESSION['address_country'] = $data['address_country'] ?? '';

    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false]);
}
