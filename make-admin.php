<?php
include 'db.php';

// REPLACE THIS with the email you want to make Admin
$email = 'rebyidejene8949@gmail.com'; 

$sql = "UPDATE users SET role = 'admin' WHERE email = '$email'";

if ($conn->query($sql) === TRUE) {
    echo "Successfully updated $email to Admin!";
} else {
    echo "Error: " . $conn->error;
}
?>