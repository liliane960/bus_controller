<?php
// Correct path to db.php
require_once '../config/db.php';

// Initialize DB connection
$db = new Database();
$conn = $db->connect();

// Check connection (for MySQLi style)
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch all users
$sql = "SELECT user_id, username, role FROM users ORDER BY user_id ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>police Dashboard - Manage bus</title>
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
    <h1>police Dashboard</h1>

    <!-- police Navigation -->
    <nav>
        <ul>
            <li><a href="view_notifications.php">View Notifications</a></li>
            <li><a href="view_car_report.php">View Car Reports</a></li>
            <li><a href="passenger_counter.php">Passenger Counter</a></li>
            <li><a href="logout.php">logout</a></li>
        </ul>
    </nav>
    
    <container>
    <h2>User Management</h2>
    </container>
</body>
</html>

<?php
$conn->close();
?>
