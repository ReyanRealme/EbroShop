<?php
include 'db.php'; 
// --- 1. ERROR REPORTING ---
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- 2. DATABASE CONNECTION ---
// If db.php fails, we handle it gracefully here
if (!file_exists('db.php')) {
    die("Error: db.php is missing from the server.");
}


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



       
$htmlBody = "
<html>
<head>
    <style>
        .container { font-family: sans-serif; max-width: 600px; margin: auto; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; }
        .header { background: #136835; color: white; padding: 20px; text-align: center; }
        .content { padding: 30px; text-align: center; color: #333; line-height: 1.6; }
        .btn { background: #008cff; color: white !important; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block; margin-top: 20px; }
        .footer { background: #f4f4f4; padding: 15px; text-align: center; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'><h2>EbRoShop Online</h2></div>
        <div class='content'>
            <h3>ሚስጥራዊ ቁጥርዎን ይቀይሩ</h3>
            <p>ሰላም፣ የአካውንትዎን ሚስጥራዊ ቁጥር ለመቀየር ከታች ያለውን ሰማያዊ ቁልፍ ይጫኑ።</p>
            <a href='$resetLink' class='btn'>ሚስጥራዊ ቁጥር ይቀይሩ</a>
            <p style='margin-top:20px; font-size:13px;'>ይህ ጥያቄ ከእርስዎ ካልቀረበ እባክዎ እንዳይቀይሩ።</p>
        </div>
        <div class='footer'>&copy; " . date("Y") . " EbRoShop Online Shopping</div>
    </div>
</body>
</html>";


$data = array(
    "sender" => array("name" => "EbRoShop", "email" => $senderEmail),
    "to" => array(array("email" => $email)),
    "subject" => "Reset Your Password",
    "htmlContent" => $htmlBody
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
// ==========================================
// PART B: THE NEW PASSWORD FORM (MOBILE OPTIMIZED)
// ==========================================
if (isset($_GET['token'])): 
    $token = $_GET['token'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Set New Password</title>
    <style>
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            text-align: center; 
            padding: 20px; 
            background: #f4f4f4; 
            margin: 0;
        }
        .box { 
            max-width: 400px; 
            margin: 40px auto; 
            background: white; 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
        }
        h2 { 
            color: #333; 
            font-size: 24px; /* Larger Header */
            margin-bottom: 20px;
        }
        input { 
            width: 100%; 
            padding: 15px; /* Bigger touch area */
            margin: 15px 0; 
            border: 1px solid #ccc; 
            border-radius: 8px; 
            font-size: 18px; /* Larger Text for Mobile */
            box-sizing: border-box; 
        }
        button { 
            background: #0076ad; 
            color: white; 
            border: none; 
            width: 100%; 
            padding: 16px; 
            cursor: pointer; 
            border-radius: 50px; 
            font-size: 18px; 
            font-weight: bold;
            transition: background 0.3s;
        }
        button:active {
            background: #005a84;
            transform: scale(0.98);
        }

       /* Styling the password container to position the eye */ 
       .password-container { 
                    position: relative;
                    width: 100%;
        } 
       .password-container input {
                width: 100%; 
                padding: 15px;
                padding-right: 45px;
                /* Leave space for the eye */ 
                margin: 15px 0; 
                border: 1px solid #ccc; 
                border-radius: 8px; 
                font-size: 18px; 
                box-sizing: border-box; 
        }
       .toggle-password {
                 position: absolute;
                 right: 15px;
                 top: 50%; 
                 transform: translateY(-50%); 
                 cursor: pointer; 
                 color: #666; 
                 font-size: 20px; 
                 z-index: 2; 
        } 
</style>
</head>
<body>
    
   <div class="box">
    <h2 style='color:#136835;'>Enter New Password</h2>
    <p style="color: #666; font-size: 14px;">Enter a strong password for your EbRoShop account.</p>
    
    <form method="POST" action="forget.php">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <div class="password-container"> 
            <input type="password" name="new_pass" id="new_pass" placeholder="አዲስ ሚስጥራዊ ቁጥር ያስገቡ" required minlength="6"> 
            <i class="fa-solid fa-eye toggle-password" id="eyeIcon" onclick="togglePassword()"></i>
        </div> 
        <button type="submit" name="update_now">Update Password</button>
    </form>

    <div style="margin-top: 20px; padding: 15px; border-top: 1px dashed #136835; background-color: #f9fffb; border-radius: 8px;">
        <h4 style="margin: 0 0 8px 0; color: #136835; font-size: 14px;">መከተል ያለብዎት መመሪያዎች፦</h4>
        <ul style="margin: 0; padding-left: 20px; color: #4a5568; font-size: 12.5px; line-height: 1.6;">
            <li><strong>አዲስ ፓስወርድ ያስገቡ፦</strong> ቢያንስ 6 ሆሄያት ወይም ቁጥሮችን ይጠቀሙ።</li>
            <li><strong>ደህንነት፦</strong> ለሌላ ሰው የማይገመት እና የእርስዎን ስም ወይም ስልክ ቁጥር ያላካተተ ቢሆን ይመረጣል።</li>
            <li><strong>ምስጢራዊነት፦</strong> የፈጠሩትን አዲስ ሚስጥራዊ ቁጥር ለማንም ሰው አያጋሩ።</li>
        </ul>
        <p style="margin-top: 10px; color: #718096; font-size: 12px; font-style: italic;">
            * ይህ መመሪያ የአካውንትዎን ደህንነት በከፍተኛ ሁኔታ ለመጠበቅ ይረዳል።
        </p>
    </div>
</div>


      <script>
        function togglePassword() {
            const passwordInput = document.getElementById("new_pass");
            const eyeIcon = document.getElementById("eyeIcon");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                // Change eye to eye-slash
                eyeIcon.classList.remove("fa-eye");
                eyeIcon.classList.add("fa-eye-slash");
            } else {
                passwordInput.type = "password";
                // Change eye-slash back to eye
                eyeIcon.classList.remove("fa-eye-slash");
                eyeIcon.classList.add("fa-eye");
            }
        }
    </script>
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
        echo "<script>alert('Your password has been successfully changed! Please login.'); window.location.href='login.html';</script>";
    } else {
        echo "<h3>Link invalid or expired.</h3>";
    }
}
?>