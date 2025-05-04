<?php
session_start();
require '../../db_connection.php';

if (!isset($_SESSION['lecturer_uid'])) {
    header("Location: login/login.php");
    exit();
}

$lecturer_id = $_SESSION['lecturer_uid'];
$today = date('Y-m-d');

$stmt = $pdo->prepare("SELECT * FROM lecturer_reminders WHERE lecturer_id = ? AND reminder_date = ? ORDER BY reminder_time ASC");
$stmt->execute([$lecturer_id, $today]);
$reminders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Today's Reminders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h2>Today's Reminders</h2>

    <?php if (count($reminders) > 0): ?>
        <ul class="list-group">
            <?php foreach ($reminders as $rem): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span><?= htmlspecialchars($rem['note']) ?></span>
                    <span class="text-muted"><?= date('h:i A', strtotime($rem['reminder_time'])) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="alert alert-info mt-3">No reminders for today.</div>
    <?php endif; ?>
</div>
</body>
</html>
