<?php
session_start();
header('Content-Type: application/json');

// 1. Security Check: Must be logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

include 'db.php';

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// 2. Get the data from the JavaScript Fetch request
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit();
}

$user_id = $_SESSION['user_id'];
$name    = mysqli_real_escape_string($conn, $data['name']);
$phone   = mysqli_real_escape_string($conn, $data['phone']);
$payment = mysqli_real_escape_string($conn, $data['payment']);
$total   = $data['total'];
$cart    = $data['cart'];

// 3. Start Transaction (Ensures both tables save correctly or neither does)
$conn->begin_transaction();

try {
    // Insert into 'orders' table
    $stmt1 = $conn->prepare("INSERT INTO orders (user_id, full_name, phone, total_amount, payment_method) VALUES (?, ?, ?, ?, ?)");
    $stmt1->bind_param("issds", $user_id, $name, $phone, $total, $payment);
    $stmt1->execute();
    
    $order_id = $conn->insert_id; // Get the ID of the order we just created

    // Insert each item into 'order_items' table
    $stmt2 = $conn->prepare("INSERT INTO order_items (order_id, product_name, price, quantity) VALUES (?, ?, ?, ?)");
    
    foreach ($cart as $item) {
        $stmt2->bind_param("isdi", $order_id, $item['name'], $item['price'], $item['qty']);
        $stmt2->execute();
    }

    $conn->commit();
    echo json_encode(['success' => true, 'order_id' => $order_id]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>