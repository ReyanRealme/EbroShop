<?php


session_start();

// 1. Clear all session variables
$_SESSION = array();

// 2. Destroy the session
session_destroy();

// 3. Redirect to the login/home page
header("Location: login.html");
exit();
?>