<?php
// Correct path to db.php
require_once '../config/db.php';

// Initialize DB connection
$db = new Database();
$conn = $db->connect();

// Check connection (for MySQLi style)
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch all users
$sql = "SELECT user_id, username, role FROM users ORDER BY user_id ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Manage Users</title>
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
    <h1>Admin Dashboard</h1>

    <!-- Admin Navigation -->
    <nav>
        <ul>
            <li><a href="register.php">Add New User</a></li>
            <li><a href="add_car.php">Add Car</a></li>
            <li><a href="add_driver.php">Add Driver</a></li>
            <li><a href="view_notifications.php">View Notifications</a></li>
            <li><a href="car_reports.php">View Car Reports</a></li>
            <li><a href="passenger_counter.php">Passenger Counter</a></li>
        </ul>
    </nav>

    <h2>User Management</h2>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($user = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['user_id']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td>
                            <a href="edit_user.php?id=<?= $user['user_id'] ?>">Edit</a> |
                            <a href="delete_user.php?id=<?= $user['user_id'] ?>" onclick="return confirm('Are you sure?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">No users found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>
