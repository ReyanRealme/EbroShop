<?php
session_start();
header('Content-Type: application/json');

$response = ['loggedIn' => false, 'name' => ''];

if (isset($_SESSION['full_name'])) {
    $response['loggedIn'] = true;
    $response['name'] = $_SESSION['full_name'];
}

echo json_encode($response);
?>