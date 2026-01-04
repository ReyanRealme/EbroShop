<?php
// SETTINGS
$admin_email = "ebroshoponline@gmail.com"; 
$shop_name = "EbRo-Shop Online";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get Data from the JavaScript
    $name    = $_POST['name'] ?? 'Not Provided';
    $phone   = $_POST['phone'] ?? 'Not Provided';
    $address = $_POST['address'] ?? 'Not Provided';
    $payment = $_POST['paymentMethod'] ?? 'Not Provided';
    $total   = $_POST['totalPrice'] ?? '0';
    $cartData = json_decode($_POST['cartData'], true);

    // Design the "Pen-Written" Style Email
    $subject = "📝 NEW ORDER: $name";
    
    $message = "
    <html>
    <body style='background-color: #fdfdfd; padding: 15px; font-family: \"Courier New\", Courier, monospace;'>
        <div style='max-width: 450px; margin: auto; background: #fff; padding: 25px; border: 1px solid #000; box-shadow: 5px 5px 0px #eee;'>
            
            <div style='text-align: center; border-bottom: 2px dashed #136835; padding-bottom: 10px; margin-bottom: 20px;'>
                <h2 style='margin: 0; color: #136835;'>$shop_name</h2>
                <p style='margin: 5px 0; font-size: 12px;'>Date: " . date("d M Y, h:i A") . "</p>
            </div>

            <div style='margin-bottom: 20px; line-height: 1.6;'>
                <p style='margin: 4px 0;'><strong>NAME:</strong> $name</p>
                <p style='margin: 4px 0;'><strong>PHONE:</strong> $phone</p>
                <p style='margin: 4px 0;'><strong>ADDRESS:</strong> $address</p>
                <p style='margin: 4px 0;'><strong>PAYMENT:</strong> $payment</p>
            </div>

            <table style='width: 100%; border-collapse: collapse;'>
                <tr style='border-bottom: 1px solid #000;'>
                    <th style='text-align: left; padding: 5px;'>ITEM</th>
                    <th style='text-align: center; padding: 5px;'>QTY</th>
                    <th style='text-align: right; padding: 5px;'>PRICE</th>
                </tr>";

    foreach ($cartData as $item) {
        $itemTotal = $item['price'] * $item['qty'];
        $message .= "
                <tr>
                    <td style='padding: 8px 5px; font-size: 14px;'>" . $item['name'] . "</td>
                    <td style='padding: 8px 5px; text-align: center;'>" . $item['qty'] . "</td>
                    <td style='padding: 8px 5px; text-align: right;'>$" . number_format($itemTotal, 2) . "</td>
                </tr>";
    }

    $message .= "
            </table>

            <div style='margin-top: 20px; border-top: 2px dashed #136835; padding-top: 15px;'>
                <div style='display: flex; justify-content: space-between; font-size: 20px; font-weight: bold; color: #136835;'>
                    <span>GRAND TOTAL:</span>
                    <span style='float: right;'>$" . number_format($total, 2) . "</span>
                </div>
            </div>

            <div style='text-align: center; margin-top: 30px; font-size: 11px; color: #777;'>
                -- END OF RECEIPT --
            </div>
        </div>
    </body>
    </html>";

    // Headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: orders@ebroshoponline.com" . "\r\n";

    // Send the Email
    mail($admin_email, $subject, $message, $headers);

    echo json_encode(["status" => "success"]);
}
?>