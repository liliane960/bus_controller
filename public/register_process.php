<?php
require_once '../config/db.php';

$db = new Database();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    if (empty($username) || empty($password) || empty($email) || empty($role)) {
        die("Please fill all fields.");
    }

    // Validate role
    $allowed_roles = ['admin', 'driver', 'police'];
    if (!in_array($role, $allowed_roles)) {
        die("Invalid role selected.");
    }

    // Check for duplicate username
    $check = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        die("Username already exists.");
    }
    $check->close();

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $hashed_password, $email, $role);

    if ($stmt->execute()) {
        echo "User registered successfully. <a href='admin_dashboard.php'>Back to Dashboard</a>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
