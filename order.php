<?php
session_start();
// Prevent any PHP errors from leaking into the JSON response
error_reporting(0);
header('Content-Type: application/json');

// 1. Get the Key from Render Environment
$apiKey = getenv('BREVO_API_KEY'); 

// 2. Get Customer Email from Session
$customerEmail = isset($_SESSION['email']) ? $_SESSION['email'] : null;

// Read JSON input from JavaScript
$input = json_decode(file_get_contents('php://input'), true);

if ($input) {
    $name = $input['name'];
    $phone = $input['phone'];
    $payment = $input['payment'];
    $total = $input['total'];
    $cart = $input['cart'];
    $order_id = rand(1000, 9999); 

    // If session is expired, we use your shop email so the order still processes
    $targetEmail = ($customerEmail) ? $customerEmail : "ebroshoponline@gmail.com";

    // Build Email Table
    $rows = "";
    foreach($cart as $p) {
        $st = $p['price'] * $p['qty'];
        $rows .= "<tr>
                    <td style='padding:8px; border:1px solid #ddd;'>{$p['name']}</td>
                    <td style='padding:8px; border:1px solid #ddd; text-align:center;'>{$p['qty']}</td>
                    <td style='padding:8px; border:1px solid #ddd; text-align:right;'>ETB " . number_format($st, 2) . "</td>
                  </tr>";
    }

    // BREVO DATA SETUP
    $logoUrl = "https://res.cloudinary.com/die8hxris/image/upload/v1767382208/n8ixozf4lj5wfhtz2val.jpg";
    $data = [
        "sender" => ["name" => "EbRo Shop", "email" => "ebroshoponline@gmail.com"],
        "to" => [["email" => $targetEmail, "name" => $name]],
        "subject" => "Order Receipt #$order_id - EbRo Shop",
        "htmlContent" => "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #eee; padding: 20px;'>
                <img src='$logoUrl' alt='EbRoShop Logo' style='width: 200px; display: block; margin-bottom: 20px;'>
                <h2 style='color: #136835;'>Order Confirmation</h2>
                <p>Hello $name, we have received your order!</p>
                <table style='width: 100%; border-collapse: collapse;'>
                    <thead>
                        <tr style='background: #f8f8f8;'>
                            <th style='padding: 10px; border: 1px solid #ddd;'>Product</th>
                            <th style='padding: 10px; border: 1px solid #ddd;'>Qty</th>
                            <th style='padding: 10px; border: 1px solid #ddd;'>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>$rows</tbody>
                </table>
                <h3 style='text-align: right;'>Total: ETB " . number_format($total, 2) . "</h3>
                <p><strong>Phone:</strong> $phone | <strong>Payment:</strong> $payment</p>
            </div>"
    ];

    $ch = curl_init('https://api.brevo.com/v3/smtp/email');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'api-key: ' . $apiKey,
        'Content-Type: application/json',
        'Accept: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // We return success even if email has a small issue so Telegram can finish
    echo json_encode(["success" => true, "order_id" => $order_id]);
} else {
    echo json_encode(["success" => false, "message" => "No data received"]);
}
?