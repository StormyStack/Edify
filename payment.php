<?php
session_start();
include("config/db.php");

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = intval($_POST['course_id']);
    $payment_method = $_POST['payment_method'];
    $tx_id = trim($_POST['tx_id']);
    $username = $_SESSION['username'];

    $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($student_id);
    $stmt->fetch();
    $stmt->close();

    $check = $conn->prepare("SELECT * FROM payments WHERE student_id=? AND course_id=?");
    $check->bind_param("ii", $student_id, $course_id);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        $check->close();
        die("<script>alert('Payment already submitted for this course.'); window.location.href='dashboard.php';</script>");
    }
    $check->close();

    $insert = $conn->prepare("INSERT INTO payments (student_id, course_id, payment_method, tx_id, status) VALUES (?, ?, ?, ?, 'pending')");
    $insert->bind_param("iiss", $student_id, $course_id, $payment_method, $tx_id);
    if ($insert->execute()) {
        $insert->close();
        echo "<script>
            alert('Payment submitted successfully! Await admin approval.');
            window.location.href='dashboard.php';
        </script>";
    } else {
        die("Failed to submit payment.");
    }
    exit();
}

if (!isset($_GET['course_id']) || !isset($_GET['payment_method'])) {
    die("Invalid request.");
}

$course_id = intval($_GET['course_id']);
$payment_method = $_GET['payment_method'];

$stmt2 = $conn->prepare("SELECT course_name, price FROM courses WHERE id=?");
$stmt2->bind_param("i", $course_id);
$stmt2->execute();
$result = $stmt2->get_result();
$course = $result->fetch_assoc();
$stmt2->close();

if (!$course) die("Course not found.");

$accounts = [
    "bkash" => "017XXXXXXXX",
    "nagad" => "018XXXXXXXX",
    "paypal" => "payment@edify.com"
];

$account_info = $accounts[strtolower($payment_method)] ?? "Contact Admin";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment for <?= htmlspecialchars($course['course_name']); ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: aliceblue; margin: 0; }
        .container { max-width: 500px; margin: 50px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        h2 { text-align:center; }
        .account-info { background:whitesmoke; padding:10px; border-radius:8px; margin:10px 0; }
        label, input, button { display:block; width:100%; margin:10px 0; }
        input { padding:10px; border-radius:8px; border:1px solid lightgray; }
        button { padding:12px; background:royalblue; color:white; border:none; border-radius:8px; cursor:pointer; }
        button:hover { background:mediumblue; }
    </style>
    </style>
</head>
<body>
<div class="container">
    <h2>Payment for <?= htmlspecialchars($course['course_name']); ?></h2>
    <p><strong>Price:</strong> <?= htmlspecialchars($course['price']); ?> ৳</p>
    <p><strong>Method:</strong> <?= ucfirst(htmlspecialchars($payment_method)); ?></p>
    <div class="account-info">Send payment to: <strong><?= htmlspecialchars($account_info); ?></strong></div>

    <form method="POST">
        <input type="hidden" name="course_id" value="<?= $course_id; ?>">
        <input type="hidden" name="payment_method" value="<?= htmlspecialchars($payment_method); ?>">
        <label>Enter Transaction ID:</label>
        <input type="text" name="tx_id" required placeholder="TX ID">
        <button type="submit">Submit Payment</button>
    </form>
</div>
</body>
</html>
