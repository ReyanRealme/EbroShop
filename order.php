<?php
ob_start(); 
session_start();
error_reporting(0); 
header('Content-Type: application/json');

include 'db.php'; 

// CONFIGURATION
$apiKey = getenv('BREVO_API_KEY'); 
$botToken = "8552120519:AAHYu7diuQjM8y-qy6I5jlqWm0IwB-y7RYM"; // Replace with your Bot Token
$chatId = "5335234629";     // Replace with your Chat ID

if ($_POST && $apiKey) {
    // 1. COLLECT DATA FROM FORMDATA
    $name    = mysqli_real_escape_string($conn, $_POST['name']); 
    $phone   = mysqli_real_escape_string($conn, $_POST['phone']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']); 
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $payment = mysqli_real_escape_string($conn, $_POST['payment']);
    $total   = $_POST['total'];
    $cart    = json_decode($_POST['cart'], true);
    $order_id = "ORD-" . rand(10000, 99999); 

    // 2. SAVE TO DATABASE (Just like register.php)
    // We try to find the user_id based on the email provided
    $user_id = 0;
    $user_check = $conn->query("SELECT id FROM users WHERE email = '$email' LIMIT 1");
    if($user_check && $user_check->num_rows > 0) {
        $user_id = $user_check->fetch_assoc()['id'];
    }

    $sql = "INSERT INTO orders (user_id, order_id, total_amount, payment_method, status) 
            VALUES ('$user_id', '$order_id', '$total', '$payment', 'Pending')";
    
    if (!$conn->query($sql)) {
        ob_end_clean();
        echo json_encode(["success" => false, "error" => "DB Error: " . $conn->error]);
        exit;
    }

    // 3. TELEGRAM PART (With Photo Proof)
    $caption = "📦 *New Order $order_id*\n\n"
             . "👤 Customer: $name\n"
             . "📞 Phone: $phone\n"
             . "💰 Total: ETB " . number_format($total, 2) . "\n"
             . "💳 Method: $payment";

    if (isset($_FILES['proof'])) {
        $photo = $_FILES['proof']['tmp_name'];
        $post_fields = [
            'chat_id' => $chatId, 
            'photo' => new CURLFile($photo), 
            'caption' => $caption, 
            'parse_mode' => 'Markdown'
        ];
        $ch = curl_init("https://api.telegram.org/bot$botToken/sendPhoto");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }

    // 4. EMAIL PART (Beautiful Design)
    if (!empty($email)) {
        $rows = "";
        foreach($cart as $p) {
            $sub = $p['price'] * $p['qty'];
            $rows .= "<tr>
                <td style='padding:10px; border-bottom:1px solid #eee;'>{$p['name']}</td>
                <td style='padding:10px; border-bottom:1px solid #eee; text-align:center;'>{$p['qty']}</td>
                <td style='padding:10px; border-bottom:1px solid #eee; text-align:right;'>ETB ".number_format($sub, 2)."</td>
            </tr>";
        }

        $htmlBody = "
        <div style='font-family: Arial; max-width: 600px; margin: auto; border: 1px solid #eee; border-radius: 10px;'>
            <div style='background: #136835; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0;'>
                <h1>Order Confirmed!</h1>
            </div>
            <div style='padding: 20px;'>
                <p>Hello <b>$name</b>, your order <b>#$order_id</b> is being processed.</p>
                <table style='width:100%; border-collapse: collapse;'>
                    <thead><tr style='background:#f4f4f4;'><th>Item</th><th>Qty</th><th>Subtotal</th></tr></thead>
                    <tbody>$rows</tbody>
                </table>
                <h3 style='text-align:right; color:#136835;'>Total: ETB ".number_format($total, 2)."</h3>
                <p><b>Payment:</b> $payment | <b>Phone:</b> $phone</p>
            </div>
            <div style='background: #f4f4f4; padding: 10px; text-align: center; font-size: 12px;'>
                Thank you for choosing EbRoShop!
            </div>
        </div>";
        $emailData = [
            "sender" => ["name" => "EbRoShop", "email" => "ebroshoponline@gmail.com"],
            "to" => [["email" => $email, "name" => $name]],
            "subject" => "Your Receipt - Order $order_id",
            "htmlContent" => $htmlBody
        ];

        $ch = curl_init('https://api.brevo.com/v3/smtp/email');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['api-key: ' . $apiKey, 'Content-Type: application/json']);
        curl_exec($ch);
        curl_close($ch);
    }

    ob_end_clean(); 
    echo json_encode(["success" => true, "order_id" => $order_id]);
}
?>