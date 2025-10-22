<?php
include 'db.php'; // Include the database connection

session_start(); // Start a session
header('Content-Type: application/json'); // Set header to return JSON

$response = array();

$username = $_POST['username'];
$password = $_POST['password'];

// --- Use Prepared Statements to find the user ---
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();

    // --- Verify the hashed password ---
    if (password_verify($password, $user['password'])) {
        // Password is correct!
        $_SESSION['username'] = $user['username']; // Store username in session
        $_SESSION['user_id'] = $user['id'];
        
        $response['status'] = 'success';
        $response['message'] = 'Login Successful! Welcome, ' . $user['username'];
    } else {
        // Invalid password
        $response['status'] = 'error';
        $response['message'] = 'Invalid username or password.';
    }
} else {
    // No user found
    $response['status'] = 'error';
    $response['message'] = 'Invalid username or password.';
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>