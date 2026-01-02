<?php
// --- 1. ERROR REPORTING ---
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- 2. DATABASE CONNECTION ---
// If db.php fails, we handle it gracefully here
if (!file_exists('db.php')) {
    die("Error: db.php is missing from the server.");
}
include 'db.php'; 

// ==========================================
// PART A: SENDING THE RESET LINK
// ==========================================
if (isset($_POST['request_reset'])) {
    $email = $_POST['email'];
    $token = bin2hex(random_bytes(32));
    
    // Update the user record
    $stmt = $conn->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
    $stmt->bind_param("ss", $token, $email);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $apiKey = getenv('BREVO_API_KEY'); 
        $senderEmail = 'ebroshoponline@gmail.com'; 

        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host_url = $_SERVER['HTTP_HOST'];
        $resetLink = "$protocol://$host_url/forget.php?token=$token";

        $data = array(
            "sender" => array("name" => "EbRoShop", "email" => $senderEmail),
            "to" => array(array("email" => $email)),
            "subject" => "Reset Your Password",
            "htmlContent" => "<html><body><h3>Reset Password</h3><p>Link: <a href='$resetLink'>$resetLink</a></p></body></html>"
        );

        $ch = curl_init('https://api.brevo.com/v3/smtp/email');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'api-key: ' . $apiKey,
            'Content-Type: application/json',
            'Accept: application/json'
        ));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode == 201 || $httpCode == 200) {
            echo "<script>alert('Success! Check email.'); window.location.href='login.html';</script>";
        } else {
            echo "<h3>API Error ($httpCode)</h3><pre>$response</pre>";
        }
    } else {
        echo "<script>alert('Email not found.'); window.history.back();</script>";
    }
}

// ==========================================
// PART B: NEW PASSWORD FORM
// ==========================================
if (isset($_GET['token'])): 
    $token = $_GET['token'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Set New Password</title>
    <style>
        body { font-family: Arial; text-align: center; padding: 50px; background: #f4f4f4; }
        .box { max-width: 350px; margin: auto; background: white; padding: 30px; border-radius: 8px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; box-sizing: border-box; }
        button { background: #0076ad; color: white; border: none; width: 100%; padding: 10px; cursor: pointer; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="box">
        <h2>New Password</h2>
        <form method="POST" action="forget.php">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <input type="password" name="new_pass" placeholder="New Password" required minlength="6">
            <button type="submit" name="update_now">Update Password</button>
        </form>
    </div>
</body>
</html>
<?php endif; ?>

<?php
// ==========================================
// PART C: UPDATING THE DATABASE
// ==========================================
if (isset($_POST['update_now'])) {
    $token = $_POST['token'];
    $hashed = password_hash($_POST['new_pass'], PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?");
    $stmt->bind_param("ss", $hashed, $token);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        echo "<script>alert('Updated! Please login.'); window.location.href='login.html';</script>";
    } else {
        echo "<h3>Link invalid or expired.</h3>";
    }
}
?>