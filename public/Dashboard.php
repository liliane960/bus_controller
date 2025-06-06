<?php
require_once '../config/db.php';
$db = new Database();
$conn = $db->connect();

$adminCount = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'admin'")->fetch_assoc()['total'];
$driverCount = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'driver'")->fetch_assoc()['total'];
$notificatCount = $conn->query("SELECT COUNT(*) as total FROM notifications ")->fetch_assoc()['total'];
$policeCount = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'police'")->fetch_assoc()['total'];
?>

<!-- Always displayed user summary -->
 <center ><h1 style="color: #3498db;"> ADMIN DASHBOARD</h1></center>
<!-- <h2>User Summary</h2> -->
<div style="display: flex; gap: 20px; margin-top: 20px;">
    <div style="flex: 1; background: #eafaf1; padding: 15px; border-radius: 8px; text-align: center;">
        <h3>Total Admins</h3>
        <!-- <p style="font-size: 24px; color: #16a085;"><?= $adminCount ?></p> -->
        <p style="font-size: 24px; color: #3498db;"><?= $adminCount ?></p>
    </div>
    <div style="flex: 1; background: #fdf6e3; padding: 15px; border-radius: 8px; text-align: center;">
        <h3>Total Drivers</h3>
        <p style="font-size: 24px; color: #d35400;"><?= $driverCount ?></p>
    </div>
    <div style="flex: 1; background: #fce4ec; padding: 15px; border-radius: 8px; text-align: center;">
        <h3>Total Police</h3>
        <p style="font-size: 24px; color: #c0392b;"><?= $policeCount ?></p>
    </div>
    <div style="flex: 1; background: #fdf6e3; padding: 15px; border-radius: 8px; text-align: center;">
        <h3>Total Notifications</h3>
        <p style="font-size: 24px; color: #d35400;"><?= $notificatCount ?></p>
            <?php if (isset($_GET['msg'])): ?>
        <p class="success-msg"><?= htmlspecialchars($_GET['msg']) ?></p>
    <?php endif; ?>
    </div>
</div>

<?php $conn->close(); ?>
