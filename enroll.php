<?php
session_start();
include("config/db.php");

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['course_id'])) die("No course selected.");

$course_id = intval($_GET['course_id']);

$stmt = $conn->prepare("SELECT u.id FROM enrollments e JOIN users u ON e.student_id = u.id WHERE u.username=? AND e.course_id=?");
$stmt->bind_param("si", $_SESSION['username'], $course_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    header("Location: course.php?id=$course_id&already=1");
    exit();
}
$stmt->close();

$stmt2 = $conn->prepare("SELECT * FROM courses WHERE id=?");
$stmt2->bind_param("i", $course_id);
$stmt2->execute();
$result = $stmt2->get_result();
$course = $result->fetch_assoc();
$stmt2->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enroll in <?= htmlspecialchars($course['course_name']); ?></title>
    <link rel="stylesheet" href="assets/css/enroll.css">
</head>
<body>
<div class="container">
    <h2>Enroll in <?= htmlspecialchars($course['course_name']); ?></h2>
    <p><strong>Price:</strong> <?= htmlspecialchars($course['price']); ?> ৳</p>

    <form method="GET" action="payment.php">
        <input type="hidden" name="course_id" value="<?= $course['id']; ?>">
        <label>Select Payment Method:</label>
        <select name="payment_method" required>
            <option value="">--Choose--</option>
            <option value="bkash">bKash</option>
            <option value="nagad">Nagad</option>
            <option value="PayPal">PayPal</option>
        </select>
        <button type="submit">Proceed to Payment</button>
    </form>
</div>
</body>
</html>
