<?php
include 'db.php'; // Your database connection

$query = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

if ($query !== '') {
    // Searches Name, Category, and Description all at once
    $sql = "SELECT id, name, price, image, stock, status FROM products 
            WHERE name LIKE '%$query%' 
            OR category LIKE '%$query%' 
            OR description LIKE '%$query%'
            LIMIT 20";
            
    $result = $conn->query($sql);
    $products = [];

    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    // Send the data back to the search page as JSON
    header('Content-Type: application/json');
    echo json_encode($products);
} else {
    echo json_encode([]);
}
?>