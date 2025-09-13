<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header("Location: ../login.php");
    exit();
}

include("../config/db.php");
$teacher_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id, course_name FROM courses WHERE teacher_id = ?");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$courses = $stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = intval($_POST['course_id']);
    $title = $_POST['title'];
    $description = $_POST['description'];
    $total_marks = intval($_POST['total_marks']);
    $due_date = $_POST['due_date'];
    $pdf_file_path = null;

    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == 0) {
        $uploadDir = '../uploads/assessments/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $fileName = time() . '_' . basename($_FILES['pdf_file']['name']);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $targetFile)) {
            $pdf_file_path = 'uploads/assessments/' . $fileName;
        } else {
            $error = "Failed to upload PDF file.";
        }
    }

    if (empty($error)) {
        $stmt = $conn->prepare("INSERT INTO assessments (course_id, title, description, total_marks, due_date, pdf_file) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ississ", $course_id, $title, $description, $total_marks, $due_date, $pdf_file_path);

        if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Error creating assessment: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Create Assessment</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        body { font-family: sans-serif; }
        .nav-buttons { margin: 20px; text-align: center; }
        .nav-buttons a { 
            display: inline-block; 
            margin: 0 10px; 
            padding: 8px 15px; 
            background: #007bff; 
            color: #fff; 
            text-decoration: none; 
            border-radius: 4px; 
        }
        form { max-width: 600px; margin: 20px auto; padding: 20px; background: #fff; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        form input, form select, form textarea { width: 100%; padding: 8px 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; }
        form button { background: #007bff; color: #fff; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        .message { text-align: center; margin-bottom: 15px; }
        .message.success { color: green; }
        .message.error { color: red; }
    </style>
</head>
<body>

<h1 style="text-align:center;">Create Assessment</h1>
<div class="nav-buttons">
    <a href="../index.php">Home</a>
    <a href="dashboard.php">Dashboard</a>
</div>

<form method="post" enctype="multipart/form-data">
    <?php if (!empty($error)) echo "<div class='message error'>$error</div>"; ?>

    <label for="course_id">Select Course:</label>
    <select name="course_id" id="course_id" required>
        <option value="">-- Select Course --</option>
        <?php while($course = $courses->fetch_assoc()): ?>
            <option value="<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['course_name']); ?></option>
        <?php endwhile; ?>
    </select>

    <label for="title">Assessment Title:</label>
    <input type="text" name="title" id="title" required>

    <label for="description">Description:</label>
    <textarea name="description" id="description" rows="4"></textarea>

    <label for="total_marks">Total Marks:</label>
    <input type="number" name="total_marks" id="total_marks" required>

    <label for="due_date">Due Date:</label>
    <input type="date" name="due_date" id="due_date" required>

    <label for="pdf_file">Upload PDF (optional):</label>
    <input type="file" name="pdf_file" id="pdf_file" accept="application/pdf">

    <button type="submit">Create Assessment</button>
</form>

</body>
</html>
