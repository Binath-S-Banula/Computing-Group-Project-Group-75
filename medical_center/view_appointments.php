<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="css/manage_doctors.css">

<?php
// Handle approve/reject form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status_action'])) {
    $appointmentId = $_POST['appointment_id'];
    $action = $_POST['status_action']; 

    $newStatus = ($action === 'approve') ? 'approved' : 'rejected';

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Get doctor_availability_id first
        $stmt = $pdo->prepare("SELECT doctor_availability_id FROM medical_appointments WHERE id = ?");
        $stmt->execute([$appointmentId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $availabilityId = $result['doctor_availability_id'];

            // Update appointment status
            $stmt = $pdo->prepare("UPDATE medical_appointments SET status = ? WHERE id = ?");
            $stmt->execute([$newStatus, $appointmentId]);

            // If rejected, also clear student_id in doctor_availability
            if ($newStatus === 'rejected') {
                $stmt = $pdo->prepare("UPDATE doctor_availability SET student_id = NULL WHERE id = ?");
                $stmt->execute([$availabilityId]);
            }

            $pdo->commit();
            $_SESSION['message'] = ['text' => "Appointment #$appointmentId has been $newStatus.", 'type' => 'success'];
        } else {
            $_SESSION['message'] = ['text' => "Appointment not found.", 'type' => 'error'];
        }

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['message'] = ['text' => 'Failed to update appointment: ' . $e->getMessage(), 'type' => 'error'];
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch all medical appointments 
try {
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("
        SELECT 
            ma.id,
            ma.reason,
            ma.status,
            ma.appointment_date,
            ma.appointment_time_range,
            s.name AS student_name,
            d.name AS doctor_name,
            d.specialization AS doctor_specialization
        FROM medical_appointments ma
        JOIN students s ON ma.student_id = s.id
        JOIN doctors d ON ma.doctor_id = d.id
        ORDER BY 
            CASE 
                WHEN ma.appointment_date < :today AND ma.status IN ('approved', 'pending') THEN 1
                WHEN ma.appointment_date >= :today AND ma.status IN ('approved', 'pending') THEN 2
                WHEN ma.appointment_date < :today AND ma.status IN ('rejected', 'cancelled', 'disabled') THEN 3
                WHEN ma.appointment_date >= :today AND ma.status IN ('rejected', 'cancelled', 'disabled') THEN 4
            END,
            ma.appointment_date DESC, 
            ma.appointment_time_range ASC
    ");

    $stmt->execute(['today' => $today]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['message'] = ['text' => 'Failed to load appointments: ' . $e->getMessage(), 'type' => 'error'];
    header("Location: error_page.php");
    exit;
}

?>

<div class="dashboard-title d-flex justify-content-between align-items-center mb-4">
    <h2 class="card-title">
        <i class="fas fa-calendar-check"></i> All Appointments
    </h2>
    <button id="updateBtn" style="z-index:100;" class="btn btn-primary add-btn">Update Expired Appointments</button>
</div>

<!-- expired appointments update message -->
<div id="result" class="mt-4"></div>

<!-- approve reject message  -->
<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?= htmlspecialchars($_SESSION['message']['type']) ?> alert-dismissible fade show" role="alert" id="message-alert">
        <?= htmlspecialchars($_SESSION['message']['text']) ?>
    </div>
    <?php unset($_SESSION['message']); ?>
    <script>
        setTimeout(function() {
            var alert = document.getElementById('message-alert');
            if (alert) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(function() {
                    alert.remove();
                }, 500); 
            }
        }, 2000);
    </script>
<?php endif; ?>


<?php if (!empty($appointments)): ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Doctor</th>
                    <th>Date & Time</th>
                    <th>Reason</th>
                    <th>Status / Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appt): ?>
                    <tr id="row-<?= $appt['id'] ?>">
                        <td><?= htmlspecialchars($appt['id']) ?></td>
                        <td><span class="student-name"><?= htmlspecialchars($appt['student_name']) ?></span></td>
                        <td><span class="doctor-name"><?= htmlspecialchars($appt['doctor_name']) ?></span></td>
                        <td>
                            <div class="appointment-date"><?= date('M d, Y', strtotime($appt['appointment_date'])) ?></div>
                            <span class="appointment-time"><?= htmlspecialchars($appt['appointment_time_range']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($appt['reason']) ?></td>
                        <td>
                            <?php if ($appt['status'] === 'pending'): ?>
                                <form method="POST" class="d-flex gap-2">
                                    <input type="hidden" name="appointment_id" value="<?= $appt['id'] ?>">
                                    <button type="submit" name="status_action" value="approve" class="btn-success approve-btn">
                                        <i class="fas fa-check-circle"></i> Approve
                                    </button>
                                    <button type="submit" name="status_action" value="reject" class="btn-danger reject-btn">
                                        <i class="fas fa-times-circle"></i> Reject
                                    </button>
                                </form>
                            <?php else: ?>
                                <?php
                                switch ($appt['status']) {
                                    case 'approved':
                                        echo '<span class="badge badge-success">Approved</span>';
                                        break;
                                    case 'rejected':
                                        echo '<span class="badge badge-danger">Rejected</span>';
                                        break;
                                    case 'cancelled':
                                        echo '<span class="badge badge-warning">Cancelled</span>';
                                        break;
                                    case 'disabled':
                                        echo '<span class="badge badge-dark">Disabled</span>';
                                        break;
                                    default:
                                        echo '<span class=" badge badge info">Unknown</span>';
                                        break;
                                }
                                ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="btn-div">
            <a href="current_appointments.php" class="view-back">
                <i class="fas fa-arrow-left"></i> Active Appointments
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="empty-state">
        <i class="fas fa-calendar-xmark"></i>
        <h4>No Appointments Found</h4>
        <p>There are currently no active appointments in the system.</p>
    </div>
<?php endif; ?>

<!-- update expired appointments -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    document.getElementById('updateBtn').addEventListener('click', function() {
        fetch('sections/auto_update_appointments.php', {
            method: 'GET'
        })
        .then(response => response.text())
        .then(data => {
            const resultDiv = document.getElementById('result');
            resultDiv.innerHTML = `<div class="alert alert-success">${data}</div>`; 

            setTimeout(() => {
                resultDiv.innerHTML = '';
            }, 3000);
        })
        .catch(error => {
            const resultDiv = document.getElementById('result');
            resultDiv.innerHTML = `<div class="alert alert-danger">Fetch error: ${error}</div>`;

            setTimeout(() => {
                resultDiv.innerHTML = '';
            }, 3000);
        });
    });
</script>


<?php include 'includes/footer.php'; ?>
