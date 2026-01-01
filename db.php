<?php
// FORCE ERRORS TO SHOW
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Add a test message to the logs
error_log("Attempting to connect to Aiven Database...");

$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$port = getenv('DB_PORT');
$dbname = "defaultdb";

$conn = mysqli_init();
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

if (!mysqli_real_connect($conn, $host, $user, $pass, $dbname, $port)) {
    // If it fails, this WILL show in your Render logs
    error_log("DATABASE CONNECTION FAILED: " . mysqli_connect_error());
    die("Connection failed. Check Render logs.");
} else {
    error_log("DATABASE CONNECTION SUCCESSFUL!");
}
?>