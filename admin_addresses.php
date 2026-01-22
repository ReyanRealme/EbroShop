<?php
include 'db.php';

// Security: Only allow Admin to see this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// 1. Handle Search Logic (Search by Name, City, or Phone)
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// 2. SQL Query: Fetch all users and their address attributes
// This uses the fields from your screenshot: first_name, last_name, address_line_1, city, phone
$sql = "SELECT id, first_name, last_name, email, phone, address_line_1, city, country 
        FROM users 
        WHERE (first_name LIKE '%$search%' 
        OR last_name LIKE '%$search%' 
        OR city LIKE '%$search%'
        OR phone LIKE '%$search%')
        ORDER BY city ASC";

$result = $conn->query($sql);
$total_entries = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Delivery Directory</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f8fafc; margin: 0; padding: 20px; color: #334155; }
        .container { max-width: 1100px; margin: 0 auto; background: #ffffff; border-radius: 20px; padding: 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; }
        .title-area h2 { margin: 0; color: #136835; font-size: 24px; text-transform: uppercase; letter-spacing: 1px; }
        .title-area p { margin: 5px 0 0; color: #64748b; font-size: 14px; }

        .search-form { display: flex; align-items: center; background: #f1f5f9; padding: 5px 15px; border-radius: 30px; border: 1px solid #cbd5e1; width: 350px; }
        .search-form input { border: none; background: transparent; padding: 10px; outline: none; width: 100%; font-size: 14px; }
        .search-form button { background: none; border: none; cursor: pointer; color: #136835; font-size: 16px; }

        .table-wrapper { overflow-x: auto; border-radius: 12px; border: 1px solid #e2e8f0; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th { background: #f8fafc; padding: 15px; text-align: left; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #475569; border-bottom: 2px solid #e2e8f0; }
        td { padding: 16px 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; vertical-align: middle; }
        tr:hover { background-color: #f1f5f9; }

        .city-badge { background: #eef6ff; color: #0076ad; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; text-transform: uppercase; }
        .user-name { font-weight: 700; color: #1e293b; display: block; }
        .phone-link { color: #136835; text-decoration: none; font-weight: 600; }
        
        .back-link { text-decoration: none; color: #0076ad; font-weight: 800; font-size: 13px; margin-bottom: 20px; display: inline-block; text-transform: uppercase; }
    </style>
</head>
<body>

<div class="container">
    <a href="admin_dashboard.php" class="back-link"><i class="fa fa-arrow-left"></i> Dashboard</a>

    <div class="admin-header">
        <div class="title-area">
            <h2><i class="fa fa-map-location-dot"></i> Delivery Directory</h2>
            <p>Managing <?php echo $total_entries; ?> registered shipping addresses</p>
        </div>

        <form method="GET" class="search-form">
            <button type="submit"><i class="fa fa-search"></i></button>
            <input type="text" name="search" placeholder="Search by name or city..." value="<?php echo htmlspecialchars($search); ?>">
        </form>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Street Address</th>
                    <th>City / Region</th>
                    <th>Phone Number</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <span class="user-name"><?php echo htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?></span>
                                <small style="color:#64748b;">ID: #<?php echo $row['id']; ?></small>
                            </td>
                            <td>
                                <div style="color: #334155; font-weight: 500;">
                                    <?php echo !empty($row['address_line_1']) ? htmlspecialchars($row['address_line_1']) : '<i style="color:#ccc;">Not filled</i>'; ?>
                                </div>
                                <small style="color: #94a3b8;"><?php echo htmlspecialchars($row['country']); ?></small>
                            </td>
                            <td>
                                <span class="city-badge"><?php echo htmlspecialchars($row['city'] ? $row['city'] : 'Pending'); ?></span>
                            </td>
                            <td>
                                <a href="tel:<?php echo $row['phone']; ?>" class="phone-link">
                                    <i class="fa fa-phone-flip" style="font-size: 12px;"></i> 
                                    <?php echo $row['phone'] ? htmlspecialchars($row['phone']) : 'N/A'; ?>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 60px; color: #94a3b8;">
                            <i class="fa fa-search-minus" style="font-size: 40px; margin-bottom: 15px; display: block;"></i>
                            No addresses found for "<?php echo htmlspecialchars($search); ?>"
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>