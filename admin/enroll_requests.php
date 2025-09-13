<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

function redirectWithMessage($msg) {
    header("Location: enroll_request.php?msg=" . urlencode($msg));
    exit();
}

if (!empty($_GET['action']) && !empty($_GET['payment_id'])) {
    $payment_id = intval($_GET['payment_id']);
    $action = $_GET['action'];

    $stmt = $conn->prepare("SELECT student_id, course_id, status FROM payments WHERE id = ?");
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();
    $stmt->bind_result($student_id, $course_id, $status);

    if ($stmt->fetch()) {
        $stmt->close();

        if ($status !== 'pending') {
            redirectWithMessage("This payment has already been processed.");
        }

        if ($action === 'approve') {
            $conn->begin_transaction();
            try {
                $ins = $conn->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
                $ins->bind_param("ii", $student_id, $course_id);
                $ins->execute();
                $ins->close();

                $upd = $conn->prepare("UPDATE payments SET status = 'completed' WHERE id = ?");
                $upd->bind_param("i", $payment_id);
                $upd->execute();
                $upd->close();

                $conn->commit();
                redirectWithMessage("Payment approved and student enrolled.");
            } catch (Exception $e) {
                $conn->rollback();
                redirectWithMessage("Error processing approval: " . $e->getMessage());
            }
        } elseif ($action === 'reject') {
            $upd = $conn->prepare("UPDATE payments SET status = 'rejected' WHERE id = ?");
            $upd->bind_param("i", $payment_id);
            $upd->execute();
            $upd->close();

            redirectWithMessage("Payment rejected.");
        } else {
            die("Invalid action.");
        }
    } else {
        $stmt->close();
        redirectWithMessage("Payment not found.");
    }
}


$query = "
    SELECT p.id, u.username, c.course_name, p.payment_method, p.tx_id, p.status
    FROM payments p
    JOIN users u ON p.student_id = u.id
    JOIN courses c ON p.course_id = c.id
    ORDER BY p.id DESC
";
$result = $conn->query($query);

?>
<!DOCTYPE html>
<head>
<title>Enrollment Requests</title>
<style>
body { font-family: Arial, sans-serif; background: #f4f6f8; padding: 20px; }
h2 { color: #333; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
th { background: #007bff; color: white; }
a { padding: 5px 10px; border-radius: 5px; color: white; text-decoration: none; margin-right: 5px; }
.approve { background: #28a745; }
.reject { background: #dc3545; }
.message { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 10px; }
</style>
</head>
<body>
<h2>Enrollment Requests</h2>

<?php if (!empty($_GET['msg'])): ?>
<div class="message"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php endif; ?>

<table>
<tr>
    <th>ID</th>
    <th>Student</th>
    <th>Course</th>
    <th>Payment Method</th>
    <th>TX ID</th>
    <th>Status</th>
    <th>Action</th>
</tr>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= htmlspecialchars($row['username']) ?></td>
    <td><?= htmlspecialchars($row['course_name']) ?></td>
    <td><?= htmlspecialchars(ucfirst($row['payment_method'])) ?></td>
    <td><?= htmlspecialchars($row['tx_id']) ?></td>
    <td><?= htmlspecialchars($row['status']) ?></td>
    <td>
        <?php if ($row['status'] === 'pending'): ?>
            <a class="approve" href="?action=approve&payment_id=<?= $row['id'] ?>">Approve</a>
            <a class="reject" href="?action=reject&payment_id=<?= $row['id'] ?>">Reject</a>
        <?php else: ?>
            -
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
