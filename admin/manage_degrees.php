<?php include 'includes/header.php'; ?>

<!-- custom css  -->
<link rel="stylesheet" href="css/manage_degrees.css">

<?php
    // Add degree
    if (isset($_POST['add_degree'])) {
        $stmt = $pdo->prepare("INSERT INTO degrees (degree_name, faculty_id) VALUES (?, ?)");
        $stmt->execute([$_POST['degree_name'], $_POST['faculty_id']]);
        $_SESSION['message'] = "Degree added successfully!";
        $_SESSION['messageType'] = 'success';
        header("Location: manage_degrees.php");
        exit;
    }

    // Edit degree
    if (isset($_POST['update_degree'])) {
        $stmt = $pdo->prepare("UPDATE degrees SET degree_name = ?, faculty_id = ? WHERE id = ?");
        $stmt->execute([$_POST['degree_name'], $_POST['faculty_id'], $_POST['degree_id']]);
        $_SESSION['message'] = "Degree updated successfully!";
        $_SESSION['messageType'] = 'success';
        header("Location: manage_degrees.php");
        exit;
    }

    // Delete degree
    if (isset($_POST['delete_degree'])) {
        $stmt = $pdo->prepare("DELETE FROM degrees WHERE id = ?");
        $stmt->execute([$_POST['degree_id']]);
        $_SESSION['message'] = "Degree deleted successfully!";
        $_SESSION['messageType'] = 'success';
        header("Location: manage_degrees.php");
        exit;
    }


$faculties = $pdo->query("SELECT * FROM faculties")->fetchAll();
$degrees = $pdo->query("SELECT d.*, f.faculty_name FROM degrees d JOIN faculties f ON d.faculty_id = f.id")->fetchAll();
?>


    <div class="dashboard-header">
        <h1 class="dashboard-title">Degree Management</h1>
        <p class="dashboard-subtitle">Add, edit, and manage academic degrees in the system</p>
    </div>

    <div class="action-bar">
        <h4 class="mb-0">All Degrees (<?= count($degrees) ?>)</h4>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addDegreeModal">
            <i class="fas fa-plus btn-icon"></i>Add Degree
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

    <!-- degrees table  -->
    <div class="card">
        <div class="card-body">
            <?php if (count($degrees) > 0): ?>
                <table class="table mb-0">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Degree Name</th>
                        <th>Faculty</th>
                        <th class="text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($degrees as $index => $degree): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td>
                                <span><?= htmlspecialchars($degree['degree_name']) ?></span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark p-2">
                                    <i class="fas fa-building me-1"></i>
                                    <?= htmlspecialchars($degree['faculty_name']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons justify-content-center">
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $degree['id'] ?>">
                                        <i class="fas fa-edit btn-icon"></i>Edit
                                    </button>
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $degree['id'] ?>">
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
                    <i class="fas fa-graduation-cap"></i>
                    <h4>No Degrees Available</h4>
                    <p>Start by adding your first degree using the button above.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

<!-- Add Degree Modal -->
<div class="modal fade" id="addDegreeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle me-2"></i>Add New Degree
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Degree Name</label>
                    <input type="text" name="degree_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Faculty</label>
                    <select name="faculty_id" class="form-select" required>
                        <option value="">Select Faculty</option>
                        <?php foreach ($faculties as $f): ?>
                            <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['faculty_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="add_degree" class="btn btn-success">
                    <i class="fas fa-save btn-icon"></i>Save Degree
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit & Delete Modals -->
<?php foreach ($degrees as $degree): ?>  
    <!-- Edit Modal -->
    <div class="modal fade" id="editModal<?= $degree['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Edit Degree
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="degree_id" value="<?= $degree['id'] ?>">
                    <div class="mb-3">
                        <label class="form-label">Degree Name</label>
                        <input type="text" name="degree_name" class="form-control" value="<?= htmlspecialchars($degree['degree_name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Faculty</label>
                        <select name="faculty_id" class="form-select" required>
                            <?php foreach ($faculties as $f): ?>
                                <option value="<?= $f['id'] ?>" <?= $f['id'] == $degree['faculty_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($f['faculty_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_degree" class="btn btn-primary">
                        <i class="fas fa-save btn-icon"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal<?= $degree['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="post" class="modal-content">
                <input type="hidden" name="degree_id" value="<?= $degree['id'] ?>">
                <div class="modal-header" style="background-color: var(--danger-light);">
                    <h5 class="modal-title" style="color: var(--danger-dark);">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirm Deletion
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                    <p class="mb-1">Are you sure you want to delete this degree?</p>
                    <h5 class="fw-bold"><?= htmlspecialchars($degree['degree_name']) ?></h5>
                    <p class="text-muted small mt-2">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_degree" class="btn btn-danger">
                        <i class="fas fa-trash-alt btn-icon"></i>Yes, Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>


<?php include 'includes/footer.php'; ?>