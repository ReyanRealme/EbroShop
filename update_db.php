<?php
include 'db.php';

// 1. Check if the 'agreed_to_terms' column already exists in the users table
$checkColumn = $conn->query("SHOW COLUMNS FROM `users` LIKE 'agreed_to_terms'");
$exists = ($checkColumn->num_rows > 0);

if (!$exists) {
    // 2. Add the column if it is missing
    // We use TINYINT(1) with DEFAULT 0 (meaning not agreed yet)
    $sql = "ALTER TABLE users ADD agreed_to_terms TINYINT(1) NOT NULL DEFAULT 0";
    
    if ($conn->query($sql)) {
        echo "Success: Column 'agreed_to_terms' added to users table.<br>";
    } else {
        echo "Error adding column: " . $conn->error . "<br>";
    }
} else {
    echo "Notice: Column 'agreed_to_terms' already exists. No changes made.<br>";
}

// 3. Optional: Set existing users to 1 if you want to assume they already agreed
// $conn->query("UPDATE users SET agreed_to_terms = 1");

?>