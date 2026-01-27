<?php
include 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'not_logged_in']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Get User
$u_sql = "SELECT first_name, last_name, phone FROM users WHERE id = '$user_id'";
$u_res = $conn->query($u_sql);
$user = $u_res->fetch_assoc();

// Get Cart
$c_sql = "SELECT p.name, p.price, c.quantity as qty, p.image_url as image 
          FROM cart c JOIN products p ON c.product_id = p.id 
          WHERE c.user_id = '$user_id'";
$c_res = $conn->query($c_sql);
$cart = [];
while($row = $c_res->fetch_assoc()) { $cart[] = $row; }

echo json_encode([
    'full_name' => $user['first_name'] . " " . $user['last_name'],
    'phone' => $user['phone'],
    'cart' => $cart
]);