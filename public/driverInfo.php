<?php
// session_start(); // Start session to access user_id
require_once '../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in.");
}

$db = new Database();
$conn = $db->connect();

$userId = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? '';

// Get bus IDs assigned to the logged-in driver
$busStmt = $conn->prepare("SELECT bus_id FROM buses WHERE driver_id = ?");
$busStmt->bind_param("i", $userId);
$busStmt->execute();
$busResult = $busStmt->get_result();

$busIds = [];
while ($row = $busResult->fetch_assoc()) {
    $busIds[] = $row['bus_id'];
}
$busStmt->close();

$userNotificationCount = 0;

// Count notifications only if driver has buses
if (!empty($busIds)) {
    // Generate placeholders for the IN clause
    $placeholders = implode(',', array_fill(0, count($busIds), '?'));

    $types = str_repeat('i', count($busIds)); // Type string for bind_param

    $notifQuery = "SELECT COUNT(*) AS total FROM notifications WHERE bus_id IN ($placeholders)";
    $notifStmt = $conn->prepare($notifQuery);
    $notifStmt->bind_param($types, ...$busIds);
    $notifStmt->execute();
    $notifResult = $notifStmt->get_result()->fetch_assoc();
    $userNotificationCount = $notifResult['total'];
    $notifStmt->close();
}

// Count total drivers
$driverCount = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'driver'")->fetch_assoc()['total'];

// Count total police
$policeCount = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'police'")->fetch_assoc()['total'];

$conn->close();
?>
