<?php
$admin_email = "ebroshoponline@gmail.com"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? 'Guest';
    $phone = $_POST['phone'] ?? 'N/A';
    $payment = $_POST['paymentMethod'] ?? 'N/A';
    $total = $_POST['totalPrice'] ?? '0';
    $cartData = json_decode($_POST['cartData'], true);

    $message = "<html><body>";
    $message .= "<h2>New Order from $name</h2>";
    $message .= "<p>Phone: $phone | Payment: $payment</p>";
    $message .= "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
    $message .= "<tr><th>Item</th><th>Qty</th><th>Price</th></tr>";
    
    foreach ($cartData as $item) {
        $message .= "<tr><td>{$item['name']}</td><td>{$item['qty']}</td><td>ETB " . ($item['price'] * $item['qty']) . "</td></tr>";
    }
    
    $message .= "</table><h3>Total: ETB $total</h3></body></html>";

    $headers = "MIME-Version: 1.0\r\nContent-type:text/html;charset=UTF-8\r\nFrom: EbRo-Shop <order@ebroshop.onrender.com>";
    mail($admin_email, "Receipt for $name", $message, $headers);
}
?>