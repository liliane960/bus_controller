<?php
require_once '../config/db.php';

$db = new Database();
$conn = $db->connect();

// Get bus ID from query string
$bus_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($bus_id <= 0) {
    die("Invalid Bus ID.");
}

// Fetch bus details
$busStmt = $conn->prepare("SELECT * FROM buses WHERE bus_id = ?");
$busStmt->bind_param("i", $bus_id);
$busStmt->execute();
$busResult = $busStmt->get_result();

if ($busResult->num_rows !== 1) {
    die("Bus not found.");
}

$bus = $busResult->fetch_assoc();

// Fetch drivers
$drivers = $conn->query("SELECT user_id, username FROM users WHERE role = 'driver'");

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plate_number = trim($_POST['plate_number']);
    $capacity = intval($_POST['capacity']);
    $driver_id = intval($_POST['driver_id']);
    $status = trim($_POST['status']);

    if (empty($plate_number) || $capacity <= 0 || $driver_id <= 0 || empty($status)) {
        $message = "❌ All fields are required and must be valid.";
    } else {
        $stmt = $conn->prepare("UPDATE buses SET plate_number = ?, capacity = ?, driver_id = ?, status = ? WHERE bus_id = ?");
        $stmt->bind_param("siisi", $plate_number, $capacity, $driver_id, $status, $bus_id);

        if ($stmt->execute()) {
            header("Location: view_buses.php?success=1");
            exit;
        } else {
            $message = "❌ Error updating bus: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Bus</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        form {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ccc;
            width: 400px;
        }

        label {
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
        }

        .message {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>

<h2>Edit Bus</h2>

<?php if (!empty($message)): ?>
    <div class="message <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<form method="POST">
    <label for="plate_number">Plate Number:</label>
    <input type="text" name="plate_number" value="<?= htmlspecialchars($bus['plate_number']) ?>" required>

    <label for="capacity">Capacity:</label>
    <input type="number" name="capacity" value="<?= $bus['capacity'] ?>" required>

    <label for="driver_id">Assign Driver:</label>
    <select name="driver_id" required>
        <option value="">Select Driver</option>
        <?php while ($driver = $drivers->fetch_assoc()): ?>
            <option value="<?= $driver['user_id'] ?>" <?= $driver['user_id'] == $bus['driver_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($driver['username']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label for="status">Status:</label>
    <select name="status" required>
        <option value="active" <?= $bus['status'] === 'active' ? 'selected' : '' ?>>Active</option>
        <option value="inactive" <?= $bus['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
    </select>

    <button type="submit">Update Bus</button>
</form>

<p><a href="view_buses.php">⬅ Back to Bus List</a></p>

</body>
</html>
