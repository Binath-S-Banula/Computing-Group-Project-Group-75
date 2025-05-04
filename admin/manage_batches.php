<?php include 'includes/header.php'; ?>

<!-- custom css  -->
<link rel="stylesheet" href="css/manage_degrees.css">

<?php
// Add batch
if (isset($_POST['add_batch'])) {
    $stmt = $pdo->prepare("INSERT INTO batches (batch_name) VALUES (?)");
    $stmt->execute([$_POST['batch_name']]);
    $_SESSION['message'] = "Batch added successfully!";
    $_SESSION['messageType'] = 'success';
    header("Location: manage_batches.php");
    exit;
}

// Edit batch
if (isset($_POST['update_batch'])) {
    $stmt = $pdo->prepare("UPDATE batches SET batch_name = ? WHERE id = ?");
    $stmt->execute([$_POST['batch_name'], $_POST['batch_id']]);
    $_SESSION['message'] = "Batch updated successfully!";
    $_SESSION['messageType'] = 'success';
    header("Location: manage_batches.php");
    exit;
}

// Delete batch
if (isset($_POST['delete_batch'])) {
    $stmt = $pdo->prepare("DELETE FROM batches WHERE id = ?");
    $stmt->execute([$_POST['batch_id']]);
    $_SESSION['message'] = "Batch deleted successfully!";
    $_SESSION['messageType'] = 'success';
    header("Location: manage_batches.php");
    exit;
}

$batches = $pdo->query("SELECT * FROM batches")->fetchAll();
?>


    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <h1 class="dashboard-title">Manage Batches</h1>
        <p class="dashboard-subtitle">Create, edit and organize your batches</p>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
        <div>
            <h4 class="mb-0">All Batches(<?= count($batches) ?>)</h4>
        </div>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addBatchModal">
            <i class="fas fa-plus btn-icon"></i>Add New Batch
        </button>
    </div>


    <!-- success message  -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['messageType'] ?> alert-dismissible show" role="alert" id="success-message">
            <?php if ($_SESSION['messageType'] === 'success'): ?>
                <i class="fas fa-check-circle me-2"></i>
            <?php else: ?>
                <i class="fas fa-exclamation-circle me-2"></i>
            <?php endif; ?>
            <?= $_SESSION['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <script>
            setTimeout(function() {
                const message = document.getElementById('success-message');
                message.classList.add('fade-out');
                setTimeout(() => {
                    message.remove();
                }, 1000);
            }, 3000);
        </script>
        <?php unset($_SESSION['message'], $_SESSION['messageType']); ?>
    <?php endif; ?>

    <!-- Batches Table -->
    <div class="card">
        <div class="card-body">
            <?php if (count($batches) > 0): ?>
                <table class="table table-hover mb-0">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Batch Name</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($batches as $index => $batch): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($batch['batch_name']) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $batch['id'] ?>">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $batch['id'] ?>">
                                        <i class="fas fa-trash-alt me-1"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h4>No Batches Found</h4>
                    <p>Get started by adding your first batch!</p>
                    <button class="btn btn-success mt-3" data-bs-toggle="modal" data-bs-target="#addBatchModal">
                        <i class="fas fa-plus me-2"></i>Add New Batch
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>


<!-- Add Batch Modal -->
<div class="modal fade" id="addBatchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Add New Batch</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label class="form-label">Batch Name</label>
                    <input type="text" name="batch_name" class="form-control" placeholder="Enter batch name" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="add_batch" class="btn btn-success">
                    <i class="fas fa-save me-2"></i>Save Batch
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit & Delete Modals -->
<?php foreach ($batches as $batch): ?>
    <!-- Edit Modal -->
    <div class="modal fade" id="editModal<?= $batch['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Batch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="batch_id" value="<?= $batch['id'] ?>">
                    <div class="form-group mb-3">
                        <label class="form-label">Batch Name</label>
                        <input type="text" name="batch_name" class="form-control" value="<?= htmlspecialchars($batch['batch_name']) ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_batch" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal<?= $batch['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="post" class="modal-content">
                <input type="hidden" name="batch_id" value="<?= $batch['id'] ?>">
                <div class="modal-header" style="background-color: var(--danger-light);">
                    <h5 class="modal-title" style="color: var(--danger-dark);"><i class="fas fa-exclamation-triangle me-2"></i>Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-trash-alt text-danger" style="font-size: 48px;"></i>
                    </div>
                    <p class="text-center">Are you sure you want to delete <strong><?= htmlspecialchars($batch['batch_name']) ?></strong>?</p>
                    <p class="text-center text-muted small">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_batch" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-2"></i>Yes, Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>


<?php include 'includes/footer.php'; ?>