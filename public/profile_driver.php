<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    die("Access denied. Driver only.");
}

$userId = $_SESSION['user_id'];
$db = new Database();
$conn = $db->connect();

// Fetch driver info
$sql = "SELECT username, email, role, created_at FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Fetch assigned bus
$busSql = "SELECT bus_id, plate_number FROM buses WHERE driver_id = ?";
$busStmt = $conn->prepare($busSql);
$busStmt->bind_param("i", $userId);
$busStmt->execute();
$busResult = $busStmt->get_result();
$bus = $busResult->fetch_assoc();
$busStmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Driver Profile</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 40px auto; }
        h1, h2 { color: #333; }
        .info { margin-bottom: 30px; }
    </style>
</head>
<body>

    <h1>Driver Profile</h1>

    <div class="info">
        <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
        <p><strong>Joined on:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
    </div>

    <h2>Assigned Bus</h2>
    <?php if ($bus): ?>
        <p><strong>Bus ID:</strong> <?= htmlspecialchars($bus['bus_id']) ?></p>
        <p><strong>Plate Number:</strong> <?= htmlspecialchars($bus['plate_number']) ?></p>
    <?php else: ?>
        <p>You have no bus assigned yet.</p>
    <?php endif; ?>

    <!-- <p><a href="driver_dashboard.php">← Back to Dashboard</a></p> -->

</body>
</html>
