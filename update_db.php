<?php
include 'db.php';

// The specific email you want to clear
$target_email = 'ebrohayru77@gmail.com';

// 1. Prepare a direct delete query
$sql = "DELETE FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $target_email);

echo "<div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #136835; border-radius: 10px; max-width: 500px; margin: 50px auto; text-align: center;'>";

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo "<h2 style='color: #136835;'>Task Successful! / ተግባሩ ተሳክቷል!</h2>";
        echo "<p>The email <b>$target_email</b> has been completely removed from the database.</p>";
        echo "<p style='color: #555;'>You can now go to the registration page and sign up as a new user.</p>";
        echo "<hr style='border: 0; border-top: 1px solid #eee;'>";
        echo "<p>አሁን ኢሜይሉ ስለተሰረዘ እንደ አዲስ መመዝገብ ይችላሉ።</p>";
    } else {
        echo "<h2 style='color: #e74c3c;'>Not Found / አልተገኘም</h2>";
        echo "<p>The email <b>$target_email</b> does not exist in your database records.</p>";
    }
} else {
    echo "<h2 style='color: #e74c3c;'>Error / ስህተት</h2>";
    echo "Something went wrong: " . $conn->error;
}

echo "<br><a href='register.php' style='display: inline-block; padding: 10px 20px; background: #136835; color: white; text-decoration: none; border-radius: 5px;'>Go to Registration</a>";
echo "</div>";

$stmt->close();
$conn->close();
?>