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
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $old_pass = $_POST['old_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    // 1. Check if new passwords match
    if ($new_pass !== $confirm_pass) {
        die("Passwords do not match.");
    }

    // 2. Get current password hash from DB
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // 3. Verify Old Password
    // Note: Use password_verify if you use hashing, or $old_pass == $user['password'] if plain text (not recommended)
    if (password_verify($old_pass, $user['password']) || $old_pass == $user['password']) {
        
        // 4. Update to New Password (Hashed for security)
        $new_hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->bind_param("si", $new_hashed_pass, $user_id);
        
        if ($update->execute()) {
            echo "<script>alert('Password updated successfully!'); window.location.href='account.php';</script>";
        } else {
            echo "Error updating password.";
        }
    } else {
        echo "<script>alert('Current password is incorrect.'); window.history.back();</script>";
    }
}
$conn->close();
?>