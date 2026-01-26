<?php
include 'db.php'; // Ensures session_start() and $conn are active

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
// Inside cart_handler.php
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if ($product_id <= 0) {
    die("Error: Invalid Product ID. Please go back and try again.");
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    // Check if product is already in this specific user's cart
    $check_sql = "SELECT id FROM cart WHERE user_id = '$user_id' AND product_id = '$product_id'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // Update quantity if it already exists
        $sql = "UPDATE cart SET quantity = quantity + $quantity WHERE user_id = '$user_id' AND product_id = '$product_id'";
    } else {
        // Insert new item if it doesn't exist
        $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES ('$user_id', '$product_id', '$quantity')";
    }

    if ($conn->query($sql)) {
        // SUCCESS: Redirect to the Cart page as requested
        header("Location: Cart.html");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}