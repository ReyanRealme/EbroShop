<?php
include 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

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

$conn->begin_transaction();

try {
    // 1. Insert main order
    $stmt1 = $conn->prepare("INSERT INTO orders (user_id, full_name, phone, total_amount, payment_method) VALUES (?, ?, ?, ?, ?)");
    $stmt1->bind_param("issds", $user_id, $name, $phone, $total, $payment);
    $stmt1->execute();
    $order_id = $conn->insert_id; 

    // 2. Insert items into order_items
    $stmt2 = $conn->prepare("INSERT INTO order_items (order_id, product_name, price, quantity) VALUES (?, ?, ?, ?)");
    foreach ($cart as $item) {
        $stmt2->bind_param("isdi", $order_id, $item['name'], $item['price'], $item['qty']);
        $stmt2->execute();
    }

    // 3. THE EDIT IS HERE: Delete products from database cart for this user
    $stmt3 = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt3->bind_param("i", $user_id);
    $stmt3->execute();

    // 4. Commit everything to the database
    $conn->commit();

    // 5. Send ONE success message back to JavaScript
    echo json_encode(['success' => true, 'order_id' => $order_id]);

} catch (Exception $e) {
    // If anything fails, cancel everything
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>