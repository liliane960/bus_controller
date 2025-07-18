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

// Validate notification_id
if (!isset($_GET['notification_id']) || !is_numeric($_GET['notification_id'])) {
    echo "<p style='color:red;'>Invalid notification ID. <a href='driver_dashboard.php'>Go back</a></p>";
    exit;
}

$notificationId = (int)$_GET['notification_id'];
$error = "";
$success = "";

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newComment = trim($_POST['comment'] ?? '');

    if ($userRole === 'driver') {
        $checkSql = "SELECT n.notification_id
                     FROM notifications n
                     INNER JOIN buses b ON n.bus_id = b.bus_id
                     WHERE n.notification_id = ? AND b.driver_id = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param('ii', $notificationId, $userId);
        $checkStmt->execute();
        $checkStmt->store_result();
        if ($checkStmt->num_rows === 0) {
            echo "<p style='color:red;'>Unauthorized to edit this comment.</p>";
            exit;
        }
        $checkStmt->close();
    } elseif (!in_array($userRole, ['admin', 'police'])) {
        echo "<p style='color:red;'>Unauthorized role.</p>";
        exit;
    }

    $updateSql = "UPDATE notifications SET comment = ? WHERE notification_id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param('si', $newComment, $notificationId);

    if ($updateStmt->execute()) {
        $updateStmt->close();
        $conn->close();

        // Redirect based on role
        if ($userRole === 'driver') {
            header("Location: driver_dashboard.php?msg=Comment+updated+successfully");
        } else {
            header("Location: admin_dashboard.php?msg=Comment+updated+successfully");
        }
        exit;
    } else {
        $error = "Failed to update comment.";
    }
}

// Fetch notification info
$sql = "SELECT notification_id, bus_id, message, sent_at, comment FROM notifications WHERE notification_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $notificationId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p style='color:red;'>Notification not found. <a href='driver_dashboard.php'>Go back</a></p>";
    exit;
}

$notification = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Comment</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; background-color: #f5f5f5; padding: 20px; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        textarea { width: 100%; height: 100px; margin-top: 5px; }
        input[type="submit"] { margin-top: 15px; padding: 10px 20px; cursor: pointer; background-color: #3498db; color: white; border: none; border-radius: 4px; }
        .message { color: red; margin-top: 15px; }
        a.back-link { display: inline-block; margin-top: 20px; color: #3498db; text-decoration: none; }
    </style>
</head>
<body>

    <h1>Edit Comment for Notification #<?= $notification['notification_id'] ?></h1>

    <p><strong>Bus ID:</strong> <?= htmlspecialchars($notification['bus_id']) ?></p>
    <p><strong>Message:</strong> <?= htmlspecialchars($notification['message']) ?></p>
    <p><strong>Date:</strong> <?= htmlspecialchars($notification['sent_at']) ?></p>

    <?php if (!empty($error)): ?>
        <p class="message"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label for="comment">Comment:</label>
        <textarea name="comment" id="comment" required><?= htmlspecialchars($notification['comment']) ?></textarea>
        <br>
        <input type="submit" value="Save Comment">
    </form>

    <?php if ($userRole === 'driver'): ?>
        <a class="back-link" href="driver_dashboard.php">← Back to Dashboard</a>
    <?php else: ?>
        <a class="back-link" href="view_notification.php">← Back to Notifications</a>
    <?php endif; ?>

</body>
</html>
