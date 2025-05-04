<?php
session_start();
require '../db_connection.php';

if (!isset($_SESSION['lecturer_uid'])) {
    header("Location: login/login.php");
    exit();
}

$lecturer_id = $_SESSION['lecturer_uid'];

// Handle Create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $message = trim($_POST['message']);
    if (!empty($message) && isset($_POST['allocations'])) {
        $insert = $pdo->prepare("INSERT INTO announcements (lecturer_id, subject_allocation_id, message, created_at) VALUES (?, ?, ?, NOW())");
        foreach ($_POST['allocations'] as $alloc_id) {
            $insert->execute([$lecturer_id, $alloc_id, $message]);
        }
        $_SESSION['announcement_success'] = "Announcement sent successfully.";
        header("Location: announcements.php");
        exit();
    }
}

// Handle Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit') {
    $editId = $_POST['announcement_id'];
    $newMessage = trim($_POST['edit_message']);
    $stmt = $pdo->prepare("UPDATE announcements SET message = ? WHERE id = ? AND lecturer_id = ?");
    $stmt->execute([$newMessage, $editId, $lecturer_id]);
    $_SESSION['announcement_success'] = "Announcement updated successfully.";
    header("Location: announcements.php");
    exit();
}

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete') {
    $deleteId = $_POST['announcement_id'];
    $stmt = $pdo->prepare("DELETE FROM announcements WHERE id = ? AND lecturer_id = ?");
    $stmt->execute([$deleteId, $lecturer_id]);
    $_SESSION['announcement_success'] = "Announcement deleted.";
    header("Location: announcements.php");
    exit();
}

// Fetch allocations
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

// Fetch announcements
$annStmt = $pdo->prepare("SELECT a.id, a.message, a.created_at, s.name AS subject_name, d.degree_name, b.batch_name
                          FROM announcements a
                          JOIN subject_allocations sa ON a.subject_allocation_id = sa.id
                          JOIN subjects s ON sa.subject_id = s.id
                          JOIN degrees d ON sa.degree_id = d.id
                          JOIN batches b ON sa.batch_id = b.id
                          WHERE a.lecturer_id = ?
                          ORDER BY a.created_at DESC");
$annStmt->execute([$lecturer_id]);
$announcements = $annStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Announcements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">
<div class="container">
    <h2 class="mb-4">Create Announcement</h2>

    <?php if (isset($_SESSION['announcement_success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['announcement_success'] ?></div>
        <?php unset($_SESSION['announcement_success']); ?>
    <?php endif; ?>

    <form method="POST" class="card p-4 bg-white shadow-sm mb-4">
        <input type="hidden" name="action" value="create">
        <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea name="message" id="message" class="form-control" rows="3" required></textarea>
        </div>

        <label class="form-label mb-2">Select Target Groups</label>
        <div class="row g-2">
            <?php foreach ($allocations as $alloc): ?>
                <div class="col-md-4">
                    <div class="form-check border p-2 rounded bg-light">
                        <input class="form-check-input" type="checkbox" name="allocations[]"
                               value="<?= $alloc['allocation_id'] ?>" id="alloc_<?= $alloc['allocation_id'] ?>">
                        <label class="form-check-label" for="alloc_<?= $alloc['allocation_id'] ?>">
                            <strong><?= htmlspecialchars($alloc['subject_name']) ?></strong><br>
                            <?= htmlspecialchars($alloc['degree_name']) ?> - <?= htmlspecialchars($alloc['batch_name']) ?>
                        </label>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Send Announcement</button>
    </form>

    <h3 class="mt-5">Your Announcements</h3>
    <?php if (count($announcements) > 0): ?>
        <div class="table-responsive mt-3">
            <table class="table table-bordered table-hover bg-white">
                <thead class="table-light">
                <tr>
                    <th>Message</th>
                    <th>Subject</th>
                    <th>Degree</th>
                    <th>Batch</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($announcements as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['message']) ?></td>
                        <td><?= htmlspecialchars($a['subject_name']) ?></td>
                        <td><?= htmlspecialchars($a['degree_name']) ?></td>
                        <td><?= htmlspecialchars($a['batch_name']) ?></td>
                        <td><?= date('Y-m-d H:i', strtotime($a['created_at'])) ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $a['id'] ?>">Edit</button>
                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $a['id'] ?>">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted">No announcements sent yet.</p>
    <?php endif; ?>
</div>

<!-- All Modals -->
<?php foreach ($announcements as $a): ?>
<!-- Edit Modal -->
<div class="modal fade" id="editModal<?= $a['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $a['id'] ?>" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="announcement_id" value="<?= $a['id'] ?>">
        <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel<?= $a['id'] ?>">Edit Announcement</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <textarea name="edit_message" class="form-control" rows="4" required><?= htmlspecialchars($a['message']) ?></textarea>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-warning">Save Changes</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
    </form>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal<?= $a['id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $a['id'] ?>" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="announcement_id" value="<?= $a['id'] ?>">
        <div class="modal-header">
            <h5 class="modal-title text-danger" id="deleteModalLabel<?= $a['id'] ?>">Confirm Deletion</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            Are you sure you want to delete this announcement?
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-danger">Delete</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
    </form>
  </div>
</div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.classList.add('fade');
            setTimeout(() => alert.remove(), 500);
        }, 3000);
    });
</script>
</body>
</html>
