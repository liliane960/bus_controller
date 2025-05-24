<?php
require_once '../config/db.php';

// Get DB connection
$db = (new Database())->connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validate inputs
    if (empty($username) || empty($password) || empty($role)) {
        die("Please fill all fields");
    }

    // Check if username already exists
    $checkQuery = "SELECT user_id FROM users WHERE username = ?";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        die("Username already taken. Please choose another.");
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $query = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->bind_param("sss", $username, $hashed_password, $role);

    if ($stmt->execute()) {
        echo "User created successfully.";
        // Optionally: header('Location: admin_dashboard.php');
    } else {
        echo "Error creating user: " . $stmt->error;
    }
} else {
    echo "Invalid request.";
}
?>
