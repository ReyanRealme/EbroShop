<?php
// fetch_search_results.php
include 'db.php';

$query = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

if ($query !== '') {
    // This searches ALL categories and ALL products at once
    $sql = "SELECT id, name, price, image, stock, status FROM products 
            WHERE name LIKE '%$query%' 
            OR category LIKE '%$query%' 
            OR description LIKE '%$query%'
            LIMIT 15";
            
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