<?php
include 'db.php';

// Security: Only allow Admin to see this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// 1. Handle Search Logic
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// 2. Fetch Users: If $search is empty, it selects ALL users.
$sql = "SELECT id, first_name, last_name, email, phone, role, created_at 
        FROM users 
        WHERE (first_name LIKE '%$search%' 
        OR last_name LIKE '%$search%' 
        OR email LIKE '%$search%')
        ORDER BY created_at DESC";

$result = $conn->query($sql);
$total_found = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ebroshop Admin | User Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; margin: 0; padding: 20px; color: #334155; }
        .container { max-width: 1100px; margin: 0 auto; background: #ffffff; border-radius: 20px; padding: 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        
        /* Header & Search Area */
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; gap: 20px; flex-wrap: wrap; }
        .title-area h2 { margin: 0; color: #1e293b; font-size: 24px; }
        .title-area p { margin: 5px 0 0; color: #64748b; font-size: 14px; }

        .search-form { display: flex; align-items: center; background: #f1f5f9; padding: 5px 15px; border-radius: 30px; border: 1px solid #cbd5e1; width: 350px; transition: 0.3s; }
        .search-form:focus-within { background: #fff; border-color: #185282; box-shadow: 0 0 0 4px rgba(24, 82, 130, 0.1); }
        .search-form input { border: none; background: transparent; padding: 10px; outline: none; width: 100%; font-size: 14px; }
        .search-form button { background: none; border: none; cursor: pointer; color: #64748b; font-size: 16px; }

        /* Modern Table Styling */
        .table-wrapper { overflow-x: auto; border-radius: 12px; border: 1px solid #e2e8f0; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th { background: #f8fafc; padding: 15px; text-align: left; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; border-bottom: 1px solid #e2e8f0; }
        td { padding: 16px 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background-color: #f1f5f9; }

        .user-info { display: flex; align-items: center; gap: 12px; }
        .avatar { width: 35px; height: 35px; background: #185282; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; }
        
        .role-pill { padding: 4px 12px; border-radius: 50px; font-size: 11px; font-weight: 700; }
        .role-admin { background: #dcfce7; color: #15803d; }
        .role-user { background: #f1f5f9; color: #475569; }

        .no-results { text-align: center; padding: 60px 0; color: #94a3b8; }
        .back-link { text-decoration: none; color: #185282; font-weight: 600; font-size: 14px; display: inline-flex; align-items: center; margin-bottom: 20px; }
        .back-link i { margin-right: 8px; }
    </style>
</head>
<body>

<div class="container">
    <a href="admin_dashboard.php" class="back-link"><i class="fa fa-arrow-left"></i> Back to Control Center</a>

    <div class="admin-header">
        <div class="title-area">
            <h2>User Management</h2>
            <p>Showing <?php echo $total_found; ?> total members registered in Ebroshop</p>
        </div>
        <form method="GET" class="search-form">
            <button type="submit"><i class="fa fa-search"></i></button>
            <input type="text" name="search" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($search); ?>">
            <?php if($search): ?>
                <a href="admin_users.php" style="color:#94a3b8; text-decoration:none; font-size:12px; margin-left:5px;">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Email Address</th>
                    <th>Phone Number</th>
                    <th>Account Level</th>
                    <th>Joined Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($user = $result->fetch_assoc()): 
                        $initial = strtoupper(substr($user['first_name'], 0, 1));
                    ?>
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="avatar"><?php echo $initial; ?></div>
                                    <div>
                                        <div style="font-weight: 600; color: #1e293b;"><?php echo $user['first_name'] . " " . $user['last_name']; ?></div>
                                        <div style="font-size: 12px; color: #64748b;">ID: #<?php echo $user['id']; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td style="color: #1e293b; font-weight: 500;"><?php echo $user['email']; ?></td>
                            <td><?php echo $user['phone'] ? $user['phone'] : '<span style="color:#cbd5e1">Not set</span>'; ?></td>
                            <td>
                                <span class="role-pill <?php echo ($user['role'] == 'admin') ? 'role-admin' : 'role-user'; ?>">
                                    <?php echo strtoupper($user['role']); ?>
                                </span>
                            </td>
                            <td style="color: #64748b;"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="no-results">
                            <i class="fa fa-users-slash" style="font-size: 40px; margin-bottom: 15px; display: block;"></i>
                            No users found matching <strong>"<?php echo htmlspecialchars($search); ?>"</strong>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>