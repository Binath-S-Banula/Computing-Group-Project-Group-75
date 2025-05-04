<?php
session_start();
require_once('../../db_connection.php');

$student_id = $_SESSION['student_uid'] ?? null;
if (!$student_id) {
    exit(); 
}

$today = date('Y-m-d');

// Get today's favorite events for the student
$stmt = $pdo->prepare("
    SELECT ce.*
    FROM favorite_events fe
    JOIN career_events ce ON fe.event_id = ce.id
    WHERE fe.student_id = ? AND ce.event_date = ?
");
$stmt->execute([$student_id, $today]);
$events_today = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($events_today)) {
    exit(); // No output if there are no favorite events today
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


<div class="container mt-4">
    <?php foreach ($events_today as $event): ?>
        <?php
            // Format time period
            $start = date('g:i A', strtotime($event['start_time']));
            $end = date('g:i A', strtotime($event['end_time']));
            $time_period = "$start - $end";
        ?>
        <div class="alert alert-info">
            📢 <strong>Reminder:</strong> You have a favorite event <strong><?= htmlspecialchars($event['event_name']) ?></strong> happening today.
            <br>
            🕒 <strong>Time:</strong> <?= $time_period ?>
            <br>
            📍 <strong>Location:</strong> <?= htmlspecialchars($event['location']) ?>
        </div>
    <?php endforeach; ?>
</div>


