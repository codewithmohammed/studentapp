<?php
session_start();
include 'db.php'; 

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: You must be logged in.");
}

// Get the logged-in teacher's ID
$teacher_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- Get Common Form Data ---
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $parent_name = $_POST['parent_name'];
    $parent_job = $_POST['parent_job'];
    $parent_phone = $_POST['parent_phone'];

    // Check if this is an UPDATE or an INSERT
    if (isset($_POST['student_id'])) {
        
        // --- THIS IS AN UPDATE ---
        $id = $_POST['student_id'];
        
        // 1. Get the old image path (and check ownership)
        $stmt_img = $conn->prepare("SELECT image_path FROM students WHERE id = ? AND created_by_user_id = ?");
        $stmt_img->bind_param("ii", $id, $teacher_id);
        $stmt_img->execute();
        $result_img = $stmt_img->get_result();
        
        if ($result_img->num_rows == 0) {
            die("Error: You do not have permission to edit this student.");
        }
        
        $row_img = $result_img->fetch_assoc();
        $image_path = $row_img['image_path']; // Default to old image
        $stmt_img->close();

        // 2. Check if a NEW image was uploaded
        if (isset($_FILES["student_image"]) && $_FILES["student_image"]["error"] == 0) {
            // Delete the old image file (if it's not the default)
            if (file_exists($image_path) && $image_path != 'uploads/default.png') {
                unlink($image_path);
            }
            // Upload the new image
            $target_dir = "uploads/";
            $file_extension = pathinfo($_FILES["student_image"]["name"], PATHINFO_EXTENSION);
            $unique_file_name = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $unique_file_name;
            
            if (move_uploaded_file($_FILES["student_image"]["tmp_name"], $target_file)) {
                $image_path = $target_file; 
            }
        }
        
        // 3. Prepare the UPDATE SQL query (note the extra check in WHERE)
        $sql = "UPDATE students SET full_name = ?, email = ?, phone = ?, address = ?, 
                parent_name = ?, parent_job = ?, parent_phone = ?, image_path = ? 
                WHERE id = ? AND created_by_user_id = ?";
                
        $stmt = $conn->prepare($sql);
        // 'ssssssssii' = 8 strings, 2 integers
        $stmt->bind_param("ssssssssii", 
            $full_name, $email, $phone, $address, 
            $parent_name, $parent_job, $parent_phone, $image_path, 
            $id, $teacher_id
        );

        // 4. Execute and Redirect
        if ($stmt->execute()) {
            header("Location: dashboard.php?page=view_students&status=updated");
        } else {
            echo "Error: " . $stmt->error;
        }

    } else {
        
        // --- THIS IS AN INSERT (New Student) ---
        
        // 1. Handle Image Upload
        $image_path = "uploads/default.png"; // Default
        if (isset($_FILES["student_image"]) && $_FILES["student_image"]["error"] == 0) {
            // ... (same upload logic as before) ...
            $target_dir = "uploads/";
            $file_extension = pathinfo($_FILES["student_image"]["name"], PATHINFO_EXTENSION);
            $unique_file_name = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $unique_file_name;
            
            if (move_uploaded_file($_FILES["student_image"]["tmp_name"], $target_file)) {
                $image_path = $target_file; 
            }
        }

        // 2. Prepare the INSERT SQL query (with the new column)
        $sql = "INSERT INTO students (full_name, email, phone, address, parent_name, parent_job, parent_phone, image_path, created_by_user_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
        $stmt = $conn->prepare($sql);
        // 'ssssssssi' = 8 strings, 1 integer
        $stmt->bind_param("ssssssssi", 
            $full_name, $email, $phone, $address, 
            $parent_name, $parent_job, $parent_phone, $image_path,
            $teacher_id // Add the teacher's ID
        );

        // 3. Execute and Redirect
        if ($stmt->execute()) {
            header("Location: dashboard.php?page=add_student&status=success");
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();

} else {
    header("Location: dashboard.php");
}
?>