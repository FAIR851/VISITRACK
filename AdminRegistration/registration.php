<?php
include '../connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Registration Logic
    if (isset($_POST['signUp'])) {
        $firstName = trim($_POST['firstname']);
        $lastName = trim($_POST['lastname']);
        $email = trim($_POST['username']);
        $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('Email Address Already Exists!');</script>";
        } else {
            $insertQuery = "INSERT INTO users (firstname, lastname, email, password) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("ssss", $firstName, $lastName, $email, $password);

            if ($stmt->execute()) {
                echo "<script>
                        alert('Registration successful! Redirecting to homepage...');
                        window.location.href = 'registration.php';
                </script>";
            } else {
                echo "<script>alert('Error registering user: " . $stmt->error . "');</script>";
            }
        }
        $stmt->close();
    }

    // Login Logic
    if (isset($_POST['signIn'])) {
        $email = trim($_POST['username']);
        $password = trim($_POST['password']);

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['email'] = $user['email'];
                echo "<script>
                        alert('Login successful! Redirecting to homepage...');
                        window.location.href = '../Dashboard/dashboard.php';
                    </script>";
                exit();
            } else {
                echo "<script>alert('Incorrect Password');</script>";
            }
        } else {
            echo "<script>alert('Email not found');</script>";
        }
        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> 
    <meta http-equiv="x-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN FORM</title>
    <link rel="stylesheet" href="registration.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

    <!-- Login Form -->
    <div class="wrapper" id="loginForm">
        <form action="registration.php" method="POST">
            <img src="..\Images\National_Teachers_College_logo.png" alt="Logo" class="logo"> 
            <h1>NTC GUARD PASS</h1>
            <div class="Input-box">
                <label for="username">Email/Username:</label>
                <input type="text" name="username" id="username" placeholder="Username" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="Input-box">
                <label for="login-password">Password:</label>
                <input type="password" name="password" id="login-password" placeholder="Password" required>
                <i class='bx bx-show toggle-password' data-target="login-password"></i>
            </div>

            <div class="remember-forgot">

                <label><input type="checkbox"> Remember me</label>
                <a href="forgtpass.php"> Forgot password?</a>
            </div>
            <button type="submit" class="btn" name="signIn">Login</button>
            <div class="register-link">
                <p>Don't have an account? <a href="#" id="showRegisterForm">Register</a></p>
            </div>
        </form>
    </div>

    <!-- Registration Form -->
    <div class="wrapper" id="registerForm" style="display: none;">
        <form action="registration.php" method="POST">
            <h2>Registration Form</h2>
            <div class="Input-box">
                <label for="firstname">Firstname:</label>
                <input type="text" name="firstname" id="firstname" placeholder="First name" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="Input-box">
                <label for="lastname">Lastname:</label>
                <input type="text" name="lastname" id="lastname" placeholder="Last name" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="Input-box">
                <label for="username">Email/Username:</label>
                <input type="text" name="username" id="username" placeholder="Username" required>
                <i class='bx bx-envelope'></i>
            </div>
            <div class="Input-box">
                <label for="register-password">Password:</label>
                <input type="password" name="password" id="register-password" placeholder="Password" required>
                <i class='bx bx-show toggle-password' data-target="register-password"></i>
            </div>

            <button type="submit" class="Signup" name="signUp">Sign up</button>
            <div class="login-link">
                <p>Already have an account? <a href="#" id="showLoginForm">Login</a></p>
            </div>
        </form>
    </div>

    <script src="registration.js"></script>
</body>
</html>
