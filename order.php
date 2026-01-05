<?php
session_start();
header('Content-Type: application/json');

// 1. Get the Key from Render
$apiKey = getenv('BREVO_API_KEY'); 

// 2. Get Customer Email from Session
$customerEmail = isset($_SESSION['email']) ? $_SESSION['email'] : null;

// Read JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$customerEmail) {
    echo json_encode(["success" => false, "message" => "Please login first. User email not found in session."]);
    exit;
}

if (!$apiKey) {
    echo json_encode(["success" => false, "message" => "Render Environment Variable 'BREVO_API_KEY' is missing."]);
    exit;
}

if ($input) {
    $name = $input['name'];
    $phone = $input['phone'];
    $payment = $input['payment'];
    $total = $input['total'];
    $cart = $input['cart'];
    $order_id = rand(1000, 9999); 

    // Create Product Table for Email
    $rows = "";
    foreach($cart as $p) {
        $st = $p['price'] * $p['qty'];
        $rows .= "<tr>
                    <td style='padding:8px; border:1px solid #ddd;'>{$p['name']}</td>
                    <td style='padding:8px; border:1px solid #ddd; text-align:center;'>{$p['qty']}</td>
                    <td style='padding:8px; border:1px solid #ddd; text-align:right;'>ETB " . number_format($st, 2) . "</td>
                  </tr>";
    }

    // BREVO DATA SETUP (Same as your register.php)
    $data = [
        "sender" => ["name" => "EbRo Shop", "email" => "ebroshoponline@gmail.com"],
        "to" => [["email" => $customerEmail, "name" => $name]],
        "subject" => "Order Receipt #$order_id - EbRo Shop",
        "htmlContent" => "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #eee; padding: 20px;'>
                <h2 style='color: #0b91ff; text-align:center;'>Thank you for your order!</h2>
                <p>Hello $name, we have received your order and it is being processed.</p>
                <hr>
                <p><strong>Order ID:</strong> #$order_id</p>
                <p><strong>Phone:</strong> $phone</p>
                <p><strong>Payment Method:</strong> $payment</p>
                <table style='width: 100%; border-collapse: collapse; margin-top: 20px;'>
                    <thead>
                        <tr style='background: #f8f8f8;'>
                            <th style='padding: 10px; border: 1px solid #ddd;'>Product</th>
                            <th style='padding: 10px; border: 1px solid #ddd;'>Qty</th>
                            <th style='padding: 10px; border: 1px solid #ddd;'>Price</th>
                        </tr>
                    </thead>
                    <tbody>$rows</tbody>
                </table>
                <h3 style='text-align: right;'>Total: ETB " . number_format($total, 2) . "</h3>
                <br>
                <p style='font-size: 12px; color: #777;'>If you have questions, contact us at +251970130755</p>
            </div>"
    ];

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

    if ($httpCode >= 200 && $httpCode < 300) {
        echo json_encode(["success" => true, "order_id" => $order_id]);
    } else {
        echo json_encode(["success" => false, "message" => "Brevo Error $httpCode: " . $response]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid JSON input."]);
}
?>