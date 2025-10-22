<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    die("Error: You must be logged in.");
}

// Check if an ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // --- Security: Use Prepared Statements ---
    
    // 1. Get the image path to delete the file
   // 1. Get the image path (and check ownership)
    $teacher_id = $_SESSION['user_id'];
    $stmt_select = $conn->prepare("SELECT image_path FROM students WHERE id = ? AND created_by_user_id = ?");
    $stmt_select->bind_param("ii", $id, $teacher_id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $image_path = $row['image_path'];
        
        // 2. Delete the image file from the 'uploads' folder
        // (Don't delete if it's a default image!)
        if (file_exists($image_path) && $image_path != 'uploads/default.png') {
            unlink($image_path);
        }
    }
    $stmt_select->close();

    // 3. Delete the student record from the database
    // 3. Delete the student record from the database (and check ownership)
    $stmt_delete = $conn->prepare("DELETE FROM students WHERE id = ? AND created_by_user_id = ?");
    $stmt_delete->bind_param("ii", $id, $teacher_id);

    if ($stmt_delete->execute()) {
        // Success: Redirect back to the view page with a success message
        header("Location: dashboard.php?page=view_students&status=deleted");
    } else {
        echo "Error deleting record: " . $stmt_delete->error;
    }
    
    $stmt_delete->close();
    $conn->close();

} else {
    // No ID provided
    header("Location: dashboard.php?page=view_students");
}
?>