<?php
require_once '../config/db.php';

$db = new Database();
$conn = $db->connect();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if user_id is provided via GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("User ID is required.");
}

$user_id = intval($_GET['id']);

// Prepare statement to prevent SQL injection
$stmt = $conn->prepare("SELECT user_id, username, email, role, created_at FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found.");
}

$user = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View User Details</title>
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
    <h1>User Details</h1>
    <!-- <a href="admin_dashboard.php">Back to Dashboard</a> -->
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>User ID</th>
            <td><?= htmlspecialchars($user['user_id']) ?></td>
        </tr>
        <tr>
            <th>Username</th>
            <td><?= htmlspecialchars($user['username']) ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?= htmlspecialchars($user['email']) ?></td>
        </tr>
        <tr>
            <th>Role</th>
            <td><?= htmlspecialchars(ucfirst($user['role'])) ?></td>
        </tr>
        <tr>
            <th>Created At</th>
            <td><?= htmlspecialchars($user['created_at']) ?></td>
        </tr>
    </table>
</body>
</html>
