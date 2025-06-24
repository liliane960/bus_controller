<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Admin only.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $username = trim($_POST['username']);
    $role = $_POST['role'];
    $newPassword = trim($_POST['new_password'] ?? '');

    if (empty($username) || empty($role)) {
        $error = "Username and role are required.";
    } else {
        $db = new Database();
        $conn = $db->connect();

        if (!empty($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateSql = "UPDATE users SET username = ?, role = ?, password = ? WHERE user_id = ?";
            $stmt = $conn->prepare($updateSql);
            $stmt->bind_param('sssi', $username, $role, $hashedPassword, $userId);
        } else {
            $updateSql = "UPDATE users SET username = ?, role = ? WHERE user_id = ?";
            $stmt = $conn->prepare($updateSql);
            $stmt->bind_param('ssi', $username, $role, $userId);
        }

        if ($stmt->execute()) {
            header("Location: view_users.php?msg=User updated successfully");
            exit();
        } else {
            $error = "Error updating user: " . $conn->error;
        }
        $stmt->close();
        $conn->close();
    }
}

// Fetch user data for editing
$userId = $_GET['id'] ?? null;
if (!$userId) {
    die("User ID required.");
}

$db = new Database();
$conn = $db->connect();

$userSql = "SELECT user_id, username, role, created_at FROM users WHERE user_id = ?";
$stmt = $conn->prepare($userSql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$user) {
    die("User not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; padding: 20px; }
        h1 { color: #333; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="password"], select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #3498db; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #2980b9; }
        .error { color: red; margin-bottom: 15px; }
        .info { background: #f9f9f9; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>

    <h1 style="margin-top: 100px; font-size: 20px;">Edit User</h1>

    <?php if (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <div class="info">
        <p><strong>User ID:</strong> <?= $user['user_id'] ?></p>
        <p><strong>Created:</strong> <?= $user['created_at'] ?></p>
    </div>

    <form method="POST">
        <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
        
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required value="<?= htmlspecialchars($user['username']) ?>">
        </div>

        <div class="form-group">
            <label for="role">Role:</label>
            <select name="role" id="role" required>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="driver" <?= $user['role'] === 'driver' ? 'selected' : '' ?>>Driver</option>
                <option value="police" <?= $user['role'] === 'police' ? 'selected' : '' ?>>Police</option>
            </select>
        </div>

        <div class="form-group">
            <label for="new_password">New Password (leave blank to keep current):</label>
            <input type="password" name="new_password" id="new_password">
        </div>

        <button type="submit">Update User</button>
        <a href="view_users.php" style="margin-left: 10px; text-decoration: none; color: #3498db;">Cancel</a>
    </form>

</body>
</html>
