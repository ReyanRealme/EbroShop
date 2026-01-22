<?php
include 'db.php';

// Security
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// 1. ADVANCED SEARCH: Fix to allow spaces between first and last names
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Using CONCAT so 'Rebyu Dejene' matches the database
$sql = "SELECT id, first_name, last_name, email, role 
        FROM users 
        WHERE (CONCAT(first_name, ' ', last_name) LIKE '%$search%' 
        OR email LIKE '%$search%') 
        AND id != '".$_SESSION['user_id']."' 
        ORDER BY id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>User Manager | Ebroshop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Roboto, sans-serif; background: #f0f2f5; margin: 0; padding: 12px; }
        .admin-card { background: #fff; border-radius: 16px; padding: 18px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border-top: 6px solid #136835; }
        
        .header-section { margin-bottom: 20px; }
        .header-section h2 { margin: 0; font-size: 22px; color: #1e293b; display: flex; align-items: center; gap: 10px; }
        
        /* Mobile Search Bar */
        .search-container { display: flex; align-items: center; background: #f1f3f5; padding: 12px; border-radius: 12px; margin: 15px 0; border: 1px solid #e2e8f0; }
        .search-container input { border: none; background: transparent; width: 100%; outline: none; font-size: 16px; color: #333; }
        .search-container i { color: #136835; margin-right: 10px; }

        /* Scannable Table */
        .table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; min-width: 450px; }
        th { text-align: left; padding: 12px 10px; font-size: 11px; text-transform: uppercase; color: #64748b; background: #fafafa; border-bottom: 2px solid #eee; }
        td { padding: 15px 10px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        
        .user-id { font-size: 10px; color: #94a3b8; font-weight: bold; }
        .user-name { font-weight: 700; color: #136835; font-size: 15px; display: block; }
        .user-email { font-size: 12px; color: #64748b; }
        
        /* Role Badges */
        .role-badge { padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: 800; text-transform: uppercase; }
        .role-admin { background: #136835; color: white; }
        .role-user { background: #e2e8f0; color: #475569; }

        .back-nav { text-decoration: none; color: #64748b; font-size: 13px; font-weight: 600; display: inline-block; margin-bottom: 12px; }
        .action-btn { color: #136835; text-decoration: none; font-size: 18px; padding: 5px; }
    </style>
</head>
<body>

<div class="admin-card">
    <a href="admin_dashboard.php" class="back-nav"><i class="fa fa-arrow-left"></i> Dashboard</a>
    
    <div class="header-section">
        <h2><i class="fa fa-users"></i> User Management</h2>
    </div>

    <form method="GET" class="search-container">
        <i class="fa fa-search"></i>
        <input type="text" name="search" placeholder="Search name with space (e.g. Rebyu Dejene)" value="<?php echo htmlspecialchars($search); ?>">
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User Details</th>
                    <th>Account Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="user-id">#<?php echo $row['id']; ?></td>
                            <td>
                                <span class="user-name"><?php echo htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?></span>
                                <span class="user-email"><?php echo htmlspecialchars($row['email']); ?></span>
                            </td>
                            <td>
                                <span class="role-badge <?php echo (strtolower($row['role']) == 'admin') ? 'role-admin' : 'role-user'; ?>">
                                    <?php echo strtoupper($row['role']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="admin_roles.php?search=<?php echo urlencode($row['email']); ?>" class="action-btn" title="Change Permissions">
                                    <i class="fa fa-user-gear"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center; padding:30px; color:#94a3b8;">No users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>