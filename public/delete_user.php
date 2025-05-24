<?php
session_start();
require_once '../config/db.php';

// ✅ Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// ✅ Check if ID is passed
if (!isset($_GET['id'])) {
    header('Location: admin_dashboard.php');
    exit;
}

$id = intval($_GET['id']);

$db = (new Database())->connect(); // using mysqli

// ✅ Prevent self-deletion
if ($_SESSION['user_id'] == $id) {
    header('Location: admin_dashboard.php?error=cannot_delete_self');
    exit;
}

// ✅ Check table for correct ID column: likely `user_id` not `id`
$query = "DELETE FROM users WHERE user_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header('Location: admin_dashboard.php?success=user_deleted');
} else {
    header('Location: admin_dashboard.php?error=delete_failed');
}
exit;
?>
