<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="css/lecturer_notifications.css">
<?php

// Handle approve/reject action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notification_id'], $_POST['action'])) {
    $notificationId = $_POST['notification_id'];
    $action = $_POST['action'];

    if (in_array($action, ['approved', 'rejected'])) {
        // Update notification status based on the action
        $stmt = $pdo->prepare("UPDATE lecturer_notifications SET status = ? WHERE id = ? AND lecturer_id = ?");
        $stmt->execute([$action, $notificationId, $lecturer_uid]);
    }

    // Prevent resubmission on refresh
    header("Location: lecturer_notifications.php");
    exit;
}

// Fetch notifications
$stmt = $pdo->prepare("
    SELECT 
        ln.id as notif_id, 
        ln.status, 
        tp.id as proposal_id, 
        tp.day, 
        tp.start_time, 
        tp.end_time, 
        s.name AS subject_name, 
        tp.reason
    FROM lecturer_notifications ln
    JOIN timetable_proposals tp ON ln.proposal_id = tp.id
    JOIN subjects s ON tp.subject_id = s.id
    WHERE ln.lecturer_id = ?
    ORDER BY ln.created_at DESC
");

$stmt->execute([$lecturer_uid]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<?php if (count($notifications) === 0): ?>
    <div class="empty-state">
        <div class="empty-icon">
            <i class="far fa-bell-slash"></i>
        </div>
        <h3>No pending notifications</h3>
        <p class="empty-message">You're all caught up! No new timetable proposals require your attention.</p>
    </div>
<?php else: ?>
    <?php foreach ($notifications as $notif): ?>
        <div class="notification-card card-<?= htmlspecialchars($notif['status']) ?>">
            <div class="notification-header">
                <div class="proposal-id">
                    <i class="fas fa-hashtag me-1"></i>Proposal #<?= htmlspecialchars($notif['proposal_id']) ?>
                </div>
                <?php if ($notif['status'] !== 'pending'): ?>
                    <span class="status-badge badge-<?= htmlspecialchars($notif['status']) ?>">
                        <i class="fas fa-<?= $notif['status'] === 'approved' ? 'check' : 'times' ?> me-1"></i>
                        <?= ucfirst(htmlspecialchars($notif['status'])) ?>
                    </span>
                <?php else: ?>
                    <span class="status-badge badge-pending">
                        <i class="fas fa-clock me-1"></i>
                        Pending Response
                    </span>
                <?php endif; ?>
            </div>
            
            <div class="notification-body">
                <div class="proposal-subject"><?= htmlspecialchars($notif['subject_name']) ?></div>
                
                <div class="proposal-time">
                    <i class="far fa-calendar-alt"></i>
                    <?= htmlspecialchars($notif['day']) ?> (<?= htmlspecialchars($notif['start_time']) ?> - <?= htmlspecialchars($notif['end_time']) ?>)
                </div>
                
                <div class="proposal-description">
                    You have been requested to take over <strong><?= htmlspecialchars($notif['subject_name']) ?></strong>
                    on the scheduled time above. Please review and respond to this proposal.
                </div>
            </div>
            
            <?php if ($notif['status'] === 'pending'): ?>
                <div class="notification-footer">
                    <form method="POST" class="d-flex gap-2">
                        <input type="hidden" name="notification_id" value="<?= htmlspecialchars($notif['notif_id']) ?>">
                        <button type="submit" name="action" value="approved" class="btn btn-approve">
                            <i class="fas fa-check me-2"></i>Approve
                        </button>
                        <button type="submit" name="action" value="rejected" class="btn btn-reject">
                            <i class="fas fa-times me-2"></i>Reject
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>


<?php include 'includes/footer.php'; ?>
