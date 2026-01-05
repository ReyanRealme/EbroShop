<?php
session_start();
error_reporting(0); // Prevents breaking the JSON output
header('Content-Type: application/json');

// 1. Include DB to find the user if session is lost
include 'db.php'; 

$apiKey = getenv('BREVO_API_KEY'); 
$input = json_decode(file_get_contents('php://input'), true);

if ($input && $apiKey) {
    $name = mysqli_real_escape_string($conn, $input['name']);
    $phone = $input['phone'];
    $total = $input['total'];
    $cart = $input['cart'];
    $order_id = rand(1000, 9999); 

    // --- FIX: GET THE CORRECT USER EMAIL ---
    $customerEmail = null;

    // First: Try the Session
    if (isset($_SESSION['email'])) {
        $customerEmail = $_SESSION['email'];
    } 
    // Second: Try looking them up in the DB by the Name provided
    else {
        $search = "SELECT email FROM users WHERE first_name LIKE '%$name%' LIMIT 1";
        $res = $conn->query($search);
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $customerEmail = $row['email'];
        }
    }

    // Third: If still nothing, send to Admin so the order isn't lost
    if (!$customerEmail) {
        $customerEmail = "ebroshoponline@gmail.com"; 
    }

    // Build Email Content
    $rows = "";
    foreach($cart as $p) {
        $sub = $p['price'] * $p['qty'];
        $rows .= "<tr><td style='padding:8px; border:1px solid #ddd;'>{$p['name']}</td><td style='text-align:center; border:1px solid #ddd;'>{$p['qty']}</td><td style='text-align:right; border:1px solid #ddd;'>ETB " . number_format($sub, 2) . "</td></tr>";
    }

    $logoUrl = "https://res.cloudinary.com/die8hxris/image/upload/v1767382208/n8ixozf4lj5wfhtz2val.jpg";
    $data = [
        "sender" => ["name" => "EbRo Shop", "email" => "ebroshoponline@gmail.com"],
        "to" => [["email" => $customerEmail, "name" => $name]], // Sends to the customer!
        "subject" => "Your Order Receipt #$order_id - EbRo Shop",
        "htmlContent" => "
            <div style='font-family: Arial; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #eee;'>
                <img src='$logoUrl' style='width: 180px; margin-bottom: 20px;'>
                <h2 style='color: #136835;'>Order Confirmed!</h2>
                <p>Hello $name, thank you for shopping. Here is your receipt:</p>
                <table style='width: 100%; border-collapse: collapse;'>
                    <tr style='background: #f4f4f4;'><th>Item</th><th>Qty</th><th>Subtotal</th></tr>
                    $rows
                </table>
                <h3 style='text-align: right;'>Total: ETB " . number_format($total, 2) . "</h3>
            </div>"
    ];

    $ch = curl_init('https://api.brevo.com/v3/smtp/email');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['api-key: '.$apiKey, 'Content-Type: application/json']);
    curl_exec($ch);
    curl_close($ch);

    echo json_encode(["success" => true, "order_id" => $order_id]);
}
?>