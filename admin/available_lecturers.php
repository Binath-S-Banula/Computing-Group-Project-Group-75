<?php include 'includes/header.php'; ?>

<?php
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


// Get values from GET parameters
$selectedDay = $_GET['day'] ?? '';
$start = $_GET['start_time'] ?? '';
$end = $_GET['end_time'] ?? '';
$proposalId = $_GET['proposal_id'] ?? ''; // <-- needed for assigning

if (empty($selectedDay) || empty($start) || empty($end)) {
    die('Required parameters are missing.');
}
// Handle assignment post (save lecturer)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_lecturer_id'], $_POST['proposal_id'])) {
    $assignedLecturerId = $_POST['assign_lecturer_id'];
    $proposalId = $_POST['proposal_id'];

    // Update the proposal
    $updateStmt = $pdo->prepare("UPDATE timetable_proposals SET new_lecturer = ? WHERE id = ?");
    $updateStmt->execute([$assignedLecturerId, $proposalId]);

    // Insert notification
    $notifyStmt = $pdo->prepare("INSERT INTO lecturer_notifications (lecturer_id, proposal_id) VALUES (?, ?)");
    $notifyStmt->execute([$assignedLecturerId, $proposalId]);

    header("Location: proposal_details.php?id=" . $proposalId);
    exit;
}

    // Get the current lecturer from the proposal
    $currentLecturerStmt = $pdo->prepare("SELECT lecturer_id FROM timetable_proposals WHERE id = ?");
    $currentLecturerStmt->execute([$proposalId]);
    $currentLecturerId = $currentLecturerStmt->fetchColumn();

    // Get the faculty_id of the current lecturer
    $facultyStmt = $pdo->prepare("SELECT faculty_id FROM lecturers WHERE id = ?");
    $facultyStmt->execute([$currentLecturerId]);
    $currentFacultyId = $facultyStmt->fetchColumn();

    // Load lecturers from the same faculty, excluding the current lecturer
    $stmt = $pdo->prepare("SELECT * FROM lecturers WHERE faculty_id = ? AND id != ?");
    $stmt->execute([$currentFacultyId, $currentLecturerId]);
    $lecturers = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Time Slot Definitions
$allTimeSlots = [
    "9:00 AM - 10:00 AM",
    "10:00 AM - 11:00 AM",
    "11:00 AM - 12:00 PM",
    "1:00 PM - 2:00 PM",
    "2:00 PM - 3:00 PM",
    "3:00 PM - 4:00 PM",
    "4:00 PM - 5:00 PM",
    "5:00 PM - 6:00 PM",
];

$timeSlotStartMap = [
    "9:00 AM" => "9:00 AM - 10:00 AM",
    "10:00 AM" => "10:00 AM - 11:00 AM",
    "11:00 AM" => "11:00 AM - 12:00 PM",
    "1:00 PM" => "1:00 PM - 2:00 PM",
    "2:00 PM" => "2:00 PM - 3:00 PM",
    "3:00 PM" => "3:00 PM - 4:00 PM",
    "4:00 PM" => "4:00 PM - 5:00 PM",
    "5:00 PM" => "5:00 PM - 6:00 PM",
];

$timeSlotEndMap = [
    "10:00 AM" => "9:00 AM - 10:00 AM",
    "11:00 AM" => "10:00 AM - 11:00 AM",
    "12:00 PM" => "11:00 AM - 12:00 PM",
    "2:00 PM" => "1:00 PM - 2:00 PM",
    "3:00 PM" => "2:00 PM - 3:00 PM",
    "4:00 PM" => "3:00 PM - 4:00 PM",
    "5:00 PM" => "4:00 PM - 5:00 PM",
    "6:00 PM" => "5:00 PM - 6:00 PM",
];




$availableLecturers = [];

if (!empty($selectedDay) && !empty($start) && !empty($end)) {
    if (isset($timeSlotStartMap[$start]) && isset($timeSlotEndMap[$end])) {
        $startSlot = $timeSlotStartMap[$start];
        $endSlot = $timeSlotEndMap[$end];

        $startIndex = array_search($startSlot, $allTimeSlots);
        $endIndex = array_search($endSlot, $allTimeSlots);

        if ($startIndex !== false && $endIndex !== false && $startIndex <= $endIndex) {
            $selectedSlots = array_slice($allTimeSlots, $startIndex, $endIndex - $startIndex + 1);

            foreach ($lecturers as $lecturer) {
                // inside foreach ($lecturers as $lecturer) { ... }
                $spreadsheet = getGoogleSheetAsSpreadsheet($lecturer['sheet_id']);
                $sheet = $spreadsheet->getActiveSheet();
                $data = $sheet->toArray();

                $dayColumnIndex = -1;
                foreach ($data[0] as $i => $val) {
                    if (strtolower(trim($val)) === strtolower($selectedDay)) {
                        $dayColumnIndex = $i;
                        break;
                    }
                }
                if ($dayColumnIndex === -1) continue;

                $mergedCells = $sheet->getMergeCells();
                // Build slot-to-row index
$slotRowMap = [];
for ($i = 1; $i < count($data); $i++) {
    $slot = trim($data[$i][0]);
    if (in_array($slot, $allTimeSlots)) {
        $slotRowMap[$slot] = $i + 1; // +1 for actual row number in Excel
    }
}

// Get row range for selected slots
$selectedRows = [];
foreach ($selectedSlots as $slot) {
    if (isset($slotRowMap[$slot])) {
        $selectedRows[] = $slotRowMap[$slot];
    }
}

// Check for overlap with merged ranges
$isAvailable = true;

foreach ($mergedCells as $range) {
    [$startCell, $endCell] = explode(':', $range);

    $startCol = Coordinate::columnIndexFromString(preg_replace('/\d+/', '', $startCell));
    $startRow = (int)preg_replace('/[^\d]/', '', $startCell);
    $endCol = Coordinate::columnIndexFromString(preg_replace('/\d+/', '', $endCell));
    $endRow = (int)preg_replace('/[^\d]/', '', $endCell);

    // Only consider this merged cell if it's in the selected day column
    if ($startCol !== $dayColumnIndex + 1) continue;

    $value = $sheet->getCell($startCell)->getValue();
    if (trim($value) !== '') {
        foreach ($selectedRows as $row) {
            if ($row >= $startRow && $row <= $endRow) {
                $isAvailable = false;
                break 2; // Exit both loops early
            }
        }
    }
}

// Check non-merged cells in selected rows
foreach ($selectedRows as $rowNum) {
    $colNum = $dayColumnIndex + 1;
    $cellCoord = Coordinate::stringFromColumnIndex($colNum) . $rowNum;
    $cellValue = $sheet->getCell($cellCoord)->getValue();

    if (trim($cellValue) !== '') {
        $isAvailable = false;
        break;
    }
}


                if ($isAvailable) {
                    $availableLecturers[] = $lecturer;
                }

            }
        }
    }
}

function getGoogleSheetAsSpreadsheet($sheetId) {
    $csvUrl = "https://docs.google.com/spreadsheets/d/$sheetId/export?format=csv";
    $tempFile = tempnam(sys_get_temp_dir(), 'sheet_') . '.csv';
    file_put_contents($tempFile, file_get_contents($csvUrl));
    return IOFactory::load($tempFile);
}
?>


    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Search Form Card -->
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <i class="fa fa-search me-2"></i>
                    <span>Find Available Lecturers</span>
                </div>
                <div class="card-body">
                    <form id="autoForm" method="GET">
                        <input type="hidden" name="proposal_id" value="<?= htmlspecialchars($proposalId) ?>">
                        <div class="mb-3">
                            <label for="day" class="form-label"><i class="fa fa-calendar-day me-2"></i>Day</label>
                            <select name="day" id="day" class="form-select" required>
                                <option value="">Select Day</option>
                                <?php
                                $days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];
                                foreach ($days as $day) {
                                    $selected = ($selectedDay === $day) ? "selected" : "";
                                    echo "<option value='$day' $selected>$day</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="start_time" class="form-label"><i class="fa fa-clock me-2"></i>Start Time</label>
                            <select name="start_time" id="start_time" class="form-select" required>
                                <option value="">Select Start Time</option>
                                <?php
                                foreach (array_keys($timeSlotStartMap) as $time) {
                                    $selected = ($start === $time) ? "selected" : "";
                                    echo "<option value='$time' $selected>$time</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="end_time" class="form-label"><i class="fa fa-hourglass-end me-2"></i>End Time</label>
                            <select name="end_time" id="end_time" class="form-select" required>
                                <option value="">Select End Time</option>
                                <?php
                                foreach (array_keys($timeSlotEndMap) as $time) {
                                    $selected = ($end === $time) ? "selected" : "";
                                    echo "<option value='$time' $selected>$time</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-filter me-2"></i>Find Available Lecturers</button>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (!empty($selectedDay) && !empty($start) && !empty($end)): ?>
                <div class="card mt-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="fa fa-list-check me-2"></i>
                        <span>Available Lecturers</span>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($availableLecturers)): ?>
                            <p class="mb-3">
                                <span class="result-count"><?= count($availableLecturers) ?></span> lecturer(s) available for 
                                <span class="badge bg-success"><?= htmlspecialchars($selectedDay) ?> <?= htmlspecialchars($start) ?> - <?= htmlspecialchars($end) ?></span>
                            </p>
                            <div class="list-group">
                                <?php foreach ($availableLecturers as $lecturer): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center lecturer-item p-3 mb-2">
                                        <div><i class="fa fa-user-tie me-2"></i><?= htmlspecialchars($lecturer['name']) ?></div>
                                        <div class="d-flex gap-2">
                                            <a href="https://docs.google.com/spreadsheets/d/<?= $lecturer['sheet_id'] ?>" target="_blank" class="btn btn-sm btn-outline-success">
                                                <i class="fa fa-external-link me-1"></i> View Schedule
                                            </a>
                                            <form method="POST" action="">
                                                <input type="hidden" name="assign_lecturer_id" value="<?= htmlspecialchars($lecturer['id']) ?>">
                                                <input type="hidden" name="proposal_id" value="<?= htmlspecialchars($proposalId) ?>">
                                                <button type="submit" class="btn btn-sm btn-primary">
                                                    <i class="fa fa-check-circle me-1"></i> Assign
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fa fa-calendar-xmark fa-3x text-danger mb-3"></i>
                                <p class="no-results">No lecturers are available at this time.</p>
                                <p class="text-muted">Try selecting a different day or time slot.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>


    <?php include 'includes/footer.php'; ?>