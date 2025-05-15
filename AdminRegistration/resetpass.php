<?php
include '../connect.php';
session_start();
// Set timezone to your local timezone
date_default_timezone_set('Asia/Manila'); // Adjust this to your timezone

$error = '';
$success = '';

// For debugging
$debug = false; // Set to true to see debugging information

// Check if token is valid (if coming from email link)
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Check if token exists in password_resets table
    $query = "SELECT * FROM password_resets WHERE token = ? AND expires > NOW()";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // For debugging
    if ($conn->error) {
        $error = "Database error: " . $conn->error;
    } elseif ($result->num_rows === 0) {
        // Check if token exists but is expired
        $expiredQuery = "SELECT * FROM password_resets WHERE token = ? AND expires <= NOW()";
        $expiredStmt = $conn->prepare($expiredQuery);
        $expiredStmt->bind_param("s", $token);
        $expiredStmt->execute();
        $expiredResult = $expiredStmt->get_result();
        
        if ($expiredResult->num_rows > 0) {
            $error = "Your reset token has expired. Please request a new password reset.";
        } else {
            $error = "Invalid token. Please request a new password reset.";
        }
    }
}

// Handle password reset form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate passwords
    if (empty($password) || empty($confirm_password)) {
        $error = "Please fill in all fields";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long";
    } else {
        // If token is valid (from email link)
        if (isset($_GET['token'])) {
            $token = $_GET['token'];
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // First, get the email associated with the token
            $query = "SELECT email FROM password_resets WHERE token = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $email = $row['email'];
                
                // Update the password for the user with this email
                $updateQuery = "UPDATE users SET password = ? WHERE Email = ?";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bind_param("ss", $hashed_password, $email);
                
                if ($updateStmt->execute()) {
                    // Delete the used token
                    $deleteQuery = "DELETE FROM password_resets WHERE token = ?";
                    $deleteStmt = $conn->prepare($deleteQuery);
                    $deleteStmt->bind_param("s", $token);
                    $deleteStmt->execute();
                    
                    $success = "Password updated successfully. You can now login with your new password.";
                } else {
                    $error = "Error updating password. Please try again.";
                }
            } else {
                $error = "Could not process your request. Please try again.";
            }
        } 
        // Else if user is logged in (changing password from account)
        elseif (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $query = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $hashed_password, $user_id);
            
            if ($stmt->execute()) {
                $success = "Password changed successfully.";
            } else {
                $error = "Error changing password. Please try again.";
            }
        } else {
            $error = "Invalid request. Please use the password reset link sent to your email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"> 
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>
  <link rel="stylesheet" href="forgtpass.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
  <div class="wrapper" id="loginForm">
    <h1>Reset Password</h1>
    
    <?php if ($error): ?>
      <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
      <?php if ($debug && isset($_GET['token'])): ?>
        <div style="background-color: #f8f9fa; padding: 10px; margin: 10px 0; border: 1px solid #ccc;">
          <p><strong>Debug Information:</strong></p>
          <p>Token from URL: <?php echo htmlspecialchars($_GET['token']); ?></p>
          <?php
            $checkQuery = "SELECT * FROM password_resets WHERE token = ?";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bind_param("s", $_GET['token']);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            if ($checkResult->num_rows > 0) {
              $row = $checkResult->fetch_assoc();
              echo "<p>Token found in database!</p>";
              echo "<p>Expires: " . $row['expires'] . "</p>";
              echo "<p>Current time: " . date("Y-m-d H:i:s") . "</p>";
              if (strtotime($row['expires']) < time()) {
                echo "<p style='color:red;'>Token is expired.</p>";
              } else {
                echo "<p style='color:green;'>Token is valid but error occurred.</p>";
              }
            } else {
              echo "<p>Token not found in database.</p>";
            }
          ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>
    
    <?php if ($success): ?>
      <div class="alert success"><?php echo htmlspecialchars($success); ?>
        <p><a href="registration.php">Return to Login</a></p>
      </div>
    <?php endif; ?>
    
    <?php if (!$success): ?>
    <form action="" method="POST">
      <div class="Input-box">
        <label for="password">New Password:</label>
        <input type="password" name="password" id="password" placeholder="New Password" required>
        <i class='bx bx-show toggle-password' data-target="password"></i>
      </div>

      <div class="Input-box">
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
        <i class='bx bx-show toggle-password' data-target="confirm_password"></i>
      </div>

      <button type="submit" class="btn" name="reset_password">Reset Password</button>
    </form>
    <?php endif; ?>
  </div>
  
  <script>
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(icon => {
      icon.addEventListener('click', function() {
        const target = this.getAttribute('data-target');
        const input = document.getElementById(target);
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        this.classList.toggle('bx-show');
        this.classList.toggle('bx-hide');
      });
    });
  </script>
</body>
</html>