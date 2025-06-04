<?php
require_once '../config/db.php';

$db = new Database();
$conn = $db->connect();

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="passenger_counts.csv"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');

// Output the column headings
fputcsv($output, ['Count ID', 'Bus ID', 'Plate Number', 'Passenger Count', 'Recorded At']);

// Fetch passenger count data with bus plate numbers
$sql = "
    SELECT pc.count_id, pc.bus_id, b.plate_number, pc.passenger_count, pc.recorded_at
    FROM passenger_counts pc
    LEFT JOIN buses b ON pc.bus_id = b.bus_id
    ORDER BY pc.recorded_at DESC
";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['count_id'],
            $row['bus_id'],
            $row['plate_number'],
            $row['passenger_count'],
            $row['recorded_at']
        ]);
    }
}

fclose($output);
$conn->close();
exit;
