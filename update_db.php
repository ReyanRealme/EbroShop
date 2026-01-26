<?php
include 'db.php';

// 1. Check if the column already exists
$checkColumn = $conn->query("SHOW COLUMNS FROM `order_items` LIKE 'product_id'");
$exists = ($checkColumn->num_rows > 0);

if (!$exists) {
    // 2. Add the column if it is missing
    $sql1 = "ALTER TABLE order_items ADD product_id INT AFTER order_id";
    if ($conn->query($sql1)) {
        echo "Step 1 Success: Column 'product_id' added.<br>";
    } else {
        echo "Step 1 Error: " . $conn->error . "<br>";
    }
} else {
    echo "Step 1: Column 'product_id' already exists. Skipping...<br>";
}

// 3. Link existing order names to product IDs
$sql2 = "UPDATE order_items oi 
         JOIN products p ON oi.product_name = p.name 
         SET oi.product_id = p.id 
         WHERE oi.product_id IS NULL OR oi.product_id = 0";

if ($conn->query($sql2)) {
    echo "Step 2 Success: Existing orders linked to Product IDs.<br>";
} else {
    echo "Step 2 Error: " . $conn->error;
}
?>