<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    die("Access denied. Please log in.");
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'];

$db = new Database();
$conn = $db->connect();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Query notifications depending on role
if ($userRole === 'admin' || $userRole === 'police') {
    $sql = "SELECT notification_id, bus_id, message, sent_at, comment FROM notifications ORDER BY sent_at DESC";
    $stmt = $conn->prepare($sql);
} elseif ($userRole === 'driver') {
    $sql = "SELECT n.notification_id, n.bus_id, n.message, n.sent_at, n.comment
            FROM notifications n
            INNER JOIN buses b ON n.bus_id = b.bus_id
            WHERE b.driver_id = ?
            ORDER BY n.sent_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
} else {
    die("Unauthorized role.");
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Notifications</title>
    <style>
        table { border-collapse: collapse; width: 80%; margin: auto; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        input.comment-input { width: 90%; }
        button.save-comment-btn { cursor: pointer; }
        span.status-msg { font-weight: bold; margin-left: 10px; }
    </style>
</head>
<body>
    <h1 style="text-align:center;">Notifications</h1>

    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Bus ID</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Comment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['notification_id'] ?></td>
                    <td><?= htmlspecialchars($row['bus_id']) ?></td>
                    <td><?= htmlspecialchars($row['message']) ?></td>
                    <td><?= $row['sent_at'] ?></td>
                    <td><?= $row['comment'] ?></td>
                    <td> </td>
                    <!-- <td>
                        <input type="text" 
                               class="comment-input" 
                               data-id="<?= $row['notification_id'] ?>" 
                               value="<?= htmlspecialchars($row['comment']) ?>">
                        <button class="save-comment-btn" data-id="<?= $row['notification_id'] ?>">Save</button>
                        <span class="status-msg" id="status-<?= $row['notification_id'] ?>"></span>
                    </td> -->
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align:center;">No notifications available.</p>
    <?php endif; ?>

<script>
document.querySelectorAll('.save-comment-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        const input = document.querySelector(`input.comment-input[data-id='${id}']`);
        const comment = input.value;

        fetch('save_comment.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({notification_id: id, comment: comment})
        })
        .then(res => res.json())
        .then(data => {
            const status = document.getElementById('status-' + id);
            if(data.success){
                status.textContent = 'Saved!';
                status.style.color = 'green';
            } else {
                status.textContent = 'Failed!';
                status.style.color = 'red';
            }
            setTimeout(() => { status.textContent = ''; }, 3000);
        })
        .catch(() => {
            const status = document.getElementById('status-' + id);
            status.textContent = 'Error!';
            status.style.color = 'red';
            setTimeout(() => { status.textContent = ''; }, 3000);
        });
    });
});
</script>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
