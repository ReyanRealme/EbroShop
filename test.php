<?php
// 1. CHANGE THIS TO YOUR EMAIL
$to = "ebroshoponline@gmail.com"; 

$subject = "Server Email Test";
$message = "Hello! If you are reading this, your server can send emails perfectly without Brevo.";
$headers = "From: test@ebroshop.onrender.com";

echo "<h1>Testing Email System...</h1>";

// 2. THIS RUNS THE TEST
if(mail($to, $subject, $message, $headers)) {
    echo "<p style='color:green; font-size:20px;'>✅ SUCCESS! The email was sent to $to.</p>";
    echo "<p>Now check your <b>Spam folder</b> if it is not in your Inbox.</p>";
} else {
    echo "<p style='color:red; font-size:20px;'>❌ FAILED! Your server blocks the mail() function.</p>";
    echo "<p>If it fails, you will need to use Brevo or another API.</p>";
}
?>