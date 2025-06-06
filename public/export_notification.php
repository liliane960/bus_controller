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

// Prepare SQL based on role
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

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=notifications_export.csv');

// Open output stream
$output = fopen('php://output', 'w');

// Output column headers
fputcsv($output, ['Notification ID', 'Bus ID', 'Message', 'Sent At', 'Comment']);

// Output each row
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['notification_id'],
        $row['bus_id'],
        $row['message'],
        $row['sent_at'],
        $row['comment']
    ]);
}

fclose($output);
$stmt->close();
$conn->close();
exit;
?>
