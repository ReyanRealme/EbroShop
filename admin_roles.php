<?php
include 'db.php';

// Security: Only allow Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

$message = "";
$status = "";

// 1. Handle Role Update
if (isset($_POST['update_role'])) {
    $target_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $new_role = mysqli_real_escape_string($conn, $_POST['role_type']);

    $update = $conn->query("UPDATE users SET role = '$new_role' WHERE id = '$target_id'");
    if ($update) {
        $message = "Success! Role updated to " . strtoupper($new_role);
        $status = "success";
    } else {
        $message = "Error: " . $conn->error;
        $status = "error";
    }
}

// 2. Handle Search Logic (To find the user you want to change)
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$sql = "SELECT id, first_name, last_name, email, role FROM users 
        WHERE (first_name LIKE '%$search%' OR email LIKE '%$search%')
        ORDER BY role ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Role Control</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f8fafc; margin: 0; padding: 20px; color: #334155; }
        .container { max-width: 900px; margin: 0 auto; background: #ffffff; border-radius: 20px; padding: 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; gap: 20px; flex-wrap: wrap; }
        .title-area h2 { margin: 0; color: #136835; font-size: 24px; }
        
        /* Search Form */
        .search-form { display: flex; align-items: center; background: #f1f5f9; padding: 5px 15px; border-radius: 30px; border: 1px solid #cbd5e1; width: 350px; }
        .search-form input { border: none; background: transparent; padding: 10px; outline: none; width: 100%; font-size: 14px; }
        .search-form button { background: none; border: none; cursor: pointer; color: #136835; }

        /* Notification */
        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; font-weight: 600; }
        .success { background: #dcfce7; color: #166534; }
        .error { background: #fee2e2; color: #991b1b; }

        /* Table */
        .table-wrapper { overflow-x: auto; border-radius: 12px; border: 1px solid #e2e8f0; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th { background: #f8fafc; padding: 15px; text-align: left; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #475569; border-bottom: 2px solid #e2e8f0; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        
        /* Action Buttons */
        .role-btn { border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: 700; font-size: 11px; transition: 0.2s; text-transform: uppercase; }
        .btn-make-admin { background: #136835; color: white; margin-right: 5px; }
        .btn-make-user { background: #64748b; color: white; }
        .role-btn:hover { opacity: 0.8; transform: translateY(-1px); }

        .role-badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 800; }
        .badge-admin { background: #136835; color: white; }
        .badge-user { background: #e2e8f0; color: #475569; }

        .back-link { text-decoration: none; color: #136835; font-weight: 800; font-size: 13px; margin-bottom: 20px; display: inline-block; }
    </style>
</head>
<body>

<div class="container">
    <a href="admin_dashboard.php" class="back-link"><i class="fa fa-arrow-left"></i> Back to Panel</a>
    <div class="admin-header">
        <div class="title-area">
            <h2><i class="fa fa-user-shield"></i> Role Management</h2>
            <p style="color:#64748b; margin:5px 0 0; font-size:14px;">Find a user to grant or revoke admin access</p>
        </div>

        <form method="GET" class="search-form">
            <button type="submit"><i class="fa fa-search"></i></button>
            <input type="text" name="search" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($search); ?>">
        </form>
    </div>

    <?php if($message): ?>
        <div class="alert <?php echo $status; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>User Detail</th>
                    <th>Current Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($user = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <strong><?php echo $user['first_name'] . " " . $user['last_name']; ?></strong><br>
                                <small style="color:#64748b;"><?php echo $user['email']; ?></small>
                            </td>
                            <td>
                                <span class="role-badge <?php echo ($user['role'] == 'admin') ? 'badge-admin' : 'badge-user'; ?>">
                                    <?php echo strtoupper($user['role']); ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <?php if($user['role'] != 'admin'): ?>
                                        <button type="submit" name="update_role" value="1" class="role-btn btn-make-admin">
                                            <input type="hidden" name="role_type" value="admin">
                                            Make Admin
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" name="update_role" value="1" class="role-btn btn-make-user">
                                            <input type="hidden" name="role_type" value="user">
                                            Remove Admin
                                        </button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3" style="text-align:center; padding:40px; color:#94a3b8;">No users found for "<?php echo htmlspecialchars($search); ?>"</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>