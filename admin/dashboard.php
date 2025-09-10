<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <h1>Welcome Admin</h1>

    <div class="dashboard-container">
        <h2>Dashboard</h2>
        <ul>
            <li><a href="manage_users.php">👥 Manage Users</a></li>
            <li><a href="manage_courses.php">📚 Manage Courses</a></li>
            <li><a href="../logout.php">🚪 Logout</a></li>
        </ul>
    </div>
</body>
</html>

