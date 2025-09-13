<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../login.php");
    exit();
}

include("../config/db.php");
$student_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assessment_id'])) {
    $assessment_id = intval($_POST['assessment_id']);
    $uploadDir = '../uploads/submissions/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    if (isset($_FILES['submission_file']) && $_FILES['submission_file']['error'] == 0) {
        $fileName = time() . '_' . basename($_FILES['submission_file']['name']);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['submission_file']['tmp_name'], $targetFile)) {
            $file_path = 'uploads/submissions/' . $fileName;

            $stmt = $conn->prepare("INSERT INTO submissions (assessment_id, student_id, file_path) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $assessment_id, $student_id, $file_path);
            $stmt->execute();
            $stmt->close();

            header("Location: my_assessments.php?success=1");
            exit();
        } else {
            $error = "Failed to upload file.";
        }
    } else {
        $error = "Please select a file to submit.";
    }
}

$success = '';
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success = "Assignment submitted successfully!";
}

$stmt = $conn->prepare("
    SELECT a.id, a.title, a.description, a.total_marks, a.due_date, a.pdf_file, a.status, c.course_name,
           s.file_path AS submitted_file
    FROM assessments a
    JOIN courses c ON a.course_id = c.id
    JOIN enrollments e ON e.course_id = c.id
    LEFT JOIN submissions s ON s.assessment_id = a.id AND s.student_id = ?
    WHERE e.student_id = ?
    ORDER BY a.due_date ASC
");
$stmt->bind_param("ii", $student_id, $student_id);
$stmt->execute();
$assessments = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>My Assessments</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        body { font-family: sans-serif; margin: 0; padding: 0; background:#f9f9f9; }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background: #007bff;
            color: #fff;
        }
        header h1 { margin: 0; font-size: 20px; }
        .nav-buttons a { 
            display: inline-block; 
            margin-left: 10px; 
            padding: 8px 15px; 
            background: #fff; 
            color: #007bff; 
            text-decoration: none; 
            border-radius: 4px; 
            font-weight: bold;
        }
        .assessment-card {
            max-width: 700px;
            margin: 20px auto;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: #fff;
        }
        .assessment-card h3 { margin: 0 0 8px; }
        .assessment-card p { margin: 4px 0; color: #555; }
        .assessment-card a { display: inline-block; margin-top: 8px; text-decoration: none; color: #fff;
        .submit-form { margin-top: 10px; }
        .submit-form input[type="file"] { margin-bottom: 8px; }
        .submit-form button {
            margin-top: 5px;
            padding: 5px 10px;
            background: cyan;
            color: #542020ff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .message { text-align: center; margin-bottom: 15px; }
        .message.success { color: green; }
        .message.error { color: red; }
    </style>
</head>
<body>

<header>
    <h1>My Assessments</h1>
    <div class="nav-buttons">
        <a href="../index.php">Home</a>
        <a href="dashboard.php">Dashboard</a>
    </div>
</header>

<div class="dashboard-container">
    <?php if (!empty($success)) echo "<div class='message success'>$success</div>"; ?>
    <?php if (!empty($error)) echo "<div class='message error'>$error</div>"; ?>

    <?php if ($assessments->num_rows > 0): ?>
        <?php while ($a = $assessments->fetch_assoc()): ?>
            <div class="assessment-card">
                <h3><?php echo htmlspecialchars($a['title']); ?></h3>
                <p><strong>Course:</strong> <?php echo htmlspecialchars($a['course_name']); ?></p>
                <p><?php echo htmlspecialchars($a['description']); ?></p>
                <p><strong>Total Marks:</strong> <?php echo $a['total_marks']; ?></p>
                <p><strong>Due Date:</strong> <?php echo date("d M Y", strtotime($a['due_date'])); ?></p>
                <p><strong>Status:</strong> <?php echo ucfirst($a['status']); ?></p>

                <?php if (!empty($a['pdf_file'])): ?>
                    <p><a href="../<?php echo $a['pdf_file']; ?>" target="_blank">Download Assignment PDF</a></p>
                <?php endif; ?>

                <?php if (empty($a['submitted_file'])): ?>
                    <form method="post" enctype="multipart/form-data" class="submit-form">
                        <input type="hidden" name="assessment_id" value="<?php echo $a['id']; ?>">
                        <input type="file" name="submission_file" required>
                        <button type="submit">Submit Assignment</button>
                    </form>
                <?php else: ?>
                    <p><strong>Submitted File:</strong> <a href="../<?php echo $a['submitted_file']; ?>" target="_blank">View</a></p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center; margin-top:20px;">No assessments available yet.</p>
    <?php endif; ?>
</div>

</body>
</html>
