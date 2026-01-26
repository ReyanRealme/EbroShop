<?php
include 'db.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = mysqli_real_escape_string($conn, $_POST['first_name']);
    $lname = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    $update_sql = "UPDATE users SET first_name='$fname', last_name='$lname', email='$email', phone='$phone' WHERE id='$user_id'";

    if ($conn->query($update_sql) === TRUE) {
        $message = "<div class='alert success'><i class='fa-solid fa-circle-check'></i> Profile updated successfully!</div>";
        $_SESSION['user_name'] = $fname;
    } else {
        $message = "<div class='alert error'><i class='fa-solid fa-circle-xmark'></i> Update failed: " . $conn->error . "</div>";
    }
}

$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - EbRoShop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary-color: #136835;
            --bg-color: #f0f2f5;
            --text-dark: #333;
            --text-light: #666;
        }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background-color: var(--bg-color);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .edit-card {
            background: white;
            width: 100%;
            max-width: 420px;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h2 {
            margin: 0;
            color: var(--primary-color);
            font-size: 26px;
        }

        .header p {
            color: var(--text-light);
            font-size: 14px;
            margin-top: 5px;
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
            color: var(--text-dark);
        }

        .input-group {
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            transition: 0.3s;
        }

        .input-group input {
            width: 100%;
            padding: 14px 15px 14px 45px;
            border: 2px solid #eee;
            border-radius: 12px;
            font-size: 15px;
            box-sizing: border-box;
            outline: none;
            transition: 0.3s;
        }

        .input-group input:focus {
            border-color: var(--primary-color);
        }

        .input-group input:focus + i {
            color: var(--primary-color);
        }

        /* Buttons */
        .btn-save {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 16px;
            width: 100%;
            border-radius: 12px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 4px 10px rgba(19, 104, 53, 0.2);
        }

        .btn-save:hover {
            background-color: #0d4d27;
            transform: translateY(-1px);
        }

        .back-btn {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--text-light);
            text-decoration: none;
            font-size: 14px;
            transition: 0.3s;
        }

        .back-btn:hover {
            color: var(--text-dark);
        }

        /* Modern Alerts */
        .alert {
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .success { background: #e8f5e9; color: #2e7d32; }
        .error { background: #ffebee; color: #c62828; }
    </style>
</head>
<body>

<div class="edit-card">
    <div class="header">
        <h2>Edit Profile</h2>
        <p>Keep your contact details up to date</p>
    </div>

    <?php echo $message; ?>

    <form action="edit_profile.php" method="POST">
        <div class="form-group">
            <label>First Name</label>
            <div class="input-group">
                <i class="fa-solid fa-circle-user"></i>
                <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label>Last Name</label>
            <div class="input-group">
                <i class="fa-solid fa-circle-user"></i>
                <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <div class="input-group">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label>Phone Number</label>
            <div class="input-group">
                <i class="fa-solid fa-phone"></i>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="Not set (e.g. 09...)">
            </div>
        </div>

        <button type="submit" class="btn-save">Save Changes</button>
        
        <a href="account.php" class="back-btn">
            <i class="fa-solid fa-arrow-left"></i> Back to Account
        </a>
    </form>
</div>

</body>
</html>