<?php

include 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $old_pass = $_POST['old_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    // 1. Check if new passwords match
   if ($new_pass !== $confirm_pass) {
    echo "<script>
            alert('Passwords do not match. Please try again.\\n\ያስገቡት አዲስ የይለፍ ቃል ከማረጋገጫው ጋር አይመሳሰልም። እባክዎ በድጋሚ ይሞክሩ።'); 
            window.history.back();
          </script>";
    exit();
}

    // 2. Get current password hash from DB
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // 3. Verify Old Password
    // Note: Use password_verify if you use hashing, or $old_pass == $user['password'] if plain text (not recommended)
    if (password_verify($old_pass, $user['password']) || $old_pass == $user['password']) {
        
        // 4. Update to New Password (Hashed for security)
        $new_hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->bind_param("si", $new_hashed_pass, $user_id);
        
       if ($update->execute()) {
            echo "<script>
                    alert('Success! Your password has been updated securely.\\n\\nእንኳን ደስ አለዎት! የይለፍ ቃልዎ በትክክል ተቀይሯል። አሪፍ ቆይታ ይሁንልዎ።'); 
                    window.location.href='account.php';
                  </script>";
        } else {
            echo "Error updating password.";
        }
    } else {
          echo "<script>
                  alert('The current password you entered is incorrect. Please try again.\\n\\nያስገቡት የበፊቱ የይለፍ ቃል ትክክል አይደለም። እባክዎ በድጋሚ በትክክል ይሞክሩ።'); 
                  window.history.back();
                </script>";
      }
}
$conn->close();
?>