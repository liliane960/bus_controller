<?php
require_once '../config/db.php';
$db = new Database();
$conn = $db->connect();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/style.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f5f5f5;
        }

        .container {
            display: flex;
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
        }

        nav ul li a:hover,
        nav ul li a.active {
            background-color: #3498db;
            font-weight: bold;
        }

        /* main {
            margin-left: 220px; 
            flex-grow: 1;
            background-color: white;
        } */
        main {
            margin-left: 220px; /* width of the fixed sidebar */
            flex-grow: 1;
            background-color: white;
        }

        #fixed-summary {
            position: fixed;
            top: 0;
            left: 220px;
            right: 0;
            background-color: #ecf0f1;
            padding: 15px 20px;
            border-bottom: 1px solid #ccc;
            z-index: 999;
        }

        #main-content {
            margin-top: 100px; /* height of fixed-summary */
            padding: 20px;
            max-height: calc(100vh - 100px);
            overflow-y: auto;
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
            const page = window.location.hash ? window.location.hash.substring(1) : 'profile_admin.php';
            loadPage(page);
        });
    </script>
</head>
<body>

<div class="container">
    <!-- Sidebar -->
    <nav>
        <ul>
            <li><a href="profile_admin.php">Profile</a></li>
            <li><a href="register.php">Register User</a></li>
            <li><a href="add_bus.php">Register Bus</a></li>
            <li><a href="view_users.php">View Users</a></li>
            <li><a href="view_notifications.php">View Notifications</a></li>
            <li><a href="view_buses.php">Bus Report</a></li>
            <li><a href="passenger_counter.php">Passenger Counter</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- Main content area -->
    <main>
        <!-- Fixed summary at the top -->
        <section id="fixed-summary">
            <?php include 'Dashboard.php'; ?>
        </section>

        <!-- Dynamic content -->
        <section id="main-content">
            <p>Loading...</p>
        </section>
    </main>
</div>

</body>
</html>
