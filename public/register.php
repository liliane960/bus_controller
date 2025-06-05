<!DOCTYPE html>
<html>
<head>
    <title>Register New User</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Register New User</h1>
    <form action="register_process.php" method="POST">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Role:</label><br>
        <select name="role" required>
            <option value="">-- Select Role --</option>
            <option value="admin">Admin</option>
            <option value="driver">Driver</option>
            <option value="police">Police</option>
        </select><br><br>

        <button type="submit">Register</button>
    </form>
    <br>
        <script>
        // If successful, reload after 3 seconds
        <?php if ($isSuccess): ?>
        setTimeout(function () {
            location.reload();
        }, 3000);
        <?php endif; ?>
    </script>
</html>
