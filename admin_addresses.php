<?php
include 'db.php';

// Security: Only allow Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// 1. Handle Search Logic
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// 2. SQL Query: Fetching names from 'users' and location from 'addresses'
// We use u.first_name and u.last_name for the "Full Name"
$sql = "SELECT u.first_name AS user_fname, u.last_name AS user_lname, u.email,
               a.adress1, a.address2, a.city, a.phone, a.country, a.zip_code
        FROM users u
        INNER JOIN addresses a ON u.id = a.user_id 
        WHERE (u.first_name LIKE '%$search%' 
        OR u.last_name LIKE '%$search%' 
        OR a.city LIKE '%$search%'
        OR a.phone LIKE '%$search%')
        ORDER BY u.last_name ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ebroshop | Address Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Inter', 'Segoe UI', sans-serif; background: #f1f5f9; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: #ffffff; border-radius: 20px; padding: 35px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border-top: 8px solid #136835; }
        
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 20px; }
        .top-bar h2 { margin: 0; color: #1e293b; font-size: 26px; }
        
        .search-box { display: flex; align-items: center; background: #f8fafc; padding: 8px 18px; border-radius: 50px; border: 2px solid #e2e8f0; width: 380px; transition: 0.3s; }
        .search-box:focus-within { border-color: #136835; background: #fff; }
        .search-box input { border: none; background: transparent; padding: 10px; outline: none; width: 100%; font-size: 14px; }
        .search-box i { color: #136835; }

        .table-wrapper { overflow-x: auto; border-radius: 15px; border: 1px solid #e2e8f0; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th { background: #f8fafc; padding: 18px; text-align: left; font-size: 12px; font-weight: 800; text-transform: uppercase; color: #64748b; border-bottom: 2px solid #e2e8f0; }
        td { padding: 18px; border-bottom: 1px solid #f1f5f9; font-size: 14px; vertical-align: middle; }
        
        .user-info { display: flex; flex-direction: column; }
        .user-name { font-weight: 700; color: #136835; font-size: 15px; }
        .user-email { font-size: 12px; color: #94a3b8; }

        .address-block { line-height: 1.6; color: #334155; }
        .city-badge { background: #eef6ff; color: #0076ad; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; }
        
        .phone-cell { font-family: monospace; font-weight: 700; color: #136835; }
        .back-link { text-decoration: none; color: #64748b; font-weight: 700; font-size: 14px; margin-bottom: 20px; display: inline-flex; align-items: center; gap: 8px; }
        .back-link:hover { color: #136835; }
    </style>
</head>
<body>

<div class="container">
    <a href="admin_dashboard.php" class="back-link"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>

    <div class="top-bar">
        <div>
            <h2><i class="fa fa-map-location-dot" style="color:#136835;"></i> Delivery Directory</h2>
            <p style="color:#64748b; margin: 5px 0 0; font-size: 14px;">Full customer names fetched from user accounts</p>
        </div>

        <form method="GET" class="search-box">
            <i class="fa fa-search"></i>
            <input type="text" name="search" placeholder="Search by name, email or city..." value="<?php echo htmlspecialchars($search); ?>">
        </form>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Customer (From Users)</th>
                    <th>Street Address</th>
                    <th>City & Zip</th>
                    <th>Phone & Country</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div class="user-info">
                                    <span class="user-name"><?php echo htmlspecialchars($row['user_fname'] . " " . $row['user_lname']); ?></span>
                                    <span class="user-email"><?php echo htmlspecialchars($row['email']); ?></span>
                                </div>
                            </td>
                            <td class="address-block">
                                <b>Line 1:</b> <?php echo htmlspecialchars($row['adress1']); ?><br>
                                <?php if(!empty($row['address2'])): ?>
                                    <b>Line 2:</b> <?php echo htmlspecialchars($row['address2']); ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="city-badge"><?php echo htmlspecialchars($row['city']); ?></span><br>
                                <small style="color:#94a3b8;">Zip: <?php echo htmlspecialchars($row['zip_code']); ?></small>
                            </td>
                            <td>
                                <div class="phone-cell"><?php echo htmlspecialchars($row['phone']); ?></div>
                                <div style="font-size: 11px; color:#64748b; text-transform: uppercase;"><?php echo htmlspecialchars($row['country']); ?></div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center; padding:50px; color:#94a3b8;">No addresses found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>