<?php
session_start();
require_once '../config/db.php';

// Only allow drivers to access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    die("Access denied. Driver only.");
}

$userId = $_SESSION['user_id'];
$db = new Database();
$conn = $db->connect();

// Fetch driver user info (removed email column as it doesn't exist)
$sql = "SELECT username, role, created_at FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Fetch driver's assigned buses
$busSql = "SELECT bus_id, plate_number, capacity, status FROM buses WHERE driver_id = ?";
$busStmt = $conn->prepare($busSql);
$busStmt->bind_param("i", $userId);
$busStmt->execute();
$buses = $busStmt->get_result();
$busStmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Driver Profile</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 40px auto; }
        h1, h2 { color: #333; }
        .info, .buses { margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 6px; background: #f9f9f9; }
        p { margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>

    <h1 style="margin-top: 100px; font-size: 20px;">Driver Profile</h1>

    <div class="info">
        <h2>Account Info</h2>
        <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
        <p><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
        <p><strong>Joined On:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
    </div>

    <div class="buses">
        <h2>Assigned Buses</h2>
        <?php if ($buses->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Bus ID</th>
                    <th>Plate Number</th>
                    <th>Capacity</th>
                    <th>Status</th>
                </tr>
                <?php while ($bus = $buses->fetch_assoc()): ?>
                    <tr>
                        <td><?= $bus['bus_id'] ?></td>
                        <td><?= htmlspecialchars($bus['plate_number']) ?></td>
                        <td><?= $bus['capacity'] ?></td>
                        <td><?= ucfirst($bus['status']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No buses assigned yet.</p>
        <?php endif; ?>
    </div>

    <!-- <p><a href="driver_dashboard.php">← Back to Dashboard</a></p> -->

</body>
</html>
