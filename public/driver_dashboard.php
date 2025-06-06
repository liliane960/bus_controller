<?php
session_start();
require_once '../config/db.php';

$db = new Database();
$conn = $db->connect();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Ensure the driver is logged in
if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in.");
}

$userId = $_SESSION['user_id'];

// Step 1: Get bus IDs assigned to this driver
$busIds = [];
$busStmt = $conn->prepare("SELECT bus_id FROM buses WHERE driver_id = ?");
$busStmt->bind_param("i", $userId);
$busStmt->execute();
$busResult = $busStmt->get_result();

while ($row = $busResult->fetch_assoc()) {
    $busIds[] = $row['bus_id'];
}
$busStmt->close();

// Step 2: Count notifications related to those buses
$notificationCount = 0;

if (!empty($busIds)) {
    $placeholders = implode(',', array_fill(0, count($busIds), '?'));
    $types = str_repeat('i', count($busIds));

    $notifQuery = "SELECT COUNT(*) as total FROM notifications WHERE bus_id IN ($placeholders)";
    $notifStmt = $conn->prepare($notifQuery);
    $notifStmt->bind_param($types, ...$busIds);
    $notifStmt->execute();
    $notifResult = $notifStmt->get_result()->fetch_assoc();
    $notificationCount = $notifResult['total'];
    $notifStmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver Dashboard</title>
    <link rel="stylesheet" href="css/style.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f5f5f5;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 220px;
            height: 100vh;
            background-color: #2c3e50;
            padding-top: 20px;
            color: white;
            overflow-y: auto; /* optional: scroll only the sidebar if menu gets too long */
            z-index: 1000;
        }


        nav ul {
            list-style-type: none;
            padding: 0;
        }

        nav ul li a {
            display: block;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            cursor: pointer;
        }

        nav ul li a:hover,
        nav ul li a.active {
            background-color: #3498db;
            font-weight: bold;
        }

        /* main {
            flex-grow: 1;
            padding: 20px;
            background-color: white;
            overflow-y: auto;
        } */
        main {
            margin-left: 220px; /* width of the fixed sidebar */
            flex-grow: 1;
            background-color: white;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
        }

        th {
            background-color: #3498db;
            color: white;
        }
    </style>

    <script>
        function loadPage(page) {
            fetch(page)
                .then(response => {
                    if (!response.ok) throw new Error('Page load error');
                    return response.text();
                })
                .then(html => {
                    document.getElementById('main-content').innerHTML = html;
                    window.location.hash = page;
                    setActiveLink(page);
                })
                .catch(error => {
                    document.getElementById('main-content').innerHTML = "<p>Error loading page.</p>";
                    console.error(error);
                });
        }

        function setupNavLinks() {
            document.querySelectorAll('nav a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const page = this.getAttribute('href');

                    if (page === 'logout.php') {
                        if (confirm("Are you sure you want to log out?")) {
                            window.location.href = page;
                        }
                    } else {
                        loadPage(page);
                    }
                });
            });
        }

        function setActiveLink(page) {
            document.querySelectorAll('nav a').forEach(link => {
                const linkPage = link.getAttribute('href');
                if (linkPage === page) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            setupNavLinks();
            const page = window.location.hash ? window.location.hash.substring(1) : 'view_profile.php';
            loadPage(page);
        });
    </script>
</head>
<body>

<div class="container">
    <!-- Sidebar -->
    <nav>
        <ul>
            <li><a href="profile_driver.php">View Profile</a></li>
            <li><a href="view_notifications.php">View Notifications</a></li>
            <li><a href="view_car_report.php">View Bus Reports</a></li>
            <li><a href="passenger_counter.php">Passenger Counter</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- Main content -->
    <main>
        <!-- Dashboard Heading -->
        <section id="fixed-summary">
            <center><h1 style="color: #3498db;">Driver DASHBOARD</h1></center>

            <?php include 'driverInfo.php'; ?>

            <div style="text-align: center; margin-top: 10px;">
                <h3 style="color: #2c3e50;">Notifications your bus: 
                    <span style="color: #e74c3c;"><?php echo $notificationCount; ?></span>
                </h3>
            </div>
        </section>

        <!-- Dynamic Page Content -->
        <section id="main-content">
            <p>Loading...</p>
        </section>
    </main>
</div>

</body>
</html>
