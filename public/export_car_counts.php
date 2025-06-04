<?php
require_once '../config/db.php';
$db = new Database();
$conn = $db->connect();

// Set headers to trigger download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="car_counts.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Output CSV column headers
fputcsv($output, ['Bus ID', 'Plate Number', 'Capacity', 'Status', 'Driver ID']);

// Fetch and write data
$sql = "SELECT `bus_id`, `plate_number`, `capacity`, `status`, `driver_id` FROM `buses`";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['bus_id'],
            $row['plate_number'],
            $row['capacity'],
            $row['status'],
            $row['driver_id']
        ]);
    }
} else {
    // No data found
    fputcsv($output, ['No data available']);
}

fclose($output);
$conn->close();
exit;
