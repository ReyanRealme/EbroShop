<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = mysqli_real_escape_string($conn, $_POST['first_name']);
    $lname = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $pass  = $_POST['password'];
    
    // Capture the checkbox value (1 if checked, 0 if not)
    $agreed = isset($_POST['terms_agree']) ? 1 : 0;

    $checkEmail = "SELECT email FROM users WHERE email = '$email'";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        echo "<script>alert('Error: Email already exists.'); window.history.back();</script>";
    } else {
        $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
        
        // Add 'agreed_to_terms' and its value '$agreed' to your SQL
        $sql = "INSERT INTO users (first_name, last_name, email, phone, password, role, agreed_to_terms) 
                VALUES ('$fname', '$lname', '$email', '$phone', '$hashed_password', 'customer', '$agreed')";
        
        if ($conn->query($sql) === TRUE) {
            
            // --- BREVO EMAIL SYSTEM ---
            $apiKey = getenv('BREVO_API_KEY'); 
            $senderEmail = 'ebroshoponline@gmail.com'; 
            $siteUrl = "https://ebroshop.onrender.com"; 
            $logoUrl = "https://res.cloudinary.com/die8hxris/image/upload/v1767382208/n8ixozf4lj5wfhtz2val.jpg";
        
                    $data = array(
            "sender" => array("name" => "EbRoShop", "email" => $senderEmail),
            "to" => array(array("email" => $email, "name" => $fname)),
            "subject" => "Welcome to EbRoShop - Your Account is Ready!",
            "htmlContent" => "
                <div style='font-family:\"Segoe UI\",Arial,sans-serif; max-width: 600px; margin: auto; padding: 30px; border-radius: 15px; border: 1px solid #f0f0f0; background-color: #ffffff;'>
                    <div style='text-align: left; margin-bottom: 25px;'>
                        <img src='$logoUrl' alt='EbRoShop Logo' style='width: 200px; height: auto; display: block;'>
                    </div>
                    
                    <h1 style='color: #136835; font-size: 26px; margin-bottom: 10px;'>Your Shopping Journey Begins!</h1>
                    
                    <p style='font-size: 17px; color: #333; line-height: 1.6;'>
                        Hello <strong>$fname</strong>, <br>
                        We are thrilled to have you with us. Your account is now fully activated and ready for use. 
                    </p>
                    
                    <p style='font-size: 15px; color: #555; line-height: 1.5;'>
                        Experience a seamless shopping experience. Log in now to manage your orders, save your favorites, and enjoy a faster checkout process.
                    </p>
        
                    <div style='text-align: center; margin: 35px 0;'>
                        <a href='$siteUrl' style='background-color: #136835; color: #ffffff; padding: 18px 45px; text-decoration: none; font-size: 18px; border-radius: 8px; display: inline-block; font-weight: bold; box-shadow: 0 4px 12px rgba(19,104,53,0.2);'>
                            Start Shopping Now
                        </a>
                    </div>
        
                    <div style='background-color: #f4fcf7; padding: 20px; border-left: 4px solid #136835; text-align: left; margin-top: 30px;'>
                        <h3 style='color: #136835; margin: 0 0 10px 0; font-size: 18px;'>እንኳን ደስ አለዎት!</h3>
                        <p style='color: #444; font-size: 14px; margin: 0; line-height: 1.6;'>
                            የኢብሮ ሾፕ (EbRoShop) አካውንትዎ በተሳካ ሁኔታ ተከፍቷል። አሁን ወደ ድረ-ገጻችን በመግባት ጥራት ያላቸውን ምርቶች በተመጣጣኝ ዋጋ መግዛት ይችላሉ። ለፈጣን ክፍያ እና ትዕዛዝዎን ለመከታተል ሁልጊዜ መግባት (Login) አይርሱ።
                        </p>
                    </div>
        
                    <hr style='border: none; border-top: 1px solid #eee; margin: 30px 0;'>
                    
                    <p style='font-size: 12px; color: #888; text-align: center; line-height: 1.6;'>
                        Questions? Our support team is here for you. <br>
                        Reply to this email or call our helpline: <b style='color: #136835;'>+251970130755</b>
                    </p>
                </div>"
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

            curl_exec($ch);
            curl_close($ch);

            // Final Success Alert
            echo "<script>
                    alert('Congratulations! You have successfully created an account. Welcome message sent to $email');
                    window.location.href = 'login.html';
                  </script>";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
$conn->close();
?>