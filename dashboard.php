<?php
// Start the session to check if the user is logged in
session_start();

// If 'username' is not set in the session, it means they are not logged in.
// Redirect them back to the login page (index.html).
if (!isset($_SESSION['username'])) {
    header('Location: index.html');
    exit(); // Stop the script
}

// Get the logged-in username to display it
$username = $_SESSION['username'];

// Get the current page from the URL (e.g., dashboard.php?page=add_student)
// If no page is set, default to 'welcome'
$page = isset($_GET['page']) ? $_GET['page'] : 'welcome';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="dashboard.css"> </head>
<body>
    <div class="dashboard-container">
        
        <div class="sidebar">
            <h2>Teachers Panel</h2>
            <nav>
                <ul>
                    <li><a href="dashboard.php?page=add_student">Register Student</a></li>
                    <li><a href="dashboard.php?page=view_students">View All Students</a></li>
                    </ul>
            </nav>
            <div class="logout-area">
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
        
        <div class="main-content">
            <header>
                <h3>Welcome, <?php echo htmlspecialchars($username); ?>!</h3>
            </header>
            
            <main>
                <?php
                // This is the "router"
                // It includes the correct PHP file based on the $page variable
                if ($page == 'add_student') {
                    include 'add_student.php';
                } elseif ($page == 'view_students') {
                    include 'view_students.php';
                } else {
                    // Default 'welcome' page content
                    echo "<h2>Welcome to the Dashboard</h2>";
                    echo "<p>Select an option from the menu on the left to get started.</p>";
                }
                ?>
            </main>
        </div>
        
    </div>
</body>
</html>