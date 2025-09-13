<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../login.php");
    exit();
}

include("../config/db.php");

$student_id = $_SESSION['user_id'];

// Fetch student info
$student = $conn->query("SELECT * FROM users WHERE id = $student_id")->fetch_assoc();

// Count of enrolled courses
$course_count = $conn->query("SELECT COUNT(*) AS total FROM enrollments WHERE student_id = $student_id")->fetch_assoc()['total'];

// Count of assessments
$assessment_count = $conn->query("
    SELECT COUNT(a.id) AS total
    FROM assessments a
    JOIN courses c ON a.course_id = c.id
    JOIN enrollments e ON c.id = e.course_id
    WHERE e.student_id = $student_id
")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

<h1>Welcome, <?php echo htmlspecialchars($student['username']); ?></h1>

<div class="dashboard-container">
    <h2>Dashboard</h2>

    <div class="counts">
        <span>My Courses: <?php echo htmlspecialchars($course_count); ?></span> |
        <span>My Assessments: <?php echo htmlspecialchars($assessment_count); ?></span>
    </div>

    <ul>
        <li><a href="my_courses.php">My Courses</a></li>
        <li><a href="my_assessments.php">My Assessments</a></li>
        <li><a href="../logout.php">Logout</a></li>
    </ul>
</div>

</body>
</html>
