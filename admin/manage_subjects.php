<?php include 'includes/header.php'; ?>
<!-- custom css  -->
<link rel="stylesheet" href="css/manage_degrees.css">

<?php
// Fetch faculties for use in select options
$faculties = $pdo->query("SELECT * FROM faculties")->fetchAll(PDO::FETCH_ASSOC);

// Add Subject
if (isset($_POST['add_subject'])) {
    $stmt = $pdo->prepare("INSERT INTO subjects (name, code, faculty_id) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['name'], $_POST['code'], $_POST['faculty_id']]);
    $_SESSION['message'] = "Subject added successfully!";
    $_SESSION['messageType'] = 'success';
    header("Location: manage_subjects.php");
    exit;
}

// Edit Subject
if (isset($_POST['update_subject'])) {
    $stmt = $pdo->prepare("UPDATE subjects SET name = ?, code = ?, faculty_id = ? WHERE id = ?");
    $stmt->execute([$_POST['name'], $_POST['code'], $_POST['faculty_id'], $_POST['subject_id']]);
    $_SESSION['message'] = "Subject updated successfully!";
    $_SESSION['messageType'] = 'success';
    header("Location: manage_subjects.php");
    exit;
}

// Delete Subject
if (isset($_POST['delete_subject'])) {
    $stmt = $pdo->prepare("DELETE FROM subjects WHERE id = ?");
    $stmt->execute([$_POST['subject_id']]);
    $_SESSION['message'] = "Subject deleted successfully!";
    $_SESSION['messageType'] = 'success';
    header("Location: manage_subjects.php");
    exit;
}

// Fetch subjects with faculty name
$subjects = $pdo->query("
    SELECT subjects.*, faculties.faculty_name 
    FROM subjects 
    JOIN faculties ON subjects.faculty_id = faculties.id
")->fetchAll(PDO::FETCH_ASSOC);
?>


    <div class="dashboard-header">
        <h1 class="dashboard-title">Subject Management</h1>
        <p class="dashboard-subtitle">Add, edit, and manage academic subjects in the system</p>
    </div>

    <div class="action-bar">
        <h4 class="mb-0">All Subjects (<?= count($subjects) ?>)</h4>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
            <i class="fas fa-plus btn-icon"></i>Add Subject
        </button>
    </div>


    <!-- success message  -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['messageType'] ?> alert-dismissible show" role="alert" id="success-message">
            <i class="fas fa-<?= $_SESSION['messageType'] == 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
            <?= $_SESSION['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <script>
            setTimeout(() => {
                const msg = document.getElementById('success-message');
                msg.classList.add('fade-out');
                setTimeout(() => msg.remove(), 1000);
            }, 3000);
        </script>
        <?php unset($_SESSION['message'], $_SESSION['messageType']); ?>
    <?php endif; ?>

    <!-- subjects table  -->
    <div class="card">
        <div class="card-body">
            <?php if (count($subjects) > 0): ?>
                <table class="table mb-0">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Subject Name</th>
                        <th>Subject Code</th>
                        <th>Faculty</th>
                        <th class="text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($subjects as $index => $subject): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($subject['name']) ?></td>
                            <td><span class="badge bg-light text-dark"><?= htmlspecialchars($subject['code']) ?></span></td>
                            <td><?= htmlspecialchars($subject['faculty_name']) ?></td>
                            <td>
                                <div class="action-buttons justify-content-center">
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $subject['id'] ?>">
                                        <i class="fas fa-edit btn-icon"></i>Edit
                                    </button>
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $subject['id'] ?>">
                                        <i class="fas fa-trash-alt btn-icon"></i>Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-book"></i>
                    <h4>No Subjects Available</h4>
                    <p>Start by adding your first subject using the button above.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Subject Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle me-2"></i>Add New Subject
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Subject Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Subject Code</label>
                    <input type="text" name="code" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Faculty</label>
                    <select name="faculty_id" class="form-select" required>
                        <option value="">Select Faculty</option>
                        <?php foreach ($faculties as $faculty): ?>
                            <option value="<?= $faculty['id'] ?>"><?= htmlspecialchars($faculty['faculty_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="add_subject" class="btn btn-success">
                    <i class="fas fa-save btn-icon"></i>Save Subject
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit & Delete Modals -->
<?php foreach ($subjects as $subject): ?>
    <!-- Edit Modal -->
    <div class="modal fade" id="editModal<?= $subject['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Edit Subject
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="subject_id" value="<?= $subject['id'] ?>">
                    <div class="mb-3">
                        <label class="form-label">Subject Name</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($subject['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject Code</label>
                        <input type="text" name="code" class="form-control" value="<?= htmlspecialchars($subject['code']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Faculty</label>
                        <select name="faculty_id" class="form-select" required>
                            <?php foreach ($faculties as $faculty): ?>
                                <option value="<?= $faculty['id'] ?>" <?= $faculty['id'] == $subject['faculty_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($faculty['faculty_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_subject" class="btn btn-primary">
                        <i class="fas fa-save btn-icon"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal<?= $subject['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="post" class="modal-content">
                <input type="hidden" name="subject_id" value="<?= $subject['id'] ?>">
                <div class="modal-header" style="background-color: var(--danger-light);">
                    <h5 class="modal-title" style="color: var(--danger-dark);">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirm Deletion
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                    <p class="mb-1">Are you sure you want to delete this subject?</p>
                    <h5 class="fw-bold"><?= htmlspecialchars($subject['name']) ?> (<?= htmlspecialchars($subject['code']) ?>)</h5>
                    <p class="text-muted small mt-2">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_subject" class="btn btn-danger">
                        <i class="fas fa-trash-alt btn-icon"></i>Yes, Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>



<?php include 'includes/footer.php'; ?>