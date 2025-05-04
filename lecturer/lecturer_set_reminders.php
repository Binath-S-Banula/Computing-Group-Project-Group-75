<?php
session_start();
require '../db_connection.php';

if (!isset($_SESSION['lecturer_uid'])) {
    header("Location: login/login.php");
    exit();
}

$lecturer_id = $_SESSION['lecturer_uid'];
$message = "";

// Handle submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reminders'])) {
    $reminders = $_POST['reminders'];

    foreach ($reminders as $r) {
        $note = trim($r['note']);
        $date = $r['date'] ?: date('Y-m-d');
        $time = $r['time'] ?: date('H:i');

        if ($note) {
            $stmt = $pdo->prepare("INSERT INTO lecturer_reminders (lecturer_id, note, reminder_date, reminder_time) VALUES (?, ?, ?, ?)");
            $stmt->execute([$lecturer_id, $note, $date, $time]);
        }
    }

    $_SESSION['reminder_message'] = "Reminders saved.";
    header("Location: lecturer_set_reminders.php");
    exit();
}

// Fetch any session message
if (isset($_SESSION['reminder_message'])) {
    $message = $_SESSION['reminder_message'];
    unset($_SESSION['reminder_message']);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Set Reminders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function addReminder() {
            const container = document.getElementById('reminderContainer');
            const now = new Date().toISOString().slice(0, 10);
            const div = document.createElement('div');
            div.classList.add('mb-3', 'border', 'p-3', 'rounded');
            div.innerHTML = `
                <textarea name="reminders[][note]" class="form-control mb-2" placeholder="Reminder note..." required></textarea>
                <div class="d-flex gap-2">
                    <input type="date" name="reminders[][date]" class="form-control" value="${now}" required>
                    <input type="time" name="reminders[][time]" class="form-control" required>
                </div>
            `;
            container.appendChild(div);
        }
    </script>
</head>
<body class="p-4">
<div class="container">
    <h2>Set Reminders</h2>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div id="reminderContainer">
            <!-- Initial reminder input -->
            <div class="mb-3 border p-3 rounded">
                <textarea name="reminders[][note]" class="form-control mb-2" placeholder="Reminder note..." required></textarea>
                <div class="d-flex gap-2">
                    <input type="date" name="reminders[][date]" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    <input type="time" name="reminders[][time]" class="form-control" required>
                </div>
            </div>
        </div>

        <button type="button" onclick="addReminder()" class="btn btn-secondary mb-3">Add Another Reminder</button><br>
        <button type="submit" class="btn btn-primary">Save Reminders</button>
    </form>
</div>
</body>
</html>
