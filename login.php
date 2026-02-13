<?php
include 'db.php';

// 1. SAVE THE PREVIOUS PAGE (Run this before any POST logic)
if (!isset($_SESSION['redirect_to']) && isset($_SERVER['HTTP_REFERER'])) {
    $ref = $_SERVER['HTTP_REFERER'];
    // Only save if the referrer is NOT the login page or process script
    if (strpos($ref, 'login.html') === false && strpos($ref, 'login.php') === false) {
        $_SESSION['redirect_to'] = $ref;
    }
}

/**
 * 2. PROCESS LOGIN POST REQUEST
 */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass  = $_POST['password'];

    // Search for the user
    $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = $conn->query($sql);

    // FIX FOR LINE 14: Check if user exists before accessing the array
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Now it's safe to check the password
        if (password_verify($pass, $user['password'])) {
            
            // 3. SET SESSION DATA
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['role']      = $user['role'];
            $_SESSION['full_name'] = isset($user['first_name']) ? $user['first_name'] . " " . $user['last_name'] : $email;

           // 4. FINAL REDIRECT LOGIC
            // Check if the hidden input sent a URL
            if (!empty($_POST['redirect_to'])) {
                $target = $_POST['redirect_to'];
                
                // Safety check: if it's the login page, go to home.php instead
                if (strpos($target, 'login.html') !== false) {
                    $target = 'home.php';
                }
            } else {
                $target = 'home.php';
            }
            
            header("Location: " . $target);
            exit();
        }
    }
    
    // ERROR HANDLING
    echo "<script>alert('Invalid Email or Password'); window.history.back();</script>";
}
?>