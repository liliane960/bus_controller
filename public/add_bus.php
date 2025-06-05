<?php
require_once '../config/db.php';

$db = new Database();
$conn = $db->connect();

$driverResult = $conn->query("SELECT user_id, username FROM users WHERE role = 'driver'");

// Handle success message passed via GET (e.g., after redirect from add_bu_hhandle.php)
$message = isset($_GET['message']) ? $_GET['message'] : '';
$isSuccess = strpos($message, '✅') !== false;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register New Bus</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Register New Bus</h1>

    <?php if (!empty($message)): ?>
        <p style="color: <?= $isSuccess ? 'green' : 'red' ?>;">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form action="add_bu_hhandle.php" method="POST">
        <label for="plate_number">Plate Number:</label><br>
        <input 
            type="text" 
            name="plate_number" 
            id="plate_number" 
            placeholder="Eg: RAD000A" 
            required 
            pattern="[A-Z]{3}[0-9]{3}[A-Z]" 
            title="Format: 3 letters, 3 numbers, 1 letter (e.g., RAA123B). 'RAD000A' is not allowed."><br><br>

        <label for="capacity">Capacity:</label><br>
        <input 
            type="number" 
            name="capacity" 
            id="capacity" 
            placeholder="Enter capacity (1 - 80)" 
            min="1" 
            max="80" 
            required><br><br>

        <label for="driver_id">Assign Driver:</label><br>
        <select name="driver_id" id="driver_id" required>
            <option value="">-- Select Driver --</option>
            <?php while ($driver = $driverResult->fetch_assoc()): ?>
                <option value="<?= $driver['user_id'] ?>"><?= htmlspecialchars($driver['username']) ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <button type="submit">Register Bus</button>
    </form>

    <script>
        document.getElementById('capacity').addEventListener('input', function () {
            const value = parseInt(this.value, 10);
            if (value < 1) this.value = 1;
            if (value > 80) this.value = 80;
        });

        document.getElementById('plate_number').addEventListener('input', function () {
            if (this.value.toUpperCase() === 'RAD000A') {
                alert("'RAD000A' is not allowed.");
                this.value = '';
            }
        });

        // If successful, reload after 3 seconds
        <?php if ($isSuccess): ?>
        setTimeout(function () {
            location.reload();
        }, 3000);
        <?php endif; ?>
    </script>
</body>
</html>
