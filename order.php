<?php
// Where you receive the order
$admin_email = "ebroshoponline@gmail.com"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = $_POST['name'];
    $phone   = $_POST['phone'];
    $payment = $_POST['paymentMethod'];
    $total   = $_POST['totalPrice'];
    $cartData = json_decode($_POST['cartData'], true);

    $subject = "📝 NEW ORDER: $name";
    
    // Receipt Design
    $message = "
    <html>
    <body style='font-family: monospace; padding: 20px; background-color: #f4f4f4;'>
        <div style='max-width: 450px; background: #fff; padding: 20px; border: 1px solid #000; margin: auto;'>
            <h2 style='text-align: center; color: #136835;'>EbRo-Shop Online</h2>
            <hr>
            <p><strong>NAME:</strong> $name</p>
            <p><strong>PHONE:</strong> $phone</p>
            <p><strong>PAYMENT:</strong> $payment</p>
            <br>
            <table style='width: 100%; border-collapse: collapse;'>
                <tr style='border-bottom: 2px solid #000;'>
                    <th style='text-align: left; padding-bottom: 5px;'>ITEM</th>
                    <th style='text-align: center;'>QTY</th>
                    <th style='text-align: right;'>PRICE</th>
                </tr>";

    foreach ($cartData as $item) {
        $message .= "
                <tr>
                    <td style='padding: 8px 0; border-bottom: 1px solid #eee;'>{$item['name']}</td>
                    <td style='text-align: center;'>{$item['qty']}</td>
                    <td style='text-align: right;'>ETB " . number_format($item['price'] * $item['qty'], 2) . "</td>
                </tr>";
    }

    $message .= "
            </table>
            <br>
            <div style='border-top: 2px dashed #136835; padding-top: 10px; text-align: right;'>
                <h3 style='margin: 0; color: #136835;'>TOTAL: ETB " . number_format($total, 2) . "</h3>
            </div>
        </div>
    </body>
    </html>";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: order-system@ebroshop.com" . "\r\n";

    if(mail($admin_email, $subject, $message, $headers)) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error"]);
    }
}
?>