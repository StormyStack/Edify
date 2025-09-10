<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

include("../config/db.php");

function getUserCount($conn, $role) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM users WHERE role = ?");
    $stmt->bind_param("s", $role);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = 0;
    if ($row = $result->fetch_assoc()) {
        $count = $row['total'];
    }
    $stmt->close();
    return $count;
}

$student_count = getUserCount($conn, 'student');
$teacher_count = getUserCount($conn, 'teacher');
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

        <div class="counts">
            <div class="count-card">👨‍🎓 Students: <?php echo htmlspecialchars($student_count); ?></div>
            <div class="count-card">👩‍🏫 Teachers: <?php echo htmlspecialchars($teacher_count); ?></div>
        </div>

        <ul>
            <li><a href="manage_users.php">👥 Manage Users</a></li>
            <li><a href="manage_courses.php">📚 Manage Courses</a></li>
            <li><a href="../logout.php">🚪 Logout</a></li>
        </ul>
    </div>
</body>
</html>
