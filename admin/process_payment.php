<?php
session_start();
include("../config/db.php");


if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. You must be an admin.");
}

if (!isset($_GET['action']) || !isset($_GET['payment_id'])) {
    die("Invalid request.");
}

$payment_id = intval($_GET['payment_id']);
$action = $_GET['action'];

$stmt = $conn->prepare("SELECT student_id, course_id, status FROM payments WHERE id = ?");
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$stmt->bind_result($student_id, $course_id, $status);

if ($stmt->fetch()) {
    $stmt->close();

    if ($status !== 'pending') {
        $message = "This payment has already been processed.";
        header("Location: dashboard.php?msg=" . urlencode($message));
        exit();
    }

    if ($action === 'approve') {

        $insert = $conn->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
        $insert->bind_param("ii", $student_id, $course_id);
        $insert->execute();
        $insert->close();
        $update = $conn->prepare("UPDATE payments SET status='completed' WHERE id=?");
        $update->bind_param("i", $payment_id);
        $update->execute();
        $update->close();

        $message = "Payment approved and student enrolled successfully!";
    }
    elseif ($action === 'reject') {

        $update = $conn->prepare("UPDATE payments SET status='rejected' WHERE id=?");
        $update->bind_param("i", $payment_id);
        $update->execute();
        $update->close();

        $message = "Payment rejected successfully!";
    } else {
        die("Invalid action.");
    }

    header("Location: dashboard.php?msg=" . urlencode($message));
    exit();

} else {
    $stmt->close();
    $message = "Payment not found.";
    header("Location: dashboard.php?msg=" . urlencode($message));
    exit();
}
?>
<?php
session_start();
include("../config/db.php");


if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. You must be an admin.");
}

if (!isset($_GET['action']) || !isset($_GET['payment_id'])) {
    die("Invalid request.");
}

$payment_id = intval($_GET['payment_id']);
$action = $_GET['action'];

$stmt = $conn->prepare("SELECT student_id, course_id, status FROM payments WHERE id = ?");
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$stmt->bind_result($student_id, $course_id, $status);

if ($stmt->fetch()) {
    $stmt->close();

    if ($status !== 'pending') {
        $message = "This payment has already been processed.";
        header("Location: dashboard.php?msg=" . urlencode($message));
        exit();
    }

    if ($action === 'approve') {

        $insert = $conn->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
        $insert->bind_param("ii", $student_id, $course_id);
        $insert->execute();
        $insert->close();
        $update = $conn->prepare("UPDATE payments SET status='completed' WHERE id=?");
        $update->bind_param("i", $payment_id);
        $update->execute();
        $update->close();

        $message = "Payment approved and student enrolled successfully!";
    }
    elseif ($action === 'reject') {

        $update = $conn->prepare("UPDATE payments SET status='rejected' WHERE id=?");
        $update->bind_param("i", $payment_id);
        $update->execute();
        $update->close();

        $message = "Payment rejected successfully!";
    } else {
        die("Invalid action.");
    }

    header("Location: dashboard.php?msg=" . urlencode($message));
    exit();

} else {
    $stmt->close();
    $message = "Payment not found.";
    header("Location: dashboard.php?msg=" . urlencode($message));
    exit();
}
?>
