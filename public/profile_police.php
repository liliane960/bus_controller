<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'police') {
    die("Access denied. Police only.");
}

$userId = $_SESSION['user_id'];
$db = new Database();
$conn = $db->connect();

$sql = "SELECT user_id, username, email, role, created_at FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found.");
}

$user = $result->fetch_assoc();
$stmt->close();

// Optional: Fetch police-handled notifications
$notifSql = "SELECT n.notification_id, b.plate_number, n.message, n.sent_at
             FROM notifications n
             JOIN buses b ON n.bus_id = b.bus_id
             ORDER BY n.sent_at DESC LIMIT 5"; // You can filter by police_id if needed

$notifResult = $conn->query($notifSql);
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Police Profile</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 40px auto; }
        h1, h2 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 10px; text-align: left; }
        .info { margin-bottom: 30px; }
    </style>
</head>
<body>

    <h1 style="margin-top: 100px; font-size: 20px;">Police Profile</h1>

    <div class="info">
        <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
        <p><strong>Joined on:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
    </div>

    <!-- <p><a href="police_dashboard.php">← Back to Dashboard</a></p> -->

</body>
</html>
