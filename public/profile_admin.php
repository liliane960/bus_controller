<?php
session_start();
require_once '../config/db.php';

// Only allow admins to access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Admin only.");
}

$userId = $_SESSION['user_id'];
$db = new Database();
$conn = $db->connect();

// Fetch admin user info (removed email column as it doesn't exist)
$sql = "SELECT username, role, created_at FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Fetch system statistics
$totalUsers = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$totalDrivers = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='driver'")->fetch_assoc()['total'];
$totalPolice = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='police'")->fetch_assoc()['total'];
$totalNotifications = $conn->query("SELECT COUNT(*) as total FROM notifications")->fetch_assoc()['total'];

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Profile</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 40px auto; }
        h1, h2 { color: #333; }
        .info, .stats { margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 6px; background: #f9f9f9; }
        p { margin: 10px 0; }
    </style>
</head>
<body>

    <h1 style="margin-top: 100px; font-size: 20px;">Admin Profile</h1>

    <div class="info">
        <h2>Account Info</h2>
        <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
        <p><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
        <p><strong>Joined On:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
    </div>

    <div class="stats">
        <h2>System Statistics</h2>
        <p><strong>Total Users:</strong> <?= $totalUsers ?></p>
        <p><strong>Total Drivers:</strong> <?= $totalDrivers ?></p>
        <p><strong>Total Police:</strong> <?= $totalPolice ?></p>
        <p><strong>Total Notifications:</strong> <?= $totalNotifications ?></p>
    </div>

</body>
</html>
