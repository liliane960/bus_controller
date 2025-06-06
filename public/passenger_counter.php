<?php
require_once '../config/db.php';

$db = new Database();
$conn = $db->connect();

// Fetch passenger count records with bus plate numbers
$sql = "
    SELECT pc.count_id, pc.bus_id, b.plate_number, pc.passenger_count, pc.recorded_at
    FROM passenger_counts pc
    LEFT JOIN buses b ON pc.bus_id = b.bus_id
    ORDER BY pc.recorded_at DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Passenger Counter</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1 style="margin-top: 100px; font-size: 20px;">Passenger Counter Records</h1>
    <!-- <a href="admin_dashboard.php">Back to Dashboard</a><br><br> -->
    <a href="export_passenger_counts.php" target="_blank">Export to CSV</a>
    <br><br>

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Count ID</th>
                <th>Bus ID</th>
                <th>Plate Number</th>
                <th>Passenger Count</th>
                <th>Recorded At</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['count_id']) ?></td>
                        <td><?= htmlspecialchars($row['bus_id']) ?></td>
                        <td><?= htmlspecialchars($row['plate_number']) ?></td>
                        <td><?= htmlspecialchars($row['passenger_count']) ?></td>
                        <td><?= htmlspecialchars($row['recorded_at']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">No records found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

<?php $conn->close(); ?>
