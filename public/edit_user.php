<?php
session_start();
require_once '../config/db.php';

// Only admin can access
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$db = (new Database())->getConnection();

if (!isset($_GET['id'])) {
    header('Location: admin_dashboard.php');
    exit;
}

$id = intval($_GET['id']);
$message = "";

// Fetch user data
$query = "SELECT id, username, role FROM users WHERE id = :id LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: admin_dashboard.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $role = $_POST['role'];
    $password = $_POST['password'];  // Optional

    if (empty($username) || empty($role)) {
        $message = "Username and Role are required.";
    } else {
        // Check if password is set to update it
        if (!empty($password)) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $updateQuery = "UPDATE users SET username = :username, role = :role, password = :password WHERE id = :id";
            $stmt = $db->prepare($updateQuery);
            $stmt->bindParam(':password', $passwordHash);
        } else {
            $updateQuery = "UPDATE users SET username = :username, role = :role WHERE id = :id";
            $stmt = $db->prepare($updateQuery);
        }

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            $message = "User updated successfully.";
            // Refresh data
            $user['username'] = $username;
            $user['role'] = $role;
        } else {
            $message = "Error updating user.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
    <h1>Edit User</h1>
    <a href="admin_dashboard.php">Back to Dashboard</a>

    <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Username:</label><br />
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required /><br />

        <label>Role:</label><br />
        <select name="role" required>
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="manager" <?= $user['role'] === 'manager' ? 'selected' : '' ?>>Manager</option>
            <option value="driver" <?= $user['role'] === 'driver' ? 'selected' : '' ?>>Driver</option>
        </select><br />

        <label>New Password (leave blank to keep current):</label><br />
        <input type="password" name="password" /><br /><br />

        <button type="submit">Update User</button>
    </form>
</body>
</html>
