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

<!-- include header here  -->
<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="css/announcements.css">


    <h1 class="page-title">
        <i class="fas fa-bullhorn me-2"></i>Announcements Management
    </h1>

    <?php if (isset($_SESSION['announcement_success'])): ?>
        <div class="alert alert-success d-flex align-items-center">
            <i class="fas fa-check-circle me-2"></i>
            <div><?= $_SESSION['announcement_success'] ?></div>
        </div>
        <?php unset($_SESSION['announcement_success']); ?>
    <?php endif; ?>

    <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="create-tab" data-bs-toggle="tab" data-bs-target="#create-tab-pane" type="button" role="tab" aria-controls="create-tab-pane" aria-selected="true">
                <i class="fas fa-plus-circle me-2"></i>Create Announcement
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-tab-pane" type="button" role="tab" aria-controls="history-tab-pane" aria-selected="false">
                <i class="fas fa-history me-2"></i>Announcement History
            </button>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <!-- Create Announcement Tab -->
        <div class="tab-pane fade show active" id="create-tab-pane" role="tabpanel" aria-labelledby="create-tab" tabindex="0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Create New Announcement</h3>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="mb-4">
                            <label for="message" class="form-label">
                                <i class="fas fa-pen me-2"></i>Announcement Message
                            </label>
                            <textarea name="message" id="message" class="form-control" rows="4" required placeholder="Type your announcement message here..."></textarea>
                        </div>

                        <h4 class="section-title mt-4">
                            <i class="fas fa-users me-2"></i>Select Target Groups
                        </h4>
                        
                        <div class="select-all-wrapper">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label fw-bold" for="selectAll">
                                    Select All Groups
                                </label>
                            </div>
                        </div>

                        <div class="row g-3">
                            <?php foreach ($allocations as $alloc): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="target-group-item">
                                        <div class="form-check">
                                            <input class="form-check-input allocation-checkbox" type="checkbox" name="allocations[]"
                                                   value="<?= $alloc['allocation_id'] ?>" id="alloc_<?= $alloc['allocation_id'] ?>">
                                            <label class="form-check-label" for="alloc_<?= $alloc['allocation_id'] ?>">
                                                <div class="fw-bold text-primary mb-1"><?= htmlspecialchars($alloc['subject_name']) ?></div>
                                                <div class="small">
                                                    <span class="badge bg-secondary me-1"><?= htmlspecialchars($alloc['degree_name']) ?></span>
                                                    <span class="badge bg-info"><?= htmlspecialchars($alloc['batch_name']) ?></span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="reset" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-undo me-2"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Send Announcement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- History Tab -->
        <div class="tab-pane fade" id="history-tab-pane" role="tabpanel" aria-labelledby="history-tab" tabindex="0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Your Announcement History</h3>
                    <div class="input-group" style="max-width: 300px;">
                        <input type="text" class="form-control" id="searchAnnouncements" placeholder="Search announcements...">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (count($announcements) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover" id="announcementsTable">
                                <thead>
                                <tr>
                                    <th>Message</th>
                                    <th>Target</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($announcements as $a): ?>
                                    <tr>
                                        <td class="announcement-message"><?= htmlspecialchars($a['message']) ?></td>
                                        <td>
                                            <span class="badge bg-primary"><?= htmlspecialchars($a['subject_name']) ?></span>
                                            <div class="small mt-1">
                                                <?= htmlspecialchars($a['degree_name']) ?> - <?= htmlspecialchars($a['batch_name']) ?>
                                            </div>
                                        </td>
                                        <td class="announcement-date">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            <?= date('M d, Y', strtotime($a['created_at'])) ?>
                                            <div class="small text-muted">
                                                <i class="far fa-clock me-1"></i>
                                                <?= date('h:i A', strtotime($a['created_at'])) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="table-actions">
                                                <button class="btn btn-sm btn-warning action-btn" data-bs-toggle="modal" data-bs-target="#editModal<?= $a['id'] ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger action-btn" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $a['id'] ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <h5>No announcements sent yet</h5>
                            <p class="text-muted">Your announcements will appear here once you create them.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>


<!-- Edit Modals -->
<?php foreach ($announcements as $a): ?>
<!-- Edit Modal -->
<div class="modal fade" id="editModal<?= $a['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $a['id'] ?>" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="announcement_id" value="<?= $a['id'] ?>">
        <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel<?= $a['id'] ?>">
                <i class="fas fa-edit me-2"></i>Edit Announcement
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label for="edit_message<?= $a['id'] ?>" class="form-label">Announcement Message</label>
                <textarea name="edit_message" id="edit_message<?= $a['id'] ?>" class="form-control" rows="4" required><?= htmlspecialchars($a['message']) ?></textarea>
            </div>
            <div class="small text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Target: <?= htmlspecialchars($a['subject_name']) ?> (<?= htmlspecialchars($a['degree_name']) ?> - <?= htmlspecialchars($a['batch_name']) ?>)
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                <i class="fas fa-times me-1"></i>Cancel
            </button>
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-save me-1"></i>Save Changes
            </button>
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
        <div class="modal-header bg-danger text-white">
            <h5 class="modal-title" id="deleteModalLabel<?= $a['id'] ?>">
                <i class="fas fa-exclamation-triangle me-2"></i>Confirm Deletion
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this announcement?</p>
            <div class="alert alert-warning">
                <i class="fas fa-info-circle me-2"></i>
                This action cannot be undone.
            </div>
            <div class="p-3 bg-light rounded">
                <div class="mb-2 fw-bold">Announcement content:</div>
                <div class="announcement-message"><?= htmlspecialchars($a['message']) ?></div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                <i class="fas fa-times me-1"></i>Cancel
            </button>
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash me-1"></i>Delete
            </button>
        </div>
    </form>
  </div>
</div>
<?php endforeach; ?>

<script>
    // Auto-hide alerts after 3 seconds
    document.addEventListener('DOMContentLoaded', function() {
        // Handle alerts
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500);
            }, 3000);
        });
        
        // Select All functionality
        const selectAllCheckbox = document.getElementById('selectAll');
        const allocationCheckboxes = document.querySelectorAll('.allocation-checkbox');
        
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                allocationCheckboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });
            
            allocationCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const allChecked = Array.from(allocationCheckboxes).every(cb => cb.checked);
                    selectAllCheckbox.checked = allChecked;
                });
            });
        }
        
        // Search functionality
        const searchInput = document.getElementById('searchAnnouncements');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const searchText = this.value.toLowerCase();
                const table = document.getElementById('announcementsTable');
                const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
                
                for (let i = 0; i < rows.length; i++) {
                    const rowText = rows[i].textContent.toLowerCase();
                    if (rowText.includes(searchText)) {
                        rows[i].style.display = '';
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            });
        }
    });
</script>


<!-- footer  -->
<?php include 'includes/footer.php'; ?>