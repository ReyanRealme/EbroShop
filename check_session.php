<?php
// Use your master database file to ensure the 30-day session is active
include 'db.php'; 

header('Content-Type: application/json');

$response = ['loggedIn' => false, 'name' => ''];

// Use the session variables set during login.php
if (isset($_SESSION['user_id'])) {
    $response['loggedIn'] = true;
    $response['name'] = $_SESSION['full_name'] ?? 'User';
}

echo json_encode($response);
?>