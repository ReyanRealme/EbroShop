<?php
// 1. Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. CHECK LOGIN STATUS
// This ensures only ONE view is active at a time
$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$userName = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : "User";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account - EBRO Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root { --primary-blue: #0076ad; --border-light: #ddd; }
        body, html { margin: 0; padding: 0; height: 100%; font-family: Arial, sans-serif; background: transparent; }
        
        .overlay-mask { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.4); display: flex; justify-content: flex-end; z-index: 9999; }
        .sidebar { background: #fff; width: 85%; max-width: 380px; height: 100%; padding: 25px; box-sizing: border-box; box-shadow: -5px 0 15px rgba(0, 0, 0, 0.2); }
        
        /* HEADER */
        .side-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #eee; padding-bottom: 15px; }
        .side-header h2 { margin: 0; font-size: 22px; color: #333; }
        .close-x { text-decoration: none; color: #333; font-size: 30px; cursor: pointer; }

        /* LOGIN VIEW */
        .field-label { display: block; font-weight: bold; margin-bottom: 8px; font-size: 14px; color: #555; }
        .input-box { width: 100%; padding: 12px; border: 1px solid var(--border-light); border-radius: 4px; box-sizing: border-box; margin-bottom: 20px; }
        .btn-blue { width: 100%; padding: 14px; background: #fff; color: var(--primary-blue); border: 2px solid var(--primary-blue); font-weight: bold; cursor: pointer; margin-bottom: 15px; text-transform: uppercase; border-radius: 4px; }

        /* STATUS VIEW (ACCOUNT MENU) */
        .account-menu { list-style: none; padding: 0; margin: 0; }
        .menu-row { border-bottom: 1px solid #f0f0f0; }
        .menu-row a { display: flex; align-items: center; padding: 18px 0; text-decoration: none; color: #333; font-size: 16px; font-weight: 500; }
        .menu-row a i { width: 30px; color: var(--primary-blue); font-size: 18px; }
    </style>
</head>
<body>

<div class="overlay-mask">
    <div class="sidebar">
        
        <div class="side-header">
            <h2><?php echo $isLoggedIn ? "Hi, " . htmlspecialchars($userName) : "Login"; ?></h2>
            <a href="javascript:history.back()" class="close-x">&times;</a>
        </div>

        <?php if (!$isLoggedIn): ?>
            <form action="login.php" method="POST">
                <label class="field-label">Email Address</label>
                <input type="email" name="email" class="input-box" placeholder="Email" required>
                
                <label class="field-label">Password</label>
                <input type="password" name="password" class="input-box" placeholder="Password" required>

                <button type="submit" class="btn-blue">Login</button>
            </form>
            <p style="text-align:center;"><a href="forget.html" style="color:var(--primary-blue); text-decoration:none; font-size:14px;">Forgot password?</a></p>
            <button class="btn-blue" onclick="location.href='register.html'">Create Account</button>

        <?php else: ?>
            <ul class="account-menu">
                <li class="menu-row"><a href="account.php"><i class="fa-regular fa-user"></i> Account Details</a></li>
                <li class="menu-row"><a href="address.php"><i class="fa-solid fa-location-dot"></i> Addresses</a></li>
                <li class="menu-row"><a href="change_password.html"><i class="fa-solid fa-lock"></i> Change Password</a></li>
                <li class="menu-row">
                    <a href="javascript:void(0)" onclick="confirmLogout()" style="color: #cc0000;">
                        <i class="fa-solid fa-right-from-bracket" style="color: #cc0000;"></i> Log Out
                    </a>
                </li>
            </ul>
        <?php endif; ?>

    </div>
</div>

<script>
    function confirmLogout() {
        if (confirm("Are you sure you want to log out?")) {
            window.location.href = 'logout.php';
        }
    }
</script>

</body>
</html>