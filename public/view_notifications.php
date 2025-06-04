<?php
require_once '../config/db.php';

$db = new Database();
$conn = $db->connect();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch notifications
$sql = "SELECT notification_id, bus_id, message, sent_at, status FROM notifications ORDER BY sent_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Notifications</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Notifications</h1>
    <!-- <a href="admin_dashboard.php">Back to Dashboard</a> -->
    <br><br>

    <?php if ($result && $result->num_rows > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Message</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['notification_id'] ?></td>
                        <td><?= htmlspecialchars($row['message']) ?></td>
                        <td><?= $row['sent_at'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No notifications available.</p>
    <?php endif; ?>
</body>
</html>

<?php
$conn->close();
?>
