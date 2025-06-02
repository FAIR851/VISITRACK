<?php
include 'connect.php'; 
session_start();

function sendVipWelcomeEmail($vipEmail, $firstName, $lastName) {
    $wifiPassword = "admin123"; 
    $currentDate = date('F j, Y');
    
    $subject = "Your VIP WiFi Access to NTC - $currentDate";
    // this is the email part, dont remove the css , kasi hindi binabasa pag naka separete yung style so dyan muna sya for the meantime
    $body = "
    <html>
    <head>
        <style>
            body { font-family: 'Arial', sans-serif; line-height: 1.6; }
            .vip-header { 
                color: #d4af37;
                border-bottom: 2px solid #d4af37;
                padding-bottom: 10px;
            }
            .password-box {
                background-color: #f5f5f5;
                border: 1px solid #ddd;
                padding: 15px;
                margin: 20px 0;
                border-radius: 5px;
                font-size: 18px;
            }
        </style>
    </head>
    <body>
        <h2 class='vip-header'>VIP WiFi Access</h2>
        <p>Dear $firstName $lastName,</p>
        
        <p>As a VIP visitor to NTC, here are your exclusive WiFi credentials:</p>
        
        <div class='password-box'>
            <strong>Network:</strong> NTC-VIP-GUEST<br>
            <strong>Password:</strong> $wifiPassword
        </div>
        
        <p>Visit Date: $currentDate</p>
        
        <p>Best regards,<br>
        <strong>NTC VIP Support</strong></p>
    </body>
    </html>
    ";
    
    $headers = "From: NTC VIP Services <vip-services@ntc.edu.ph>\r\n";
    $headers .= "Reply-To: no-reply@ntc.edu.ph\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($vipEmail, $subject, $body, $headers);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    processVisitorForm();
}

function insertVisitor($conn, $lastname, $firstname, $middle_initial, $suffix, $email, $purpose, $zip_code, $municipality, $barangay, $visitor_type) {
    $stmt = $conn->prepare("INSERT INTO visitors (Lastname, Firstname, Middle_Initial, Suffix, Email, purpose, ZIP_Code, Municipality, Barangay_Village, visitor_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $lastname, $firstname, $middle_initial, $suffix, $email, $purpose, $zip_code, $municipality, $barangay, $visitor_type);

    if ($stmt->execute()) {

        if (strtoupper($visitor_type) === 'VIP') {
            if (sendVipWelcomeEmail($email, $firstname, $lastname)) {
                $email_status = " (VIP credentials sent)";
            } else {
                $email_status = " (VIP email failed)";
                error_log("Failed to send VIP email to: $email");
            }
        }
        echo "<script>alert('Visitor logged successfully!" . ($email_status ?? '') . "'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Error: Could not log visitor');</script>";
    }

    $stmt->close();
    $conn->close();
}

function processVisitorForm() {
    global $conn;

    $lastname = $_POST['Lastname'] ?? null;
    $firstname = $_POST['Firstname'] ?? null;
    $middle_initial = $_POST['Middle_Initial'] ?? null;
    $suffix = $_POST['Suffix'] ?? null;
    $email = $_POST['Email'] ?? null;
    $purpose = $_POST['purposes'] ?? null;
    $zip_code = $_POST['ZIP_Code'] ?? null;
    $municipality = $_POST['Municipality'] ?? null;
    $barangay = $_POST['Barangay_Village'] ?? null;
    $visitor_type = $_POST['type'] ?? null;

    if ($lastname && $firstname && $email && $purpose && $zip_code && $municipality && $barangay && $visitor_type) {
        insertVisitor($conn, $lastname, $firstname, $middle_initial, $suffix, $email, $purpose, $zip_code, $municipality, $barangay, $visitor_type);
    } else {
        echo "<script>alert('Please fill in all required fields.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <title>Logbook System</title>
</head>
<body>
    <div class="border">
    </div>
    <a href="AdminRegistration/registration.php" class="to-guard-form">GUARD PASS</a>


<div class="container" id="container">  


    <form action="index.php" method="POST">
        
        
        <div class="form-container name-form active">
            <h1 class="Welcome1">Welcome to NTC!</h1>


            <input type="text" name="Lastname" id="Lastname" placeholder="Lastname" required>
            <input type="text" name="Firstname" id="Firstname" placeholder="Firstname" required>
            <input type="text" name="Middle_Initial" id="Middle_Initial" placeholder="Middle Initial" required>
            <input type="text" name="Suffix" id="Suffix" placeholder="Suffix">
            <input type="email" name="Email" id="Email" placeholder="Email" required>


        </div>

        <div class="form-container contact-address-form">
            <h1 class="Welcome">Welcome to NTC!</h1>

            <label for="purposes-input"></label>
            <input list="purposes" id="purposes-input" name="purposes" placeholder="Purposes" required>

            <datalist id="purposes">
                <option value="Registrar">
                <option value="SOAST">
                <option value="Libcom">
                <option value="Bookstore">
                <option value="OSA">
            </datalist>

            <input type="number" name="ZIP_Code" id="ZIP_Code" placeholder="ZIP Code" required>
            <input type="text" name="Municipality" id="Municipality" placeholder="Municipality" required>
            <input type="text" name="Barangay_Village" id="Barangay_Village" placeholder="Barangay/Village" required>

            <label for="type-input"></label>
            <input list="type" id="type-input" name="type" placeholder="Visitor Type" required>
                <datalist id="type">
                    <option id="REGULAR" value="REGULAR">
                    <option id="VIP" value="VIP"><b>
                </datalist>

            <button type="submit">Submit</button>    
        </div>
    </form>

    <div class="toggle-container">
        <div class="toggle">
            <img src="Images/Logo.png" class="logo">
            <div class="toggle-panel toggle-left">
                <h1 class="reg-subtitle">Enter required information!</h1>
                <p>Want to go back and re-enter your information?</p>
                <button class="hidden" id="namehid">Go Back!</button>
            </div>
            <img src="Images/Logo.png" class="logo2">
            <div class="toggle-panel toggle-right">
                <h1 class="reg-subtitle">Welcome to the Visitor Registration Form!</h1>
                <p>Fill up the required personal details to continue in our university</p>
                <button class="hidden" id="conhid">Continue!</button>
            </div>
        </div>
    </div>
</div>

<div class="border1">
</div>
<script src="script.js"></script>
</body>
</html>
