<?php
session_start();
require_once '../config/db.php';

// Only allow admins to register new users
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Admin only.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (empty($username) || empty($password) || empty($role)) {
        header("Location: register.php?error=All fields are required");
        exit();
    }

    // Validate role
    $validRoles = ['admin', 'driver', 'police'];
    if (!in_array($role, $validRoles)) {
        header("Location: register.php?error=Invalid role selected");
        exit();
    }

    $db = new Database();
    $conn = $db->connect();

    // Check if username already exists
    $checkStmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: register.php?error=Username already exists");
        exit();
    }
    $checkStmt->close();

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user (removed email column)
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hashed_password, $role);

    if ($stmt->execute()) {
        header("Location: register.php?success=User registered successfully");
    } else {
        header("Location: register.php?error=Registration failed: " . $conn->error);
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: register.php");
}
?>
