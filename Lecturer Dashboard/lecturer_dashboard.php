<?php
include_once __DIR__ . '/../assets/db_connect.php';
session_start();

if (!isset($_SESSION["lecturer_id"])) {
    header("Location: login.html");
    exit();
}

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

$lecturer_id = $_SESSION["lecturer_id"];
$timetable_file = "../lecturer_timetables/lecturer{$lecturer_id}.xlsx";

if (!file_exists($timetable_file)) {
    die("<p>No timetable found for this lecturer.</p>");
}

$spreadsheet = IOFactory::load($timetable_file);
$worksheet = $spreadsheet->getActiveSheet();
// $today = date('l');
$today = "Monday"; // Manually set a test day
$dayColumn = null;

foreach ($worksheet->getRowIterator(1)->current()->getCellIterator() as $cell) {
    if (trim($cell->getValue()) === $today) {
        $dayColumn = $cell->getColumn();
        break;
    }
}

if (!$dayColumn) {
    die("<p class='alert alert-danger'>Error: Could not find column for $today.</p>");
}

$mergedCells = $worksheet->getMergeCells();
$mergedMap = [];
foreach ($mergedCells as $range) {
    [$startCell, $endCell] = explode(":", $range);
    [$startCol, $startRow] = Coordinate::coordinateFromString($startCell);
    [$endCol, $endRow] = Coordinate::coordinateFromString($endCell);
    if ($startCol == $dayColumn) {
        $rowSpan = $endRow - $startRow + 1;
        $mergedMap[$startRow] = ['value' => $worksheet->getCell($startCell)->getValue(), 'rowspan' => $rowSpan];
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
    <title>Lecturer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">Lecturer Timetable</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../login_signup/lecturer_logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card mb-4">
            <div class="card-header">Today's Schedule (<?php echo $today; ?>)</div>
            <div class="card-body p-0">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th><?php echo $today; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $hasSchedule = false;
                        foreach ($worksheet->getRowIterator(2) as $row) {
                            $rowIndex = $row->getRowIndex();
                            $time = $worksheet->getCell("A$rowIndex")->getValue();
                            if (empty($time)) continue;
                            echo "<tr><td>$time</td>";
                            if (isset($mergedMap[$rowIndex])) {
                                if ($mergedMap[$rowIndex] !== 'skip') {
                                    echo "<td rowspan='{$mergedMap[$rowIndex]['rowspan']}'>{$mergedMap[$rowIndex]['value']}</td>";
                                    $hasSchedule = true;
                                }
                            } else {
                                $subject = $worksheet->getCell("$dayColumn$rowIndex")->getValue();
                                echo "<td>$subject</td>";
                                $hasSchedule = true;
                            }
                            echo "</tr>";
                        }
                        if (!$hasSchedule) {
                            echo "<tr><td colspan='2' class='text-center'>No classes scheduled for today.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Weekly Timetable</div>
            <div class="card-body p-0">
                <table class="table table-bordered">
                    <?php
                    foreach ($worksheet->getRowIterator() as $row) {
                        echo "<tr>";
                        foreach ($row->getCellIterator() as $cell) {
                            $value = $cell->getValue() ?: "&nbsp;";
                            echo "<td>$value</td>";
                        }
                        echo "</tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>