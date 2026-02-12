<?php
include 'db.php';

// --- 1. CATCH THE PREVIOUS PAGE ---
// This stores the URL of the page the user was on BEFORE they clicked login
if (!isset($_SESSION['redirect_to']) && isset($_SERVER['HTTP_REFERER'])) {
    $ref = $_SERVER['HTTP_REFERER'];
    // Don't save if the referrer is the login page itself
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

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // --- FIXED LINE 14 ERROR ---
        // We verify the password ONLY after we are sure $user is an array
        if (password_verify($pass, $user['password'])) {
            
            // 3. SAVE DATA INTO THE SESSION
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['role']      = $user['role'];
            $_SESSION['full_name'] = isset($user['first_name']) ? $user['first_name'] . " " . $user['last_name'] : $email;

            // 4. REDIRECT BACK TO PREVIOUS PAGE
            $redirectTo = isset($_SESSION['redirect_to']) ? $_SESSION['redirect_to'] : 'home.php';
            unset($_SESSION['redirect_to']); // Clear it so it doesn't loop
            
            // Add ?login=success to the URL for the welcome message
            $connector = (strpos($redirectTo, '?') !== false) ? '&' : '?';
            header("Location: " . $redirectTo . $connector . "login=success");
            exit();
        }
    }
    
    // 5. ERROR HANDLING
    echo "<script>alert('Invalid Email or Password'); window.history.back();</script>";
}
?>