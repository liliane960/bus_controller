<?php
session_start();
require_once '../config/db.php';

// Only allow police to access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'police') {
    die("Access denied. Police only.");
}

$userId = $_SESSION['user_id'];
$db = new Database();
$conn = $db->connect();

// Fetch police user info (removed email column as it doesn't exist)
$sql = "SELECT username, role, created_at FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Fetch recent notifications
$notificationSql = "SELECT n.notification_id, n.bus_id, b.plate_number, n.message, n.sent_at 
                    FROM notifications n 
                    JOIN buses b ON n.bus_id = b.bus_id 
                    ORDER BY n.sent_at DESC 
                    LIMIT 10";
$notifications = $conn->query($notificationSql);

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Police Profile</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 40px auto; }
        h1, h2 { color: #333; }
        .info, .notifications { margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 6px; background: #f9f9f9; }
        p { margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>

    <h1 style="margin-top: 100px; font-size: 20px;">Police Profile</h1>

    <div class="info">
        <h2>Account Info</h2>
        <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
        <p><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
        <p><strong>Joined On:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
    </div>

    <div class="notifications">
        <h2>Recent Notifications</h2>
        <?php if ($notifications && $notifications->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Bus ID</th>
                    <th>Plate Number</th>
                    <th>Message</th>
                    <th>Date</th>
                </tr>
                <?php while ($notification = $notifications->fetch_assoc()): ?>
                    <tr>
                        <td><?= $notification['bus_id'] ?></td>
                        <td><?= htmlspecialchars($notification['plate_number']) ?></td>
                        <td><?= htmlspecialchars($notification['message']) ?></td>
                        <td><?= $notification['sent_at'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No recent notifications.</p>
        <?php endif; ?>
    </div>

</body>
</html>
