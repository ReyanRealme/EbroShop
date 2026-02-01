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
                "subject" => "Customer account confirmation",
                "htmlContent" => "
                    <div style='font-family:Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; border-radius: 10px; border: 1px solid #eee; text-align:center;'>
                        <div style='text-align: left;'>
                            <img src='$logoUrl' alt='EbRoShop Logo' style='width: 260px; height: auto; display: block;'>
                        </div>
                        <h1 style=' color: #136835;'>Welcome to EbRoShop!</h1>
                        <p style='font-size: 16px; color: #555;'>Hello $fname, your customer account is now active.</p>
                        <p style='color:#777;'>Next time you shop with us, log in for faster checkout.</p>
                        <br>
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='$siteUrl' style='background-color: #136835; color: white; padding: 15px 35px; text-decoration: none; font-size: 18px; border-radius: 50px; display: inline-block; font-weight: bold;'>
                              Visit our store
                           </a>
                        </div>
                        <br>
                        <p style='font-size: 12px; color: #bbb;'>If you have any questions, reply to this email or contact us at $senderEmail. Helpline: <b style='color: #0b91ff;'>+251970130755</b></p>
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