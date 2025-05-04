<?php
session_start();
require '../db_connection.php'; 

if (!isset($_SESSION['lecturer_uid'])) {
    header("Location: login/login.php");
    exit();
}

$lecturer_id = $_SESSION['lecturer_uid'];
$message = "";

// Handle Save/Update Note
if (isset($_POST['save_note'])) {
    $note = trim($_POST['note']);

    // Check if slot exists
    $stmt = $pdo->prepare("SELECT * FROM lecturer_notes WHERE lecturer_id = ?");
    $stmt->execute([$lecturer_id]);

    if ($stmt->rowCount() > 0) {
        // Update existing note
        $update = $pdo->prepare("UPDATE lecturer_notes SET note = ?, updated_at = NOW() WHERE lecturer_id = ?");
        $update->execute([$note, $lecturer_id]);
        $message = "Note updated.";
    } else {
        // Create slot and insert note
        $insert = $pdo->prepare("INSERT INTO lecturer_notes (lecturer_id, note) VALUES (?, ?)");
        $insert->execute([$lecturer_id, $note]);
        $message = "Note added.";
    }
}

// Handle Clear Note
if (isset($_POST['clear_note'])) {
    $clear = $pdo->prepare("UPDATE lecturer_notes SET note = NULL, updated_at = NOW() WHERE lecturer_id = ?");
    $clear->execute([$lecturer_id]);
    $message = "Note cleared.";
}

// Fetch existing note
$noteText = "";
$stmt = $pdo->prepare("SELECT note FROM lecturer_notes WHERE lecturer_id = ?");
$stmt->execute([$lecturer_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
    $noteText = $row['note'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Lecturer Notes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
    <div class="container">
        <h2>My Daily Note</h2>
        <?php if ($message): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST" class="mb-4">
            <div class="form-group mb-3">
                <label for="note">Today's Plan or Reminder:</label>
                <textarea name="note" class="form-control" rows="4"><?= htmlspecialchars($noteText ?? '') ?></textarea>
            </div>
            <button type="submit" name="save_note" class="btn btn-primary">Save Note</button>
            <button type="submit" name="clear_note" class="btn btn-outline-danger">Clear Note</button>
        </form>
    </div>
</body>
</html>
