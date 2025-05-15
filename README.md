# VISITRACK
VISITOR REGISTRATION SYSTEM SETUP (XAMPP + PHP + MySQL + VS CODE + EMAIL CONFIG)

---------------------------------------------------------
STEP 0: ADD PHP TO ENVIRONMENT VARIABLES (IMPORTANT)
---------------------------------------------------------
To run PHP in CMD or VS Code terminal:

1. Press Windows Key + S and search: Environment Variables
2. Click "Edit the system environment variables"
3. In the System Properties window, click "Environment Variables..."
4. Under "System Variables", select "Path" and click "Edit"
5. Click "New" and add:
   C:\xampp\php
6. Click OK to save everything.

	To check: Open CMD and type:
	php -v
	You should see the PHP version.

---------------------------------------------------------
STEP 1: INSTALL VISUAL STUDIO CODE (VS Code)
---------------------------------------------------------
- Download: https://code.visualstudio.com/
- Install for your OS.

---------------------------------------------------------
STEP 2: INSTALL EXTENSIONS IN VS CODE
---------------------------------------------------------
In VS Code, install these extensions:

1. PHP Extension Pack
2. Live Server (by Ritwick Dey)
3. HTML CSS Support
4. Prettier - Code Formatter (optional)

Click the Extensions icon (left sidebar) to install them.


---------------------------------------------------------
STEP 3: INSTALL AND START XAMPP
---------------------------------------------------------
- Download from: https://www.apachefriends.org/index.html
- Install it.
- Open XAMPP Control Panel.
- Start Apache and MySQL.

another shot guide

GUIDE: RUN YOUR PHP PROJECT WITH XAMPP + VS CODE

--------------------------------------------------
STEP 1: Move Your Project to 'htdocs'
--------------------------------------------------
1. Go to your XAMPP installation folder:
   C:\\xampp\\htdocs

2. Copy or move your project folder (e.g., visitor_system) into the htdocs folder.
   Example: C:\\xampp\\htdocs\\visitor_system

--------------------------------------------------
STEP 2: Open Project in VS Code
--------------------------------------------------
1. Open Visual Studio Code
2. Click File > Open Folder
3. Navigate to: C:\\xampp\\htdocs\\visitor_system
4. Click "Select Folder"
5. Your code is now opened in VS Code.

--------------------------------------------------
STEP 3: Start XAMPP Services
--------------------------------------------------
1. Open XAMPP Control Panel
2. Click "Start" for:
   - Apache
   - MySQL 
--------------------------------------------------
STEP 4: Run the Project in Browser
--------------------------------------------------
1. Open your web browser
2. Go to:
   http://localhost/visitor_system

Visitor Registration System Setup Guide
=======================================

Prerequisites:
- Windows OS
- Administrator access

1. Environment Configuration
----------------------------
Add PHP to System Path:
1. Press Win+S and search "Environment Variables"
2. Select "Edit the system environment variables"
3. Click "Environment Variables" button
4. Under "System Variables", select "Path" and click "Edit"
5. Click "New" and add: C:\xampp\php
6. Click OK to save all changes

Verify installation:
Open CMD and type: php -v
(Should display PHP version)

2. Software Installation
------------------------
Required software:
- VS Code: https://code.visualstudio.com/
- XAMPP: https://www.apachefriends.org/index.html

3. VS Code Extensions
---------------------
Install these extensions:
- PHP Extension Pack
- Live Server
- HTML CSS Support
- Prettier (optional)

4. Database Setup
-----------------
1. Access phpMyAdmin: http://localhost/phpmyadmin
2. Create database named: visitor_db
3. Execute these SQL commands:

CREATE TABLE visitors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    Lastname VARCHAR(50) NOT NULL,
    Firstname VARCHAR(50) NOT NULL,
    Middle_Initial VARCHAR(5),
    Suffix VARCHAR(10),
    Email VARCHAR(100) NOT NULL,
    purpose TEXT NOT NULL,
    visitor_type ENUM('Regular', 'VIP') NOT NULL,
    ZIP_Code VARCHAR(10) NOT NULL,
    Municipality VARCHAR(100) NOT NULL,
    Barangay_Village VARCHAR(50) NOT NULL,
    visit_date DATE NOT NULL DEFAULT (CURRENT_DATE),
    visit_time TIME NOT NULL DEFAULT (CURRENT_TIME)
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);


CREATE TABLE IF NOT EXISTS password_resets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL,
  token VARCHAR(255) NOT NULL,
  expires DATETIME NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

5. Project Structure
--------------------
1. Create folder: C:\xampp\htdocs\visitor_system

6. Email Configuration
----------------------
1. Edit php.ini (C:\xampp\php\php.ini):

[mail function]
For Win32 only.
https://php.net/smtp
SMTP=smtp.gmail.com
https://php.net/smtp-port
smtp_port=587

For Win32 only.
https://php.net/sendmail-from
sendmail_from = "imtheonlyoneknows61@gmail.com"

; For Unix only.  You may supply arguments as well (default: "sendmail -t -i").
https://php.net/sendmail-path
sendmail_path = "\"C:\xampp\sendmail\sendmail.exe\" -t"

2. Edit sendmail.ini (C:\xampp\sendmail\sendmail.ini):

[sendmail]

smtp_server=smtp.gmail.com
smtp_port=587
error_logfile=error.log
debug_logfile=debug.log
auth_username=imtheonlyoneknows61@gmail.com
auth_password=curdwywwbistkodi
forcer_sender=imtheonlyoneknows61@gmail.com

7. Final Steps
-------------
1. Restart XAMPP (stop and start both Apache and MySQL)
2. Access your system at: http://localhost/visitor_system

Troubleshooting:
- Check XAMPP logs for errors
- Verify ports 80/443 aren't blocked try nyo ibang port
- Ensure Gmail allows "less secure apps" or use App Password
- Restart your computer if services won't start
