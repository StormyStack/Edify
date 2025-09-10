<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

include("../config/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];

    if ($action === 'Cancel') {
        $stmt = $conn->prepare("UPDATE requests SET status = 'Cancelled' WHERE id = ?");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$result = $conn->query("SELECT * FROM requests ORDER BY id ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>View Requests</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <h1>All Requests</h1>
    <div class="dashboard-container">
        <a href="dashboard.php">⬅ Back to Dashboard</a>

        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Type</th>
                <th>Description</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php if ($result->num_rows > 0): ?>
                <?php while($request = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($request['id']); ?></td>
                        <td><?php echo htmlspecialchars($request['username']); ?></td>
                        <td><?php echo htmlspecialchars($request['type']); ?></td>
                        <td><?php echo htmlspecialchars($request['description']); ?></td>
                        <td><?php echo htmlspecialchars($request['status'] ?? 'Pending'); ?></td>
                        <td>
                            <?php if (($request['status'] ?? '') === 'Pending'): ?>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                    <button type="submit" name="action" value="Cancel">Cancel</button>
                                </form>
                            <?php else: ?>
                                <span>N/A</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No requests found.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
