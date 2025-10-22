<?php
include 'db.php'; // Include the database connection

header('Content-Type: application/json'); // Set header to return JSON

$response = array();

// Get data from the form
$email = $_POST['email'];
$username = $_POST['username'];
$password = $_POST['password'];

// --- Security First: HASH the password ---
// We never store plain-text passwords.
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// --- Prevent SQL Injection with Prepared Statements ---

// 1. Check if email or username already exists
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
$stmt->bind_param("ss", $email, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // User already exists
    $response['status'] = 'error';
    $response['message'] = 'Email or Username already exists.';
} else {
    // 2. Insert new user
    $stmt = $conn->prepare("INSERT INTO users (email, username, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $username, $hashed_password);

    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'Sign Up Successful! You can now log in.';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Error: ' . $stmt->error;
    }
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>