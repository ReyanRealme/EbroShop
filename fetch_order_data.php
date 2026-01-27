<?php
include 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'not_logged_in']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user profile details
$user_sql = "SELECT first_name, last_name, phone, email FROM users WHERE id = '$user_id'";
$u_res = $conn->query($user_sql);
$user = $u_res->fetch_assoc();

$full_name = $user['first_name'] . " " . $user['last_name'];

// Get cart items
$cart_sql = "SELECT p.name, p.price, c.quantity as qty, p.image_url as image 
             FROM cart c JOIN products p ON c.product_id = p.id 
             WHERE c.user_id = '$user_id'";
$c_res = $conn->query($cart_sql);

$cart = [];
while($row = $c_res->fetch_assoc()) {
    $cart[] = $row;
}

echo json_encode([
    'full_name' => $full_name,
    'phone'     => $user['phone'],
    'email'     => $user['email'],
    'cart'      => $cart
]);