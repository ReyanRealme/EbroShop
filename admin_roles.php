<?php
include 'db.php';

// Security: Only allow users with 'admin' role to access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.html");
    exit();
}

$message = "";
$status = "";

// Handle the Form Submission
if (isset($_POST['update_role'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // We force the input to lowercase 'admin' or 'user' to match your database defaults
    $role_to_set = strtolower(mysqli_real_escape_string($conn, $_POST['role_type'])); 

    // Check if the user exists first
    $user_check = $conn->query("SELECT id FROM users WHERE email = '$email'");

    if ($user_check && $user_check->num_rows > 0) {
        // Update the role (This will work now because we changed the column to VARCHAR)
        $sql = "UPDATE users SET role = '$role_to_set' WHERE email = '$email'";
        
        if ($conn->query($sql)) {
            $message = "Success: <b>$email</b> has been updated to <b>$role_to_set</b>.";
            $status = "success";
        } else {
            $message = "Database Error: Unable to update role.";
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
    <title>Ebroshop | Admin Permissions</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f4f8; margin: 0; padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        
        .role-card { background: #ffffff; width: 100%; max-width: 420px; padding: 40px; border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); border-top: 8px solid #136835; }
        
        .header { text-align: center; margin-bottom: 30px; }
        .header i { font-size: 50px; color: #136835; margin-bottom: 15px; }
        .header h2 { margin: 0; color: #1e293b; font-size: 22px; text-transform: uppercase; letter-spacing: 1px; }
        .header p { color: #64748b; font-size: 14px; margin-top: 5px; }

        /* Notification Styling */
        .alert { padding: 15px; border-radius: 12px; margin-bottom: 25px; font-size: 14px; text-align: center; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 10px; font-weight: 700; color: #475569; font-size: 13px; text-transform: uppercase; }
        
        input[type="email"] { width: 100%; padding: 14px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 15px; outline: none; box-sizing: border-box; transition: 0.3s; }
        input[type="email"]:focus { border-color: #136835; box-shadow: 0 0 0 4px rgba(19, 104, 53, 0.1); }

        /* Modern Radio Group */
        .role-toggle { display: flex; gap: 12px; margin-bottom: 30px; }
        .role-toggle label { flex: 1; position: relative; cursor: pointer; }
        .role-toggle input { display: none; }
        .role-toggle span { display: block; padding: 12px; text-align: center; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 12px; color: #64748b; font-weight: 600; transition: 0.3s; font-size: 14px; }
        
        .role-toggle input:checked + span { background: #136835; border-color: #136835; color: #ffffff; }

        .btn-confirm { width: 100%; padding: 16px; border: none; border-radius: 12px; background: #136835; color: #ffffff; font-weight: 700; font-size: 16px; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 12px rgba(19, 104, 53, 0.2); }
        .btn-confirm:hover { background: #0e522a; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(19, 104, 53, 0.3); }

        .back-link { display: block; text-align: center; margin-top: 25px; text-decoration: none; color: #64748b; font-size: 13px; font-weight: 600; transition: 0.2s; }
        .back-link:hover { color: #136835; }
    </style>
</head>
<body>

<div class="role-card">
    <div class="header">
        <i class="fa fa-user-gear"></i>
        <h2>Role Management</h2>
        <p>Assign administrative authority</p>
    </div>

    <?php if($message): ?>
        <div class="alert <?php echo $status; ?>">
            <i class="fa <?php echo ($status == 'success') ? 'fa-check-circle' : 'fa-triangle-exclamation'; ?>"></i>
            <span><?php echo $message; ?></span>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>User Identity (Email)</label>
            <input type="email" name="email" placeholder="user@ebroshop.com" required>
        </div>

        <div class="form-group">
            <label>New Permission Level</label>
            <div class="role-toggle">
                <label>
                    <input type="radio" name="role_type" value="admin" checked>
                    <span>ADMIN</span>
                </label>
                <label>
                    <input type="radio" name="role_type" value="user">
                    <span>USER</span>
                </label>
            </div>
        </div>

        <button type="submit" name="update_role" class="btn-confirm">
            Apply Permissions
        </button>
    </form>

    <a href="admin_dashboard.php" class="back-link">
        <i class="fa fa-arrow-left"></i> Return to Dashboard
    </a>
</div>

</body>
</html>