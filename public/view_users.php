<?php
require_once '../config/db.php';
$db = new Database();
$conn = $db->connect();

$result = $conn->query("SELECT user_id, username, role FROM users");

$adminCount = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'admin'")->fetch_assoc()['total'];
$driverCount = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'driver'")->fetch_assoc()['total'];
$policeCount = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'police'")->fetch_assoc()['total'];
?>


<h2 style="margin-top: 100px; font-size: 20px;">Users List</h2>
<table border="1" cellpadding="8" cellspacing="0">
    <tr><th>ID</th><th>Username</th><th>Role</th><th>Action</th></tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['user_id']) ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['role']) ?></td>
            <td>
                <a href="edit_user.php?id=<?= $row['user_id'] ?>">Edit</a> |
                <a href="delete_user.php?id=<?= $row['user_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
            <td>
        </tr>
    <?php endwhile; ?>
</table>

<?php $conn->close(); ?>
