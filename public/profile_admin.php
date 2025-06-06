<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Admin only.");
}

$userId = $_SESSION['user_id'];
$db = new Database();
$conn = $db->connect();

// Fetch admin user info
$sql = "SELECT username, email, role, created_at FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Optional: Fetch system statistics
$totalUsers = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$totalDrivers = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='driver'")->fetch_assoc()['total'];
$totalNotifications = $conn->query("SELECT COUNT(*) as total FROM notifications")->fetch_assoc()['total'];

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Profile</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 40px auto; }
        h1, h2 { color: #333; }
        .info { margin-bottom: 30px; }
    </style>
</head>
<body>

    <h1>Admin Profile</h1>

    <div class="info">
        <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
        <p><strong>Joined on:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
    </div>

    <h2>System Summary</h2>
    <ul>
        <li>Total Users: <?= $totalUsers ?></li>
        <li>Total Drivers: <?= $totalDrivers ?></li>
        <li>Total Notifications: <?= $totalNotifications ?></li>
    </ul>

    <p><a href="admin_dashboard.php">← Back to Dashboard</a></p>

</body>
</html>
