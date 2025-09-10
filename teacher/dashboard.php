<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header("Location: ../login.php");
    exit();
}

include("../config/db.php");

$course_count = $conn->query("SELECT COUNT(*) AS total FROM courses WHERE id = ".$_SESSION['user_id'])->fetch_assoc()['total'];
$student_count = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'student'")->fetch_assoc()['total'];
$assessment_count = $conn->query("SELECT COUNT(*) AS total FROM assessments WHERE id = ".$_SESSION['user_id'])->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <h1>Welcome Teacher</h1>

    <div class="dashboard-container">

        <div class="counts">
            <div class="count-card">Courses: <?php echo htmlspecialchars($course_count); ?></div>
            <div class="count-card">Students: <?php echo htmlspecialchars($student_count); ?></div>
            <div class="count-card">Assessments: <?php echo htmlspecialchars($assessment_count); ?></div>
        </div>

        <ul>
            <li><a class="button" href="manage_courses.php">📚 Manage Courses</a></li>
            <li><a class="button" href="assessment_overview.php">📝 Assessment Overview</a></li>
            <li><a class="button" href="student_overview.php">👨‍🎓 Student Overview</a></li>
            <li><a class="button" href="../logout.php">🚪 Logout</a></li>
        </ul>
    </div>
</body>
</html>
