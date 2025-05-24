<?php
session_start();
require_once '../config/db.php';

// Only admin allowed
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: admin_dashboard.php');
    exit;
}

$id = intval($_GET['id']);

$db = (new Database())->getConnection();

// Prevent admin from deleting themselves (optional)
if ($_SESSION['user_id'] == $id) {
    // Cannot delete yourself
    header('Location: admin_dashboard.php?error=cannot_delete_self');
    exit;
}

$query = "DELETE FROM users WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);

if ($stmt->execute()) {
    header('Location: admin_dashboard.php?success=user_deleted');
} else {
    header('Location: admin_dashboard.php?error=delete_failed');
}
exit;
?>
