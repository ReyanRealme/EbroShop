<?php
include 'db.php';

// Security: Only allow Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

/**
 * We select first_name and last_name from USERS (u)
 * and address details from ADDRESSES (a)
 * I have updated 'adress1' to 'address1' based on standard naming.
 */
$sql = "SELECT u.first_name, u.last_name, u.email,
               a.address1, a.address2, a.city, a.phone, a.country, a.zip_code
        FROM users u
        INNER JOIN addresses a ON u.id = a.user_id 
        WHERE (u.first_name LIKE '%$search%' 
        OR u.last_name LIKE '%$search%' 
        OR a.city LIKE '%$search%'
        OR a.phone LIKE '%$search%')
        ORDER BY u.id DESC";

// If the query fails, it's because of the column name. Let's try the other spelling automatically.
$result = $conn->query($sql);

if (!$result) {
    // If 'address1' failed, try 'adress1' (one 'd')
    $sql = str_replace("address1", "adress1", $sql);
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ebroshop | Address Directory</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; margin: 0; padding: 20px; }
        .container { max-width: 1100px; margin: auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.1); border-top: 6px solid #136835; }
        
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .search-box { display: flex; align-items: center; background: #f8f9fa; padding: 8px 20px; border-radius: 50px; border: 1px solid #ddd; width: 350px; }
        .search-box input { border: none; background: transparent; outline: none; padding: 5px; width: 100%; }
        
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; background: #f8f9fa; color: #666; text-transform: uppercase; font-size: 12px; border-bottom: 2px solid #eee; }
        td { padding: 15px; border-bottom: 1px solid #f1f1f1; font-size: 14px; }
        
        .user-name { font-weight: bold; color: #136835; display: block; }
        .email-sub { font-size: 12px; color: #999; }
        .addr-main { color: #333; font-weight: 500; }
        .city-badge { background: #e7f3ed; color: #136835; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }
        
        .back-link { text-decoration: none; color: #666; font-size: 13px; margin-bottom: 20px; display: inline-block; }
        .back-link:hover { color: #136835; }
    </style>
</head>
<body>

<div class="container">
    <a href="admin_dashboard.php" class="back-link"><i class="fa fa-arrow-left"></i> Dashboard</a>

    <div class="header-flex">
        <h2><i class="fa fa-map-marker-alt" style="color:#136835;"></i> Delivery Directory</h2>
        <form method="GET" class="search-box">
            <i class="fa fa-search" style="color:#136835;"></i>
            <input type="text" name="search" placeholder="Search customer or city..." value="<?php echo htmlspecialchars($search); ?>">
        </form>
    </div>

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Customer (Account)</th>
                    <th>Full Delivery Address</th>
                    <th>City & Zip</th>
                    <th>Contact</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <span class="user-name"><?php echo htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?></span>
                                <span class="email-sub"><?php echo htmlspecialchars($row['email']); ?></span>
                            </td>
                            <td>
                                <div class="addr-main">
                                    <?php 
                                        // We use a dynamic check to display whichever column exists
                                        $display_addr = isset($row['address1']) ? $row['address1'] : $row['adress1'];
                                        echo htmlspecialchars($display_addr); 
                                    ?>
                                </div>
                                <div style="font-size: 12px; color:#777;"><?php echo htmlspecialchars($row['address2']); ?></div>
                            </td>
                            <td>
                                <span class="city-tag"><?php echo htmlspecialchars($row['city']); ?></span><br>
                                <small>Zip: <?php echo htmlspecialchars($row['zip_code']); ?></small>
                            </td>
                            <td>
                                <div style="font-weight:bold; color:#136835;"><?php echo htmlspecialchars($row['phone']); ?></div>
                                <div style="font-size:11px; text-transform:uppercase; color:#999;"><?php echo htmlspecialchars($row['country']); ?></div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center; padding:40px; color:#999;">No records found. Check if address table is empty.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>