<?php
$admin_email = "ebroshoponline@gmail.com"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $payment = $_POST['paymentMethod'];
    $total = $_POST['totalPrice'];
    $cartData = json_decode($_POST['cartData'], true);

    $subject = "📝 New Receipt: $name";
    
    $message = "
    <html>
    <body style='font-family: sans-serif; line-height: 1.6;'>
        <div style='max-width: 500px; border: 1px solid #ddd; padding: 20px;'>
            <h2 style='color: #136835; text-align: center;'>EbRo-Shop Order</h2>
            <p><strong>Customer:</strong> $name</p>
            <p><strong>Phone:</strong> $phone</p>
            <p><strong>Payment:</strong> $payment</p>
            <hr>
            <table style='width: 100%; border-collapse: collapse;'>
                <thead>
                    <tr style='background: #f4f4f4;'>
                        <th style='padding: 8px; text-align: left;'>Item</th>
                        <th style='padding: 8px; text-align: center;'>Qty</th>
                        <th style='padding: 8px; text-align: right;'>Total</th>
                    </tr>
                </thead>
                <tbody>";

    foreach ($cartData as $item) {
        $sub = $item['price'] * $item['qty'];
        $message .= "
                <tr>
                    <td style='padding: 8px; border-bottom: 1px solid #eee;'>{$item['name']}</td>
                    <td style='padding: 8px; border-bottom: 1px solid #eee; text-align: center;'>{$item['qty']}</td>
                    <td style='padding: 8px; border-bottom: 1px solid #eee; text-align: right;'>ETB " . number_format($sub, 2) . "</td>
                </tr>";
    }

    $message .= "
                </tbody>
            </table>
            <h3 style='text-align: right; color: #136835;'>Grand Total: ETB " . number_format($total, 2) . "</h3>
        </div>
    </body>
    </html>";

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: EbRo-Shop <order@ebroshop.onrender.com>\r\n";

    mail($admin_email, $subject, $message, $headers);
}
?>