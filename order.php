<?php
header('Content-Type: application/json');

// 1. YOUR BREVO API KEY
$apiKey = 'BREVO_API_KEY'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $payment = $_POST['paymentMethod'];
    $total = $_POST['totalPrice'];
    $cartData = json_decode($_POST['cartData'], true);

    $tableRows = "";
    foreach ($cartData as $item) {
        $rowTotal = $item['price'] * $item['qty'];
        $tableRows .= "<tr>
            <td style='padding:8px; border:1px solid #ddd;'>{$item['name']}</td>
            <td style='padding:8px; border:1px solid #ddd; text-align:center;'>{$item['qty']}</td>
            <td style='padding:8px; border:1px solid #ddd; text-align:right;'>ETB " . number_format($rowTotal, 2) . "</td>
        </tr>";
    }

    $data = [
        "sender" => ["name" => "EbRo-Shop", "email" => "system@ebroshop.com"],
        "to" => [["email" => "ebroshoponline@gmail.com"]],
        "subject" => "New Order: $name",
        "htmlContent" => "<html><body>
            <h2>Order Details</h2>
            <p><strong>Customer:</strong> $name</p>
            <p><strong>Phone:</strong> $phone</p>
            <p><strong>Payment:</strong> $payment</p>
            <table border='1' style='border-collapse:collapse; width:100%;'>
                <tr style='background:#f4f4f4;'><th>Item</th><th>Qty</th><th>Subtotal</th></tr>
                $tableRows
            </table>
            <h3>Total: ETB " . number_format($total, 2) . "</h3>
        </body></html>"
    ];

    $ch = curl_init('https://api.brevo.com/v3/smtp/email');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['api-key: '.$apiKey, 'Content-Type: application/json']);
    curl_exec($ch);
    curl_close($ch);
    
    echo json_encode(["status" => "ok"]);
}
?>