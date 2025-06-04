<?php
session_start();
require_once '../config/db.php';

$db = new Database();
$conn = $db->connect();

$message = '';

// Redirect non-admin users
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<p style='color: red;'>Access denied. Only admins can access this page.</p>";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $plate_number = trim($_POST['plate_number']);
    $capacity = intval($_POST['capacity']);
    $driver_id = intval($_POST['driver_id']);
    $status = 'active';

    if (empty($plate_number) || $capacity <= 0 || $driver_id <= 0) {
        $message = "❌ All fields are required and must be valid.";
    } else {
        // ✅ Check if the driver is already assigned to a bus
        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM buses WHERE driver_id = ?");
        $checkStmt->bind_param("i", $driver_id);
        $checkStmt->execute();
        $checkStmt->bind_result($count);
        $checkStmt->fetch();
        $checkStmt->close();

        if ($count > 0) {
            $message = "❌ This driver is already assigned to another bus. Please choose a different driver.";
        } else {
            // Proceed to insert
            $stmt = $conn->prepare("INSERT INTO buses (plate_number, capacity, driver_id, status) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("siis", $plate_number, $capacity, $driver_id, $status);

            if ($stmt->execute()) {
                $message = "✅ Bus added successfully.";
            } else {
                $message = "❌ Error: " . $stmt->error;
            }
        }
    }
}

// Fetch all drivers
$driverResult = $conn->query("SELECT user_id, username FROM users WHERE role = 'driver'");
?>

<h2>Add New Bus</h2>

<?php if (!empty($message)): ?>
    <p style="color: <?= strpos($message, '✅') !== false ? 'green' : 'red' ?>;">
        <?= htmlspecialchars($message) ?>
    </p>
<?php endif; ?>

<form method="POST">
    <label for="plate_number">Plate Number:</label><br>
    <input type="text" name="plate_number" required><br><br>

    <label for="capacity">Capacity:</label><br>
    <input type="number" name="capacity" required><br><br>

    <label for="driver_id">Assign Driver:</label><br>
    <select name="driver_id" required>
        <option value="">-- Select Driver --</option>
        <?php while ($driver = $driverResult->fetch_assoc()): ?>
            <option value="<?= $driver['user_id'] ?>"><?= htmlspecialchars($driver['username']) ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <button type="submit">Add Bus</button>
</form>
