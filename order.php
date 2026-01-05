<?php
session_start();
error_reporting(0);
header('Content-Type: application/json');

include 'db.php'; // Needed to find the user's email if session is gone

$apiKey = getenv('BREVO_API_KEY'); 
$input = json_decode(file_get_contents('php://input'), true);

if ($input && $apiKey) {
    $name = mysqli_real_escape_string($conn, $input['name']);
    $phone = $input['phone'];
    $payment = $input['payment'];
    $total = $input['total'];
    $cart = $input['cart'];
    $order_id = rand(1000, 9999); 

    // --- LOGIC TO FIND USER EMAIL ---
    $customerEmail = isset($_SESSION['email']) ? $_SESSION['email'] : null;

    if (!$customerEmail) {
        // Look up email by the name provided in the form
        $search = "SELECT email FROM users WHERE first_name LIKE '%$name%' LIMIT 1";
        $res = $conn->query($search);
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $customerEmail = $row['email'];
        } else {
            // Final fallback to you so the order isn't lost
            $customerEmail = "ebroshoponline@gmail.com"; 
        }
    }

    // Build Table for Email
    $rows = "";
    foreach($cart as $p) {
        $st = $p['price'] * $p['qty'];
        $rows .= "<tr>
                    <td style='padding:8px; border:1px solid #ddd;'>{$p['name']}</td>
                    <td style='padding:8px; border:1px solid #ddd; text-align:center;'>{$p['qty']}</td>
                    <td style='padding:8px; border:1px solid #ddd; text-align:right;'>ETB " . number_format($st, 2) . "</td>
                  </tr>";
    }

    $logoUrl = "https://res.cloudinary.com/die8hxris/image/upload/v1767382208/n8ixozf4lj5wfhtz2val.jpg";
    $data = [
        "sender" => ["name" => "EbRo Shop", "email" => "ebroshoponline@gmail.com"],
        "to" => [["email" => $customerEmail, "name" => $name]],
        "subject" => "Your EbRo Shop Receipt #$order_id",
        "htmlContent" => "
            <div style='font-family: Arial; max-width: 600px; margin: auto; border: 1px solid #eee; padding: 20px;'>
                <img src='$logoUrl' style='width: 180px; margin-bottom: 20px;'>
                <h2 style='color: #136835;'>Order Confirmed!</h2>
                <p>Hello $name, thank you for shopping with us. Your order is being prepared.</p>
                <table style='width: 100%; border-collapse: collapse;'>
                    <tr style='background: #f4f4f4;'><th>Item</th><th>Qty</th><th>Subtotal</th></tr>
                    $rows
                </table>
                <h3 style='text-align: right;'>Total: ETB " . number_format($total, 2) . "</h3>
                <p><b>Phone:</b> $phone<br><b>Payment:</b> $payment</p>
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
?