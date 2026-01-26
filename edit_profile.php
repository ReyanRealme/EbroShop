<?php
// Remove session_start() from here because db.php already handles it
include 'db.php'; 

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// 2. Handle the Update Request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = mysqli_real_escape_string($conn, $_POST['first_name']);
    $lname = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    // Check length before updating to prevent Fatal Error
    if (strlen($phone) > 20) {
         $message = "<div style='color: red; text-align: center;'>Error: Phone number is too long!</div>";
    } else {
        $update_sql = "UPDATE users SET first_name='$fname', last_name='$lname', email='$email', phone='$phone' WHERE id='$user_id'";

        if ($conn->query($update_sql) === TRUE) {
            $message = "<div style='color: green; text-align: center;'>Profile updated successfully!</div>";
            $_SESSION['user_name'] = $fname;
        } else {
            $message = "<div style='color: red; text-align: center;'>Error: " . $conn->error . "</div>";
        }
    }
}

// 3. Fetch current user data to show in the inputs
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
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .edit-container { max-width: 450px; margin: 40px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        h2 { color: #136835; text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; font-size: 14px; color: #333; }
        .input-wrapper { position: relative; }
        .input-wrapper i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #666; }
        input { width: 100%; padding: 12px 12px 12px 45px; border: 1px solid #ccc; border-radius: 8px; font-size: 16px; box-sizing: border-box; }
        .btn-update { background-color: #136835; color: white; border: none; padding: 14px; width: 100%; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        .btn-update:hover { background-color: #0d4d27; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>

<div class="edit-container">
    <h2>Edit Information</h2>
    
    <?php echo $message; ?>

    <form action="edit_profile.php" method="POST">
        <div class="form-group">
            <label>First Name</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-user"></i>
                <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label>Last Name</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-user"></i>
                <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label>Email</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label>Phone Number</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-phone"></i>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>
        </div>

        <button type="submit" class="btn-update">Update Profile</button>
        <a href="account.php" class="back-link">Cancel and Go Back</a>
    </form>
</div>

</body>
</html>