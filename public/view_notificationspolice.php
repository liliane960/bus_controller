<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    die("Access denied. Please log in.");
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'];

$db = new Database();
$conn = $db->connect();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch notifications based on role
if ($userRole === 'admin' || $userRole === 'police') {
    $sql = "SELECT notification_id, bus_id, message, sent_at, comment FROM notifications ORDER BY sent_at DESC";
    $stmt = $conn->prepare($sql);
} elseif ($userRole === 'driver') {
    $sql = "SELECT n.notification_id, n.bus_id, n.message, n.sent_at, n.comment
            FROM notifications n
            INNER JOIN buses b ON n.bus_id = b.bus_id
            WHERE b.driver_id = ?
            ORDER BY n.sent_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
} else {
    die("Unauthorized role.");
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Notifications police</title>
    <style>
        table { border-collapse: collapse; width: 90%; margin: 30px auto; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .success-msg { text-align: center; color: green; font-weight: bold; margin-top: 20px; }
        .edit-link { text-decoration: none; color: #2980b9; }
        .edit-link:hover { text-decoration: underline; }
        body { font-family: Arial, sans-serif; }

        th {
            background-color: #3498db;
            color: white;
        }
    </style>
</head>
<body>

    <h1 style="text-align: center; margin-top: 100px; font-size: 20px;">Notifications police</h1>
    <a href="export_notification.php" target="_blank">Export to CSV</a>
    <?php if (isset($_GET['msg'])): ?>
        <p class="success-msg"><?= htmlspecialchars($_GET['msg']) ?></p>
    <?php endif; ?>

    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>notification_id</th>
                    <th>Bus ID</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Comment</th>
                    <!-- <th>Action</th> -->
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['notification_id'] ?></td>
                        <td><?= htmlspecialchars($row['bus_id']) ?></td>
                        <td><?= htmlspecialchars($row['message']) ?></td>
                        <td><?= $row['sent_at'] ?></td>
                        <td><?= htmlspecialchars($row['comment']) ?></td>
                        <!-- <td>
                            <a class="edit-link" href="edit_comment.php?notification_id=<?= $row['notification_id'] ?>">Edit</a>
                        </td> -->
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align:center;">No notifications available.</p>
    <?php endif; ?>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
