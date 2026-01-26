<?php
include 'db.php';

// SQL to create the cart table
$sql = "CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "<h2 style='color:green;'>Success! Cart table created successfully.</h2>";
    echo "<p>You can now use the 'Buy Again' button.</p>";
} else {
    echo "<h2 style='color:red;'>Error creating table:</h2> " . $conn->error;
}

// Close connection
$conn->close();
?>