<?php
session_start();
require '../db_connection.php';

if (!isset($_SESSION['lecturer_uid'])) {
    header("Location: login/login.php");
    exit();
}

$lecturer_id = $_SESSION['lecturer_uid'];

// Fetch subject allocation IDs assigned to this lecturer
$sql = "SELECT sa.id AS allocation_id, sa.subject_id, sa.degree_id, sa.batch_id, 
               s.name AS subject_name, d.degree_name, b.batch_name
        FROM subject_allocations sa
        JOIN degrees d ON sa.degree_id = d.id
        JOIN batches b ON sa.batch_id = b.id
        JOIN subjects s ON sa.subject_id = s.id
        WHERE sa.lecturer_id = ?";
        
$stmt = $pdo->prepare($sql);
$stmt->execute([$lecturer_id]);
$allocations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);
    if (!empty($message) && isset($_POST['allocations']) && is_array($_POST['allocations'])) {
        $insert = $pdo->prepare("INSERT INTO announcements (lecturer_id, subject_allocation_id, message) VALUES (?, ?, ?)");
        foreach ($_POST['allocations'] as $allocation_id) {
            $insert->execute([$lecturer_id, $allocation_id, $message]);
        }
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Announcement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h2>Create Announcement</h2>

    <?php if ($success): ?>
        <div class="alert alert-success">Announcement sent successfully.</div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="message" class="form-label">Announcement Message</label>
            <textarea name="message" id="message" class="form-control" rows="4" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Select Groups</label>
            <?php if (count($allocations) > 0): ?>
                <div class="row">
                    <?php foreach ($allocations as $alloc): ?>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       name="allocations[]"
                                       value="<?= $alloc['allocation_id'] ?>"
                                       id="allocation_<?= $alloc['allocation_id'] ?>">
                                <label class="form-check-label" for="allocation_<?= $alloc['allocation_id'] ?>">
                                    <?= htmlspecialchars($alloc['subject_name']) ?> - <?= htmlspecialchars($alloc['degree_name']) ?> (<?= htmlspecialchars($alloc['batch_name']) ?>)
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted">You are not assigned to any groups yet.</p>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Send Announcement</button>
    </form>
</div>
</body>
</html>
