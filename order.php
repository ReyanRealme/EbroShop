<?php
// SETTINGS
$admin_email = "ebroshoponline@gmial.com"; // YOUR EMAIL HERE
$shop_name = "EbRo-Shop";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect Form Data
    $name = $_POST['name'] ?? 'Not Provided';
    $phone = $_POST['phone'] ?? 'Not Provided';
    $address = $_POST['address'] ?? 'Not Provided';
    $payment = $_POST['paymentMethod'] ?? 'Not Provided';
    $total = $_POST['totalPrice'] ?? '0';
    $cartData = json_decode($_POST['cartData'], true);

    // Build the "Handwritten Pen" Style Email
    $subject = "New Order from $name";
    
    $message = "
    <html>
    <body style='background-color: #f4f4f4; padding: 20px; font-family: \"Courier New\", Courier, monospace;'>
        <div style='max-width: 450px; margin: auto; background: #fff; padding: 25px; border: 1px solid #ddd; box-shadow: 5px 5px 0px #ccc;'>
            
            <div style='text-align: center; border-bottom: 2px dashed #136835; padding-bottom: 10px; margin-bottom: 20px;'>
                <h2 style='margin: 0; color: #136835;'>$shop_name RECEIPT</h2>
                <p style='margin: 5px 0; font-size: 12px;'>Date: " . date("d M Y, h:i A") . "</p>
            </div>

            <div style='margin-bottom: 20px; line-height: 1.6;'>
                <p style='margin: 5px 0;'><strong>CUSTOMER NAME:</strong> $name</p>
                <p style='margin: 5px 0;'><strong>PHONE NUMBER:</strong> $phone</p>
                <p style='margin: 5px 0;'><strong>ADDRESS:</strong> $address</p>
                <p style='margin: 5px 0;'><strong>PAYMENT:</strong> $payment</p>
            </div>

            <div style='border-top: 1px solid #000; padding-top: 10px;'>
                <h3 style='font-size: 16px; text-decoration: underline;'>ORDER ITEMS:</h3>";

    foreach ($cartData as $item) {
        $itemTotal = $item['price'] * $item['qty'];
        $message .= "
                <div style='display: flex; justify-content: space-between; margin-bottom: 8px;'>
                    <span style='flex: 2;'>" . $item['name'] . " (x" . $item['qty'] . ")</span>
                    <span style='flex: 1; text-align: right;'>$" . number_format($itemTotal, 2) . "</span>
                </div>";
    }

    $message .= "
            </div>

            <div style='margin-top: 20px; border-top: 2px dashed #136835; padding-top: 15px;'>
                <div style='display: flex; justify-content: space-between; font-size: 20px; font-weight: bold; color: #136835;'>
                    <span>GRAND TOTAL:</span>
                    <span>$" . number_format($total, 2) . "</span>
                </div>
            </div>

            <div style='text-align: center; margin-top: 30px; font-style: italic; font-size: 12px; color: #666;'>
                * Thank You for Shopping! *
            </div>
        </div>
    </body>
    </html>";

    // Headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: orders@ebroshop.com" . "\r\n";

    // 1. Send to Admin (You)
    mail($admin_email, $subject, $message, $headers);

    // 2. Send to Customer (Optional - requires email field in form)
    if(isset($_POST['email']) && !empty($_POST['email'])) {
        mail($_POST['email'], "Order Confirmation - EbRo-Shop", $message, $headers);
    }

    echo json_encode(["status" => "success"]);
}
?>