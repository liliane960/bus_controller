<!DOCTYPE html>
<html>
<head>
    <title>Register New User</title>
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
            max-width: 400px;
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
        input[type="password"],
        input[type="email"],
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
    <h1>Register New User</h1>

    <?php
    $message = isset($_GET['message']) ? $_GET['message'] : '';
    $isSuccess = strpos($message, '✅') !== false;
    if (!empty($message)): ?>
        <p class="<?= $isSuccess ? 'success-message' : 'error-message' ?>">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form action="register_process.php" method="POST">
        <label>Username:</label>
        <input type="text" name="username" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Role:</label>
        <select name="role" required>
            <option value="">-- Select Role --</option>
            <option value="admin">Admin</option>
            <option value="driver">Driver</option>
            <option value="police">Police</option>
        </select>

        <button type="submit">Register</button>
    </form>

    <?php if ($isSuccess): ?>
    <script>
        setTimeout(function () {
            location.reload();
        }, 3000);
    </script>
    <?php endif; ?>
</body>
</html>
