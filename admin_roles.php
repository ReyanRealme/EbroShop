<?php
include 'db.php';

// Security: Only allow Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

$message = "";
$status = "";

// 1. Handle Role Update with Error-Catching
if (isset($_POST['update_role'])) {
    $target_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $role_to_try = mysqli_real_escape_string($conn, $_POST['role_type']);

    // We try to update. If it fails, we try the alternative case (User vs user)
    try {
        $sql = "UPDATE users SET role = '$role_to_try' WHERE id = '$target_id'";
        if ($conn->query($sql)) {
            $message = "Success! Role updated to " . strtoupper($role_to_try);
            $status = "success";
        }
    } catch (mysqli_sql_exception $e) {
        // If 'user' fails, your DB likely wants 'User' (Capital U)
        $alt_role = ($role_to_try == 'user') ? 'User' : 'admin';
        $sql_alt = "UPDATE users SET role = '$alt_role' WHERE id = '$target_id'";
        
        if ($conn->query($sql_alt)) {
            $message = "Success! Role updated to " . strtoupper($alt_role);
            $status = "success";
        } else {
            $message = "Database Error: " . $e->getMessage();
            $status = "error";
        }
    }
}

// 2. Fetch Users for the Design
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
    <title>Role Management | Ebroshop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f0f4f8; margin: 0; padding: 20px; color: #334155; }
        .container { max-width: 900px; margin: 20px auto; background: #ffffff; border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border-top: 8px solid #136835; }
        
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; gap: 20px; flex-wrap: wrap; }
        .title-area h2 { margin: 0; color: #1e293b; font-size: 26px; letter-spacing: -0.5px; }
        
        .search-form { display: flex; align-items: center; background: #f8fafc; padding: 5px 15px; border-radius: 50px; border: 2px solid #e2e8f0; width: 350px; transition: 0.3s; }
        .search-form:focus-within { border-color: #136835; background: #fff; }
        .search-form input { border: none; background: transparent; padding: 10px; outline: none; width: 100%; font-size: 14px; }
        .search-form button { background: none; border: none; cursor: pointer; color: #136835; font-size: 18px; }

        .alert { padding: 15px; border-radius: 12px; margin-bottom: 25px; text-align: center; font-weight: 700; border: 1px solid transparent; }
        .success { background: #ecfdf5; color: #065f46; border-color: #a7f3d0; }
        .error { background: #fef2f2; color: #991b1b; border-color: #fecaca; }

        .table-wrapper { overflow-x: auto; border-radius: 15px; border: 1px solid #e2e8f0; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th { background: #f8fafc; padding: 18px; text-align: left; font-size: 12px; font-weight: 800; text-transform: uppercase; color: #64748b; border-bottom: 2px solid #e2e8f0; }
        td { padding: 18px; border-bottom: 1px solid #f1f5f9; font-size: 14px; vertical-align: middle; }
        
        .role-badge { padding: 6px 12px; border-radius: 50px; font-size: 11px; font-weight: 900; letter-spacing: 0.5px; }
        .badge-admin { background: #136835; color: white; }
        .badge-user { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        .action-btn { border: none; padding: 10px 16px; border-radius: 10px; cursor: pointer; font-weight: 700; font-size: 12px; transition: 0.3s; display: inline-flex; align-items: center; gap: 8px; }
        .btn-promote { background: #136835; color: white; box-shadow: 0 4px 10px rgba(19, 104, 53, 0.2); }
        .btn-demote { background: #ef4444; color: white; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2); }
        .action-btn:hover { transform: translateY(-2px); opacity: 0.9; }

        .back-link { text-decoration: none; color: #64748b; font-weight: 700; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 20px; }
        .back-link:hover { color: #136835; }
    </style>
</head>
<body>

<div class="container">
    <a href="admin_dashboard.php" class="back-link"><i class="fa fa-chevron-left"></i> Dashboard</a>

    <div class="admin-header">
        <div class="title-area">
            <h2>User Permissions</h2>
        </div>

        <form method="GET" class="search-form">
            <button type="submit"><i class="fa fa-magnifying-glass"></i></button>
            <input type="text" name="search" placeholder="Search name or email..." value="<?php echo htmlspecialchars($search); ?>">
        </form>
    </div>

    <?php if($message): ?>
        <div class="alert <?php echo $status; ?>">
            <i class="fa <?php echo ($status == 'success') ? 'fa-circle-check' : 'fa-circle-xmark'; ?>"></i>
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>User Identity</th>
                    <th>Status</th>
                    <th>Permission Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($user = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: #1e293b;"><?php echo $user['first_name'] . " " . $user['last_name']; ?></div>
                                <div style="font-size: 12px; color: #94a3b8;"><?php echo $user['email']; ?></div>
                            </td>
                            <td>
                                <span class="role-badge <?php echo (strtolower($user['role']) == 'admin') ? 'badge-admin' : 'badge-user'; ?>">
                                    <?php echo strtoupper($user['role']); ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <?php if(strtolower($user['role']) != 'admin'): ?>
                                        <input type="hidden" name="role_type" value="admin">
                                        <button type="submit" name="update_role" class="action-btn btn-promote">
                                            <i class="fa fa-user-shield"></i> Make Admin
                                        </button>
                                    <?php else: ?>
                                        <input type="hidden" name="role_type" value="user">
                                        <button type="submit" name="update_role" class="action-btn btn-demote">
                                            <i class="fa fa-user-minus"></i> Remove Admin
                                        </button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3" style="text-align:center; padding:50px; color:#94a3b8;">No matching users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>