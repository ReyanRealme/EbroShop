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

    // Check if user exists
    $check_user = $conn->query("SELECT id FROM users WHERE email = '$email'");
    
    if ($check_user->num_rows > 0) {
        $update = $conn->query("UPDATE users SET role = '$new_role' WHERE email = '$email'");
        if ($update) {
            $message = "Success! $email is now a " . strtoupper($new_role) . ".";
            $status = "success";
        }
    } else {
        $message = "Error: No user found with that email address.";
        $status = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Permissions | Ebroshop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; margin: 0; padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .role-card { background: white; width: 100%; max-width: 450px; padding: 40px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); border: 1px solid #e0e0e0; }
        
        .header { text-align: center; margin-bottom: 30px; }
        .header i { font-size: 50px; color: #185282; margin-bottom: 15px; }
        .header h2 { margin: 0; color: #333; font-size: 22px; }
        .header p { color: #777; font-size: 14px; margin-top: 5px; }

        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; font-size: 14px; }
        input[type="email"] { width: 100%; padding: 12px 15px; border-radius: 10px; border: 1px solid #ddd; box-sizing: border-box; outline: none; transition: 0.3s; }
        input:focus { border-color: #185282; box-shadow: 0 0 0 3px rgba(24,82,130,0.1); }

        .role-selector { display: flex; gap: 10px; margin-bottom: 25px; }
        .role-option { flex: 1; text-align: center; }
        .role-option input { display: none; }
        .role-option label { padding: 12px; border: 1px solid #ddd; border-radius: 10px; display: block; cursor: pointer; transition: 0.3s; font-weight: normal; }
        
        .role-option input:checked + label { background: #185282; color: white; border-color: #185282; font-weight: bold; }

        .btn-submit { width: 100%; padding: 14px; border: none; border-radius: 10px; background: #136835; color: white; font-weight: bold; cursor: pointer; font-size: 16px; transition: 0.3s; }
        .btn-submit:hover { background: #0e522a; transform: translateY(-2px); }

        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; text-align: center; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        .back-link { display: block; text-align: center; margin-top: 20px; text-decoration: none; color: #185282; font-size: 13px; font-weight: 600; }
    </style>
</head>
<body>

<div class="role-card">
    <div class="header">
        <i class="fa fa-user-shield"></i>
        <h2>Admin Permissions</h2>
        <p>Grant or Revoke Administrative Access</p>
    </div>

    <?php if($message): ?>
        <div class="alert <?php echo $status; ?>">
            <i class="fa <?php echo ($status == 'success') ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i> 
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>User Email Address</label>
            <input type="email" name="email" placeholder="example@gmail.com" required>
        </div>

        <label>Select Permission Level</label>
        <div class="role-selector">
            <div class="role-option">
                <input type="radio" name="role_type" id="make_admin" value="admin" checked>
                <label for="make_admin">Make Admin</label>
            </div>
            <div class="role-option">
                <input type="radio" name="role_type" id="make_user" value="user">
                <label for="make_user">Remove Admin</label>
            </div>
        </div>

        <button type="submit" name="update_role" class="btn-submit">
            Apply Changes
        </button>
    </form>

    <a href="admin_dashboard.php" class="back-link"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>
</div>

</body>
</html>