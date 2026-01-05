<?php
session_start();
error_reporting(0); 
header('Content-Type: application/json');

include 'db.php';

$apiKey = getenv('BREVO_API_KEY');
$input = json_decode(file_get_contents('php://input'), true);

if ($input && $apiKey) {
    $name = mysqli_real_escape_string($conn, $input['name']);
    $phone = $input['phone'];
    $payment = $input['payment'];
    $total = $input['total'];
    $cart = $input['cart'];
    $order_id = rand(1000, 9999); 

    // --- THE FIX: FIND THE REGISTERED EMAIL ---
    $customerEmail = null;

    // 1. Check if email was sent directly from the Form/JS
    if (!empty($input['email'])) {
        $customerEmail = $input['email'];
    } 
    // 2. Check the Session
    elseif (isset($_SESSION['email'])) {
        $customerEmail = $_SESSION['email'];
    } 
    // 3. Search Database by Name (Lookup logic like register.php)
    else {
        $search = "SELECT email FROM users WHERE first_name LIKE '%$name%' LIMIT 1";
        $res = $conn->query($search);
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $customerEmail = $row['email'];
        }
    }

    // Only use admin as a last resort if NO email is found anywhere
    if (!$customerEmail) {
        $customerEmail = 'ebroshoponline@gmail.com'; 
    }

    // Email Table Construction
    $rows = "";
    foreach($cart as $p) {
        $sub = $p['price'] * $p['qty'];
        $rows .= "<tr>
                    <td style='padding:12px; border-bottom:1px solid #eee;'>{$p['name']}</td>
                    <td style='padding:12px; border-bottom:1px solid #eee; text-align:center;'>{$p['qty']}</td>
                    <td style='padding:12px; border-bottom:1px solid #eee; text-align:right;'>ETB " . number_format($sub, 2) . "</td>
                  </tr>";
    }

    $logoUrl = "https://res.cloudinary.com/die8hxris/image/upload/v1767382208/n8ixozf4lj5wfhtz2val.jpg";

    $data = array(
        "sender" => array("name" => "EbRoShop", "email" => "ebroshoponline@gmail.com"),
        "to" => array(array("email" => $customerEmail, "name" => $name)), // SENDS TO USER
        "subject" => "Receipt for Order #$order_id",
        "htmlContent" => "
            <div style='font-family:Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #eee; border-radius:10px;'>
                <div style='text-align: center; border-bottom: 2px solid #136835; padding-bottom: 15px; margin-bottom: 20px;'>
                    <img src='$logoUrl' alt='EbRoShop' style='width: 200px;'>
                </div>
                <h2 style='color: #136835; text-align: center;'>Thank you for your order!</h2>
                <table style='width: 100%; border-collapse: collapse;'>
                    <tr style='background: #222; color: white;'>
                        <th style='padding:12px; text-align:left;'>Product</th>
                        <th style='padding:12px;'>Qty</th>
                        <th style='padding:12px; text-align:right;'>Price</th>
                    </tr>
                    $rows
                </table>
                <h3 style='text-align: right; color:#136835;'>Total: ETB " . number_format($total, 2) . "</h3>
                <p><b>Phone:</b> $phone | <b>Payment:</b> $payment</p>
                <p style='font-size: 12px; color: #777; text-align: center; margin-top: 20px;'>
                    Questions? Contact us at ebroshoponline@gmail.com
                </p>
            </div>"
    );

    // Send using Brevo (Logic from register.php)
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

    echo json_encode(["success" => true, "order_id" => $order_id]);
}
?>