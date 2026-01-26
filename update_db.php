<?php
include 'db.php';

// The command to add the phone column
$sql = "ALTER TABLE users MODIFY COLUMN phone VARCHAR(50)";

if ($conn->query($sql) === TRUE) {
    echo "<h1 style='color:green;'>Success! Phone column added to your Aiven database.</h1>";
    echo "<p>You can now delete this file and use your new register page.</p>";
} else {
    echo "<h1 style='color:red;'>Error or Already Exists:</h1>";
    echo "<p>" . $conn->error . "</p>";
}
?>