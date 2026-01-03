<?php

include 'db.php';

// --- 1. SECURITY LOCK (Re-enabled) ---
// We uncomment this so ONLY logged-in admins can access this file
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}



if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    // 1. HANDLE ADD NEW PRODUCT
    if ($action == 'add') {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $price = floatval($_POST['price']); // Ensure it's a number
        $image_url = mysqli_real_escape_string($conn, $_POST['image_url']);
        $status = $_POST['status'];
        $category = mysqli_real_escape_string($conn, $_POST['category']);

        $sql = "INSERT INTO products (name, price, image_url, status, category) 
                VALUES ('$name', '$price', '$image_url', '$status', '$category')";
        
        if ($conn->query($sql)) {
            header("Location: admin_dashboard.php?msg=Product Added Successfully");
            exit();
        } else {
            echo "Error adding product: " . $conn->error;
        }
    }

    // 2. HANDLE UPDATE EXISTING PRODUCT
    if ($action == 'update') {
        $id = intval($_POST['product_id']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $price = floatval($_POST['price']); // Ensure it's a number
        $image_url = mysqli_real_escape_string($conn, $_POST['image_url']);
        $status = $_POST['status'];
        $category = mysqli_real_escape_string($conn, $_POST['category']);

        $sql = "UPDATE products SET 
                name='$name', 
                price='$price', 
                image_url='$image_url', 
                status='$status', 
                category='$category' 
                WHERE id=$id";

        if ($conn->query($sql)) {
            header("Location: admin_dashboard.php?msg=Product Updated Successfully");
            exit();
        } else {
            echo "Error updating record: " . $conn->error;
        }
    }
}

$conn->close();
?>