<?php
session_start();
require '../../db_connection.php';
date_default_timezone_set('Asia/Colombo');

if (!isset($_SESSION['lecturer_uid'])) {
    header("Location: ../login/login.php");
    exit();
}

$lecturer_id = $_SESSION['lecturer_uid'];

// Add Reminder
if (isset($_POST['add_reminder'])) {
    $note = trim($_POST['note']);
    $reminder_date = $_POST['reminder_date'];
    $reminder_time = $_POST['reminder_time'];

    if (!empty($note) && !empty($reminder_date) && !empty($reminder_time)) {
        $stmt = $pdo->prepare("INSERT INTO lecturer_reminders (lecturer_id, note, reminder_date, reminder_time) VALUES (?, ?, ?, ?)");
        $stmt->execute([$lecturer_id, $note, $reminder_date, $reminder_time]);
        $_SESSION['flash_success'] = "Reminder added successfully.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Edit Reminder
if (isset($_POST['edit_reminder'])) {
    $id = $_POST['reminder_id'];
    $note = trim($_POST['edit_note']);
    $reminder_date = $_POST['edit_reminder_date'];
    $reminder_time = $_POST['edit_reminder_time'];

    $stmt = $pdo->prepare("UPDATE lecturer_reminders SET note = ?, reminder_date = ?, reminder_time = ? WHERE id = ? AND lecturer_id = ?");
    $stmt->execute([$note, $reminder_date, $reminder_time, $id, $lecturer_id]);
    $_SESSION['flash_info'] = "Reminder updated.";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Delete Reminder
if (isset($_POST['delete_reminder'])) {
    $id = $_POST['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM lecturer_reminders WHERE id = ? AND lecturer_id = ?");
    $stmt->execute([$id, $lecturer_id]);
    $_SESSION['flash_danger'] = "Reminder deleted.";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch Reminders
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

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash_success'])): ?>
        <div id="alert-msg" class="alert alert-success"><?= $_SESSION['flash_success'] ?></div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php elseif (isset($_SESSION['flash_info'])): ?>
        <div id="alert-msg" class="alert alert-info"><?= $_SESSION['flash_info'] ?></div>
        <?php unset($_SESSION['flash_info']); ?>
    <?php elseif (isset($_SESSION['flash_danger'])): ?>
        <div id="alert-msg" class="alert alert-danger"><?= $_SESSION['flash_danger'] ?></div>
        <?php unset($_SESSION['flash_danger']); ?>
    <?php endif; ?>

    <!-- Add Reminder Form -->
    <form method="POST" class="mb-4">
        <input type="hidden" name="add_reminder" value="1">
        <div class="mb-3">
            <label class="form-label">Reminder Note</label>
            <textarea name="note" class="form-control" required></textarea>
        </div>
        <div class="d-flex gap-3 mb-3">
            <div>
                <label class="form-label">Date</label>
                <input type="date" name="reminder_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div>
                <label class="form-label">Time</label>
                <input type="time" name="reminder_time" class="form-control" value="<?= date('H:i') ?>" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Save Reminder</button>
    </form>

    <h4>Your Reminders</h4>
    <?php if ($reminders): ?>
        <ul class="list-group">
            <?php foreach ($reminders as $rem): ?>
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div>
                        <strong><?= htmlspecialchars($rem['note']) ?></strong><br>
                        <?= htmlspecialchars($rem['reminder_date']) ?> at <?= date('h:i A', strtotime($rem['reminder_time'])) ?>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $rem['id'] ?>">Edit</button>
                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $rem['id'] ?>">Delete</button>
                    </div>
                </li>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal<?= $rem['id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <form method="POST" class="modal-content">
                            <input type="hidden" name="edit_reminder" value="1">
                            <input type="hidden" name="reminder_id" value="<?= $rem['id'] ?>">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Reminder</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Note</label>
                                    <textarea name="edit_note" class="form-control" required><?= htmlspecialchars($rem['note']) ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label>Date</label>
                                    <input type="date" name="edit_reminder_date" class="form-control" value="<?= $rem['reminder_date'] ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label>Time</label>
                                    <input type="time" name="edit_reminder_time" class="form-control" value="<?= $rem['reminder_time'] ?>" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Delete Modal -->
                <div class="modal fade" id="deleteModal<?= $rem['id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <form method="POST" class="modal-content">
                            <input type="hidden" name="delete_reminder" value="1">
                            <input type="hidden" name="delete_id" value="<?= $rem['id'] ?>">
                            <div class="modal-header">
                                <h5 class="modal-title">Confirm Delete</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete this reminder?
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-danger">Delete</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="alert alert-secondary">No reminders found.</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
