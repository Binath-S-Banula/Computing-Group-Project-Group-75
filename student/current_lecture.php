<?php
session_start();
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
// $today = date('l');
$today = 'Thursday';

// Get current time 
// $currentTime = date('H:i');
$currentTime = '12:00';

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

<!DOCTYPE html>
<html>
<head>
    <title>Current Lecture</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f4f8;
        }
        .container {
            margin-top: 100px;
            max-width: 600px;
        }
        .card {
            text-align: center;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .lecture-subject {
            font-size: 24px;
            font-weight: bold;
        }
        .lecture-hall {
            font-size: 16px;
            color: #555;
            margin-top: 10px;
        }
        .lecture-time {
            font-size: 18px;
            color: #555;
            margin-top: 10px;
        }
        .no-lecture {
            font-size: 20px;
            color: #888;
        }
        .debug-info {
            margin-top: 20px;
            font-size: 12px;
            color: #888;
            text-align: left;
            display: none; /* Set to "block" to show debug info */
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <?php if ($foundLecture): ?>
            <div class="lecture-subject"><?php echo htmlspecialchars($currentLecture['subject']); ?></div>
            <div class="lecture-hall"><?php echo htmlspecialchars($currentLecture['hall']); ?></div>
            <div class="lecture-time"><?php echo $currentLecture['timeFormatted']; ?></div>
        <?php else: ?>
            <div class="no-lecture">No lecture is going on right now.</div>
        <?php endif; ?>
        <div class="mt-4 text-muted">Time: <?php echo $currentTime; ?> | Day: <?php echo $today; ?></div>
    </div>
    
    <div class="debug-info">
        <pre><?php echo json_encode($debug, JSON_PRETTY_PRINT); ?></pre>
    </div>
</div>
</body>
</html>