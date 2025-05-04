<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="css/medical_appointment.css">

<?php
if (!isset($_GET['doctor_id'])) {
    header("Location: medical_center.php");
    exit;
}

// Timezone settings
date_default_timezone_set('Asia/Colombo');
$today = date('l');


$doctorId = $_GET['doctor_id'];
$studentId = $_SESSION['student_uid']; 
$slotLength = 30 * 60; // 30 minutes
$daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
$startTime = strtotime("10:00:00");
$endTime = strtotime("16:00:00");

// Fetch doctor info
$stmt = $pdo->prepare("SELECT name, specialization FROM doctors WHERE id = ?");
$stmt->execute([$doctorId]);
$doctor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doctor) {
    echo "Doctor not found.";
    exit;
}

// Fetch all doctor slots (including booked ones)
$stmt = $pdo->prepare("SELECT id, day_of_week, slot_start, student_id FROM doctor_availability WHERE doctor_id = ?");
$stmt->execute([$doctorId]);
$availableSlots = $stmt->fetchAll(PDO::FETCH_ASSOC);

$availableMap = [];
foreach ($availableSlots as $slot) {
    $availableMap[$slot['day_of_week']][$slot['slot_start']] = [
        'id' => $slot['id'],
        'student_id' => $slot['student_id']
    ];
}


// Fetch student's booked appointments where the status is pending or approved
$stmt = $pdo->prepare("
    SELECT ma.appointment_date, 
           ma.appointment_time_range, 
           ma.reason, 
           ma.status, 
           ma.doctor_availability_id, -- ADD this!
           da.day_of_week
    FROM medical_appointments ma
    JOIN doctor_availability da ON ma.doctor_availability_id = da.id
    WHERE ma.student_id = ? AND da.doctor_id = ? AND ma.status IN ('pending', 'approved')
    ORDER BY ma.appointment_date, ma.appointment_time_range
");
$stmt->execute([$studentId, $doctorId]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

    


    <!-- Student's existing appointments -->
    <div class="mb-5">

        <div class="dashboard-title">
            <h5>Your Booked Appointments with Dr. <?= htmlspecialchars($doctor['name']) ?> (<?= htmlspecialchars($doctor['specialization']) ?>)</h5>
        </div>

        <!-- success message  -->
        <div id="message-container">
            <?php if (isset($messageText)): ?>
                <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($messageText) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>

        <!-- appointment card  -->
        <?php if (!empty($appointments)): ?>
            <div class="appointments-container">
                <?php foreach ($appointments as $appt): ?>
                    <div class="appointment-card">
                        <div class="appointment-header">
                            <div class="d-flex">
                                <!-- Displaying the appointment date -->
                                <div class="appointment-day"><?= htmlspecialchars($appt['day_of_week']) ?></div>
                                <div class="appointment-date"><?= htmlspecialchars($appt['appointment_date']) ?></div>
                                
                                <!-- Displaying the appointment time range -->
                                <div class="appointment-time"><?= htmlspecialchars($appt['appointment_time_range']) ?></div>
                            </div>
                            
                            <!-- Displaying status marker -->
                            <div class="appointment-status">
                                <?php if ($appt['status'] === 'approved'): ?>
                                    <span class="badge bg-success" style="font-weight:600; font-size:15px;">Approved</span>
                                <?php elseif ($appt['status'] === 'pending'): ?>
                                    <span class="badge bg-warning" style="font-weight:600; font-size:15px;">Pending</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="appointment-footer d-flex justify-content-between align-items-center">
                            <div class="appointment-reason mb-0">
                                <strong>Reason: </strong><span style="font-weight:600; font-size:15px;"><?= htmlspecialchars($appt['reason']) ?></span>
                            </div>
                            <!-- Cancel button -->
                            <button class="btn btn-danger btn-sm cancel-btn" style="font-weight:600; font-size:15px;" 
                                    data-slot-id="<?= htmlspecialchars($appt['doctor_availability_id']) ?>">
                                Cancel Appointment
                            </button>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-appointments">
                <i class="fas fa-calendar-times me-2"></i> You have not booked any appointments yet.
            </div>
        <?php endif; ?>

    </div>


    <!-- Appointment Slots Table -->
    <div class="table-responsive">
        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th scope="col" class="time-column">Time</th>
                    <?php
                        $today = date('l'); 
                        $todayNumber = date('N'); 
                    ?>
                    <?php foreach ($daysOfWeek as $day): ?>
                        <?php
                            $dayNumber = date('N', strtotime($day));
                            if ($dayNumber < $todayNumber) {
                                // Before today → next week's date
                                $date = date('Y-m-d', strtotime("next $day"));
                            } else {
                                // Today or after → this week's date
                                $date = date('Y-m-d', strtotime("this $day"));
                            }
                        ?>
                        <th scope="col" class="day-header <?= ($day === $today) ? 'today-header' : ''; ?>">
                            <?= $day ?><br>
                            <small class="date"><?= $date ?></small>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>

            <tbody>
                <?php
                for ($time = $startTime; $time < $endTime; $time += $slotLength):
                    $slotTime = date("H:i", $time);
                ?>
                    <tr>
                        <td class="fw-bold"><?= $slotTime ?></td>
                        <?php foreach ($daysOfWeek as $day):
                            $timeStr = date("H:i:s", $time);
                            // Check if the slot exists and is available or booked
                            $isAvailable = isset($availableMap[$day][$timeStr]);
                            $availabilityId = $isAvailable ? $availableMap[$day][$timeStr]['id'] : null;
                            $bookedStudentId = $isAvailable ? $availableMap[$day][$timeStr]['student_id'] : null;
                            $isBooked = $isAvailable && $bookedStudentId != null;
                            $isUnavailable = !$isAvailable;
                            $isBookedByCurrentStudent = $isBooked && $bookedStudentId == $studentId;                            
                        ?>
                            <td 
                                class="slot-cell 
                                    <?= $isUnavailable ? 'slot-unavailable' : 
                                        ($isBooked ? ($isBookedByCurrentStudent ? 'slot-booked-own' : 'slot-booked-other') : 'slot-available') 
                                    ?>"
                                <?php if (!$isUnavailable && !$isBooked): ?>
                                    data-bs-toggle="modal" 
                                    data-bs-target="#appointmentModal" 
                                    data-availability-id="<?= $availabilityId ?>"
                                <?php endif; ?>
                                <?= $isUnavailable || $isBooked ? 'style="cursor: not-allowed;"' : '' ?>
                            >
                                <?= $isUnavailable ? 'Unavailable' : ($isBooked ? ($isBookedByCurrentStudent ? 'Booked (You)' : 'Booked') : 'Available') ?>
                            </td>

                        <?php endforeach; ?>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>

<!-- Appointment Modal -->
<div class="modal fade" id="appointmentModal" tabindex="-1" aria-labelledby="appointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="appointmentModalLabel">Book Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="appointmentForm">
                    <input type="hidden" name="doctor_id" value="<?= $doctorId ?>">
                    <input type="hidden" name="student_id" value="<?= $_SESSION['student_uid'] ?>">
                    <input type="hidden" name="doctor_availability_id" id="modalAvailabilityId">
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason</label>
                        <textarea class="form-control" id="reason" name="reason" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Book Appointment</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Confirmation Modal -->
<div class="modal fade" id="cancelConfirmModal" tabindex="-1" aria-labelledby="cancelConfirmModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title text-white" id="cancelConfirmModalLabel">Confirm Cancellation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to cancel this appointment?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
        <button type="button" class="btn btn-danger" id="confirmCancelBtn">Yes, Cancel</button>
      </div>
    </div>
  </div>
</div>


<script>
    $('#appointmentModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var availabilityId = button.data('availability-id');

        var modal = $(this);
        modal.find('.modal-body #modalAvailabilityId').val(availabilityId);
    });
</script>


<script>
    function showMessage(message, type) {
        const container = $('#message-container');
        const alertType = type === 'success' ? 'alert-success' : 'alert-danger';
        container.html(`
            <div class="alert ${alertType} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);

        // Automatically close the message 
        setTimeout(function() {
            container.find('.alert').alert('close');
        }, 2000);
    }

    // When booking form is submitted
    $('#appointmentForm').on('submit', function(e) {
        e.preventDefault(); 

        $.ajax({
            type: 'POST',
            url: 'sections/save_appointment.php',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#appointmentModal').modal('hide');
                    localStorage.setItem('appointmentMessage', response.message);
                    localStorage.setItem('appointmentMessageType', 'success');
                    location.reload();
                } else {
                    localStorage.setItem('appointmentMessage', response.message);
                    localStorage.setItem('appointmentMessageType', 'error');
                    location.reload(); 
                }
            },
            error: function() {
                localStorage.setItem('appointmentMessage', 'Something went wrong while booking the appointment.');
                localStorage.setItem('appointmentMessageType', 'error');
                location.reload(); 
            }
        });
    });

    // Cancel button click to open confirmation modal
let cancelData = {};
$('.cancel-btn').on('click', function() {
    cancelData = {
        slotId: $(this).data('slot-id') // Store slot ID 
    };
    $('#cancelConfirmModal').modal('show');
});

// Confirm cancel inside modal
$('#confirmCancelBtn').on('click', function() {
    $.ajax({
        type: 'POST',
        url: 'sections/cancel_appointment.php',
        data: {
            doctor_id: <?= $doctorId ?>, 
            student_id: <?= $studentId ?>, 
            slot_id: cancelData.slotId 
        },
        dataType: 'json',
        success: function(response) {
            $('#cancelConfirmModal').modal('hide');
            if (response.success) {
                localStorage.setItem('appointmentMessage', response.message);
                localStorage.setItem('appointmentMessageType', 'success');
                location.reload();  
            } else {
                localStorage.setItem('appointmentMessage', response.message);
                localStorage.setItem('appointmentMessageType', 'error');
                location.reload(); 
            }
        },
        error: function() {
            $('#cancelConfirmModal').modal('hide');
            localStorage.setItem('appointmentMessage', 'Something went wrong while cancelling the appointment.');
            localStorage.setItem('appointmentMessageType', 'error');
            location.reload(); 
        }
    });
});


    // Check if there is a message 
    $(document).ready(function() {
        const message = localStorage.getItem('appointmentMessage');
        const messageType = localStorage.getItem('appointmentMessageType');

        if (message) {
            // Show the stored message
            showMessage(message, messageType);
            // Clear the message from localStorage 
            localStorage.removeItem('appointmentMessage');
            localStorage.removeItem('appointmentMessageType');
        }
    });
</script>




<?php include 'includes/footer.php'; ?>
