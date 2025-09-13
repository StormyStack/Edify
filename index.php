<?php
session_start();
include("config/db.php");

$courses = [];
$result = $conn->query("SELECT * FROM courses ORDER BY created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}
?>
<!DOCTYPE html>
<head>
 
    <title>Edify - Courses</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        header {
            display: flex;
            justify-content: center;
            align-items: center;
            background: #5c9addff;
            padding: 12px 20px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-center {
            font-size: 22px;
            font-weight: bold;
            color: #fff;
        }
        .header-right {
            position: absolute;
            right: 20px;
        }
        .header-right a {
            margin-left: 10px;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            color: #fff;
        }
        .btn-login {
            background: #28a745;
        }
        .btn-register {
            background: #17a2b8;
        }
        .btn-dashboard {
            background: #007bff;
        }
        .btn-logout {
            background: #dc3545;
        }

        .courses-section {
            max-width: 1200px;
            margin: 25px auto;
            padding: 0 15px;
        }
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 18px;
        }

        .course-card {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            display: flex;
            flex-direction: column;
            height: 260px;
        }
        .course-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }
        .course-card img {
            width: 100%;
            height: 140px;
            object-fit: cover;
        }
        .course-body {
            padding: 10px 12px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            flex: 1;
        }
        .course-title {
            font-size: 15px;
            font-weight: 600;
            color: #1c1d1f;
            margin-bottom: 6px;
            line-height: 1.3;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .course-description {
            font-size: 13px;
            color: #6a6f73;
            line-height: 1.2;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
    </style>
</head>
<body>

<header>
    <div class="header-center">Edify</div>
    <div class="header-right">
        <?php if (isset($_SESSION['role'])): ?>
            <a href="dashboard.php" class="btn-dashboard">Dashboard</a>
            <a href="logout.php" class="btn-logout">Logout</a>
        <?php else: ?>
            <a href="login.php" class="btn-login">Login</a>
            <a href="register.php" class="btn-register">Register</a>
        <?php endif; ?>
    </div>
</header>

<section class="courses-section">
    <div class="courses-grid">
        <?php if (count($courses) > 0): ?>
            <?php foreach ($courses as $c): ?>
                <?php 
                    $imagePath = !empty($c['image']) ? htmlspecialchars($c['image']) : 'assets/images/default-course.jpg';
                ?>
                <div class="course-card" onclick="location.href='course.php?id=<?= $c['id'] ?>'">
                    <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($c['course_name']) ?>">
                    <div class="course-body">
                        <div class="course-title"><?= htmlspecialchars($c['course_name']); ?></div>
                        <div class="course-description"><?= htmlspecialchars(substr($c['description'], 0, 60)); ?>...</div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No courses available.</p>
        <?php endif; ?>
    </div>
</section>

</body>
</html>
