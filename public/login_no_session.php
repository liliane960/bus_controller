<?php
require_once '../config/db.php';

$db = new Database();
$conn = $db->connect();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $message = "Please fill in both fields.";
    } else {
        $stmt = $conn->prepare("SELECT username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Redirect and pass role info in URL (not secure!)
                header("Location: admin_dashboard.php?username=" . urlencode($user['username']) . "&role=" . urlencode($user['role']));
                exit();
            } else {
                $message = "Invalid username or password.";
            }
        } else {
            $message = "Invalid username or password.";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Without Session</title>
</head>
<body>
    <h1>Login</h1>
    <?php if ($message): ?>
        <p style="color:red;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="POST" action="login_no_session.php">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>
