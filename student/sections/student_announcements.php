<?php
session_start();
require '../../db_connection.php';

if (!isset($_SESSION['student_uid'])) {
    header("Location: ../login/login.php");
    exit();
}

$student_id = $_SESSION['student_uid'];

// Fetch student degree and batch
$studentStmt = $pdo->prepare("SELECT degree_id, batch_id FROM students WHERE id = ?");
$studentStmt->execute([$student_id]);
$student = $studentStmt->fetch(PDO::FETCH_ASSOC);

// Fetch announcements
$sql = "SELECT a.message, a.created_at, s.name AS subject_name, l.name AS lecturer_name
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
    <title>Announcements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
<div class="container">
    <h2 class="mb-4">Announcements</h2>

    <?php if (count($announcements) > 0): ?>
        <?php foreach ($announcements as $a): ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($a['subject_name']) ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted">By <?= htmlspecialchars($a['lecturer_name']) ?> on <?= date('F j, Y \a\t g:i A', strtotime($a['created_at'])) ?></h6>
                    <p class="card-text mt-3"><?= nl2br(htmlspecialchars($a['message'])) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info">No announcements available for your group.</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
