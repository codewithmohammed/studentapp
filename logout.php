<?php
session_start(); // 1. Find the existing session

session_unset(); // 2. Unset all session variables

session_destroy(); // 3. Destroy the session

// 4. Redirect the user back to the login page
header("Location: index.html");
exit();
?>