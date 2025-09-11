<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header("Location: ../login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$message = "";

if (isset($_POST['add_course'])) {
    $name = trim($_POST['course_name']);
    $desc = trim($_POST['description']);
    $duration = trim($_POST['duration']);
    $price = trim($_POST['price']);

    $imagePath = null;
    if (!empty($_FILES['course_image']['name'])) {
        $targetDir = "../uploads/courses/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $fileName = time() . "_" . basename($_FILES["course_image"]["name"]);
        $targetFile = $targetDir . $fileName;
        if (move_uploaded_file($_FILES["course_image"]["tmp_name"], $targetFile)) {
            $imagePath = "uploads/courses/" . $fileName;
        }
    }

    $stmt = $conn->prepare("INSERT INTO courses (teacher_id, course_name, description, image, duration, price) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssd", $teacher_id, $name, $desc, $imagePath, $duration, $price);
    $message = $stmt->execute() ? "Course added successfully!" : "Error: " . $stmt->error;
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Course</title>
    <link rel="stylesheet" href="../assets/css/teacher.css">
</head>
<body>
    <div class="card">
        <h2>Add New Course</h2>
        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="course_name" placeholder="Course Title" required>
            <textarea name="description" placeholder="Course Description"></textarea>
            <input type="text" name="duration" placeholder="Course Duration (e.g., 4 weeks)" required>
            <input type="number" step="0.01" name="price" placeholder="Course Price" required>
            <label class="upload-label">Upload Course Image</label>
            <input type="file" name="course_image" accept="image/*">
            <button type="submit" name="add_course">Add Course</button>
        </form>
    </div>
</body>
</html>
