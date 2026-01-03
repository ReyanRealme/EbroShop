<?php
include 'db.php';

// This line is VERY important - it tells the browser we are sending data, not a webpage
header('Content-Type: application/json');

$query = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

if ($query !== '') {
    $sql = "SELECT id, name, price, image, stock, status FROM products 
            WHERE name LIKE '%$query%' 
            OR category LIKE '%$query%' 
            LIMIT 20";
            
    $result = $conn->query($sql);
    $products = [];

    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    echo json_encode($products);
} else {
    echo json_encode([]);
}
?>