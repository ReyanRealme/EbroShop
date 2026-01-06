<?php
// 1. ADD THIS TO STOP THE "UNEXPECTED TOKEN" ERROR
ob_start(); 
session_start();
error_reporting(0); 
header('Content-Type: application/json');

include 'db.php'; 

$apiKey = getenv('BREVO_API_KEY'); 
$input = json_decode(file_get_contents('php://input'), true);

if ($input && $apiKey) {
    // Sanitize input
    $name = mysqli_real_escape_string($conn, $input['name']); 
    $phone = $input['phone'];
    $payment = $input['payment'];
    $total = $input['total'];
    $cart = $input['cart'];
    $order_id = rand(1000, 9999); 

    // --- NEW: SAVE TO DATABASE (THE MISSING PART) ---
    // First, find the user_id so it shows in Account History
    $user_id = 0;
    $search_user = "SELECT id, email FROM users WHERE first_name = '$name' OR CONCAT(first_name, ' ', last_name) = '$name' LIMIT 1";
    $user_res = $conn->query($search_user);
    if ($user_res && $user_res->num_rows > 0) {
        $user_row = $user_res->fetch_assoc();
        $user_id = $user_row['id'];
        $customerEmail = $user_row['email'];
    }

    // Now, actually save the order into the 'orders' table
    $sql_save = "INSERT INTO orders (user_id, order_id, total_amount, payment_method, status) 
                 VALUES ('$user_id', '$order_id', '$total', '$payment', 'Pending')";
    $conn->query($sql_save);

    // --- YOUR EMAIL LOGIC (FULLY RESTORED) ---
    if ($customerEmail) {
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
            "to" => array(array("email" => $customerEmail, "name" => $name)),
            "subject" => "Your Order Confirmation #$order_id",
            "htmlContent" => "
                <div style='font-family:Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #eee; border-radius:10px;'>
                    <div style='text-align: center; border-bottom: 2px solid #136835; padding-bottom: 15px; margin-bottom: 20px;'>
                        <img src='$logoUrl' alt='EbRoShop' style='width: 200px;'>
                    </div>
                    <h2 style='color: #136835; text-align: center;'>Thank you for your order!</h2>
                    <p>Hello <b>$name</b>, your order has been received.</p>
                    <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                        <tr style='background: #222; color: white;'>
                            <th style='padding:12px; text-align:left;'>Product</th>
                            <th style='padding:12px;'>Qty</th>
                            <th style='padding:12px; text-align:right;'>Price</th>
                        </tr>
                        $rows
                        <tr style='font-weight: bold;'>
                            <td colspan='2' style='padding:15px; text-align:right;'>Total Amount:</td>
                            <td style='padding:15px; text-align:right; color:#136835;'>ETB " . number_format($total, 2) . "</td>
                        </tr>
                    </table>
                    <p><b>Phone:</b> $phone | <b>Payment:</b> $payment</p>
                    <p style='font-size: 12px; color: #777; text-align: center; margin-top: 30px;'>
                        Contact us at ebroshoponline@gmail.com or +251970130755
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
    }

    // --- FINAL CLEANUP AND SUCCESS ---
    ob_end_clean(); // Clears all hidden <br> tags
    echo json_encode(["success" => true, "order_id" => $order_id]);
}
?>