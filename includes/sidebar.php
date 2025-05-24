<aside class="sidebar">
    <ul>
        <li><a href="index.php">Dashboard</a></li>
        <li><a href="passenger_data.php">Passenger Data</a></li>
        <li><a href="bus_status.php">Bus Status</a></li>
        <li><a href="alerts.php">Alerts</a></li>
        <li><a href="users.php">Manage Users</a></li>
    </ul>
</aside>
    <div class="content">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['username']); ?></h2>
        <p>Your role: <?= htmlspecialchars($_SESSION['role']); ?></p>
        <p>Current Passenger Count: <?= $currentCount; ?></p>
    </div>  