<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

include("../config/db.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Get username of the user (non-admin)
    $stmtUser = $conn->prepare("SELECT username FROM users WHERE id = ? AND role != 'admin'");
    $stmtUser->bind_param("i", $id);
    $stmtUser->execute();
    $stmtUser->bind_result($username);
    $stmtUser->fetch();
    $stmtUser->close();

    // Check for pending reset request
    $stmtRequest = $conn->prepare("SELECT id FROM requests WHERE username = ? AND type = 'reset password' AND status = 'Pending' LIMIT 1");
    $stmtRequest->bind_param("s", $username);
    $stmtRequest->execute();
    $stmtRequest->store_result();

    if ($stmtRequest->num_rows > 0) {
        // Reset password to default
        $default_password = password_hash('123456', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $default_password, $id);
        $stmt->execute();
        $stmt->close();

        // Mark the request as completed
        $stmt = $conn->prepare("UPDATE requests SET status = 'Completed' WHERE username = ? AND type = 'reset password' AND status = 'Pending'");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->close();

        // Redirect back with success message
        header("Location: manage_users.php?success=Password+reset+successfully");
        exit();
    } else {
        // Redirect back with error message
        header("Location: manage_users.php?error=User+did+not+request+a+password+reset");
        exit();
    }
}
?>
