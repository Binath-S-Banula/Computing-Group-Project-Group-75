<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="css/manage_doctors.css">

<?php
// Add Doctor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_doctor'])) {
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];
    $stmt = $pdo->prepare("INSERT INTO doctors (name, specialization) VALUES (?, ?)");
    $stmt->execute([$name, $specialization]);

    // Set session success message
    $_SESSION['success_message'] = "Doctor added successfully.";

    header("Location: manage_doctors.php");
    exit;
}

// Delete Doctor
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM doctors WHERE id = ?");
    $stmt->execute([$deleteId]);

    // Set session success message
    $_SESSION['success_message'] = "Doctor deleted successfully.";

    header("Location: manage_doctors.php");
    exit;
}

// Toggle Status
if (isset($_GET['toggle_id'])) {
    $toggleId = $_GET['toggle_id'];
    $stmt = $pdo->prepare("UPDATE doctors SET is_active = 1 - is_active WHERE id = ?");
    $stmt->execute([$toggleId]);
    header("Location: manage_doctors.php");
    exit;
}

$doctors = $pdo->query("SELECT * FROM doctors ORDER BY id ASC")->fetchAll();
$doctorsCount = count($doctors);  // Get the count of doctors
?>

<div class="dashboard-title d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Manage Doctors</h2>
        <p>Manage doctors and set availability of doctors</p>
    </div>
    <button style="z-index:100;" class="btn btn-primary add-btn" data-bs-toggle="modal" data-bs-target="#addDoctorModal">+ Add Doctor</button>
</div>


    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['success_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success_message']); // Clear the message after displaying it ?>
    <?php endif; ?>



<div class="table-responsive">
    <table class="table bg-white">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Specialization</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($doctors as $index => $doc): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td>Dr. <?= htmlspecialchars($doc['name']) ?></td>
                    <td><?= htmlspecialchars($doc['specialization']) ?></td>
                    <td>
                        <span class="badge bg-<?= $doc['is_active'] ? 'success' : 'secondary' ?>">
                            <?= $doc['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td>
                        <a href="?toggle_id=<?= $doc['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-exchange-alt" style="margin-right: 6px;"></i>Status</a>
                        <a href="doctor_hours.php?doctor_id=<?= $doc['id'] ?>" class="btn btn-sm btn-info"><i class="fas fa-clock" style="margin-right: 6px;"></i>Set Hours</a>
                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $doc['id'] ?>"><i class="fas fa-trash-alt" style="margin-right: 6px;"></i>Delete</button>
                    </td>
                </tr>

                <!-- Delete Modal -->
                <div class="modal fade medical-modal" id="deleteModal<?= $doc['id'] ?>" tabindex="-1" aria-labelledby="deleteLabel<?= $doc['id'] ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header danger-header">
                                <h5 class="modal-title">Confirm Deletion</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete <strong><?= htmlspecialchars($doc['name']) ?></strong>?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <a href="?delete_id=<?= $doc['id'] ?>" class="btn btn-danger">Yes, Delete</a>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        </tbody>
    </table>
    <!-- if no doctors  -->
    <div class="no-doctors-container" id="no-doctors" style="display: none;">
        <i class="fas fa-user-md fa-5x"></i>
        <p>No doctors available</p>
    </div>
</div>

<!-- Add Doctor Modal -->
<div class="modal fade medical-modal" id="addDoctorModal" tabindex="-1" aria-labelledby="addDoctorLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header primary-header">
                <h5 class="modal-title" id="addDoctorLabel">Add New Doctor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="name" class="form-label">Doctor Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="specialization" class="form-label">Specialization</label>
                    <input type="text" name="specialization" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="add_doctor" class="btn btn-primary">Add Doctor</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Show the toast message if it exists
    document.addEventListener('DOMContentLoaded', function () {
        const successMessage = '<?= isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '' ?>';
        
        if (successMessage) {
            const toast = new bootstrap.Toast(document.querySelector('.toast'));
            toast.show();
        }
    });


    // Use PHP to pass the doctor count to JavaScript
    const doctorsCount = <?= $doctorsCount ?>;

    if (doctorsCount === 0) {
        document.getElementById('no-doctors').style.display = 'block';
    } else {
        document.getElementById('no-doctors').style.display = 'none';
    }
</script>

<?php include 'includes/footer.php'; ?>
