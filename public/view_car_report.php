<?php
require_once '../config/db.php';

$db = new Database();
$conn = $db->connect();

$sql = "
    SELECT b.bus_id, b.plate_number, b.capacity, b.status, u.username AS driver_name
    FROM buses b
    LEFT JOIN users u ON b.driver_id = u.user_id
    ORDER BY b.bus_id ASC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Car Report</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1 style="margin-top: 100px; font-size: 20px;">Car Report</h1>
    <a href="export_car_counts.php" target="_blank">Export to CSV</a>
    <!-- <a href="admin_dashboard.php">Back to Dashboard</a><br><br> -->

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Bus ID</th>
                <th>Plate Number</th>
                <th>Capacity</th>
                <th>Status</th>
                <th>Driver</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['bus_id']) ?></td>
                        <td><?= htmlspecialchars($row['plate_number']) ?></td>
                        <td><?= htmlspecialchars($row['capacity']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= htmlspecialchars($row['driver_name'] ?? 'Unassigned') ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">No bus records found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

<?php $conn->close(); ?>
