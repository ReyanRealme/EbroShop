<?php
include 'db.php';

// Security
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// 1. ADVANCED SEARCH: Search by Name (with space), Email, or Phone
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$sql = "SELECT u.id, u.first_name, u.last_name, u.email, u.role, a.phone 
        FROM users u 
        LEFT JOIN addresses a ON u.id = a.user_id 
        WHERE (CONCAT(u.first_name, ' ', u.last_name) LIKE '%$search%' 
        OR u.email LIKE '%$search%' 
        OR a.phone LIKE '%$search%') 
        AND u.id != '".$_SESSION['user_id']."' 
        GROUP BY u.id 
        ORDER BY u.id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>User Management | Ebroshop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Roboto, sans-serif; background: #f0f2f5; margin: 0; padding: 12px; }
        .admin-card { background: #fff; border-radius: 16px; padding: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border-top: 6px solid #136835; }
        
        .search-container { display: flex; align-items: center; background: #f1f3f5; padding: 12px; border-radius: 12px; margin: 15px 0; border: 1px solid #e2e8f0; }
        .search-container input { border: none; background: transparent; width: 100%; outline: none; font-size: 16px; }
        
        .table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; min-width: 550px; }
        th { text-align: left; padding: 12px 8px; font-size: 11px; text-transform: uppercase; color: #64748b; background: #fafafa; border-bottom: 2px solid #eee; }
        td { padding: 12px 8px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        
        .user-name { font-weight: 700; color: #136835; font-size: 14px; display: block; }
        .phone-text { font-weight: 600; font-size: 13px; color: #334155; }
        .not-set { color: #dc3545; font-size: 11px; font-style: italic; }
        
        .role-badge { display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: 10px; font-weight: 800; text-transform: uppercase; }
        .role-admin { background: #136835; color: white; }
        .role-user { background: #e6fffa; color: #136835; }

        .action-link { color: #136835; text-decoration: none; font-weight: 800; font-size: 13px; border: 1px solid #136835; padding: 4px 12px; border-radius: 6px; }
        .back-nav { text-decoration: none; color: #64748b; font-size: 13px; font-weight: 600; display: inline-block; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="admin-card">
    <a href="admin_dashboard.php" class="back-nav"><i class="fa fa-arrow-left"></i> Dashboard</a>
    <h2 style="margin:0; font-size:22px; color:#1e293b;">User Management</h2>

    <form method="GET" class="search-container">
        <i class="fa fa-search" style="color:#136835; margin-right:10px;"></i>
        <input type="text" name="search" placeholder="Search by name, email, or phone..." value="<?php echo htmlspecialchars($search); ?>">
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Contact Phone</th>
                    <th>Status</th>
                    <th>Edit</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <span class="user-name"><?php echo htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?></span>
                                <small style="color:#94a3b8; font-size:10px;"><?php echo htmlspecialchars($row['email']); ?></small>
                            </td>
                            <td class="phone-text">
                                <?php if(!empty($row['phone'])): ?>
                                    <i class="fa fa-phone" style="font-size:11px; color:#136835;"></i> <?php echo htmlspecialchars($row['phone']); ?>
                                <?php else: ?>
                                    <span class="not-set">Not Set</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="role-badge <?php echo (strtolower($row['role']) == 'admin') ? 'role-admin' : 'role-user'; ?>">
                                    <?php echo strtoupper($row['role']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="admin_roles.php?search=<?php echo urlencode($row['email']); ?>" class="action-link">
                                    <i class="fa fa-user-gear"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center; padding:30px; color:#94a3b8;">No matching users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>