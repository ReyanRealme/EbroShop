<?php
include 'db.php';

// 1. SAVE REFERRER (Backup method)
if (!isset($_SESSION['redirect_to']) && isset($_SERVER['HTTP_REFERER'])) {
    $ref = $_SERVER['HTTP_REFERER'];
    if (strpos($ref, 'login.html') === false && strpos($ref, 'login.php') === false) {
        $_SESSION['redirect_to'] = $ref;
    }
}

// 2. PROCESS LOGIN
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass  = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = $conn->query($sql);

    // FIX LINE 14: Check if user exists
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if (password_verify($pass, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['role']      = $user['role'];
            $_SESSION['full_name'] = isset($user['first_name']) ? $user['first_name'] . " " . $user['last_name'] : $email;

            // 3. DETERMINE REDIRECT TARGET
            $target = 'home.php'; // Default

            // Check hidden input first
            if (!empty($_POST['redirect_to'])) {
                $target = $_POST['redirect_to'];
            } 
            // Fallback to session if hidden input failed
            elseif (isset($_SESSION['redirect_to'])) {
                $target = $_SESSION['redirect_to'];
                unset($_SESSION['redirect_to']);
            }

            // 4. SAFETY CHECK (Prevent looping back to login page)
            if (strpos($target, 'login.html') !== false || strpos($target, 'login.php') !== false) {
                $target = 'home.php';
            }

            // Redirect without ?login=success (No Welcome Message)
            header("Location: " . $target);
            exit();
        }
    }
    
    echo "<script>alert('Invalid Email or Password'); window.history.back();</script>";
}
?>