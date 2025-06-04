<?php
require_once '../config/db.php';

$db = new Database();
$conn = $db->connect();

// Join with buses table to get plate number
$sql = "
    SELECT p.bus_id, b.plate_number, p.current_count, p.timestamp
    FROM passenger_counts p
    JOIN buses b ON p.bus_id = b.bus_id
    ORDER BY p.timestamp DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Passenger Count Report</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Passenger Count Report</h1>
    <!-- <a href="admin_dashboard.php">Back to Dashboard</a><br><br> -->

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Bus ID</th>
                <th>Plate Number</th>
                <th>Passenger Count</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['bus_id']) ?></td>
                        <td><?= htmlspecialchars($row['plate_number']) ?></td>
                        <td><?= htmlspecialchars($row['current_count']) ?></td>
                        <td><?= htmlspecialchars($row['timestamp']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">No passenger data found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

<?php $conn->close(); ?>
