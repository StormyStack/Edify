<?php
session_start();
include("config/db.php");

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $description = trim($_POST['description']);

    if ($username === '') {
        $message = "Please enter a username.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $userExists = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        if ($userExists) {
            $stmt = $conn->prepare("INSERT INTO requests (username, type, description) VALUES (?, 'reset password', ?)");
            $stmt->bind_param("ss", $username, $description);
            $stmt->execute();
            $message = $stmt->affected_rows > 0
                ? "Password reset request recorded for user: " . htmlspecialchars($username)
                : "Could not record the request.";
            $stmt->close();
        } else {
            $message = "User not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="assets/css/reset_password.css">
</head>
<body>
    <div class="form-container">
        <h2>Reset Password</h2>
        <a href="index.php" class="home-btn">Home</a>
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
       <form method="POST">
    <input type="text" name="username" placeholder="Enter Username" required><br>
    <textarea name="description" placeholder="Why do you want to reset your password?" rows="2"></textarea><br>
    <p class="info">If your password is reset, your new password will be <strong>123456</strong>.</p>
    <button class="btn" type="submit">Request Reset Password</button>
    </form>

    </div>
</body>
</html>
