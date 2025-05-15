<?php
include '../connect.php';
// qina lagay ko na time zone naliligaw lagi yung oras
date_default_timezone_set('Asia/Manila'); 

// reset email ginaya ko lang to sa dun sa previus na ginawa natin 
function sendPasswordResetEmail($email, $token) {
    $subject = "Password Reset Request - NTC ADMIN SUPPORT";
    $message = "Click the following link to reset your password: \n\n";
    $message .= "http://localhost:8000/AdminRegistration/resetpass.php?token=$token\n\n";
    $message .= "This link will expire in 1 hour.\n\n";
    $message .= "If you didn't request this, please ignore this email.\n";
    $headers = "From: NTC ADMIN SUPPORT <vip-services@ntc.edu.ph>\r\n";
    $headers .= "Reply-To: no-reply@ntc.edu.ph\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
  
    return mail($email, $subject, $message, $headers);
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['resetRequest'])) {
    $email = $_POST['email'];
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Check if email exists in database
        $sql = "SELECT Email FROM users WHERE Email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Generate unique token
            $token = bin2hex(random_bytes(32));
            $expires = date("Y-m-d H:i:s", time() + 3600); // 1 hour expiration
            
            // Delete any existing tokens for this email
            $deleteQuery = "DELETE FROM password_resets WHERE email = ?";
            $deleteStmt = $conn->prepare($deleteQuery);
            $deleteStmt->bind_param("s", $email);
            $deleteStmt->execute();
            
            // Store token in database
            $sql = "INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $email, $token, $expires);
            
            if ($stmt->execute()) {
                // Send email condition yan perds
                if (sendPasswordResetEmail($email, $token)) {
                    $success = 'Password reset link has been sent to your email.';
                    
                } else {
                    $error = 'Failed to send email. Please try again.';
                }
            } else {
                $error = 'Error processing your request. Please try again.';
            }
        } else {
            $error = 'Email not found in our system.';
        }
        
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"> 
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password</title>
  <link rel="stylesheet" href="forgtpass.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
  <div class="wrapper" id="loginForm">
    <form method="POST">
      <h1>Forgot Password</h1>
      
      <?php if ($success): ?>
        <div class="alert success"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>
      
      <?php if ($error): ?>
        <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <div class="Input-box">
        <label for="email">Email Address:</label>
        <input type="email" name="email" id="email" placeholder="Enter your email" required>
        <i class='bx bxs-envelope'></i>
      </div>

      <button type="submit" class="btn" name="resetRequest">Send Reset Link</button>

      <div class="register-link">
        <p>Remember your password? <a href="registration.php">Login</a></p>
      </div>
    </form>
  </div>
  <script src="registration.js"></script>
</body>
</html>
<?php
$conn->close();
?>