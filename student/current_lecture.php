<?php
require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
require_once '../db_connection.php';

date_default_timezone_set('Asia/Colombo');

if (!isset($_SESSION['student_uid'])) {
    die("Please log in first.");
}

$student_id = $_SESSION['student_uid'];

// Get student's batch/degree
$query = "SELECT degree_id, batch_id FROM students WHERE id = :student_id";
$stmt = $pdo->prepare($query);
$stmt->execute([':student_id' => $student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) die("Student not found.");

$degree_id = $student['degree_id'];
$batch_id = $student['batch_id'];

// Get sheet
$query = "SELECT sheet_id FROM student_timetables WHERE degree_id = :degree_id AND batch_id = :batch_id";
$stmt = $pdo->prepare($query);
$stmt->execute([':degree_id' => $degree_id, ':batch_id' => $batch_id]);
$timetable = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$timetable) die("Timetable not found.");
$sheet_id = $timetable['sheet_id'];

// Load sheet
$url = "https://docs.google.com/spreadsheets/d/$sheet_id/export?format=xlsx";
$tempFile = tempnam(sys_get_temp_dir(), 'schedule');
file_put_contents($tempFile, file_get_contents($url));
$spreadsheet = IOFactory::load($tempFile);
$worksheet = $spreadsheet->getActiveSheet();

// Today
$today = date('l');
// $today = 'Thursday'; 

// Get current time 
// $currentTime = date('H:i');
$currentTime = '9:30';

// Find column for today
$dayCol = null;
foreach ($worksheet->getColumnIterator() as $col) {
    $colIndex = $col->getColumnIndex();
    $cellValue = $worksheet->getCell("$colIndex" . "1")->getValue();
    if (trim($cellValue) === $today) {
        $dayCol = $colIndex;
        break;
    }
}
if (!$dayCol) die("No column for $today");

// STEP 1: Collect all time slots and their formatted values
$timeSlots = [];
foreach ($worksheet->getRowIterator(2) as $row) {
    $rowIndex = $row->getRowIndex();
    $timeCell = $worksheet->getCell("A$rowIndex");
    $timeValue = $timeCell->getValue();
    
    if (!$timeValue) continue;
    
    if (preg_match('/(\d{1,2}:\d{2})\s*([APMapm]{2})?\s*-\s*(\d{1,2}:\d{2})\s*([APMapm]{2})?/', $timeValue, $matches)) {
        $startText = $matches[1] . " " . ($matches[2] ?? 'AM');
        $endText = $matches[3] . " " . ($matches[4] ?? 'AM');
        
        // Convert to 24-hour format
        $startObj = DateTime::createFromFormat('g:i A', strtoupper($startText));
        $endObj = DateTime::createFromFormat('g:i A', strtoupper($endText));
        
        if ($startObj && $endObj) {
            $timeSlots[$rowIndex] = [
                'start24' => $startObj->format('H:i'),
                'end24' => $endObj->format('H:i'),
                'startFormatted' => $startObj->format('g:i A'),
                'endFormatted' => $endObj->format('g:i A'),
                'rawText' => $timeValue
            ];
        }
    }
}

// STEP 2: Find all merged cells in the day column and their time spans
$lectures = [];
$mergedRanges = $worksheet->getMergeCells();

foreach ($mergedRanges as $mergedRange) {
    [$start, $end] = explode(':', $mergedRange);
    [$startCol, $startRow] = Coordinate::coordinateFromString($start);
    [$endCol, $endRow] = Coordinate::coordinateFromString($end);
    
    // Check if this merged cell is in today's column
    if ($startCol <= $dayCol && $endCol >= $dayCol) {
        $cellValue = $worksheet->getCell($start)->getValue();
        
        if (empty(trim($cellValue))) continue;
        
        // Find all time slots covered by this merged cell
        $coveredRows = range($startRow, $endRow);
        $firstTimeSlot = null;
        $lastTimeSlot = null;
        
        foreach ($coveredRows as $row) {
            if (isset($timeSlots[$row])) {
                if ($firstTimeSlot === null) {
                    $firstTimeSlot = $timeSlots[$row];
                }
                $lastTimeSlot = $timeSlots[$row];
            }
        }
        
        // If we found valid time slots, create a lecture entry
        if ($firstTimeSlot !== null && $lastTimeSlot !== null) {
            $lines = preg_split('/\r\n|\r|\n/', trim($cellValue));
            $subject = $lines[0] ?? '';
            $hall = $lines[1] ?? '';
            
            $lectures[] = [
                'subject' => $subject,
                'hall' => $hall,
                'start24' => $firstTimeSlot['start24'],
                'end24' => $lastTimeSlot['end24'],
                'timeFormatted' => $firstTimeSlot['startFormatted'] . ' - ' . $lastTimeSlot['endFormatted']
            ];
        }
    }
}

// STEP 3: Handle non-merged cells
foreach ($worksheet->getRowIterator(2) as $row) {
    $rowIndex = $row->getRowIndex();
    
    // Check if this cell is part of a merged cell we already processed
    $isMerged = false;
    foreach ($mergedRanges as $range) {
        [$start, $end] = explode(':', $range);
        [$startCol, $startRow] = Coordinate::coordinateFromString($start);
        [$endCol, $endRow] = Coordinate::coordinateFromString($end);
        
        if ($startCol <= $dayCol && $endCol >= $dayCol && $rowIndex >= $startRow && $rowIndex <= $endRow) {
            $isMerged = true;
            break;
        }
    }
    
    // Skip if this is part of a merged cell or doesn't have a time slot
    if ($isMerged || !isset($timeSlots[$rowIndex])) {
        continue;
    }
    
    // Check the day column for this row
    $cellValue = $worksheet->getCell("$dayCol$rowIndex")->getValue();
    if (empty(trim($cellValue))) continue;
    
    $lines = preg_split('/\r\n|\r|\n/', trim($cellValue));
    $subject = $lines[0] ?? '';
    $hall = $lines[1] ?? '';
    
    $lectures[] = [
        'subject' => $subject,
        'hall' => $hall,
        'start24' => $timeSlots[$rowIndex]['start24'],
        'end24' => $timeSlots[$rowIndex]['end24'],
        'timeFormatted' => $timeSlots[$rowIndex]['startFormatted'] . ' - ' . $timeSlots[$rowIndex]['endFormatted']
    ];
}

// STEP 4: Find the current lecture
$currentLecture = null;
$currentDateTime = new DateTime($currentTime);

// Sort lectures by start time to handle any overlaps correctly
usort($lectures, function($a, $b) {
    return strcmp($a['start24'], $b['start24']);
});

foreach ($lectures as $lecture) {
    $lectureStart = new DateTime($lecture['start24']);
    $lectureEnd = new DateTime($lecture['end24']);
    
    // Check if current time is within lecture time
    // Include exact start time and exclude exact end time
    if ($currentDateTime >= $lectureStart && $currentDateTime < $lectureEnd) {
        $currentLecture = $lecture;
        break;
    }
}

$foundLecture = ($currentLecture !== null);

// For debugging
$debug = [
    'timeSlots' => $timeSlots,
    'lectures' => $lectures,
    'currentTime' => $currentTime
];
?>


        
        <?php if ($foundLecture): ?>
            <!-- Current Lecture Card -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Current Lecture ( <?php echo $today; ?> )</span>
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="lecture-card-body">
                    <div class="lecture-subject"><?php echo htmlspecialchars($currentLecture['subject']); ?></div>
                    
                    <div class="lecture-info">
                        <i class="fas fa-map-marker-alt info-icon"></i>
                        <div class="lecture-hall"><?php echo htmlspecialchars($currentLecture['hall']); ?></div>
                    </div>
                    
                    <div class="lecture-info">
                        <i class="far fa-clock info-icon"></i>
                        <div class="lecture-time"><?php echo $currentLecture['timeFormatted']; ?></div>
                    </div>
                    
                    <?php
                    // Calculate lecture progress
                    $startObj = new DateTime($currentLecture['start24']);
                    $endObj = new DateTime($currentLecture['end24']);
                    $currentObj = $currentDateTime;
                    
                    $totalDuration = $startObj->diff($endObj)->h * 60 + $startObj->diff($endObj)->i;
                    $elapsedDuration = $startObj->diff($currentObj)->h * 60 + $startObj->diff($currentObj)->i;
                    
                    $progressPercent = min(100, max(0, ($elapsedDuration / $totalDuration) * 100));
                    $remainingMinutes = $totalDuration - $elapsedDuration;
                    ?>
                    
                    <div class="progress-container">
                        <div class="progress-label">
                            <span>Lecture Progress</span>
                            <span><?php echo round($progressPercent); ?>%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: <?php echo $progressPercent; ?>%" 
                                aria-valuenow="<?php echo $progressPercent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="text-end mt-2">
                            <small class="text-muted"><?php echo $remainingMinutes; ?> minutes remaining</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php
            // Find next lecture
            $upcomingLectures = [];
            foreach ($lectures as $lecture) {
                $lectureStart = new DateTime($lecture['start24']);
                if ($lectureStart > $currentDateTime) {
                    $upcomingLectures[] = $lecture;
                }
            }

            
            if (!empty($upcomingLectures)): ?>
                <div class="next-lecture-info">
                    <div class="next-lecture-title">
                        <i class="fas fa-arrow-right me-2"></i>Upcoming Lectures
                    </div>
                    <?php foreach ($upcomingLectures as $lecture): ?>
                        <div style="padding:15px;">
                            <div class="fw-bold mt-2"><?php echo htmlspecialchars($lecture['subject']); ?></div>
                            <div class="d-flex justify-content-between">
                                <div class="text-muted"><?php echo htmlspecialchars($lecture['hall']); ?></div>
                                <div class="text-muted"><?php echo $lecture['timeFormatted']; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-muted mt-2">No more lectures scheduled for today</div>
            <?php endif; ?>

            
        <?php else: ?>
            <!-- No Lecture Card -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Class Schedule</span>
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="no-lecture-card-body">
                    <i class="fas fa-coffee no-lecture-icon"></i>
                    <div class="no-lecture">No lecture is going on right now</div>
                    
                    <?php
                    // Find next lecture
                    $nextLecture = null;
                    foreach ($lectures as $lecture) {
                        $lectureStart = new DateTime($lecture['start24']);
                        if ($lectureStart > $currentDateTime) {
                            $nextLecture = $lecture;
                            break;
                        }
                    }
                    
                    if ($nextLecture): ?>
                        <div class="next-lecture-info">
                            <div class="next-lecture-title">
                                <i class="fas fa-arrow-right me-2"></i>Next Lecture
                            </div>
                            <div class="fw-bold"><?php echo htmlspecialchars($nextLecture['subject']); ?></div>
                            <div class="d-flex justify-content-between mt-1">
                                <div class="text-muted"><?php echo htmlspecialchars($nextLecture['hall']); ?></div>
                                <div class="text-muted"><?php echo $nextLecture['timeFormatted']; ?></div>
                            </div>
                            <?php
                            // Calculate time until next lecture
                            $timeToNext = $currentDateTime->diff(new DateTime($nextLecture['start24']));
                            $hoursToNext = $timeToNext->h;
                            $minutesToNext = $timeToNext->i;
                            
                            if ($hoursToNext > 0 || $minutesToNext > 0) {
                                echo '<div class="text-end mt-2"><small class="text-muted">Starts in ';
                                if ($hoursToNext > 0) {
                                    echo $hoursToNext . ' hour' . ($hoursToNext > 1 ? 's' : '') . ' ';
                                }
                                if ($minutesToNext > 0) {
                                    echo $minutesToNext . ' minute' . ($minutesToNext > 1 ? 's' : '');
                                }
                                echo '</small></div>';
                            }
                            ?>
                        </div>
                    <?php else: ?>
                        <div class="text-muted mt-2">No more lectures scheduled for today</div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        
        
        <div class="debug-info">
            <pre><?php echo json_encode($debug, JSON_PRETTY_PRINT); ?></pre>
        </div>
