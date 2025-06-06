<?php
require_once '../config/db.php';
$db = new Database();
$conn = $db->connect();

// Validate and fetch user ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid user ID.");
}
$userId = (int)$_GET['id'];

// Handle POST submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $password = $_POST['password'] ?? ''; // Optional: only update if filled

    if (empty($username) || empty($email) || empty($role)) {
        $error = "All fields except password are required.";
    } else {
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $updateSql = "UPDATE users SET username = ?, email = ?, role = ?, password = ? WHERE user_id = ?";
            $stmt = $conn->prepare($updateSql);
            $stmt->bind_param('ssssi', $username, $email, $role, $hashedPassword, $userId);
        } else {
            $updateSql = "UPDATE users SET username = ?, email = ?, role = ? WHERE user_id = ?";
            $stmt = $conn->prepare($updateSql);
            $stmt->bind_param('sssi', $username, $email, $role, $userId);
        }

        if ($stmt->execute()) {
            header("Location: admin_dashboard.php?msg=User+updated+successfully");
            exit;
        } else {
            $error = "Failed to update user.";
        }
    }
}

// Fetch user data for form display
$userSql = "SELECT user_id, username, email, role, created_at FROM users WHERE user_id = ?";
$stmt = $conn->prepare($userSql);
$stmt->bind_param('i', $userId);
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
    <title>Edit User</title>
    <style>
        body { font-family: Arial; max-width: 600px; margin: 40px auto; }
        label { display: block; margin-top: 12px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; margin-top: 5px; }
        input[type="submit"] { margin-top: 20px; background: #3498db; color: white; cursor: pointer; }
        .error { color: red; margin-top: 10px; }
    </style>
</head>
<body>

    <h1>Edit User Id: <?= htmlspecialchars($user['user_id']) ?></h1>

    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required value="<?= htmlspecialchars($user['username']) ?>">

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required value="<?= htmlspecialchars($user['email']) ?>">

        <label for="role">Role:</label>
        <select name="role" id="role" required>
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="driver" <?= $user['role'] === 'driver' ? 'selected' : '' ?>>Driver</option>
            <option value="police" <?= $user['role'] === 'police' ? 'selected' : '' ?>>Police</option>
        </select>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password">

        <input type="submit" value="Update User">
    </form>

    <p><a href="view_users.php">← Back to Users List</a></p>

</body>
</html>
