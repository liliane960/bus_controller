<?php
require_once '../config/db.php';

$db = new Database();
$conn = $db->connect();

// Validate and get bus ID
$bus_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($bus_id <= 0) {
    die("Invalid bus ID.");
}

// Delete query
$stmt = $conn->prepare("DELETE FROM buses WHERE bus_id = ?");
$stmt->bind_param("i", $bus_id);

if ($stmt->execute()) {
    header("Location: view_buses.php");
    exit;
} else {
    echo "Error deleting bus: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
