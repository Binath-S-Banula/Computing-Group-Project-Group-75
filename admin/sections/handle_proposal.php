<?php
require '../db_connection.php';

// Load proposal data with subject name
$proposal_id = $_GET['id'];
$stmt = $pdo->prepare("
    SELECT p.*, s.name AS subject_name 
    FROM timetable_proposals p
    LEFT JOIN subjects s ON p.subject_id = s.id 
    WHERE p.id = ?
");
$stmt->execute([$proposal_id]);
$proposal = $stmt->fetch(PDO::FETCH_ASSOC);

// Save newly assigned lecturer if passed
if (isset($_GET['new_lecturer'])) {
    $newLecturerId = (int)$_GET['new_lecturer'];
    $updateLecturer = $pdo->prepare("UPDATE timetable_proposals SET new_lecturer = :lecturer WHERE id = :id");
    $updateLecturer->execute([':lecturer' => $newLecturerId, ':id' => $proposal_id]);
    $proposal['new_lecturer'] = $newLecturerId;
}

// Get all lecturers
$lecturers = $pdo->query("SELECT * FROM lecturers")->fetchAll(PDO::FETCH_ASSOC);

// If new lecturer is assigned, get their name
$newLecturerName = null;
if (!empty($proposal['new_lecturer'])) {
    $stmt = $pdo->prepare("SELECT name FROM lecturers WHERE id = ?");
    $stmt->execute([$proposal['new_lecturer']]);
    $newLecturerName = $stmt->fetchColumn();
}
?>

<div class="details-card">
    <h2 class="details-title">Request Details</h2>

    <!-- Subject -->
    <div class="detail-row">
        <div class="detail-label"><i class="fa-solid fa-book"></i> Subject</div>
        <div class="detail-value"><?= htmlspecialchars($proposal['subject_name']) ?></div>
    </div>

    <!-- Reason -->
    <div class="detail-row">
        <div class="detail-label"><i class="fa-solid fa-circle-info"></i> Reason</div>
        <div class="detail-value"><?= nl2br(htmlspecialchars($proposal['reason'])) ?></div>
    </div>

    <!-- Date -->
    <div class="detail-row">
        <div class="detail-label"><i class="fa-solid fa-calendar-day"></i> Date</div>
        <div class="detail-value"><?= htmlspecialchars($proposal['date']) ?></div>
    </div>

    <!-- Day -->
    <div class="detail-row">
        <div class="detail-label"><i class="fa-solid fa-calendar-week"></i> Day</div>
        <div class="detail-value"><?= htmlspecialchars($proposal['day']) ?></div>
    </div>

    <!-- Start Time -->
    <div class="detail-row">
        <div class="detail-label"><i class="fa-solid fa-clock"></i> Start Time</div>
        <div class="detail-value"><?= htmlspecialchars($proposal['start_time']) ?></div>
    </div>

    <!-- End Time -->
    <div class="detail-row">
        <div class="detail-label"><i class="fa-solid fa-clock"></i> End Time</div>
        <div class="detail-value"><?= htmlspecialchars($proposal['end_time']) ?></div>
    </div>

    <!-- Assigned New Lecturer -->
    <div class="detail-row">
        <div class="detail-label"><i class="fa-solid fa-user-plus"></i> Assigned Lecturer</div>
        <div class="detail-value">
            <?= $newLecturerName ? htmlspecialchars($newLecturerName) : '<span class="text-muted">None assigned</span>' ?>
        </div>
    </div>

    <!-- Assign Lecturer Button -->
    <div class="mt-3">
        <a href="available_lecturers.php?day=<?= urlencode($proposal['day']) ?>&start_time=<?= urlencode($proposal['start_time']) ?>&end_time=<?= urlencode($proposal['end_time']) ?>&proposal_id=<?= $proposal_id ?>&back=<?= urlencode($_SERVER['PHP_SELF'] . '?id=' . $proposal_id) ?>" class="btn btn-sm btn-outline-primary">
            <i class="fa fa-user-plus me-1"></i> Find Available Lecturer
        </a>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
        $action = $_POST['action'];
        if (in_array($action, ['approved', 'rejected'])) {
            $update = $pdo->prepare("UPDATE timetable_proposals SET status = :status WHERE id = :id");
            $update->execute([':status' => $action, ':id' => $proposal_id]);
            $proposal['status'] = $action;
        }
    }
    ?>

    <!-- Status -->
    <?php if (in_array($proposal['status'], ['approved', 'rejected'])): ?>
        <div class="detail-row mt-4">
            <div class="detail-label"><i class="fa-solid fa-tag"></i> Status</div>
            <div class="detail-value">
                <span class="status-indicator status-<?= $proposal['status'] ?>">
                    <?php if ($proposal['status'] === 'approved'): ?>
                        <i class="fa-solid fa-check-circle"></i>
                    <?php else: ?>
                        <i class="fa-solid fa-times-circle"></i>
                    <?php endif; ?>
                    <?= ucfirst($proposal['status']) ?>
                </span>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($proposal['status'] === 'pending'): ?>
        <div class="action-buttons mt-3">
            <form method="POST" class="d-inline">
                <input type="hidden" name="action" value="approved">
                <button type="submit" class="btn btn-approve">
                    <i class="fa-solid fa-check"></i> Approve Proposal
                </button>
            </form>
            <form method="POST" class="d-inline">
                <input type="hidden" name="action" value="rejected">
                <button type="submit" class="btn btn-reject">
                    <i class="fa-solid fa-xmark"></i> Reject Proposal
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>
