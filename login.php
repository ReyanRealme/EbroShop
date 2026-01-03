<?php
// --- 1. SET SESSION LIFETIME (30 Days = 2592000 seconds) ---
$session_expiration = 2592000; 
ini_set('session.gc_maxlifetime', $session_expiration);
ini_set('session.cookie_lifetime', $session_expiration);

// --- 2. MAKE COOKIE SECURE ---
session_set_cookie_params([
    'lifetime' => $session_expiration,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => true,     // Only sends over HTTPS (Render uses HTTPS)
    'httponly' => true,   // Protects against XSS attacks
    'samesite' => 'Lax'
]);

// --- 3. START THE SESSION ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


session_start();
include 'db.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass  = $_POST['password']; 

    // Search for the user
    $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Check if the password matches the hash in the database
        if (password_verify($pass, $user['password'])) {
            
            // 1. Save data into the Session
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['role']      = $user['role'];
            // Check if names exist, otherwise use email as display name
            $_SESSION['full_name'] = isset($user['first_name']) ? $user['first_name'] . " " . $user['last_name'] : $email;

            // 2. Redirect based on role
            if ($user['role'] == 'admin') {
                // Takes you to your special admin tools
                header("Location: admin_dashboard.php");
            } else {
                // Takes customers to the shopping page
                header("Location: home.html"); 
            }
            exit();
        }
    }
    
    // If we reach here, either email was wrong or password failed
    echo "<script>alert('Invalid Email or Password'); window.history.back();</script>";
}
?>