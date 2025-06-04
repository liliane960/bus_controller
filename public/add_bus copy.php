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
        $message = "All fields are required and must be valid.";
    } else {
        $stmt = $conn->prepare("INSERT INTO buses (plate_number, capacity, driver_id, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siis", $plate_number, $capacity, $driver_id, $status);

        if ($stmt->execute()) {
            $message = "✅ Bus added successfully.";
        } else {
            $message = "❌ Error: " . $stmt->error;
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

<form method="POST" onsubmit="submitBusForm(event)">
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

    <button type="submit">Add Bus</button>
</form>

<script>
function submitBusForm(event) {
    event.preventDefault();

    const formData = new FormData(event.target);

    fetch('add_bus.php', {
        method: 'POST',
        body: formData
    })
    .then(resp => resp.text())
    .then(html => {
        document.getElementById('main-content').innerHTML = html;
    })
    .catch(error => {
        document.getElementById('main-content').innerHTML = `<p style="color:red;">Error: ${error.message}</p>`;
    });
}
</script>
