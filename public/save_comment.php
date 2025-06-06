<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['notification_id']) || !isset($data['comment'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$notificationId = intval($data['notification_id']);
$comment = trim($data['comment']);

$db = new Database();
$conn = $db->connect();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'DB connection error']);
    exit;
}

// Optional: Add permission checks here if needed

$stmt = $conn->prepare("UPDATE notifications SET comment = ? WHERE notification_id = ?");
$stmt->bind_param("si", $comment, $notificationId);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
