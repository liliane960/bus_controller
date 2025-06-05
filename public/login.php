<?php
session_start();
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
        $stmt = $conn->prepare("SELECT user_id, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                switch ($user['role']) {
                    case 'admin':
                        header("Location: admin_dashboard.php");
                        break;
                    case 'driver':
                        header("Location: driver_dashboard.php");
                        break;
                    case 'police':
                        header("Location: police_dashboard.php");
                        break;
                    default:
                        header("Location: index.php");
                        break;
                }
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
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background: #fff;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            width: 350px;
        }

        .login-container h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .login-container label {
            font-weight: bold;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-container button {
            width: 100%;
            background: #3498db;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .login-container button:hover {
            background: #3498db;
        }

        .error {
            color: red;
            text-align: center;
        }
    </style>
    <script>
        function validateForm() {
            const username = document.forms["loginForm"]["username"].value.trim();
            const password = document.forms["loginForm"]["password"].value;

            if (username === "" || password === "") {
                alert("Both username and password are required.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <?php if ($message): ?>
            <p class="error"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form name="loginForm" method="POST" action="login.php" onsubmit="return validateForm();">
            <label>Username:</label>
            <input type="text" name="username" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
