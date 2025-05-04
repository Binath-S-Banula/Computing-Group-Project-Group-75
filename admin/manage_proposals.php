<?php include 'includes/header.php'; ?>

<?php
// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'], $_POST['proposal_id'])) {
    $proposal_id = $_POST['proposal_id'];
    $action = $_POST['action'];

    if (in_array($action, ['approved', 'rejected'])) {
        $stmt = $pdo->prepare("UPDATE timetable_proposals SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $action, ':id' => $proposal_id]);
    }

    // Redirect to avoid form resubmission on refresh
    $query = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
    header("Location: " . $_SERVER['PHP_SELF'] . $query);
    exit;
}


    $conditions = [];
    $params = [];

    if (!empty($_GET['status']) && $_GET['status'] != 'all') {
        $conditions[] = "tp.status = :status";
        $params[':status'] = $_GET['status'];
    }

    if (!empty($_GET['assignment']) && $_GET['assignment'] != 'all') {
        if ($_GET['assignment'] == 'assigned') {
            $conditions[] = "tp.new_lecturer IS NOT NULL";
        } elseif ($_GET['assignment'] == 'unassigned') {
            $conditions[] = "tp.new_lecturer IS NULL";
        }
    }

    if (!empty($_GET['search'])) {
        $conditions[] = "(tp.subject LIKE :search OR l1.name LIKE :search OR l2.name LIKE :search)";
        $params[':search'] = '%' . $_GET['search'] . '%';
    }

    $whereClause = count($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

    $stmt = $pdo->prepare("
        SELECT tp.*, 
            l1.name as lecturer_name,
            l2.name as assigned_lecturer_name,
            s.name as subject_name
        FROM timetable_proposals tp
        LEFT JOIN lecturers l1 ON tp.lecturer_id = l1.id
        LEFT JOIN lecturers l2 ON tp.new_lecturer = l2.id
        LEFT JOIN subjects s ON tp.subject_id = s.id
        $whereClause
        ORDER BY tp.created_at DESC
    ");

    $stmt->execute($params);
    $proposals = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


        <div class="section-card">
            <div class="page-header">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-clipboard-list me-2"></i>
                        Timetable Proposals
                        <span class="proposal-count"><?= count($proposals) ?></span>
                    </h1>
                </div>
                <span class="admin-badge">
                    <i class="fas fa-user-shield me-2"></i>
                    Admin Panel
                </span>
            </div>
            
            <!-- search box and filter  -->
            <form method="GET" class="toolbar d-flex flex-wrap gap-2 align-items-center">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="form-control" placeholder="Search proposals..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </div>
                <div class="filters-group d-flex gap-2">
                    <select name="status" class="form-select">
                        <option value="all" <?= ($_GET['status'] ?? '') == 'all' ? 'selected' : '' ?>>All Statuses</option>
                        <option value="pending" <?= ($_GET['status'] ?? '') == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="approved" <?= ($_GET['status'] ?? '') == 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="rejected" <?= ($_GET['status'] ?? '') == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    </select>
                    <select name="assignment" class="form-select">
                        <option value="all" <?= ($_GET['assignment'] ?? '') == 'all' ? 'selected' : '' ?>>All Lecturers</option>
                        <option value="assigned" <?= ($_GET['assignment'] ?? '') == 'assigned' ? 'selected' : '' ?>>With Assigned Lecturer</option>
                        <option value="unassigned" <?= ($_GET['assignment'] ?? '') == 'unassigned' ? 'selected' : '' ?>>Without Assigned Lecturer</option>
                    </select>
                    <button type="submit" class="btn btn-outline-secondary filter-btn">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="?" class="btn btn-link text-decoration-none clear-filter">Clear Filters</a>
                </div>
            </form>

            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="15%">Lecturer</th>
                            <th width="12%">Date & Time</th>
                            <th width="18%">Subject</th>
                            <th width="15%">Assigned To</th>
                            <th width="20%">Actions</th>
                            <th width="15%">Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($proposals) > 0): ?>
                            <?php foreach ($proposals as $proposal): ?>
                                <?php
                                    // Get assigned lecturer status (if any)
                                    $hasAssignedLecturer = !empty($proposal['new_lecturer']);
                                    
                                    if ($hasAssignedLecturer) {
                                        $stmt = $pdo->prepare("SELECT status FROM lecturer_notifications WHERE proposal_id = ? AND lecturer_id = ?");
                                        $stmt->execute([$proposal['id'], $proposal['new_lecturer']]);
                                        $notif = $stmt->fetch(PDO::FETCH_ASSOC);
                                        $notifStatus = $notif['status'] ?? 'pending';
                                    }
  
                                    // Define status color class
                                    $statusClass = 'pending';
                                    if ($proposal['status'] === 'approved') {
                                        $statusClass = 'approved';
                                    } elseif ($proposal['status'] === 'rejected') {
                                        $statusClass = 'rejected';
                                    }
                                ?>
                                <tr>
                                    <td><strong class="text-primary">#<?= $proposal['id'] ?></strong></td>
                                    <td>
                                        <span class="lecturer-name">
                                            <?= htmlspecialchars($proposal['lecturer_name'] ?? 'Lecturer #'.$proposal['lecturer_id']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="date-time-info">
                                            <div class="date-info">
                                                <i class="far fa-calendar-alt"></i>
                                                <span><?= htmlspecialchars($proposal['date']) ?></span>
                                            </div>
                                            <div class="time-info">
                                                <i class="far fa-clock"></i>
                                                <span><?= htmlspecialchars($proposal['start_time']) ?> - <?= htmlspecialchars($proposal['end_time']) ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="subject-name"><?= htmlspecialchars($proposal['subject_name'] ?? 'Unknown Subject') ?></span>
                                    </td>
                                    <td>
                                        <?php if ($hasAssignedLecturer): ?>
                                            <div class="assigned-lecturer-container">
                                                <span class="status-indicator status-<?= $notifStatus ?>">
                                                    <i class="fas fa-<?= $notifStatus === 'approved' ? 'check' : ($notifStatus === 'rejected' ? 'times' : 'clock') ?> me-1"></i>
                                                    <?= ucfirst($notifStatus) ?>
                                                </span>
                                                <span class="lecturer-name">
                                                    <?= htmlspecialchars($proposal['assigned_lecturer_name'] ?? 'Lecturer #'.$proposal['new_lecturer']) ?>
                                                </span>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">
                                                <i class="fas fa-user-slash me-1"></i>
                                                Not assigned
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <?php if ($proposal['status'] === 'pending'): ?>
                                                <form method="POST" class="d-inline-block me-1">
                                                    <input type="hidden" name="proposal_id" value="<?= $proposal['id'] ?>">
                                                    <input type="hidden" name="action" value="approved">
                                                    <button type="submit" class="btn-action btn-approve">
                                                        <i class="fas fa-check"></i>
                                                        Approve
                                                    </button>
                                                </form>
                                                <form method="POST" class="d-inline-block me-1">
                                                    <input type="hidden" name="proposal_id" value="<?= $proposal['id'] ?>">
                                                    <input type="hidden" name="action" value="rejected">
                                                    <button type="submit" class="btn-action btn-reject">
                                                        <i class="fas fa-times"></i>
                                                        Reject
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="status-badge status-<?= $statusClass ?>">
                                                    <i class="fas fa-<?= $proposal['status'] === 'approved' ? 'check' : 'times' ?> me-1"></i>
                                                    <?= ucfirst($proposal['status']) ?>
                                                </span>
                                            <?php endif; ?>
                                            
                                            <a href="proposal_details.php?id=<?= $proposal['id'] ?>" class="btn-action btn-view ms-auto">
                                                <i class="fas fa-eye"></i>
                                                Details
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="far fa-clock me-1"></i>
                                            <?= date('M d, Y H:i', strtotime($proposal['created_at'])) ?>
                                        </small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state text-center">
                                        <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                        <h4>No Proposals Found</h4>
                                        <p class="text-muted">There are no timetable proposals in the system yet.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (count($proposals) > 10): ?>
            <div class="d-flex justify-content-between align-items-center p-3 bg-light">
                <div class="pagination-info">
                    Showing <strong>1-10</strong> of <strong><?= count($proposals) ?></strong> proposals
                </div>
                <nav aria-label="Proposals pagination">
                    <ul class="pagination">
                        <li class="page-item"><a class="page-link" href="#"><i class="fas fa-chevron-left"></i></a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" href="#"><i class="fas fa-chevron-right"></i></a></li>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>


    <?php include 'includes/footer.php'; ?>