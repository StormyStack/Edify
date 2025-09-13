<?php
session_start();
include("config/db.php");

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_id = intval($_POST['course_id']);
    $username = $_SESSION['username'];


    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($student_id);
    $stmt->fetch();
    $stmt->close();


    $check = $conn->prepare("SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?");
    $check->bind_param("ii", $student_id, $course_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows == 0) {

        $insert = $conn->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
        $insert->bind_param("ii", $student_id, $course_id);
        if ($insert->execute()) {
            $insert->close();
            $check->close();

            header("Location: course.php?id=$course_id&enrolled=1");
            exit();
        } else {
            $insert->close();
            $check->close();
            die("Error: Could not enroll in course.");
        }
    } else {
        $check->close();
 
        header("Location: course.php?id=$course_id&already=1");
        exit();
    }
} else {
    die("Invalid request.");
}
?>
