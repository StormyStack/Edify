<?php
session_start();
include("config/db.php");

if (!isset($_GET['id'])) die("Course not found.");

$course_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();

if (!$course) die("Course not found.");
?>
<!DOCTYPE html>
<head>
    <title><?= htmlspecialchars($course['course_name']); ?> - Details</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .container.course-details {
            max-width: 600px;
            margin: 32px auto;
            padding: 24px 16px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px #eaeaea;
            text-align: center;
        }
        .course-details img {
            max-width: 100%;
            border-radius: 8px;
            margin-bottom: 18px;
        }
        .course-details h2 {
            font-size: 22px;
            margin-bottom: 12px;
            color: #333;
        }
        .course-details p {
            font-size: 15px;
            margin-bottom: 16px;
            color: #444;
            text-align: left;
        }
        .info {
            font-size: 15px;
            margin-bottom: 18px;
            background: #f7f7f7;
            border-radius: 6px;
            padding: 8px 0;
        }
        .info p {
            margin: 5px 0;
        }
        .btn {
            background: #4cae36;
            color: #fff;
            padding: 10px 22px;
            font-size: 15px;
            border-radius: 6px;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background: #388e2c;
        }
        .success-msg, .error-msg {
            display: block;
            padding: 10px 14px;
            border-radius: 5px;
            margin-top: 14px;
            font-size: 14px;
        }
        .success-msg { color:#256a27; background:#e6f7e6; }
        .error-msg { color:#856404; background:#fffbe6; }
    </style>
</head>
<body>
<header>
    <h1>Edify</h1>
    <div class="header-right">
        <a href="index.php">Home</a>
        <?php if(isset($_SESSION['role'])): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </div>
</header>

<main class="container course-details">
    <img src="<?= !empty($course['image']) ? htmlspecialchars($course['image']) : 'assets/images/placeholder.png' ?>" alt="Course">
    <h2><?= htmlspecialchars($course['course_name']); ?></h2>
    <p><?= nl2br(htmlspecialchars($course['description'])); ?></p>
    <div class="info">
        <p><strong>Duration:</strong> <?= htmlspecialchars($course['duration']); ?></p>
        <p><strong>Price:</strong> <?= htmlspecialchars($course['price']); ?> ৳</p>
    </div>

    <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'student'): ?>
        <a href="enroll.php?course_id=<?= $course['id']; ?>" class="btn">Enroll Now</a>
    <?php else: ?>
        <p><em>You must be logged in as a student to enroll.</em></p>
    <?php endif; ?>

    <?php if (isset($_GET['enrolled'])): ?>
        <p class="success-msg">Enrollment successful! ✅</p>
    <?php endif; ?>
    <?php if (isset($_GET['already'])): ?>
        <p class="error-msg">You are already enrolled in this course. ⚠️</p>
    <?php endif; ?>
</main>
</body>
</html>
