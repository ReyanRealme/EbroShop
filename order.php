<?php
header('Content-Type: application/json');

// 1. Get the Key from Render Environment Variables
$apiKey = getenv('BREVO_API_KEY'); 

// 2. Read the data sent from your script
$input = json_decode(file_get_contents('php://input'), true);

if (!$apiKey) {
    echo json_encode(["success" => false, "message" => "Server Error: BREVO_API_KEY is missing in Render settings."]);
    exit;
}

if ($input) {
    $name = $input['name'];
    $phone = $input['phone'];
    $payment = $input['payment'];
    $total = $input['total'];
    $cart = $input['cart'];
    $order_id = rand(1000, 9999); 

    // Build Email Table
    $rows = "";
    foreach($cart as $p) {
        $st = $p['price'] * $p['qty'];
        $rows .= "<tr><td>{$p['name']}</td><td>{$p['qty']}</td><td>ETB " . number_format($st, 2) . "</td></tr>";
    }

    $emailData = [
        "sender" => ["name" => "EbRo Shop", "email" => "system@ebroshop.com"],
        "to" => [["email" => "ebroshoponline@gmail.com"]],
        "subject" => "New Order #$order_id - $name",
        "htmlContent" => "<h3>Order Details</h3><table border='1'>$rows</table><h4>Total: ETB $total</h4>"
    ];

    // 3. Send using Brevo
    $ch = curl_init('https://api.brevo.com/v3/smtp/email');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'api-key: ' . $apiKey,
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($httpCode >= 200 && $httpCode < 300) {
        echo json_encode(["success" => true, "order_id" => $order_id]);
    } else {
        // This sends the specific error back to your screen alert
        echo json_encode([
            "success" => false, 
            "message" => "Brevo API Error (Code: $httpCode). Response: " . $response
        ]);
    }
} else {
    echo json_encode(["success" => false, "message" => "No data received by the server."]);
}
?>