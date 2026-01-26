<?php
include 'db.php';

// 1. Add product_id column to order_items if it doesn't exist
$sql1 = "ALTER TABLE order_items ADD COLUMN IF NOT EXISTS product_id INT AFTER order_id";

// 2. Try to fill product_id by matching names from the products table
$sql2 = "UPDATE order_items oi 
         JOIN products p ON oi.product_name = p.name 
         SET oi.product_id = p.id 
         WHERE oi.product_id IS NULL OR oi.product_id = 0";

if ($conn->query($sql1)) {
    echo "Step 1 Success: Column 'product_id' added.<br>";
    if ($conn->query($sql2)) {
        echo "Step 2 Success: Existing items linked to Product IDs.<br>";
    }
} else {
    echo "Error: " . $conn->error;
}
?>