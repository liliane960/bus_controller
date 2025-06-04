<?php
require_once '../config/db.php';

$db = new Database();
$conn = $db->connect();

// Fetch all buses with driver info
$sql = "SELECT b.bus_id, b.plate_number, b.capacity, b.status, u.username AS driver_name 
        FROM buses b 
        LEFT JOIN users u ON b.driver_id = u.user_id 
        ORDER BY b.bus_id ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View All Buses</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Bus List</h1>
    <a href="add_bus.php">Add New Bus</a> |
    <!-- <a href="admin_dashboard.php">Back to Dashboard</a> -->
    <br><br>

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Bus ID</th>
                <th>Plate Number</th>
                <th>Capacity</th>
                <th>Status</th>
                <th>Driver</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($bus = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $bus['bus_id'] ?></td>
                        <td><?= htmlspecialchars($bus['plate_number']) ?></td>
                        <td><?= $bus['capacity'] ?></td>
                        <td><?= ucfirst($bus['status']) ?></td>
                        <td><?= htmlspecialchars($bus['driver_name'] ?? 'Not Assigned') ?></td>
                        <td>
                            <a href="edit_bus.php?id=<?= $bus['bus_id'] ?>">Edit</a> |
                            <a href="delete_bus.php?id=<?= $bus['bus_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">No buses found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

<?php $conn->close(); ?>
