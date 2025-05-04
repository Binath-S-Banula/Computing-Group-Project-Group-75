<?php
session_start();
require '../db_connection.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='alert alert-danger'>Club not specified.</div>";
    exit();
}

$club_id = $_GET['id'];
$student_id = $_SESSION['student_uid'] ?? null;

// Fetch club info
$stmt = $pdo->prepare("SELECT * FROM club_admins WHERE id = ?");
$stmt->execute([$club_id]);
$club = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$club) {
    echo "<div class='alert alert-danger'>Club not found.</div>";
    exit();
}

// Check if student is a member
$is_member = false;
if ($student_id) {
    $stmt = $pdo->prepare("SELECT status FROM club_members WHERE club_id = ? AND student_id = ?");
    $stmt->execute([$club_id, $student_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && $row['status'] === 'active') {
        $is_member = true;
    }
}

// Fetch events: public + members-only (if member), only upcoming
$stmt = $pdo->prepare("
    SELECT * FROM club_events 
    WHERE club_id = ? 
    AND status = 'upcoming'
    AND (access_type = 'public' OR (access_type = 'members' AND ?))
    ORDER BY start_time DESC
");

$stmt->execute([$club_id, $is_member]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Flash messages
if (isset($_SESSION['flash_message'])) {
    echo "<div class='alert alert-{$_SESSION['flash_type']}'>" . $_SESSION['flash_message'] . "</div>";
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($club['club_name']) ?> - Events</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h2><?= htmlspecialchars($club['club_name']) ?></h2>
    <p><?= nl2br(htmlspecialchars($club['description'])) ?></p>
    <hr>

    <h4>Events</h4>

    <?php if (empty($events)): ?>
        <div class="alert alert-info">No events available for this club.</div>
    <?php else: ?>
        <?php foreach ($events as $event): ?>
            <?php
                // Get current attendee count and check if student registered
                $stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM club_event_registrations WHERE event_id = ?");
                $stmt->execute([$event['id']]);
                $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

                $stmt = $pdo->prepare("SELECT * FROM club_event_registrations WHERE event_id = ? AND student_id = ?");
                $stmt->execute([$event['id'], $student_id]);
                $is_registered = $stmt->fetch();
            ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($event['title']) ?></h5>
                    <p class="card-text"><?= nl2br(htmlspecialchars($event['description'])) ?></p>
                    <p><strong>Start:</strong> <?= $event['start_time'] ?> | <strong>End:</strong> <?= $event['end_time'] ?></p>
                    <p><strong>Attendees:</strong> <?= $count ?>/<?= $event['max_attendees'] ?></p>

                    <?php if ($student_id): ?>
                        <?php if ($is_registered): ?>
                            <form method="post" action="sections/cancel_registration.php">
                                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                <input type="hidden" name="club_id" value="<?= $club_id ?>">
                                <button type="submit" class="btn btn-danger">Cancel Registration</button>
                            </form>
                        <?php elseif ($count < $event['max_attendees']): ?>
                            <form method="post" action="sections/register_event.php">
                                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                <input type="hidden" name="club_id" value="<?= $club_id ?>">
                                <button type="submit" class="btn btn-success">Register</button>
                            </form>
                        <?php else: ?>
                            <p class="text-warning">Registration full.</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-muted">Login to register for this event.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
