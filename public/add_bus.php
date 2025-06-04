<?php

require_once '../config/db.php';

$db = new Database();
$conn = $db->connect();

// Fetch all drivers
$driverResult = $conn->query("SELECT user_id, username FROM users WHERE role = 'driver'");
?>

<h2>Add New Bus</h2>

<?php if (!empty($message)): ?>
    <p style="color: <?= strpos($message, '✅') !== false ? 'green' : 'red' ?>;">
        <?= htmlspecialchars($message) ?>
    </p>
<?php endif; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Register New bus</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Register New bus</h1>
    <form action="add_bu_hhandle.php" method="POST">
    <label for="plate_number">Plate Number:</label><br>
    <input type="text" name="plate_number" id="plate_number" required><br><br>

    <label for="capacity">Capacity:</label><br>
    <input type="number" name="capacity" id="capacity" required><br><br>
 

        <label for="driver_id">Assign Driver:</label><br>
        <select name="driver_id" id="driver_id" required>
            <option value="">-- Select Driver --</option>
            <?php while ($driver = $driverResult->fetch_assoc()): ?>
                <option value="<?= $driver['user_id'] ?>"><?= htmlspecialchars($driver['username']) ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <button type="submit">Register bus</button>
    </form>
    <br>
</body>
</html>
