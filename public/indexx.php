<?php
// public/index.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/db.php';
require_once '../models/PassengerCount.php';
require_once '../models/Notification.php';

$database = new Database();
$db = $database->connect();

$passengerCount = new PassengerCount($db);
$notification = new Notification($db);

// Fetch latest passenger counts
$counts = $passengerCount->getAllCounts();

// Fetch recent alerts
$alerts = $notification->getRecent(5);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Dashboard - Smart Bus Monitoring</title>
<link rel="stylesheet" href="css/style.css" />
<script src="js/app.js" defer></script>
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container">
    <h1>Dashboard</h1>
    <h2>Current Passenger Counts</h2>
    <table>
        <thead>
            <tr>
                <th>Bus ID</th>
                <th>Route</th>
                <th>Current Count</th>
                <th>Capacity</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($counts as $count): ?>
                <tr <?= $count['count'] > 50 ? 'class="overload"' : '' ?>>
                    <td><?= htmlspecialchars($count['bus_id']) ?></td>
                    <td><?= htmlspecialchars($count['route']) ?></td>
                    <td><?= htmlspecialchars($count['count']) ?></td>
                    <td>50</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Recent Alerts</h2>
    <ul class="alerts">
        <?php foreach ($alerts as $alert): ?>
            <li><?= htmlspecialchars($alert['message']) ?> — <?= htmlspecialchars($alert['created_at']) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php include '../includes/footer.php'; ?>
</body>
</html>
