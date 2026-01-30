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
    :root {
        --primary-green: #136835;
        --accent-blue: #008cff;
        --bg-light: #f8fafc;
    }

    .reset-card {
        max-width: 420px;
        margin: 50px auto;
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        padding: 40px;
        font-family: 'Inter', sans-serif;
    }

    .card-header { text-align: center; margin-bottom: 30px; }
    
    .icon-circle {
        width: 70px;
        height: 70px;
        background: var(--bg-light);
        color: var(--primary-green);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        margin: 0 auto 15px;
    }

    .card-header h2 { color: #1e293b; font-size: 24px; margin-bottom: 8px; }
    .card-header p { color: #64748b; font-size: 14px; }

    .input-group label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 8px; }
    
    .password-wrapper { position: relative; margin-bottom: 25px; }
    
    .password-wrapper input {
        width: 100%;
        padding: 14px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 16px;
        transition: all 0.3s;
    }

    .password-wrapper input:focus { border-color: var(--primary-green); outline: none; }
    
    .toggle-eye { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #94a3b8; }

    .submit-btn {
        width: 100%;
        padding: 16px;
        background: var(--primary-green);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        transition: 0.3s;
    }

    .submit-btn:hover { background: #0e522a; transform: translateY(-2px); }

    .info-section {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px dashed #e2e8f0;
    }

    .info-header { display: flex; align-items: center; gap: 8px; color: var(--primary-green); font-weight: 700; font-size: 14px; margin-bottom: 12px; }
    
    .info-list { list-style: none; padding: 0; }
    .info-list li { font-size: 12.5px; color: #64748b; margin-bottom: 8px; line-height: 1.6; position: relative; padding-left: 15px; }
    .info-list li::before { content: "•"; position: absolute; left: 0; color: var(--primary-green); font-weight: bold; }
</style>
</head>
<body>

   <div class="reset-card">
    <div class="card-header">
        <div class="icon-circle">
            <i class="fa-solid fa-lock-open"></i>
        </div>
        <h2>Secure Your Account</h2>
        <p>Create a new, strong password to regain access to EbRoShop.</p>
    </div>

    <form method="POST" action="forget.php" class="reset-form">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        
        <div class="input-group">
            <label>New Password</label>
            <div class="password-wrapper">
                <input type="password" name="new_pass" id="new_pass" placeholder="••••••••" required minlength="6">
                <i class="fa-solid fa-eye toggle-eye" id="eyeIcon" onclick="togglePassword()"></i>
            </div>
        </div>

        <button type="submit" name="update_now" class="submit-btn">
            Update Password <i class="fa-solid fa-arrow-right"></i>
        </button>
    </form>

    <div class="info-section">
        <div class="info-header">
            <i class="fa-solid fa-circle-check"></i>
            <span>መከተል ያለብዎት መመሪያዎች</span>
        </div>
        <ul class="info-list">
            <li><strong>አዲስ ፓስወርድ ያስገቡ፦</strong> ቢያንስ 6 ሆሄያት ወይም ቁጥሮችን ይጠቀሙ።</li>
            <li><strong>ደህንነት፦</strong> የእርስዎን ስም ወይም ስልክ ቁጥር አይጠቀሙ።</li>
            <li><strong>ምስጢራዊነት፦</strong> አዲሱን ሚስጥራዊ ቁጥር ለማንም አያጋሩ።</li>
        </ul>
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