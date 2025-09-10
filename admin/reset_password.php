<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

include("../config/db.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $default_password = password_hash('123456', PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ? AND role != 'admin'");
    $stmt->bind_param("si", $default_password, $id);
    $stmt->execute();
    $stmt->close();
}
header("Location: manage_users.php");
exit();
