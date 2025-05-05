<?php
session_start();

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
require_once '../db_connection.php';

//  student_id is set 
if (!isset($_SESSION['student_uid'])) {
    die("Please log in first.");
}

$student_id = $_SESSION['student_uid'];

// Get degree_id and batch_id for the student from the database
$query = "SELECT degree_id, batch_id FROM students WHERE id = :student_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Student not found.");
}

$degree_id = $student['degree_id'];
$batch_id = $student['batch_id'];

// Get the sheet_id from student_timetables based on the degree and batch
$query = "SELECT sheet_id FROM student_timetables WHERE degree_id = :degree_id AND batch_id = :batch_id LIMIT 1";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':degree_id', $degree_id, PDO::PARAM_INT);
$stmt->bindParam(':batch_id', $batch_id, PDO::PARAM_INT);
$stmt->execute();
$timetable = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$timetable) {
    die("No timetable found for this student.");
}

$sheet_id = $timetable['sheet_id'];

if (!$sheet_id) {
    die("No sheet ID found for student ID: $student_id");
}

$url = "https://docs.google.com/spreadsheets/d/$sheet_id/export?format=xlsx";
$tempFile = tempnam(sys_get_temp_dir(), 'student_schedule');
file_put_contents($tempFile, file_get_contents($url));
$spreadsheet = IOFactory::load($tempFile);
$worksheet = $spreadsheet->getActiveSheet();

$test_mode = true;

if ($test_mode) {
    $today = "Monday"; // Test whatever day you want
} else {
    $today = date('l');
}

// Get column for today's day
$dayColumn = null;
foreach ($worksheet->getRowIterator(1)->current()->getCellIterator() as $cell) {
    if (trim($cell->getValue()) === $today) {
        $dayColumn = $cell->getColumn();
        break;
    }
}
if (!$dayColumn) die("Couldn't find column for $today");

$mergedCells = $worksheet->getMergeCells();
$mergedMap = [];
foreach ($mergedCells as $range) {
    [$start, $end] = explode(":", $range);
    [$startCol, $startRow] = Coordinate::coordinateFromString($start);
    [$endCol, $endRow] = Coordinate::coordinateFromString($end);
    if ($startCol == $dayColumn) {
        $rowSpan = $endRow - $startRow + 1;
        $mergedMap[$startRow] = ['value' => $worksheet->getCell($start)->getValue(), 'rowspan' => $rowSpan];
        for ($r = $startRow + 1; $r <= $endRow; $r++) {
            $mergedMap[$r] = 'skip';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="schedule-wrapper">
                    <div class="schedule-header">
                        <h2>Student Schedule</h2>
                        <span class="today-badge"><?php echo $today; ?></span>
                    </div>
                    
                    <div class="card schedule-card">
                        <div class="schedule-table-container">
                            <table class="schedule-table">
                                <thead>
                                    <tr>
                                        <th width="30%">Time</th>
                                        <th width="70%">Schedule</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $hasClass = false;
                                    foreach ($worksheet->getRowIterator(2) as $row) {
                                        $rowIndex = $row->getRowIndex();
                                        $time = $worksheet->getCell("A$rowIndex")->getValue();
                                        if (!$time) continue;
                                        echo "<tr><td class='time-cell'>$time</td>";
                                        if (isset($mergedMap[$rowIndex])) {
                                            if ($mergedMap[$rowIndex] !== 'skip') {
                                                $val = $mergedMap[$rowIndex]['value'];
                                                echo "<td class='class-cell' rowspan='{$mergedMap[$rowIndex]['rowspan']}'>$val</td>";
                                                $hasClass = true;
                                            }
                                        } else {
                                            $val = $worksheet->getCell("$dayColumn$rowIndex")->getValue();
                                            if ($val) {
                                                echo "<td class='class-cell'>$val</td>";
                                                $hasClass = true;
                                            } else {
                                                echo "<td class='empty-cell'>Free</td>";
                                            }
                                        }
                                        echo "</tr>";
                                    }
                                    if (!$hasClass) {
                                        echo "<tr><td colspan='2' class='no-class'>No classes scheduled for today</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
