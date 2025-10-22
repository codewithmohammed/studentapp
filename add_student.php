<?php
// We must include db.php to check for an existing student
include_once 'db.php';

// --- CHECK FOR EDIT MODE ---
$edit_mode = false;
$student = null;
$page_title = "Register a New Student";
$form_action = "save_student.php"; // Default action is to save a new student

if (isset($_GET['id'])) {
    $edit_mode = true;
    $id = $_GET['id'];
    $page_title = "Edit Student Details";
    
    // Fetch the student's data
    $teacher_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ? AND created_by_user_id = ?");
    $stmt->bind_param("ii", $id, $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();
    
    if (!$student) {
        // If student not found, redirect or show error
        echo "Error: Student not found.";
        exit;
    }
}
// ----------------------------

// Check for success message (from a new registration)
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    echo "<p style='color: green; font-weight: bold;'>Student added successfully!</p>";
}
?>

<h2><?php echo $page_title; ?></h2>

<form action="save_student.php" method="POST" enctype="multipart/form-data" class="student-form">
    
    <?php
    // If in edit mode, include a hidden field to send the student's ID
    if ($edit_mode) {
        echo '<input type="hidden" name="student_id" value="' . htmlspecialchars($student['id']) . '">';
    }
    ?>
    
    <div class="form-row">
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" value="<?php echo $edit_mode ? htmlspecialchars($student['full_name']) : ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo $edit_mode ? htmlspecialchars($student['email']) : ''; ?>" required>
        </div>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" value="<?php echo $edit_mode ? htmlspecialchars($student['phone']) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="student_image">Student Image</label>
            <input type="file" id="student_image" name="student_image" accept="image/*" <?php if (!$edit_mode) echo 'required'; ?>>
            
            <?php if ($edit_mode): ?>
                <div style="margin-top: 10px;">
                    <img src="<?php echo htmlspecialchars($student['image_path']); ?>" width="100" alt="Current Image">
                    <p><small>Current image. Upload a new file to replace it.</small></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="address">Address</label>
        <textarea id="address" name="address"><?php echo $edit_mode ? htmlspecialchars($student['address']) : ''; ?></textarea>
    </div>
    
    <h3>Parent/Guardian Details</h3>
    <div class="form-row">
        <div class="form-group">
            <label for="parent_name">Parent's Name</label>
            <input type="text" id="parent_name" name="parent_name" value="<?php echo $edit_mode ? htmlspecialchars($student['parent_name']) : ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="parent_job">Parent's Job</label>
            <input type="text" id="parent_job" name="parent_job" value="<?php echo $edit_mode ? htmlspecialchars($student['parent_job']) : ''; ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="parent_phone">Parent's Phone</label>
        <input type="tel" id="parent_phone" name="parent_phone" value="<?php echo $edit_mode ? htmlspecialchars($student['parent_phone']) : ''; ?>">
    </div>

    <button type="submit" class="btn-submit">
        <?php echo $edit_mode ? 'Update Student' : 'Save Student'; ?>
    </button>
</form>