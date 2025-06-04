<?php
require_once '../config/db.php';
$db = new Database();
$conn = $db->connect();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f2f2f2;
        }

        header {
            background-color: #007BFF;
            padding: 20px;
            color: white;
            text-align: center;
        }

        nav {
            background-color: #333;
        }

        nav ul {
            margin: 0;
            padding: 0;
            list-style: none;
            display: flex;
            justify-content: center;
        }

        nav ul li {
            margin: 0;
        }

        nav ul li a {
            display: block;
            padding: 15px 20px;
            color: white;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        nav ul li a:hover {
            background-color: #555;
        }

        .container {
            padding: 20px;
            margin: 20px;
            background: white;
            min-height: 400px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #444;
        }
    </style>

    <script>
        function loadPage(page) {
            fetch(page)
                .then(response => {
                    if (!response.ok) throw new Error("Failed to load page.");
                    return response.text();
                })
                .then(html => {
                    document.querySelector('.container').innerHTML = html;
                })
                .catch(err => {
                    document.querySelector('.container').innerHTML = `<p style="color:red;">${err.message}</p>`;
                });
        }

        // Load default page when dashboard loads
        window.onload = function () {
            loadPage('view_profile.php'); // Default content
        }
    </script>
</head>
<body>

    <header>
        <h1>Driver Dashboard</h1>
    </header>

    <nav>
        <ul>
            <li><a href="#" onclick="loadPage('view_profile.php')">View Profile</a></li>
            <li><a href="#" onclick="loadPage('view_notifications.php')">View Notifications</a></li>
            <li><a href="#" onclick="loadPage('view_car_report.php')">View Car Reports</a></li>
            <li><a href="#" onclick="loadPage('passenger_counter.php')">Passenger Counter</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <h2>Loading...</h2>
    </div>

</body>
</html>
