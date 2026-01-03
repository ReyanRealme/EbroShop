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

// 1. Clear all session variables
$_SESSION = array();

// 2. Destroy the session
session_destroy();

// 3. Redirect to the login/home page
header("Location: login.html");
exit();
?>