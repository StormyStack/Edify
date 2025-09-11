<?php
session_start();
include("config/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];

switch($role) {
    case 'admin':
        header("Location: admin/dashboard.php");
        exit();
    case 'teacher':
        header("Location: teacher/dashboard.php");
        exit();
    case 'student':
        header("Location: student/dashboard.php");
        exit();
    default:
        echo "Invalid user role.";
        exit();
}
?>
