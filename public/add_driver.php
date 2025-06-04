<?php
require_once '../config/db.php';

$db = new Database();
$conn = $db->connect();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$message = '';

// Handle driver assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);

    // Update user role to driver
    $stmt = $conn->prepare("UPDATE users SET role = 'driver' WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        $message = "User has been assigned as a driver.";
    } else {
        $message = "Error: " . $conn->error;
    }

    $stmt->close();
}

// Fetch users who are not already drivers
$users = [];
$result = $conn->query("SELECT user_id, username, role FROM users WHERE role != 'driver'");

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Driver</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Assign User as Driver</h1>
    <!-- <a href="admin_dashboard.php">Back to Dashboard</a> -->

    <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if (!empty($users)): ?>
        <form method="POST" action="add_driver.php">
            <label for="user_id">Select User:</label><br>
            <select name="user_id" required>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['user_id'] ?>">
                        <?= htmlspecialchars($user['username']) ?> (<?= $user['role'] ?>)
                    </option>
                <?php endforeach; ?>
            </select><br><br>

            <button type="submit">Assign as Driver</button>
        </form>
    <?php else: ?>
        <p>All users are already assigned as drivers.</p>
    <?php endif; ?>
</body>
</html>

<?php
$conn->close();
?>
