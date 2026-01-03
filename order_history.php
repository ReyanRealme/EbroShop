<?php

session_start();
// Security: Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

include 'db.php';
$user_id = $_SESSION['user_id'];

// Fetch orders for this specific user
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Order History</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        .order-card { border: 1px solid #eee; border-radius: 8px; padding: 15px; margin-bottom: 15px; background: #fafafa; position: relative; }
        .order-id { font-weight: bold; color: #007bff; }
        .order-date { font-size: 12px; color: #888; display: block; margin-bottom: 8px; }
        .order-total { font-size: 16px; font-weight: bold; color: #333; }
        .status { float: right; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-completed { background: #d4edda; color: #155724; }
        .no-orders { text-align: center; color: #777; padding: 40px; }
        .back-btn { display: block; text-align: center; margin-top: 20px; text-decoration: none; color: #007bff; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h2>My Order History</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while($order = $result->fetch_assoc()): ?>
            <div class="order-card">
                <span class="status status-<?php echo strtolower($order['status']); ?>">
                    <?php echo $order['status']; ?>
                </span>
                <span class="order-id">Order #<?php echo $order['id']; ?></span>
                <span class="order-date"><?php echo date('F j, Y, g:i a', strtotime($order['created_at'])); ?></span>
                <div class="order-total">Total: ETB <?php echo number_format($order['total_amount'], 2); ?></div>
                <p style="font-size: 14px; color: #555; margin: 5px 0 0;">Payment: <?php echo $order['payment_method']; ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="no-orders">
            <p>You haven't placed any orders yet.</p>
        </div>
    <?php endif; ?>

    <a href="home.html" class="back-btn">← Back to Shop</a>
</div>

</body>
</html>