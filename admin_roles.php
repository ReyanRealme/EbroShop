<?php
include 'db.php';

// Security: Only allow current Admin to access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

$message = "";
$status = "";

// Handle Role Change Request
if (isset($_POST['update_role'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_role = $_POST['role_type']; // 'admin' or 'user'

    // 1. Check if user exists in the 'users' table
    // We only select 'id' to verify existence; no address columns are touched here.
    $check_user = $conn->query("SELECT id FROM users WHERE email = '$email'");
    
    if ($check_user && $check_user->num_rows > 0) {
        // 2. Update only the 'role' column
        $update = $conn->query("UPDATE users SET role = '$new_role' WHERE email = '$email'");
        if ($update) {
            $message = "Success! Account <b>$email</b> has been updated to <b>" . strtoupper($new_role) . "</b>.";
            $status = "success";
        } else {
            $message = "Database Error: Could not update role.";
            $status = "error";
        }
    } else {
        $message = "Error: No user found with the email '$email'.";
        $status = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Roles | Ebroshop Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f7f6; margin: 0; padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .role-container { background: white; width: 100%; max-width: 450px; padding: 35px; border-radius: 15px; box-shadow: 0 8px 30px rgba(0,0,0,0.1); border-top: 5px solid #136835; }
        
        .header { text-align: center; margin-bottom: 25px; }
        .header i { font-size: 45px; color: #136835; margin-bottom: 10px; }
        .header h2 { margin: 0; color: #333; }

        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; display: flex; align-items: center; gap: 10px; }
        .success { background: #e7f4e9; color: #1e7e34; border: 1px solid #c3e6cb; }
        .error { background: #fce8e9; color: #c82333; border: 1px solid #f5c6cb; }

        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #444; }
        input[type="email"] { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-size: 15px; }
        
        .radio-group { display: flex; gap: 10px; margin-top: 10px; }
        .radio-option { flex: 1; position: relative; }
        .radio-option input { display: none; }
        .radio-option label { display: block; background: #f8f9fa; border: 1px solid #ddd; padding: 12px; text-align: center; border-radius: 8px; cursor: pointer; transition: 0.2s; }
        
        .radio-option input:checked + label { background: #136835; color: white; border-color: #136835; font-weight: bold; }

        .submit-btn { width: 100%; padding: 15px; border: none; background: #136835; color: white; font-weight: bold; border-radius: 8px; cursor: pointer; font-size: 16px; margin-top: 10px; }
        .submit-btn:hover { background: #0e522a; }
        
        .back-dashboard { display: block; text-align: center; margin-top: 20px; text-decoration: none; color: #666; font-size: 14px; }
    </style>
</head>
<body>

<div class="role-container">
    <div class="header">
        <i class="fa fa-user-shield"></i>
        <h2>Admin Roles</h2>
        <p>Update user permissions by email</p>
    </div>

    <?php if($message): ?>
        <div class="alert <?php echo $status; ?>">
            <i class="fa <?php echo ($status == 'success') ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?>"></i>
            <span><?php echo $message; ?></span>
        </div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>Target User Email</label>
            <input type="email" name="email" placeholder="enter-user@email.com" required>
        </div>

        <div class="form-group">
            <label>Permission Level</label>
            <div class="radio-group">
                <div class="radio-option">
                    <input type="radio" name="role_type" id="role_admin" value="admin" checked>
                    <label for="role_admin">Promote to Admin</label>
                </div>
                <div class="radio-option">
                    <input type="radio" name="role_type" id="role_user" value="user">
                    <label for="role_user">Demote to User</label>
                </div>
            </div>
        </div>

        <button type="submit" name="update_role" class="submit-btn">Update Permissions</button>
    </form>

    <a href="admin_dashboard.php" class="back-dashboard"><i class="fa fa-arrow-left"></i> Back to Admin Panel</a>
</div>

</body>
</html>