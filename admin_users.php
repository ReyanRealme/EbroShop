<?php
include 'db.php';

// Security
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// SQL compatible with only_full_group_by mode
$sql = "SELECT u.id, u.first_name, u.last_name, u.email, MAX(a.phone) as phone
        FROM users u 
        LEFT JOIN addresses a ON u.id = a.user_id 
        WHERE (CONCAT(u.first_name, ' ', u.last_name) LIKE '%$search%' 
        OR u.email LIKE '%$search%' 
        OR a.phone LIKE '%$search%') 
        AND u.id != '".$_SESSION['user_id']."' 
        GROUP BY u.id, u.first_name, u.last_name, u.email
        ORDER BY u.id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Customer Directory | Ebroshop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Roboto, sans-serif; background: #f0f2f5; margin: 0; padding: 12px; }
        .admin-card { background: #fff; border-radius: 16px; padding: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border-top: 6px solid #136835; }
        
        /* Search Bar */
        .search-container { display: flex; align-items: center; background: #f1f3f5; padding: 12px; border-radius: 12px; margin: 15px 0; border: 1px solid #e2e8f0; }
        .search-container input { border: none; background: transparent; width: 100%; outline: none; font-size: 16px; font-weight: 600; }
        
        /* Table Layout */
        .table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; min-width: 400px; }
        th { text-align: left; padding: 12px 8px; font-size: 11px; text-transform: uppercase; color: #64748b; background: #fafafa; border-bottom: 2px solid #eee; }
        td { padding: 15px 8px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        
        /* BOLD TEXT STYLES */
        .bold-name { font-weight: 800; color: #136835; font-size: 15px; display: block; }
        .bold-email { font-weight: 700; color: #1e293b; font-size: 13px; display: block; margin-top: 2px; }
        .bold-phone { font-weight: 800; color: #334155; font-size: 14px; }
        
        .not-set { color: #dc3545; font-weight: 800; font-size: 12px; }
        .back-nav { text-decoration: none; color: #64748b; font-size: 13px; font-weight: 600; display: inline-block; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="admin-card">
    <a href="admin_dashboard.php" class="back-nav"><i class="fa fa-arrow-left"></i> Dashboard</a>
    <h2 style="margin:0; font-size:22px; color:#1e293b;">Customer Directory</h2>

    <form method="GET" class="search-container">
        <i class="fa fa-search" style="color:#136835; margin-right:10px;"></i>
        <input type="text" name="search" placeholder="Search full name or email..." value="<?php echo htmlspecialchars($search); ?>">
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Email Account</th>
                    <th>Phone Number</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <span class="bold-name"><?php echo htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?></span>
                            </td>
                            <td>
                                <span class="bold-email"><?php echo htmlspecialchars($row['email']); ?></span>
                            </td>
                            <td>
                                <?php if(!empty($row['phone'])): ?>
                                    <span class="bold-phone"><?php echo htmlspecialchars($row['phone']); ?></span>
                                <?php else: ?>
                                    <span class="not-set">NOT SET</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3" style="text-align:center; padding:40px; color:#94a3b8; font-weight:bold;">No records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>