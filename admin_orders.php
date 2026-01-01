<?php
session_start();
// Security: Only allow admins (adjust based on your 'role' column)
include 'db.php';

// 1. UPDATE STATUS LOGIC
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];
    
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
    header("Location: admin_orders.php"); // Refresh
    exit();
}

// 2. FETCH ALL ORDERS
$sql = "SELECT o.*, u.first_name, u.last_name FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC";
$all_orders = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Orders</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f4f4f4; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #136835; color: white; }
        
        /* Status badge colors */
        .badge { padding: 5px 10px; border-radius: 4px; color: white; font-weight: bold; font-size: 12px; }
        .pending { background: #000; }
        .completed { background: #136835; }
        .cancelled { background: #e74c3c; }
        
        select { padding: 5px; border-radius: 4px; }
        .btn-save { background: #0066ff; color: white; border: none; padding: 6px 10px; cursor: pointer; border-radius: 4px; }
    </style>
</head>
<body>

    <h1>Admin Order Management</h1>

    <table>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Total Amount</th>
            <th>Current Status</th>
            <th>Action</th>
        </tr>
        <?php while($row = $all_orders->fetch_assoc()): ?>
        <tr>
            <td>#<?php echo $row['id']; ?></td>
            <td><?php echo $row['first_name'] . " " . $row['last_name']; ?></td>
            <td>ETB <?php echo number_format($row['total_amount'], 2); ?></td>
            <td>
                <?php 
                $s = strtolower($row['status']);
                $class = ($s == 'completed' || $s == 'delivered') ? 'completed' : (($s == 'cancelled') ? 'cancelled' : 'pending');
                ?>
                <span class="badge <?php echo $class; ?>"><?php echo $row['status']; ?></span>
            </td>
            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                    <select name="new_status">
                        <option value="Pending">Pending</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                    <button type="submit" name="update_status" class="btn-save">Update</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>