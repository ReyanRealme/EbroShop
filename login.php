<?php
// 1. Load EVERYTHING (Session settings + Database) from db.php
// This MUST be the very first thing. No session code before this!
include 'db.php';

// 2. Process Login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass  = $_POST['password']; 

    // Search for the user
    $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Check if the password matches
        if (password_verify($pass, $user['password'])) {
            
            // Save data into the Session
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['role']      = $user['role'];
            $_SESSION['full_name'] = isset($user['first_name']) ? $user['first_name'] . " " . $user['last_name'] : $email;

            // Redirect based on role
            if ($user['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                // Tip: If you use home.php, change it here. 
                // Using .html sometimes causes session issues.
                header("Location: home.html"); 
            }
            exit();
        }
    }
    
    // Fail message
    echo "<script>alert('Invalid Email or Password'); window.history.back();</script>";
}
?>