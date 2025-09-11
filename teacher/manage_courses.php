<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header("Location: ../login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];

if (isset($_GET['delete'])) {
    $course_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM courses WHERE id=? AND teacher_id=?");
    $stmt->bind_param("ii", $course_id, $teacher_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_courses.php");
    exit();
}
$courses = [];
$stmt = $conn->prepare("SELECT * FROM courses WHERE teacher_id=? ORDER BY id DESC");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) $courses[] = $row;
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Courses</title>
    <link rel="stylesheet" href="../assets/css/teacher.css">
</head>
<body>

<h2>My Courses</h2>
<a href="add_course.php" class="add-course">+ Add New Course</a>

<table>
    <tr>
        <th>ID</th>
        <th>Course Name</th>
        <th>Duration</th>
        <th>Price</th>
        <th>Actions</th>
    </tr>
    <?php if(count($courses) > 0): ?>
        <?php foreach($courses as $c): ?>
            <tr>
                <td><?= $c['id'] ?></td>
                <td><?= htmlspecialchars($c['course_name']) ?></td>
                <td><?= htmlspecialchars($c['duration']) ?></td>
                <td><?= htmlspecialchars($c['price']) ?> ৳</td>
                <td>
                    <a href="update_course.php?id=<?= $c['id'] ?>" class="edit">Edit</a>
                    <a href="manage_courses.php?delete=<?= $c['id'] ?>" class="delete" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="5" style="text-align:center;">No courses found.</td></tr>
    <?php endif; ?>
</table>

</body>
</html>
