<?php
session_start();
include("../connect.php");

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['email'];

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'visit_time';
$order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

// Search logic
$search = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $search_condition = "WHERE Lastname LIKE '%$search%' OR Firstname LIKE '%$search%' OR Email LIKE '%$search%'";
} else {
    $search_condition = '';
}

// Pagination logic
$records_per_page = 13;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}
$offset = ($page - 1) * $records_per_page;

// Get total number of records
$count_query = "SELECT COUNT(*) as total FROM visitors $search_condition";
$count_result = mysqli_query($conn, $count_query);
$total_records = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $records_per_page);

// Main query with pagination
$query = "SELECT * FROM visitors $search_condition ORDER BY $sort $order LIMIT $offset, $records_per_page";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    
</head>
<body>
    <nav id="sidebar">
        <ul>
            <li><div class="logo-container">
            <img src="../Images/logo.png" alt="NTC Logo" class="logo">
            </div>
                <button onclick="toggleSidebar()" id="toggle-btn">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M440-240 200-480l240-240 56 56-183 184 183 184-56 56Zm264 0L464-480l240-240 56 56-183 184 183 184-56 56Z"/></svg>
                </button>
            </li>
            <li class="active">
                <a href="dashboard.php">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M560-564v-68q33-14 67.5-21t72.5-7q26 0 51 4t49 10v64q-24-9-48.5-13.5T700-600q-38 0-73 9.5T560-564Zm0 220v-68q33-14 67.5-21t72.5-7q26 0 51 4t49 10v64q-24-9-48.5-13.5T700-380q-38 0-73 9t-67 27Zm0-110v-68q33-14 67.5-21t72.5-7q26 0 51 4t49 10v64q-24-9-48.5-13.5T700-490q-38 0-73 9.5T560-454ZM260-320q47 0 91.5 10.5T440-278v-394q-41-24-87-36t-93-12q-36 0-71.5 7T120-692v396q35-12 69.5-18t70.5-6Zm260 42q44-21 88.5-31.5T700-320q36 0 70.5 6t69.5 18v-396q-33-14-68.5-21t-71.5-7q-47 0-93 12t-87 36v394Zm-40 118q-48-38-104-59t-116-21q-42 0-82.5 11T100-198q-21 11-40.5-1T40-234v-482q0-11 5.5-21T62-752q46-24 96-36t102-12q58 0 113.5 15T480-740q51-30 106.5-45T700-800q52 0 102 12t96 36q11 5 16.5 15t5.5 21v482q0 23-19.5 35t-40.5 1q-37-20-77.5-31T700-240q-60 0-116 21t-104 59ZM280-494Z"/></svg>                    <span>Logbook</span>
                </a>
            </li>
            <li>
                <a href="archive.php">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="m480-240 160-160-56-56-64 64v-168h-80v168l-64-64-56 56 160 160ZM200-640v440h560v-440H200Zm0 520q-33 0-56.5-23.5T120-200v-499q0-14 4.5-27t13.5-24l50-61q11-14 27.5-21.5T250-840h460q18 0 34.5 7.5T772-811l50 61q9 11 13.5 24t4.5 27v499q0 33-23.5 56.5T760-120H200Zm16-600h528l-34-40H250l-34 40Zm264 300Z"/></svg>
                    <span>Archive</span>
                </a>
            </li>
            <li>
                <a href="../logout.php">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z"/></svg>
                    <span style="color: rgb(193, 0, 0)">Log Out</span>
                </a>
            </li>
            
        </ul>
    </nav>



    <main>
        <div id="main-content">
            <h1 class="header">VisiTrack</h1>
            <a href="../index.php" class="register-btn">Register</a>
            <form method="post" action="../export_currentD.php"> 
                    <button type="submit" name="export" class="export-btn1">EXPORT</button>
            </form>
            <div class="table-container">
            <table>
            <thead>
                <tr>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Middle Initial</th>
                    <th>Suffix</th>
                    <th>Email</th>
                    <th>Purpose</th>
                    <th>Zip Code</th>
                    <th>Municipality</th>
                    <th>Barangay</th>
                    <th>Type</th>
                    <th>Time</th>
                    <th>Date</th>
                </tr>
            </thead>
            <?php
            
            $twentyFourHoursAgo = date('Y-m-d H:i:s', strtotime('-24 hours'));
            
            // Query to get only records from the last 24 hours pag tapos mapupunta sa archive 
            $query = mysqli_query($conn, "SELECT * FROM visitors 
                                        WHERE CONCAT(visit_date, ' ', visit_time) >= '$twentyFourHoursAgo'
                                        ORDER BY visit_date DESC, visit_time DESC");
            
            while ($row = mysqli_fetch_array($query)) {
            ?>
            <tbody>
                <tr>
                    <td><?php echo $row['Lastname']; ?></td>
                    <td><?php echo $row['Firstname']; ?></td>
                    <td><?php echo $row['Middle_Initial']; ?></td>
                    <td><?php echo $row['Suffix']; ?></td>
                    <td><?php echo $row['Email']; ?></td>
                    <td><?php echo $row['purpose']; ?></td>
                    <td><?php echo $row['ZIP_Code']; ?></td>
                    <td><?php echo $row['Municipality']; ?></td>
                    <td><?php echo $row['Barangay_Village']; ?></td>
                    <td><?php echo $row['visitor_type']; ?></td>
                    <td><?php echo date("g:i A", strtotime($row['visit_time'])); ?></td>
                    <td><?php echo $row['visit_date']; ?></td>
                </tr>
            </tbody>
            <?php } ?>
        </table>
            </div>
        </div>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=1&sort=<?= $sort ?>&order=<?= $order ?>&search=<?= urlencode($search) ?>">First</a>
                <a href="?page=<?= $page - 1 ?>&sort=<?= $sort ?>&order=<?= $order ?>&search=<?= urlencode($search) ?>">Prev</a>
            <?php else: ?>
                <span class="disabled">First</span>
                <span class="disabled">Prev</span>
            <?php endif; ?>
            
            <?php 
            // Show page numbers
            $start_page = max(1, $page - 2);
            $end_page = min($total_pages, $page + 2);
            
            if ($start_page > 1) {
                echo '<span>...</span>';
            }
            
            for ($i = $start_page; $i <= $end_page; $i++): ?>
                <a href="?page=<?= $i ?>&sort=<?= $sort ?>&order=<?= $order ?>&search=<?= urlencode($search) ?>" <?= ($i == $page) ? 'class="active"' : '' ?>><?= $i ?></a>
            <?php endfor; ?>
            
            <?php if ($end_page < $total_pages) {
                echo '<span>...</span>';
            }
            ?>
            
            <?php if ($page < $total_pages): ?>
                <a href="?page=<?= $page + 1 ?>&sort=<?= $sort ?>&order=<?= $order ?>&search=<?= urlencode($search) ?>">Next</a>
                <a href="?page=<?= $total_pages ?>&sort=<?= $sort ?>&order=<?= $order ?>&search=<?= urlencode($search) ?>">Last</a>
            <?php else: ?>
                <span class="disabled">Next</span>
                <span class="disabled">Last</span>
            <?php endif; ?>
        </div>
    </main>
    <script src="dashboard.js"></script>
</body>
</html>