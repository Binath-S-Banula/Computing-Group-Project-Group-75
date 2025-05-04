<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="css/subject_allocations.css">
<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id = $_POST['subject_id'];
    $lecturer_id = $_POST['lecturer_id'];
    $degree_id = $_POST['degree_id'];
    $batch_id = $_POST['batch_id'];

    $stmt = $pdo->prepare("INSERT INTO subject_allocations (subject_id, lecturer_id, degree_id, batch_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$subject_id, $lecturer_id, $degree_id, $batch_id]);

    // Set success message
    $_SESSION['message'] = "Subject allocated successfully!";
    $_SESSION['messageType'] = 'success';

    // Redirect to avoid resubmission
    header("Location: " . $_SERVER['PHP_SELF'] . "?faculty_id=" . urlencode($_POST['faculty_id']));
    exit;
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    $stmt = $pdo->prepare("DELETE FROM subject_allocations WHERE id = ?");
    $stmt->execute([$delete_id]);

    // Set success message
    $_SESSION['message'] = "Subject allocation removed successfully!";
    $_SESSION['messageType'] = 'success';

    header("Location: " . $_SERVER['PHP_SELF'] . (isset($_GET['faculty_id']) ? "?faculty_id=" . urlencode($_GET['faculty_id']) : ""));
    exit;
}

// Fetch faculties
$faculties = $pdo->query("SELECT * FROM faculties")->fetchAll(PDO::FETCH_ASSOC);

// Fetch subjects and degrees based on faculty (if a faculty is selected)
$subjects = [];
$degrees = [];
$lecturers = [];
$faculty_id = null;

if (isset($_GET['faculty_id'])) {
    $faculty_id = $_GET['faculty_id'];

    // Fetch subjects and degrees based on selected faculty
    $subjects = $pdo->prepare("SELECT * FROM subjects WHERE faculty_id = ?");
    $subjects->execute([$faculty_id]);
    $subjects = $subjects->fetchAll(PDO::FETCH_ASSOC);

    $degrees = $pdo->prepare("SELECT * FROM degrees WHERE faculty_id = ?");
    $degrees->execute([$faculty_id]);
    $degrees = $degrees->fetchAll(PDO::FETCH_ASSOC);

    // Fetch lecturers based on selected faculty
    $lecturers = $pdo->prepare("SELECT * FROM lecturers WHERE faculty_id = ?");
    $lecturers->execute([$faculty_id]);
    $lecturers = $lecturers->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch batches
$batches = $pdo->query("SELECT * FROM batches")->fetchAll(PDO::FETCH_ASSOC);

// Fetch allocated subjects
$allocated_subjects = $pdo->query("
    SELECT sa.id, s.name AS subject, d.degree_name AS degree, b.batch_name AS batch, l.name AS lecturer, f.faculty_name
    FROM subject_allocations sa
    JOIN subjects s ON sa.subject_id = s.id
    JOIN degrees d ON sa.degree_id = d.id
    JOIN batches b ON sa.batch_id = b.id
    JOIN lecturers l ON sa.lecturer_id = l.id
    JOIN faculties f ON s.faculty_id = f.id
    ORDER BY f.id ASC, d.id ASC, l.id ASC
")->fetchAll(PDO::FETCH_ASSOC);

?>

    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <h2 class="dashboard-title">Subject Allocation</h2>
        <p class="dashboard-subtitle">Assign subjects to lecturers, degrees and batches</p>
    </div>


    <!-- success session message  -->
    <?php if (isset($_SESSION['message'])): ?>
        <?php
        $messageType = isset($_SESSION['messageType']) ? $_SESSION['messageType'] : 'info'; // default to 'info' if not set
        $messageContent = is_array($_SESSION['message']) ? implode(', ', $_SESSION['message']) : $_SESSION['message'];
        ?>
        <div class="alert alert-<?= htmlspecialchars($messageType) ?> alert-dismissible show" role="alert" id="success-message">
            <?php if ($messageType === 'success'): ?>
                <i class="fas fa-check-circle me-2"></i>
            <?php else: ?>
                <i class="fas fa-exclamation-circle me-2"></i>
            <?php endif; ?>
            <?= htmlspecialchars($messageContent) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <script>
            setTimeout(function() {
                const message = document.getElementById('success-message');
                if (message) {
                    message.classList.add('fade-out');
                    setTimeout(() => {
                        message.remove();
                    }, 1000);
                }
            }, 3000);
        </script>
        <?php unset($_SESSION['message'], $_SESSION['messageType']); ?>
    <?php endif; ?>



    <!-- Faculty Selection Card -->
    <div class="card">
        <div class="card-body">
            <h4 class="mb-4"><i class="fas fa-building-columns me-2 text-success"></i>Select Faculty</h4>
            <div class="row align-items-end">
                <div class="col-md-8">
                    <label class="form-label visually-hidden" for="facultySelect">Faculty</label>
                    <select class="form-select" id="facultySelect" required>
                        <option value="">Select Faculty</option>
                        <?php foreach ($faculties as $faculty): ?>
                            <option value="<?= $faculty['id'] ?>" <?= ($faculty['id'] == $faculty_id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($faculty['faculty_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 text-end">
                <button type="button" 
                        class="btn btn-primary w-100" 
                        id="addAllocationButton"
                        data-bs-toggle="modal" 
                        data-bs-target="#addAllocationModal" 
                        <?= empty($faculty_id) ? 'disabled' : '' ?>>
                    <i class="fas fa-plus-circle me-2"></i>Allocate New Subject
                </button>
                </div>
            </div>

            <?php if (empty($faculty_id)): ?>
                <div class="alert alert-warning mt-4 mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Please select a faculty first.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Allocated Subjects Table -->
    <div class="card">
        <div class="card-body">
            <h4 class=""><i class="fas fa-list-check me-2 text-success"></i>Allocated Subjects</h4>
            
            <?php if (count($allocated_subjects) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Subject</th>
                                <th>Degree</th>
                                <th>Batch</th>
                                <th>Lecturer</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allocated_subjects as $index => $allocation): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($allocation['subject']) ?></td>
                                    <td><?= htmlspecialchars($allocation['degree']) ?></td>
                                    <td><?= htmlspecialchars($allocation['batch']) ?></td>
                                    <td><?= htmlspecialchars($allocation['lecturer']) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteConfirmModal"
                                                data-allocation-id="<?= $allocation['id'] ?>"
                                                data-subject="<?= htmlspecialchars($allocation['subject']) ?>">
                                            remove <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <h5>No Allocations Found</h5>
                    <p>Allocate subjects to see them listed here.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Allocation Modal -->
    <div class="modal fade" id="addAllocationModal" tabindex="-1" aria-labelledby="addAllocationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAllocationModalLabel"><i class="fas fa-link me-2"></i>Allocate Subject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="faculty_id" value="<?= $faculty_id ?>">
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Subject</label>
                            <select name="subject_id" class="form-select" id="subjectSelect" required <?= empty($subjects) ? 'disabled' : '' ?>>
                                <option value="">Select Subject</option>
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?= $subject['id'] ?>"><?= htmlspecialchars($subject['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Degree</label>
                            <select name="degree_id" class="form-select" id="degreeSelect" required <?= empty($degrees) ? 'disabled' : '' ?>>
                                <option value="">Select Degree</option>
                                <?php foreach ($degrees as $degree): ?>
                                    <option value="<?= $degree['id'] ?>"><?= htmlspecialchars($degree['degree_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Batch</label>
                            <select name="batch_id" class="form-select" required>
                                <option value="">Select Batch</option>
                                <?php foreach ($batches as $batch): ?>
                                    <option value="<?= $batch['id'] ?>"><?= htmlspecialchars($batch['batch_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Lecturer</label>
                            <select name="lecturer_id" class="form-select" required <?= empty($lecturers) ? 'disabled' : '' ?>>
                                <option value="">Select Lecturer</option>
                                <?php foreach ($lecturers as $lecturer): ?>
                                    <option value="<?= $lecturer['id'] ?>"><?= htmlspecialchars($lecturer['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check-circle me-2"></i>Assign Subject
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header danger">
                    <h5 class="modal-title danger" id="deleteConfirmModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="fas fa-trash-alt modal-delete-icon"></i>
                    <h4 class="mb-3">Are you sure?</h4>
                    <p class="mb-0">Do you really want to delete the subject allocation for <strong id="subjectName"></strong>? This process cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <a href="#" id="deleteAllocationBtn" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-2"></i>Delete
                    </a>
                </div>
            </div>
        </div>
    </div>

<script>
    document.getElementById('facultySelect').addEventListener('change', function() {
        const facultyId = this.value;
        if (facultyId) {
            window.location.href = '?faculty_id=' + facultyId;
        } else {
            window.location.href = '<?= $_SERVER['PHP_SELF'] ?>';
        }
    });
    
    // Delete confirmation modal setup
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const allocationId = this.getAttribute('data-allocation-id');
            const subject = this.getAttribute('data-subject');
            
            document.getElementById('subjectName').textContent = subject;
            document.getElementById('deleteAllocationBtn').href = '?delete_id=' + allocationId + 
                '<?= isset($_GET['faculty_id']) ? '&faculty_id=' . urlencode($_GET['faculty_id']) : '' ?>';
        });
    });
</script>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        const message = document.getElementById('successMessage');
        if (message) {
            message.classList.add('fade-out');
            setTimeout(() => {
                message.classList.add('hidden');
            }, 3000); 
        }
    });
</script>

<?php include 'includes/footer.php'; ?>