<?php
include 'db.php';

// --- NEW: CATCH THE PREVIOUS PAGE BEFORE LOGIN ATTEMPTS ---
if (!isset($_SESSION['redirect_to']) && isset($_SERVER['HTTP_REFERER'])) {
    // Only save if the referrer is NOT the login page itself
    if (strpos($_SERVER['HTTP_REFERER'], 'login.html') === false) {
        $_SESSION['redirect_to'] = $_SERVER['HTTP_REFERER'];
    }
}

// ... (Your existing database/password check code) ...

        if (password_verify($pass, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['role']      = $user['role'];
            $_SESSION['full_name'] = isset($user['first_name']) ? $user['first_name'] . " " . $user['last_name'] : $email;

            // --- FIXED REDIRECT LOGIC ---
            $target = isset($_SESSION['redirect_to']) ? $_SESSION['redirect_to'] : 'home.php';
            unset($_SESSION['redirect_to']); // Clear it after use

            // Add the success flag for the welcome message
            $joiner = (strpos($target, '?') !== false) ? '&' : '?';
            header("Location: " . $target . $joiner . "login=success");
            exit();
        }

// Check connection (from db.php)
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/**
 * 2. PROCESS LOGIN POST REQUEST
 */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass  = $_POST['password'];

    // Search for the user in your 'users' table
    $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Check if the password matches the hash in the database
        if (password_verify($pass, $user['password'])) {
            
            // 3. SAVE DATA INTO THE SESSION (Uses the 30-day rules from db.php)
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['role']      = $user['role'];
            
            // Logic for name: Use first/last name if available, otherwise email
            $_SESSION['full_name'] = isset($user['first_name']) ? $user['first_name'] . " " . $user['last_name'] : $email;

           // 4. REDIRECT BACK WITH WELCOME MESSAGE
              $redirectTo = isset($_SESSION['redirect_to']) ? $_SESSION['redirect_to'] : 'home.php';
              unset($_SESSION['redirect_to']); // Clear it for next time
              
              $connector = (strpos($redirectTo, '?') !== false) ? '&' : '?';
              header("Location: " . $redirectTo . $connector . "login=success");
              exit();
        }
    }
    
    // 5. ERROR HANDLING
    // If we reach here, either email was wrong or password failed
    echo "<script>alert('Invalid Email or Password'); window.history.back();</script>";
}
?>