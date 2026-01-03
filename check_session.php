<?php
// check_session.php
include 'db.php'; // This ensures the 30-day persistent session is used
header('Content-Type: application/json');

$response = ['loggedIn' => false, 'name' => ''];

if (isset($_SESSION['user_id'])) {
    $response['loggedIn'] = true;
    $response['name'] = $_SESSION['full_name'] ?? 'User';
}

echo json_encode($response);
?>