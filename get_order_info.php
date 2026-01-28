<?php
include 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]); exit();
}

$user_id = $_SESSION['user_id'];

// Get User Info
$u = $conn->query("SELECT first_name, last_name, phone FROM users WHERE id = $user_id")->fetch_assoc();

// Get Database Cart Items
$c_res = $conn->query("SELECT p.name, p.price, p.image_url as image, c.quantity as qty 
                       FROM cart c JOIN products p ON c.product_id = p.id 
                       WHERE c.user_id = $user_id");

$cart = [];
while($row = $c_res->fetch_assoc()) { $cart[] = $row; }

echo json_encode([
    'success' => true,
    'user' => ['name' => $u['first_name'].' '.$u['last_name'], 'phone' => $u['phone']],
    'cart' => $cart
]);