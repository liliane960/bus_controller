<?php
require_once '../config/db.php';
$db = new Database();
$conn = $db->connect();
// include 'view_notificationspolice.php';

// $driverCount = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'driver'")->fetch_assoc()['total'];
$notificatCount = $conn->query("SELECT COUNT(*) as total FROM notifications ")->fetch_assoc()['total'];
$policeCount = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'police'")->fetch_assoc()['total'];
?>

<!-- Always displayed user summary -->

<div style="display: flex; gap: 20px; margin-top: 20px;">
    <div style="flex: 1; background: #fdf6e3; padding: 15px; border-radius: 8px; text-align: center;">
        <h3>Total Notifications</h3>
        <p style="font-size: 24px; color: #d35400;"><?= $notificatCount ?></p>
            <?php if (isset($_GET['msg'])): ?>
        <p class="success-msg"><?= htmlspecialchars($_GET['msg']) ?></p>
    <?php endif; ?>
    </div>
    <div style="flex: 1; background: #fce4ec; padding: 15px; border-radius: 8px; text-align: center;">
        <h3>Total Police</h3>
        <p style="font-size: 24px; color: #c0392b;"><?= $policeCount ?></p>
    </div>
</div>

<?php $conn->close(); ?>
