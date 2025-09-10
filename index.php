<?php
session_start();
include("config/db.php");

$courses = [];
$result = $conn->query("SELECT * FROM courses");
if($result){
    while($row = $result->fetch_assoc()){
        $courses[] = $row;
    }
}
?>
<!DOCTYPE html>
<head>
    <title>Edify - Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <div class="header-center">
        <h1>Edify</h1>
    </div>
    <div class="header-right">
        <?php if(isset($_SESSION['role'])): ?>
            <a href="dashboard.php" class="btn">Dashboard</a>
            <a href="logout.php" class="btn">Logout</a>
        <?php else: ?>
            <a href="login.php" class="btn">Login</a>
            <a href="register.php" class="btn">Register</a>
        <?php endif; ?>
    </div>
</header>

<main class="container">
    <h2>Courses</h2>
    <?php if(count($courses) > 0): ?>
        <?php foreach($courses as $c): ?>
            <div class="course">
                <strong><?php echo htmlspecialchars($c['title']); ?></strong>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No courses yet.
        </p>
    <?php endif; ?>
</main>

<script src="assets/js/script.js"></script>
</body>
</html>
