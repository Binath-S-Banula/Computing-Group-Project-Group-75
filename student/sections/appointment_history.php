<?php
$studentId = $_SESSION['student_uid'];

try {
    // Fetch the appointment history 
    $stmt = $pdo->prepare("
    SELECT 
        ma.id, 
        ma.reason, 
        ma.status, 
        ma.appointment_date, 
        ma.appointment_time_range, 
        d.name AS doctor_name, 
        d.specialization AS doctor_specialization,
        d.id AS doctor_id
    FROM medical_appointments ma
    JOIN doctors d ON ma.doctor_id = d.id
    WHERE ma.student_id = ?
    ORDER BY ma.id DESC
");

    $stmt->execute([$studentId]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['message'] = ['text' => 'An error occurred while fetching appointment history: ' . $e->getMessage(), 'type' => 'error'];
    header("Location: error_page.php");
    exit;
}
?>

<link rel="stylesheet" href="css/appointment_history.css">

    <div class="card appointment-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="card-title mobile-header">
                <i class="fas fa-calendar-check"></i> 
                Appointment History
            </h2>
        </div>
        <div class="card-body">
            <?php if (!empty($appointments)): ?>
            <div class="table-responsive">
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Doctor</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Appointment Date & Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appt): ?>
                            <tr>
                                <td><?= htmlspecialchars($appt['id']) ?></td>
                                <td>
                                    <div class="doctor-info">
                                        <div>
                                            <div class="doc-name"><?= htmlspecialchars($appt['doctor_name']) ?></div>
                                            <div class="doctor-specialty"><?= htmlspecialchars($appt['doctor_specialization']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($appt['reason']) ?></td>
                                <td>
                                    <?php
                                    switch ($appt['status']) {
                                        case 'pending':
                                            echo '<span class="badge badge-pending">Pending</span>';
                                            break;
                                        case 'approved':
                                            echo '<span class="badge badge-approved">Approved</span>';
                                            break;
                                        case 'rejected':
                                            echo '<span class="badge badge-rejected">Rejected</span>';
                                            break;
                                        case 'cancelled':
                                            echo '<span class="badge badge-cancelled">Cancelled</span>';
                                            break;
                                        case 'disabled':
                                            echo '<span class="badge badge-disabled">Disabled</span>';
                                            break;
                                        default:
                                            echo '<span class="badge badge-secondary">Unknown</span>';
                                            break;
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars(date('M d, Y', strtotime($appt['appointment_date']))) ?> 
                                    - <?= htmlspecialchars($appt['appointment_time_range']) ?>
                                </td>

                                <td>
                                    <?php if ($appt['status'] === 'pending' || $appt['status'] === 'approved'): ?>
                                        <a href="medical_appointment.php?doctor_id=<?= htmlspecialchars($appt['doctor_id']) ?>" class="btn btn-action btn-view">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    <?php else: ?>
                                        <span class="action-not-needed">No action needed</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="far fa-calendar-times"></i>
                    </div>
                    <p class="empty-state-text">You have no past appointments.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

