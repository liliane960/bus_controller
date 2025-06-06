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
    <title>Police Dashboard</title>
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
            width: 220px;
            background-color: #2c3e50;
            padding-top: 20px;
            color: white;
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
            /* background-color: #1abc9c; */
            background-color: #3498db;
            font-weight: bold;
        }

        main {
            flex-grow: 1;
            padding: 20px;
            background-color: white;
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
            // const page = window.location.hash ? window.location.hash.substring(1) : 'register.php';
            loadPage(page);
        });
    </script>
</head>
<body>

<div class="container">
    <!-- Sidebar -->
    <nav>
        <ul>

            <li><a href="profile_police.php">Profils</a></li>
            <li><a href="view_notificationspolice.php">View Notifications </a></li>
            <!-- <li><a href="view_car_report.php">View Car Reports</a></li> -->
            <li><a href="passenger_counter.php">Passenger Counter</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- Main content container -->
    <main>
        <!-- Fixed top summary section -->
        <section id="fixed-summary">
            <center ><h1 style="color: #3498db;">POLICE DASHBOARD</h1></center>

            <?php include 'policeInfo.php'; ?>
        </section>

        <!-- Page-specific dynamic content -->
        <section id="main-content">
            <p>Loading...</p>
        </section>
    </main>
</div>

</body>
</html>

<!-- <?php $conn->close(); ?> -->
