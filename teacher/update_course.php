<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header("Location: ../login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$message = "";

if (!isset($_GET['id'])) die("Course ID not provided.");
$course_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ? AND teacher_id = ?");
$stmt->bind_param("ii", $course_id, $teacher_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$course) die("Course not found or permission denied.");

if (isset($_POST['update_course'])) {
    $name = trim($_POST['course_name']);
    $desc = trim($_POST['description']);
    $duration = trim($_POST['duration']);
    $price = trim($_POST['price']);

    $imagePath = $course['image'];
    if (!empty($_FILES['course_image']['name'])) {
        $targetDir = "../uploads/courses/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $fileName = time() . "_" . basename($_FILES["course_image"]["name"]);
        $targetFile = $targetDir . $fileName;
        if (move_uploaded_file($_FILES["course_image"]["tmp_name"], $targetFile)) {
            $imagePath = "uploads/courses/" . $fileName;
        }
    }

    $stmt = $conn->prepare("UPDATE courses SET course_name=?, description=?, image=?, duration=?, price=? WHERE id=? AND teacher_id=?");
    $stmt->bind_param("ssssdii", $name, $desc, $imagePath, $duration, $price, $course_id, $teacher_id);
    if ($stmt->execute()) {
        $message = "Course updated successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
    $course = [
        'course_name' => $name,
        'description' => $desc,
        'duration' => $duration,
        'price' => $price,
        'image' => $imagePath
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Course</title>
    <link rel="stylesheet" href="../assets/css/teacher.css">
</head>
<body>
    <div class="card">
        <h2>Update Course</h2>
        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="course_name" value="<?= htmlspecialchars($course['course_name']) ?>" placeholder="Course Title" required>
            <textarea name="description" placeholder="Course Description"><?= htmlspecialchars($course['description']) ?></textarea>
            <input type="text" name="duration" value="<?= htmlspecialchars($course['duration']) ?>" placeholder="Course Duration (e.g., 4 weeks)" required>
            <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($course['price']) ?>" placeholder="Course Price" required>
            <label class="upload-label">Upload Course Image (optional)</label>
            <input type="file" name="course_image" accept="image/*">
            <button type="submit" name="update_course">Update Course</button>
        </form>
    </div>
</body>
</html>
