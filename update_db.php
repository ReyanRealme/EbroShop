<?php
include 'db.php';

$target_email = 'ebrohayru77@gmail.com';

// 1. First, find the User ID
$getID = $conn->prepare("SELECT id FROM users WHERE email = ?");
$getID->bind_param("s", $target_email);
$getID->execute();
$result = $getID->get_result();
$user = $result->fetch_assoc();

echo "<div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #136835; border-radius: 10px; max-width: 500px; margin: 50px auto; text-align: center;'>";

if ($user) {
    $user_id = $user['id'];

    // 2. Delete the Child rows (orders) first
    $delOrders = $conn->prepare("DELETE FROM orders WHERE user_id = ?");
    $delOrders->bind_param("i", $user_id);
    $delOrders->execute();

    // 3. Now delete the Parent row (user)
    $delUser = $conn->prepare("DELETE FROM users WHERE id = ?");
    $delUser->bind_param("i", $user_id);
    
    if ($delUser->execute()) {
        echo "<h2 style='color: #136835;'>Success! / ተሳክቷል!</h2>";
        echo "<p>User and all related orders have been cleared.</p>";
    }
} else {
    echo "<h2 style='color: #e74c3c;'>User Not Found / ተጠቃሚው አልተገኘም</h2>";
}

echo "<br><a href='register.php' style='background: #136835; color: white; padding: 10px; text-decoration: none;'>Back to Register</a>";
echo "</div>";
?>