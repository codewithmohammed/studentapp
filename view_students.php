<?php
include_once 'db.php'; 

// We must have the teacher's ID from the session
if (!isset($_SESSION['user_id'])) {
    die("Error: Session invalid. Please log in again.");
}
$teacher_id = $_SESSION['user_id'];
?>

<h2>View All Registered Students</h2>

<?php
// ... (Your status messages for deleted/updated) ...
if (isset($_GET['status']) && $_GET['status'] == 'deleted') {
    echo "<p style='color: green; font-weight: bold;'>Student record deleted successfully!</p>";
}
if (isset($_GET['status']) && $_GET['status'] == 'updated') {
    echo "<p style='color: green; font-weight: bold;'>Student record updated successfully!</p>";
}
?>

<table class="student-table">
    <thead>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Parent's Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // --- THIS IS THE KEY CHANGE ---
        // Only select students that were created by the logged-in teacher
        $sql = "SELECT * FROM students WHERE created_by_user_id = ? ORDER BY full_name ASC";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                ?>
                <tr>
                    <td>
                        <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['full_name']); ?>">
                    </td>
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['address']); ?></td>
                    <td><?php echo htmlspecialchars($row['parent_name']); ?></td>
                    <td>
                        <a href="dashboard.php?page=add_student&id=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
                        <a href="delete_student.php?id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                    </td>
                </tr>
                <?php
            }
        } else {
            echo "<tr><td colspan='7'>No students found.</td></tr>";
        }
        $stmt->close();
        ?>
    </tbody>
</table>