<?php
require_once '../config/db.php';

$db = new Database();
$conn = $db->connect();

$driverResult = $conn->query("SELECT user_id, username FROM users WHERE role = 'driver'");

// Handle success message passed via GET
$message = isset($_GET['message']) ? $_GET['message'] : '';
$isSuccess = strpos($message, '✅') !== false;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register New Bus</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin-top: 30px;
            color: #333;
        }

        form {
            background-color: #ffffff;
            max-width: 450px;
            margin: 30px auto;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #333;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button[type="submit"] {
            width: 100%;
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #2980b9;
        }

        .success-message {
            text-align: center;
            color: green;
            margin-top: 10px;
            font-weight: bold;
        }

        .error-message {
            text-align: center;
            color: red;
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h1>Register New Bus</h1>

    <?php if (!empty($message)): ?>
        <p class="<?= $isSuccess ? 'success-message' : 'error-message' ?>">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form action="add_bu_hhandle.php" method="POST">
        <label for="plate_number">Plate Number:</label>
        <input 
            type="text" 
            name="plate_number" 
            id="plate_number" 
            placeholder="Eg: RAD000A" 
            required 
            pattern="[A-Z]{3}[0-9]{3}[A-Z]" 
            title="Format: 3 letters, 3 numbers, 1 letter (e.g., RAA123B). 'RAD000A' is not allowed.">

        <label for="capacity">Capacity:</label>
        <input 
            type="number" 
            name="capacity" 
            id="capacity" 
            placeholder="Enter capacity (1 - 80)" 
            min="1" 
            max="80" 
            required>

        <label for="driver_id">Assign Driver:</label>
        <select name="driver_id" id="driver_id" required>
            <option value="">-- Select Driver --</option>
            <?php while ($driver = $driverResult->fetch_assoc()): ?>
                <option value="<?= $driver['user_id'] ?>"><?= htmlspecialchars($driver['username']) ?></option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Register Bus</button>
    </form>

    <script>
        // Restrict capacity input within 1 to 80
        document.getElementById('capacity').addEventListener('input', function () {
            const value = parseInt(this.value, 10);
            if (value < 1) this.value = 1;
            if (value > 80) this.value = 80;
        });

        // Disallow specific plate number
        document.getElementById('plate_number').addEventListener('input', function () {
            if (this.value.toUpperCase() === 'RAD000A') {
                alert("'RAD000A' is not allowed.");
                this.value = '';
            }
        });

        // Auto reload after 3 seconds if successful
        <?php if ($isSuccess): ?>
        setTimeout(function () {
            location.reload();
        }, 3000);
        <?php endif; ?>
    </script>

</body>
</html>
