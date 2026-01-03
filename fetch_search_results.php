<?php
// fetch_search_results.php
include 'db.php'; 

// 1. Tell the browser we are sending data
header('Content-Type: application/json');

// 2. Clear any accidental text from other files
ob_clean(); 

$query = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

if ($query !== '') {
    // 3. IMPORTANT: Make sure your table is called 'products' 
    // and columns are 'name', 'category', 'image', 'price', 'stock'
    $sql = "SELECT * FROM products 
            WHERE name LIKE '%$query%' 
            OR category LIKE '%$query%' 
            LIMIT 15";
            
    $result = $conn->query($sql);
    
    if (!$result) {
        // This will help us find the error in the Browser Console
        http_response_code(500);
        echo json_encode(["error" => $conn->error]);
        exit();
    }

    $products = [];
    while($row = $result->fetch_assoc()) {
        $products = $row;
    }
    echo json_encode($products);
} else {
    echo json_encode([]);
}
exit();
?>