<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php'; 

if (isset($_POST['request_reset'])) {
    $email = $_POST['email'];
    $token = bin2hex(random_bytes(32));
    
    $stmt = $conn->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
    $stmt->bind_param("ss", $token, $email);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // --- THIS IS THE SAFE CHANGE ---
        $apiKey = getenv('BREVO_API_KEY'); 
        $senderEmail = 'ebroshoponline@gmail.com'; 

        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $resetLink = "$protocol://$host/forget.php?token=$token";

        $data = array(
            "sender" => array("name" => "EbRoShop", "email" => $senderEmail),
            "to" => array(array("email" => $email)),
            "subject" => "Reset Your Password",
            "htmlContent" => "<h3>Reset Your Password</h3><p>Click the link below:</p><a href='$resetLink'>$resetLink</a>"
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
            echo "<script>alert('Success! Check your email.'); window.location.href='login.html';</script>";
        } else {
            echo "<script>alert('Error ($httpCode): " . addslashes($response) . "'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Email not found.'); window.history.back();</script>";
    }
}

// --- PART B: PASSWORD FORM ---
if (isset($_GET['token'])): 
    $token = $_GET['token'];
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial; background: #f4f4f4; text-align: center; padding: 50px; }
        .box { max-width: 350px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; }
        button { background: #0076ad; color: white; border: none; width: 100%; padding: 10px; cursor: pointer; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="box">
        <h2>New Password</h2>
        <form method="POST" action="forget.php">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <input type="password" name="new_pass" placeholder="Enter New Password" required minlength="6">
            <button type="submit" name="update_now">Update Password</button>
        </form>
    </div>
</body>
</html>
<?php endif; ?>

<?php
// --- PART C: DB UPDATE ---
if (isset($_POST['update_now'])) {
    $token = $_POST['token'];
    $hashed = password_hash($_POST['new_pass'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?");
    $stmt->bind_param("ss", $hashed, $token);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        echo "<script>alert('Password updated!'); window.location.href='login.html';</script>";
    } else {
        echo "<h3>Link invalid or expired.</h3>";
    }
}
?>