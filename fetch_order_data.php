<?php
include 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Get cart items for this user
$sql = "SELECT p.name, p.price, c.quantity, p.image_url 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = '$user_id'";

$result = $conn->query($sql);
$cart = [];
$total = 0;

while($row = $result->fetch_assoc()) {
    $cart[] = [
        'name' => $row['name'],
        'price' => (float)$row['price'],
        'qty' => (int)$row['quantity'],
        'image' => $row['image_url']
    ];
    $total += ($row['price'] * $row['quantity']);
}

echo json_encode(['cart' => $cart, 'total' => $total]);