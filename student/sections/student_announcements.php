<?php
session_start();
require '../../db_connection.php';

// Redirect if the student is not logged in
if (!isset($_SESSION['student_uid'])) {
    header("Location: ../login/login.php");
    exit();
}

$student_id = $_SESSION['student_uid'];

// Fetch student degree and batch
$studentStmt = $pdo->prepare("SELECT degree_id, batch_id FROM students WHERE id = ?");
$studentStmt->execute([$student_id]);
$student = $studentStmt->fetch(PDO::FETCH_ASSOC);

// Fetch announcements matching student's degree and batch
$sql = "SELECT a.id, a.message, a.created_at, s.name AS subject_name,
               l.name AS lecturer_name
        FROM announcements a
        JOIN subject_allocations sa ON a.subject_allocation_id = sa.id
        JOIN subjects s ON sa.subject_id = s.id
        JOIN lecturers l ON sa.lecturer_id = l.id
        WHERE sa.degree_id = ? AND sa.batch_id = ?
        ORDER BY a.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$student['degree_id'], $student['batch_id']]);
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Announcements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">
<div class="container">
    <h2 class="mb-4">Your Announcements</h2>

    <?php if (count($announcements) > 0): ?>
        <div class="table-responsive mt-3">
            <table class="table table-bordered table-hover bg-white">
                <thead class="table-light">
                <tr>
                    <th>Message</th>
                    <th>Subject</th>
                    <th>Lecturer</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($announcements as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['message']) ?></td>
                        <td><?= htmlspecialchars($a['subject_name']) ?></td>
                        <td><?= htmlspecialchars($a['lecturer_name']) ?></td>
                        <td><?= date('Y-m-d H:i', strtotime($a['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted">No announcements available for your group.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
