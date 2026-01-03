<?php
include 'db.php'; 
header('Content-Type: application/json');

$response = ['loggedIn' => false, 'name' => '', 'role' => ''];

if (isset($_SESSION['user_id'])) {
    $response['loggedIn'] = true;
    $response['name'] = $_SESSION['full_name'] ?? 'User';
    $response['role'] = $_SESSION['role'] ?? 'customer';
}

echo json_encode($response);
?>