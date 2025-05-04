<?php
session_start();
require '../../db_connection.php';
date_default_timezone_set('Asia/Colombo');


if (!isset($_SESSION['lecturer_uid'])) {
    header("Location: ../login/login.php"); 
    exit();
}

$lecturer_id = $_SESSION['lecturer_uid'];
$today = date('Y-m-d');

// Get today's reminders ordered by time
$stmt = $pdo->prepare("SELECT * FROM lecturer_reminders WHERE lecturer_id = ? AND reminder_date = ? ORDER BY reminder_time ASC");
$stmt->execute([$lecturer_id, $today]);
$reminders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Today's Reminders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h2 class="mb-4">Today's Reminders (<?= date('F j, Y') ?>)</h2>

    <?php if (!empty($reminders)): ?>
        <ul class="list-group">
            <?php foreach ($reminders as $rem): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div><?= htmlspecialchars($rem['note']) ?></div>
                    <span class="badge bg-secondary"><?= date('h:i A', strtotime($rem['reminder_time'])) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="alert alert-info">You have no reminders for today.</div>
    <?php endif; ?>
</div>
</body>
</html>
