<?php
include 'db.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = (int)$_POST['product_id']; // Convert to number
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    // Safety: If ID is 0, don't do anything, just go back
    if ($product_id <= 0) {
        header("Location: Cart.php");
        exit();
    }

    // Check if product is already in this specific user's cart
    $check_result = $conn->query("SELECT id FROM cart WHERE user_id = '$user_id' AND product_id = '$product_id'");

    if ($check_result && $check_result->num_rows > 0) {
        // Update quantity
        $sql = "UPDATE cart SET quantity = quantity + $quantity WHERE user_id = '$user_id' AND product_id = '$product_id'";
    } else {
        // Insert new item
        $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES ('$user_id', '$product_id', '$quantity')";
    }

    if ($conn->query($sql)) {
        // IMPORTANT: Redirect to .php NOT .html
        header("Location: Cart.php"); 
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>