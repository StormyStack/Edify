<?php
session_start();
include "config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            if ($user['role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } elseif ($user['role'] == 'teacher') {
                header("Location: teacher/dashboard.php");
            } else {
                header("Location: student/dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="header-center"><a href="index.php" style="text-decoration:none;color:inherit;"><h2>Edify Login</h2></a></div>
        <div class="header-right">
            <a href="register.php">Register</a>
        </div>
    </header>

    <div class="container">
        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

        <form method="post">
            <div class="course">
                <input type="text" name="username" placeholder="Username" required style="width: 50%; padding: 8px; margin:5px 0;"><br>
                <input type="password" name="password" placeholder="Password" required style="width: 50%; padding: 8px; margin:5px 0;"><br>
                <button class="btn" type="submit">Login</button>
            </div>
        </form>
    </div>
</body>
</html>