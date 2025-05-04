<?php
session_start();
require '../../db_connection.php';
date_default_timezone_set('Asia/Colombo');

if (!isset($_SESSION['lecturer_uid'])) {
    header("Location: ../login/login.php");
    exit();
}

$lecturer_id = $_SESSION['lecturer_uid'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note = trim($_POST['note']);
    $reminder_date = $_POST['reminder_date'];
    $reminder_time = $_POST['reminder_time'];

    if (!empty($note) && !empty($reminder_date) && !empty($reminder_time)) {
        $stmt = $pdo->prepare("INSERT INTO lecturer_reminders (lecturer_id, note, reminder_date, reminder_time) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$lecturer_id, $note, $reminder_date, $reminder_time])) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
            exit();
        } else {
            header("Location: " . $_SERVER['PHP_SELF'] . "?error=1");
            exit();
        }
    } else {
        header("Location: " . $_SERVER['PHP_SELF'] . "?error=1");
        exit();
    }
}

// Fetch reminders
$stmt = $pdo->prepare("SELECT * FROM lecturer_reminders WHERE lecturer_id = ? ORDER BY reminder_date, reminder_time");
$stmt->execute([$lecturer_id]);
$reminders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set Reminders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        setTimeout(() => {
            const alert = document.getElementById('alert-msg');
            if (alert) alert.style.display = 'none';
        }, 3000);
    </script>
</head>
<body class="p-4">
<div class="container">
    <h2 class="mb-3">Set a Reminder</h2>

    <?php if (isset($_GET['success'])): ?>
        <div id="alert-msg" class="alert alert-success">Reminder saved successfully.</div>
    <?php elseif (isset($_GET['error'])): ?>
        <div id="alert-msg" class="alert alert-danger">Failed to save reminder. Please check your input.</div>
    <?php endif; ?>

    <form method="POST" class="mb-4">
        <div class="mb-3">
            <label for="note" class="form-label">Reminder Note</label>
            <textarea name="note" class="form-control" rows="3" required></textarea>
        </div>
        <div class="mb-3 d-flex gap-3">
            <div>
                <label for="reminder_date" class="form-label">Date</label>
                <input type="date" name="reminder_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div>
                <label for="reminder_time" class="form-label">Time</label>
                <input type="time" name="reminder_time" class="form-control" value="<?= date('H:i') ?>" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Save Reminder</button>
    </form>

    <h4>Your Reminders</h4>
    <?php if (count($reminders) > 0): ?>
        <ul class="list-group">
            <?php foreach ($reminders as $r): ?>
                <li class="list-group-item">
                    <strong><?= htmlspecialchars($r['note']) ?></strong><br>
                    <?= htmlspecialchars($r['reminder_date']) ?> at <?= date('h:i A', strtotime($r['reminder_time'])) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="alert alert-secondary">No reminders found.</div>
    <?php endif; ?>
</div>
</body>
</html>
