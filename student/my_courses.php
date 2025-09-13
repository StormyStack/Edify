<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../login.php");
    exit();
}

include("../config/db.php");

$student_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT c.id, c.course_name, c.description, c.image, e.enrolled_at
    FROM courses c
    JOIN enrollments e ON c.id = e.course_id
    WHERE e.student_id = ?
    ORDER BY e.enrolled_at DESC
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$courses = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>My Courses</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        .course-card {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
            background: #fff;
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .course-card img {
            width: 120px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }
        .course-info {
            text-align: left;
        }
        .course-info h3 {
            margin: 0 0 8px 0;
        }
        .course-info p {
            margin: 4px 0;
            color: #555;
        }
        .course-info span {
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>

<h1>My Courses</h1>

<div class="dashboard-container">
    <?php if ($courses->num_rows > 0): ?>
        <?php while ($course = $courses->fetch_assoc()): ?>
            <?php 
               $imagePath = !empty($course['image']) ? htmlspecialchars($course['image']) : 'assets/images/default-course.jpg';
            ?>
            <div class="course-card">
                <img src="<?php echo $imagePath; ?>" alt="Course Image">
                <div class="course-info">
                    <h3><?php echo htmlspecialchars($course['course_name']); ?></h3>
                    <p><?php echo htmlspecialchars($course['description']); ?></p>
                    <span>Enrolled on: <?php echo date("d M Y", strtotime($course['enrolled_at'])); ?></span>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>You are not enrolled in any courses yet.</p>
    <?php endif; ?>
</div>

</body>
</html>
