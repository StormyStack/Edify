<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

include("../config/db.php");

$result = $conn->query("SELECT id, username, role FROM users WHERE role != 'admin' ORDER BY id ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <h1>Manage Users</h1>
    <div class="dashboard-container">
        <a href="dashboard.php">⬅ Back to Dashboard</a>

        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
            <?php if ($result->num_rows > 0): ?>
                <?php while($user = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td>
                            <a class="delete" href="delete_user.php?id=<?php echo $user['id']; ?>">Delete</a><br><br>
                            <a class="reset" href="reset_password.php?id=<?php echo $user['id']; ?>">Reset Password</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No users found.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
