<?php
//database connection
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 2. Collect and sanitize input
    $fname = mysqli_real_escape_string($conn, $_POST['first_name']);
    $lname = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass  = $_POST['password'];

    // 3. Check if email already exists
    $checkEmail = "SELECT email FROM users WHERE email = '$email'";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        echo "<script>alert('Error: This email is already registered.'); window.history.back();</script>";
    } else {
        // 4. Hash the password for security
        $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

        // 5. Insert into database (Role defaults to 'customer')
        $sql = "INSERT INTO users (first_name, last_name, email, password, role) 
                VALUES ('$fname', '$lname', '$email', '$hashed_password', 'customer')";

        if ($conn->query($sql) === TRUE) {
            // 6. Redirect to login page on success
            echo "<script>
                    alert('Registration successful! Please login.');
                    window.location.href = 'login.html';
                  </script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>