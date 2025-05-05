<?php
session_start();
require '../db_connection.php';
date_default_timezone_set('Asia/Colombo');

if (!isset($_SESSION['lecturer_uid'])) {
    header("Location: login/login.php");
    exit();
}

$lecturer_uid = $_SESSION['lecturer_uid'];

// Add Reminder
if (isset($_POST['add_reminder'])) {
    $note = trim($_POST['note']);
    $reminder_date = $_POST['reminder_date'];
    $reminder_time = $_POST['reminder_time'];

    if (!empty($note) && !empty($reminder_date) && !empty($reminder_time)) {
        $stmt = $pdo->prepare("INSERT INTO lecturer_reminders (lecturer_id, note, reminder_date, reminder_time) VALUES (?, ?, ?, ?)");
        $stmt->execute([$lecturer_uid, $note, $reminder_date, $reminder_time]);
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
    $stmt->execute([$note, $reminder_date, $reminder_time, $id, $lecturer_uid]);
    $_SESSION['flash_info'] = "Reminder updated.";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Delete Reminder
if (isset($_POST['delete_reminder'])) {
    $id = $_POST['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM lecturer_reminders WHERE id = ? AND lecturer_id = ?");
    $stmt->execute([$id, $lecturer_uid]);
    $_SESSION['flash_danger'] = "Reminder deleted.";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch Reminders
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT * FROM lecturer_reminders WHERE lecturer_id = ? ORDER BY reminder_date, reminder_time");
$stmt->execute([$lecturer_uid]);
$all_reminders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group reminders
$upcoming_reminders = [];
$past_reminders = [];

foreach ($all_reminders as $reminder) {
    if ($reminder['reminder_date'] >= $today) {
        $upcoming_reminders[] = $reminder;
    } else {
        $past_reminders[] = $reminder;
    }
}
?>

<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="css/lecturer_set_reminders.css">


    <h3 class="page-title">
        <i class="fas fa-bell me-2"></i>Reminder Management
    </h3>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash_success'])): ?>
        <div id="alert-msg" class="alert alert-success d-flex align-items-center">
            <i class="fas fa-check-circle me-2"></i>
            <div><?= $_SESSION['flash_success'] ?></div>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php elseif (isset($_SESSION['flash_info'])): ?>
        <div id="alert-msg" class="alert alert-info d-flex align-items-center">
            <i class="fas fa-info-circle me-2"></i>
            <div><?= $_SESSION['flash_info'] ?></div>
        </div>
        <?php unset($_SESSION['flash_info']); ?>
    <?php elseif (isset($_SESSION['flash_danger'])): ?>
        <div id="alert-msg" class="alert alert-danger d-flex align-items-center">
            <i class="fas fa-exclamation-circle me-2"></i>
            <div><?= $_SESSION['flash_danger'] ?></div>
        </div>
        <?php unset($_SESSION['flash_danger']); ?>
    <?php endif; ?>

    <div class="row">
        <!-- Add Reminder Form -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Create Reminder</h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="add_reminder" value="1">
                        <div class="mb-3">
                            <label for="note" class="form-label">
                                <i class="fas fa-sticky-note me-2"></i>Reminder Note
                            </label>
                            <textarea name="note" id="note" class="form-control" rows="3" required placeholder="What do you need to remember?"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="reminder_date" class="form-label">
                                <i class="fas fa-calendar-alt me-2"></i>Date
                            </label>
                            <input type="date" name="reminder_date" id="reminder_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="reminder_time" class="form-label">
                                <i class="fas fa-clock me-2"></i>Time
                            </label>
                            <input type="time" name="reminder_time" id="reminder_time" class="form-control" value="<?= date('H:i') ?>" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-2"></i>Save Reminder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Reminders List -->
        <div class="col-lg-8">
            
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming-tab-pane" type="button" role="tab" aria-controls="upcoming-tab-pane" aria-selected="true">
                        <i class="fas fa-calendar me-2"></i>Upcoming (<?= count($upcoming_reminders) ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="past-tab" data-bs-toggle="tab" data-bs-target="#past-tab-pane" type="button" role="tab" aria-controls="past-tab-pane" aria-selected="false">
                        <i class="fas fa-history me-2"></i>Past (<?= count($past_reminders) ?>)
                    </button>
                </li>
            </ul>
            
            <div class="tab-content" id="myTabContent">
                <!-- Upcoming Reminders Tab -->
                <div class="tab-pane fade show active" id="upcoming-tab-pane" role="tabpanel" aria-labelledby="upcoming-tab" tabindex="0">
                    <?php if (count($upcoming_reminders) > 0): ?>
                        <?php foreach ($upcoming_reminders as $rem): ?>
                            <div class="reminder-card d-flex justify-content-between align-items-start">
                                <div class="reminder-content">
                                    <?php if ($rem['reminder_date'] == date('Y-m-d')): ?>
                                        <span class="date-badge badge-today">Today</span>
                                    <?php else: ?>
                                        <span class="date-badge badge-upcoming">Upcoming</span>
                                    <?php endif; ?>
                                    
                                    <div class="reminder-note"><?= htmlspecialchars($rem['note']) ?></div>
                                    
                                    <div class="reminder-time">
                                        <i class="far fa-calendar-alt me-2"></i>
                                        <?= date('D, M d, Y', strtotime($rem['reminder_date'])) ?>
                                        <i class="far fa-clock ms-3 me-2"></i>
                                        <?= date('h:i A', strtotime($rem['reminder_time'])) ?>
                                    </div>
                                </div>
                                
                                <div class="reminder-actions">
                                    <button class="btn btn-sm btn-warning action-btn" data-bs-toggle="modal" data-bs-target="#editModal<?= $rem['id'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger action-btn" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $rem['id'] ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="far fa-calendar-check"></i>
                            </div>
                            <h4>No Upcoming Reminders</h4>
                            <p class="text-muted">Create a new reminder to stay organized.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Past Reminders Tab -->
                <div class="tab-pane fade" id="past-tab-pane" role="tabpanel" aria-labelledby="past-tab" tabindex="0">
                    <?php if (count($past_reminders) > 0): ?>
                        <?php foreach ($past_reminders as $rem): ?>
                            <div class="reminder-card d-flex justify-content-between align-items-start">
                                <div class="reminder-content">
                                    <span class="date-badge badge-past">Past</span>
                                    
                                    <div class="reminder-note"><?= htmlspecialchars($rem['note']) ?></div>
                                    
                                    <div class="reminder-time">
                                        <i class="far fa-calendar-alt me-2"></i>
                                        <?= date('D, M d, Y', strtotime($rem['reminder_date'])) ?>
                                        <i class="far fa-clock ms-3 me-2"></i>
                                        <?= date('h:i A', strtotime($rem['reminder_time'])) ?>
                                    </div>
                                </div>
                                
                                <div class="reminder-actions">
                                    <button class="btn btn-sm btn-warning action-btn" data-bs-toggle="modal" data-bs-target="#editModal<?= $rem['id'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger action-btn" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $rem['id'] ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="far fa-calendar"></i>
                            </div>
                            <h4>No Past Reminders</h4>
                            <p class="text-muted">Past reminders will appear here.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>


<!-- Edit & Delete Modals -->
<?php foreach ($all_reminders as $rem): ?>
    <!-- Edit Modal -->
    <div class="modal fade" id="editModal<?= $rem['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $rem['id'] ?>" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <input type="hidden" name="edit_reminder" value="1">
                <input type="hidden" name="reminder_id" value="<?= $rem['id'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel<?= $rem['id'] ?>">
                        <i class="fas fa-edit me-2"></i>Edit Reminder
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_note<?= $rem['id'] ?>" class="form-label">Reminder Note</label>
                        <textarea name="edit_note" id="edit_note<?= $rem['id'] ?>" class="form-control" rows="3" required><?= htmlspecialchars($rem['note']) ?></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_reminder_date<?= $rem['id'] ?>" class="form-label">Date</label>
                            <input type="date" name="edit_reminder_date" id="edit_reminder_date<?= $rem['id'] ?>" class="form-control" value="<?= $rem['reminder_date'] ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_reminder_time<?= $rem['id'] ?>" class="form-label">Time</label>
                            <input type="time" name="edit_reminder_time" id="edit_reminder_time<?= $rem['id'] ?>" class="form-control" value="<?= $rem['reminder_time'] ?>" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal<?= $rem['id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $rem['id'] ?>" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <input type="hidden" name="delete_reminder" value="1">
                <input type="hidden" name="delete_id" value="<?= $rem['id'] ?>">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel<?= $rem['id'] ?>">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this reminder?</p>
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        This action cannot be undone.
                    </div>
                    <div class="p-3 bg-light rounded">
                        <div class="mb-2 fw-bold">Reminder details:</div>
                        <div class="mb-2"><?= htmlspecialchars($rem['note']) ?></div>
                        <div class="small text-muted">
                            <?= date('M d, Y', strtotime($rem['reminder_date'])) ?> at
                            <?= date('h:i A', strtotime($rem['reminder_time'])) ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide alerts
        const alertMsg = document.getElementById('alert-msg');
        if (alertMsg) {
            setTimeout(() => {
                alertMsg.classList.add('fade');
                setTimeout(() => alertMsg.remove(), 500);
            }, 3000);
        }
    });
</script>


<?php include 'includes/footer.php'; ?>