<?php
session_start();
require '../db_connection.php';

if (!isset($_SESSION['lecturer_uid'])) {
    header("Location: login/login.php");
    exit();
}

$lecturer_id = $_SESSION['lecturer_uid'];

// Handle Save/Update Note
if (isset($_POST['save_note'])) {
    $note = trim($_POST['note']);

    $stmt = $pdo->prepare("SELECT * FROM lecturer_notes WHERE lecturer_id = ?");
    $stmt->execute([$lecturer_id]);

    if ($stmt->rowCount() > 0) {
        $update = $pdo->prepare("UPDATE lecturer_notes SET note = ?, updated_at = NOW() WHERE lecturer_id = ?");
        $update->execute([$note, $lecturer_id]);
        $_SESSION['note_message'] = "Note updated.";
    } else {
        $insert = $pdo->prepare("INSERT INTO lecturer_notes (lecturer_id, note) VALUES (?, ?)");
        $insert->execute([$lecturer_id, $note]);
        $_SESSION['note_message'] = "Note added.";
    }

    header("Location: lecturer_notes.php");
    exit();
}

// Handle Clear Note
if (isset($_POST['clear_note'])) {
    $clear = $pdo->prepare("UPDATE lecturer_notes SET note = NULL, updated_at = NOW() WHERE lecturer_id = ?");
    $clear->execute([$lecturer_id]);
    $_SESSION['note_message'] = "Note cleared.";

    header("Location: lecturer_notes.php");
    exit();
}

// Fetch existing note
$noteText = "";
$stmt = $pdo->prepare("SELECT note FROM lecturer_notes WHERE lecturer_id = ?");
$stmt->execute([$lecturer_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
    $noteText = $row['note'];
}

// Message 
$message = "";
if (isset($_SESSION['note_message'])) {
    $message = $_SESSION['note_message'];
    unset($_SESSION['note_message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Lecturer Notes</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h2 class="mb-4">My Daily Note</h2>

        <?php if ($message): ?>
            <div id="noteMessage" class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST" class="mb-4">
            <div class="mb-3">
                <label for="note" class="form-label">Today's Plan or Reminder:</label>
                <textarea name="note" class="form-control" rows="4"><?= htmlspecialchars($noteText ?? '') ?></textarea>
            </div>
            <button type="submit" name="save_note" class="btn btn-primary">Save Note</button>
            <button type="submit" name="clear_note" class="btn btn-outline-danger">Clear Note</button>
        </form>
    </div>

    <script>
        // Hide message 
        setTimeout(() => {
            const msg = document.getElementById('noteMessage');
            if (msg) {
                msg.style.display = 'none';
            }
        }, 3000);
    </script>
</body>
</html>
