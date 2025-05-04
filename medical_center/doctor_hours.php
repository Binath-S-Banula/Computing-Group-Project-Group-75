<?php include 'includes/header.php'; ?>

<!-- styles -->
<link rel="stylesheet" href="css/doctor_hours.css">

<?php
$doctorId = $_GET['doctor_id'] ?? null;
if (!$doctorId) {
    echo "<div class='alert alert-danger'>Doctor ID not provided.</div>";
    exit;
}

// Timezone settings
date_default_timezone_set('Asia/Colombo');
$today = date('l');
// $today = 'Wednesday'; 

$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
$startTime = strtotime('10:00');
$endTime = strtotime('16:00');
$slotLength = 30 * 60; // 30 minutes

// Fetch doctor name
$doctorName = "";
try {
    $stmtName = $pdo->prepare("SELECT name FROM doctors WHERE id = ?");
    $stmtName->execute([$doctorId]);
    $doctorData = $stmtName->fetch(PDO::FETCH_ASSOC);
    $doctorName = $doctorData['name'] ?? "Doctor #$doctorId";
} catch (PDOException $e) {
    $doctorName = "Doctor #$doctorId";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedSlots = $_POST['slots'] ?? [];

    // Get all currently saved slots for the doctor
    $stmt = $pdo->prepare("SELECT day_of_week, slot_start FROM doctor_availability WHERE doctor_id = ?");
    $stmt->execute([$doctorId]);
    $existingSlots = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format existing slots for easier comparison
    $existingSlotsFormatted = [];
    foreach ($existingSlots as $slot) {
        $existingSlotsFormatted[] = $slot['day_of_week'] . '|' . $slot['slot_start'];
    }

    // Fetch slots with student_id assigned (booked slots)
    $stmt = $pdo->prepare("SELECT day_of_week, slot_start, student_id FROM doctor_availability WHERE doctor_id = ? AND student_id IS NOT NULL");
    $stmt->execute([$doctorId]);
    $bookedSlots = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare a list of booked slots for easy access
    $bookedSlotsFormatted = [];
    $bookedSlotsData = []; // to map slot => student_id
    foreach ($bookedSlots as $slot) {
        $key = $slot['day_of_week'] . '|' . $slot['slot_start'];
        $bookedSlotsFormatted[] = $key;
        $bookedSlotsData[$key] = $slot['student_id'];
    }

    // Handle additions (only for unbooked slots)
    foreach ($selectedSlots as $slot) {
        // Add slot if it's not already in the database
        if (!in_array($slot, $existingSlotsFormatted)) {
            [$day, $start] = explode('|', $slot);
            $slotEnd = date("H:i:s", strtotime($start) + $slotLength);
            $slotDate = date('Y-m-d', strtotime("this $day"));  // Calculate slot date
            
            // Insert new availability slot with slot_date
            $stmt = $pdo->prepare("INSERT INTO doctor_availability (doctor_id, day_of_week, slot_start, slot_end) VALUES (?, ?, ?, ?)");
            $stmt->execute([$doctorId, $day, $start, $slotEnd]);
        }
    }

    // Handle deletions (both unbooked and booked slots)
    foreach ($existingSlotsFormatted as $slot) {
        if (!in_array($slot, $selectedSlots)) {
            [$day, $start] = explode('|', $slot);

            // Get the availability row
            $stmt = $pdo->prepare("SELECT id, student_id FROM doctor_availability WHERE doctor_id = ? AND day_of_week = ? AND slot_start = ?");
            $stmt->execute([$doctorId, $day, $start]);
            $availability = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($availability) {
                $availabilityId = $availability['id'];

                // If a student had booked it, update their medical appointment status
                if (!empty($availability['student_id'])) {
                    $stmt = $pdo->prepare("UPDATE medical_appointments SET status = 'disabled' WHERE doctor_availability_id = ?");
                    $stmt->execute([$availabilityId]);
                }

                // Delete the availability slot
                $stmt = $pdo->prepare("DELETE FROM doctor_availability WHERE id = ?");
                $stmt->execute([$availabilityId]);
            }
        }
    }

    // Redirect to prevent resubmission
    header("Location: " . $_SERVER['PHP_SELF'] . "?doctor_id=$doctorId&saved=1");
    exit;
}

// Fetch existing availability (slots without student_id)
$stmt = $pdo->prepare("SELECT day_of_week, slot_start FROM doctor_availability WHERE doctor_id = ? AND student_id IS NULL");
$stmt->execute([$doctorId]);
$currentAvailability = $stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_COLUMN);

// Fetch slots with student_id assigned (booked slots)
$stmt = $pdo->prepare("SELECT day_of_week, slot_start, student_id FROM doctor_availability WHERE doctor_id = ? AND student_id IS NOT NULL");
$stmt->execute([$doctorId]);
$bookedSlots = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare a list of booked slots for easy access
$bookedSlotsFormatted = [];
foreach ($bookedSlots as $slot) {
    $bookedSlotsFormatted[] = $slot['day_of_week'] . '|' . $slot['slot_start'];
}
?>



    
    <div class="dashboard-title">
        <div class="header-container d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1"><i class="bi bi-person-badge"></i> Dr. <?= htmlspecialchars($doctorName) ?></h3>
                <p class="doctor-info mb-0">
                     Availability Schedule
                </p>
            </div>
            <a href="manage_doctors.php" class="btn back-btn btn-outline-secondary d-flex align-items-center">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>


    <div class="legend">
        <div class="legend-item">
            <div class="legend-color" style="background-color: var(--white); border: 1px solid var(--neutral-medium);"></div>
            <span>Unavailable</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background-color: var(--accent-teal);"></div>
            <span>Available</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background-color: var(--accent-light);"></div>
            <span>Hover State</span>
        </div>
    </div>

    <!-- availability table  -->
    <form method="POST" id="availabilityForm" >
        <div class="schedule-table">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col" class="time-column">Time</th>
                        <?php foreach ($days as $day): ?>
                            <?php
                                $todayNumber = date('N'); // 1=Monday, 7=Sunday
                                $dayNumber = date('N', strtotime($day));

                                if ($dayNumber < $todayNumber) {
                                    // Before today → next week's date
                                    $date = date('Y-m-d', strtotime("next $day"));
                                } else {
                                    // Today or after → this week's date
                                    $date = date('Y-m-d', strtotime("this $day"));
                                }
                            ?>
                            <th scope="col" class="day-header <?php echo ($day === $today) ? 'today-header' : ''; ?>">
                                <?= $day ?><br>
                                <small class="date"><?= $date ?></small>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>

                    <tbody>
                        <?php for ($time = $startTime; $time < $endTime; $time += $slotLength): ?>
                            <?php
                                $slotTime = date("H:i", $time);
                                $slotLabel = date("h:i A", $time);
                                $hour = (int)date("H", $time);
                                $isMorning = $hour < 12;
                                $isLunch = $hour >= 12 && $hour < 13;
                                $rowClass = $isLunch ? 'lunch-row' : '';
                            ?>
                            <tr class="<?= $rowClass ?>">
                                <td class="time-column">
                                    <?= $slotLabel ?>
                                    <div class="time-markers">
                                        <?php if ($slotTime === '10:00'): ?>
                                            <span class="morning-marker">Morning</span>
                                        <?php elseif ($slotTime === '13:00'): ?>
                                            <span class="afternoon-marker">Afternoon</span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <?php foreach ($days as $day): ?>
                                    <?php
                                        $value = "$day|$slotTime:00";
                                        $isBooked = in_array($value, $bookedSlotsFormatted);
                                        $isChecked = in_array("$slotTime:00", $currentAvailability[$day] ?? []);
                                    ?>
                                    <td class="slot-cell <?= $isChecked ? 'active' : '' ?> <?= $isBooked ? 'booked' : '' ?>" data-value="<?= $value ?>">
                                        <?= $slotLabel ?>
                                        <?php if ($isBooked): ?>
                                            <!-- Display 'Booked' message and disable the checkbox -->
                                            <div class="">
                                                <span class="booked-marker">Booked</span>
                                            </div>
                                            <input type="checkbox" name="slots[]" value="<?= $value ?>" checked>
                                        <?php else: ?>
                                            <!-- Display checkbox for available slots -->
                                            <input type="checkbox" name="slots[]" value="<?= $value ?>" <?= $isChecked ? 'checked' : '' ?>>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endfor; ?>
                    </tbody>

                </table>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <button type="button" id="clearAll" class="btn btn-secondary">
                <i class="bi bi-x-circle"></i> Clear All
            </button>
            <div>
                <button type="button" id="quickSelect" class="btn btn-secondary me-2">
                    <i class="bi bi-lightning"></i> Quick Templates
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2-circle"></i> Save Availability
                </button>
            </div>
        </div>

    </form>

    <!-- Quick Select Modal -->
    <div class="modal fade" id="quickSelectModal" tabindex="-1" aria-labelledby="quickSelectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: var(--primary-light);">
                    <h5 class="modal-title" id="quickSelectModalLabel">Quick Schedule Templates</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="list-group">
                        <button type="button" class="list-group-item list-group-item-action" data-template="morning">
                            <strong>Morning Hours</strong> - 8:00 AM to 12:00 PM, Monday-Friday
                        </button>
                        <button type="button" class="list-group-item list-group-item-action" data-template="afternoon">
                            <strong>Afternoon Hours</strong> - 1:00 PM to 5:00 PM, Monday-Friday
                        </button>
                        <button type="button" class="list-group-item list-group-item-action" data-template="mon-wed-fri">
                            <strong>Mon-Wed-Fri</strong> - Full days (8:00 AM to 5:00 PM)
                        </button>
                        <button type="button" class="list-group-item list-group-item-action" data-template="tue-thu">
                            <strong>Tue-Thu</strong> - Full days (8:00 AM to 5:00 PM)
                        </button>
                        <button type="button" class="list-group-item list-group-item-action" data-template="weekends">
                            <strong>Core Hours</strong> - 10:00 AM to 3:00 PM, Monday-Friday
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>



<!-- save success message  -->
<?php if (!empty($_GET['saved'])): ?>
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div class="toast align-items-center text-white bg-success border-0 show">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-check-circle-fill me-2"></i>
                Availability updated successfully!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<?php endif; ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
   document.addEventListener('DOMContentLoaded', function() {
    // Keep track of the original saved state from the database
    const initialSavedState = {};

    // Loop through all the slots and mark them as saved based on the data from the database
    document.querySelectorAll('.slot-cell').forEach(cell => {
        const checkbox = cell.querySelector('input[type="checkbox"]');
        const value = cell.getAttribute('data-value');
        
        // Track the initially saved state
        initialSavedState[value] = checkbox.checked;
        if (checkbox.checked) {
            cell.classList.add('active');
        }
    });

    // Handle slot selection
    document.querySelectorAll('.slot-cell').forEach(cell => {
        cell.addEventListener('click', () => {
            const checkbox = cell.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;
            cell.classList.toggle('active');
        });
    });

    // Clear all button functionality
    document.getElementById('clearAll').addEventListener('click', function() {
        if (confirm('Are you sure you want to reset your selections?')) {
            document.querySelectorAll('.slot-cell').forEach(cell => {
                const checkbox = cell.querySelector('input[type="checkbox"]');
                const value = cell.getAttribute('data-value');
                
                if (!initialSavedState[value]) { 
                    checkbox.checked = false;
                    cell.classList.remove('active');
                }
            });
        }
    });

        // Quick select modal
        const quickSelectBtn = document.getElementById('quickSelect');
        const quickSelectModal = new bootstrap.Modal(document.getElementById('quickSelectModal'));
        
        quickSelectBtn.addEventListener('click', function() {
            quickSelectModal.show();
        });

        // Handle quick template selections
        document.querySelectorAll('[data-template]').forEach(template => {
            template.addEventListener('click', function() {
                const templateType = this.getAttribute('data-template');
                applyTemplate(templateType);
                quickSelectModal.hide();
            });
        });

        function applyTemplate(templateType) {
            // First clear all
            document.querySelectorAll('.slot-cell').forEach(cell => {
                const checkbox = cell.querySelector('input[type="checkbox"]');
                checkbox.checked = false;
                cell.classList.remove('active');
            });

            const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
            
            // Apply the selected template
            document.querySelectorAll('.slot-cell').forEach(cell => {
                const value = cell.getAttribute('data-value');
                const [day, time] = value.split('|');
                const hour = parseInt(time.split(':')[0]);
                
                switch(templateType) {
                    case 'morning':
                        if (hour >= 8 && hour < 12) {
                            markAsSelected(cell);
                        }
                        break;
                    case 'afternoon':
                        if (hour >= 13 && hour < 17) {
                            markAsSelected(cell);
                        }
                        break;
                    case 'mon-wed-fri':
                        if ((day === 'Monday' || day === 'Wednesday' || day === 'Friday') && hour >= 8 && hour < 17) {
                            markAsSelected(cell);
                        }
                        break;
                    case 'tue-thu':
                        if ((day === 'Tuesday' || day === 'Thursday') && hour >= 8 && hour < 17) {
                            markAsSelected(cell);
                        }
                        break;
                    case 'weekends':
                        if (hour >= 10 && hour < 15) {
                            markAsSelected(cell);
                        }
                        break;
                }
            });
        }

        function markAsSelected(cell) {
            const checkbox = cell.querySelector('input[type="checkbox"]');
            checkbox.checked = true;
            cell.classList.add('active');
        }
    });
</script>


<!-- show success message  -->
<script>
    if (window.location.search.includes('saved=1')) {
        showToast("Data saved successfully!");

        setTimeout(function() {
            const url = new URL(window.location);
            url.searchParams.delete('saved');
            window.history.replaceState({}, document.title, url.pathname + url.search); 
        }, 3000); 
    }

    // Function to show toast notification
    function showToast(message) {
        const toast = document.createElement('div');
        toast.classList.add('toast');
        toast.innerText = message;
        document.body.appendChild(toast);
        setTimeout(function() {
            toast.classList.add('fade'); 
        }, 2500); 

        setTimeout(function() {
            toast.remove(); 
        }, 3000); 
    }
</script>


<?php include 'includes/footer.php'; ?>