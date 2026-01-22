<?php
include 'db.php';

// Security
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// 1. Improved Search for Full Names with spaces
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
// We use CONCAT to combine first and last name so you can search "Rebyu Dejene"
$search_query = "CONCAT(u.first_name, ' ', u.last_name) LIKE '%$search%' 
                 OR a.city LIKE '%$search%' 
                 OR a.phone LIKE '%$search%'";

$sql = "SELECT u.first_name, u.last_name, u.email,
               a.address1, a.address2, a.city, a.phone, a.country
        FROM users u
        INNER JOIN addresses a ON u.id = a.user_id 
        WHERE ($search_query)
        ORDER BY u.id DESC";

$result = $conn->query($sql);

// Fallback for column spelling (adress1 vs address1)
if (!$result) {
    $sql = str_replace("address1", "adress1", $sql);
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Delivery Directory</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Android/Mobile Optimized Styling */
        body { font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 10px; }
        .container { background: white; border-radius: 12px; padding: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); border-top: 5px solid #136835; }
        
        .header { margin-bottom: 15px; }
        .header h2 { font-size: 20px; color: #136835; margin: 0 0 10px 0; display: flex; align-items: center; gap: 8px; }
        
        /* Mobile Search Bar */
        .search-box { display: flex; background: #f8f9fa; border: 1px solid #ddd; border-radius: 8px; padding: 8px 12px; align-items: center; }
        .search-box input { border: none; background: transparent; outline: none; width: 100%; font-size: 16px; color: #333; }
        
        /* Scannable Card-style Table for Android */
        .table-responsive { overflow-x: auto; margin-top: 15px; }
        table { width: 100%; border-collapse: collapse; min-width: 500px; }
        th { text-align: left; padding: 10px; background: #f8f9fa; color: #666; font-size: 11px; text-transform: uppercase; border-bottom: 2px solid #eee; }
        td { padding: 12px 10px; border-bottom: 1px solid #f0f0f0; vertical-align: top; }
        
        .name-main { font-weight: 700; color: #136835; font-size: 14px; display: block; }
        .email-sub { font-size: 11px; color: #888; }
        .addr-text { font-size: 13px; color: #444; line-height: 1.4; }
        .city-label { display: inline-block; background: #e7f3ed; color: #136835; padding: 2px 6px; border-radius: 4px; font-size: 11px; font-weight: bold; margin-bottom: 3px; }
        .phone-link { color: #136835; text-decoration: none; font-weight: 700; font-size: 13px; display: block; }

        .back-nav { font-size: 13px; text-decoration: none; color: #666; margin-bottom: 10px; display: inline-block; }
    </style>
</head>
<body>

<div class="container">
    <a href="admin_dashboard.php" class="back-nav"><i class="fa fa-arrow-left"></i> Dashboard</a>
    
    <div class="header">
        <h2><i class="fa fa-truck-fast"></i> Delivery Directory</h2>
        <form method="GET" class="search-box">
            <input type="text" name="search" placeholder="Search name, phone, or city..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" style="background:none; border:none; color:#136835;"><i class="fa fa-search"></i></button>
        </form>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Address</th>
                    <th>Location</th>
                    <th>Contact</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <span class="name-main"><?php echo htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?></span>
                                <span class="email-sub"><?php echo htmlspecialchars($row['email']); ?></span>
                            </td>
                            <td class="addr-text">
                                <?php 
                                    $main_addr = isset($row['address1']) ? $row['address1'] : $row['adress1'];
                                    echo htmlspecialchars($main_addr); 
                                ?><br>
                                <small style="color:#999;"><?php echo htmlspecialchars($row['address2'] ?? ''); ?></small>
                            </td>
                            <td>
                                <span class="city-label"><?php echo htmlspecialchars($row['city']); ?></span><br>
                                <small style="color:#999; font-size:10px;"><?php echo htmlspecialchars($row['country']); ?></small>
                            </td>
                            <td>
                                <a href="tel:<?php echo $row['phone']; ?>" class="phone-link">
                                    <i class="fa fa-phone"></i> <?php echo htmlspecialchars($row['phone']); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center; padding:30px; color:#999;">No results found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>