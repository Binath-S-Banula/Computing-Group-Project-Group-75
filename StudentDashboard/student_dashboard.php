<?php
include_once __DIR__ . '/../assets/db_connect.php';
session_start();

if (!isset($_SESSION["uni_id"])) {
    header("Location: login.html");
    exit();
}

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

$faculty_names = [1 => "Computing", 2 => "Business"];
$degree_names = [1 => "Software Engineering", 2 => "Networks", 3 => "Marketing", 4 => "Business Management"];

$faculty = $faculty_names[$_SESSION["faculty_id"]];
$degree = $degree_names[$_SESSION["degree_id"]];
$batch = $_SESSION["batch"];

$timetable_file = "../timetables/{$degree}_{$batch}.xlsx";

if (!file_exists($timetable_file)) {
    die("<p>No timetable found for your degree and batch.</p>");
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
    <title>Student Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-gradient-start: #1e5631;
            --primary-gradient-end: #3a8c5f;
            --secondary-gradient-start: #0d6e37;
            --secondary-gradient-end: #3fa35c;
            --hover-gradient-start: #0a552a;
            --hover-gradient-end: #2c7340;
            --text-color: #2e4a2e;
            --border-color: #bce3cc;
            --input-bg: #f5fff7;
            --shadow-color: rgba(30, 86, 49, 0.25);
            --hover-shadow-color: rgba(30, 86, 49, 0.35);
        }

        body {
            background-color: #f8f9fa;
            color: var(--text-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: linear-gradient(to right, var(--primary-gradient-start), var(--primary-gradient-end));
            box-shadow: 0 2px 10px var(--shadow-color);
        }

        .navbar-brand {
            font-weight: 600;
            color: white !important;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px var(--shadow-color);
            margin-bottom: 25px;
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(to right, var(--secondary-gradient-start), var(--secondary-gradient-end));
            color: white;
            font-weight: 600;
            border-bottom: none;
            padding: 15px 20px;
        }

        .user-info {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px var(--shadow-color);
            margin-bottom: 25px;
        }

        .user-info .badge {
            background: linear-gradient(to right, var(--secondary-gradient-start), var(--secondary-gradient-end));
            padding: 8px 12px;
            font-size: 0.85rem;
            margin-right: 8px;
        }

        .table {
            background-color: white;
        }

        .table th {
            background-color: rgba(188, 227, 204, 0.3);
            color: var(--text-color);
            font-weight: 600;
            border-bottom: 2px solid var(--border-color);
        }

        .table td,
        .table th {
            padding: 15px;
            vertical-align: middle;
        }

        .btn-primary {
            background: linear-gradient(to right, var(--primary-gradient-start), var(--primary-gradient-end));
            border: none;
            box-shadow: 0 2px 5px var(--shadow-color);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(to right, var(--hover-gradient-start), var(--hover-gradient-end));
            box-shadow: 0 4px 8px var(--hover-shadow-color);
        }

        .logout-btn {
            color: white !important;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            opacity: 0.9;
        }

        .class-item {
            background-color: rgba(188, 227, 204, 0.2);
            border-left: 4px solid var(--secondary-gradient-end);
        }

        .no-schedule {
            color: #6c757d;
            font-style: italic;
        }

        .time-cell {
            background-color: var(--input-bg);
            font-weight: 500;
        }

        .table-weekly th {
            min-width: 100px;
        }

        .table-weekly td {
            height: 60px;
        }

        @media (max-width: 768px) {
            .user-info .badge {
                display: inline-block;
                margin-bottom: 8px;
            }
        }
    </style>
</head>

<body>

    <?php include '../assets/navbar.php'; ?>


    <div class="container">
        <!-- User Info -->
        <div class="mb-4 user-info">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4><i class="fa-user-graduate fas me-2"></i>Welcome, <?php echo $_SESSION["uni_id"]; ?></h4>
                </div>
                <div class="col-md-6 text-md-end mt-2 mt-md-0">
                    <span class="badge rounded-pill"><i class="fa-university fas me-1"></i>
                        <?php echo $faculty; ?></span>
                    <span class="badge rounded-pill"><i class="fa-graduation-cap fas me-1"></i>
                        <?php echo $degree; ?></span>
                    <span class="badge rounded-pill"><i class="fa-users fas me-1"></i> Batch
                        <?php echo $batch; ?></span>
                </div>
            </div>
        </div>

        <!-- Today's Schedule -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fa-calendar-day fas me-2"></i> Today's Schedule (<?php echo $today; ?>)
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th width="30%"><i class="fa-clock far me-2"></i>Time</th>
                                <th><i class="fa-book fas me-2"></i><?php echo $today; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $hasSchedule = false;
                            foreach ($worksheet->getRowIterator(2) as $row) {
                                $rowIndex = $row->getRowIndex();
                                $time = $worksheet->getCell("A$rowIndex")->getValue();
                                if (empty($time)) continue;
                                echo "<tr>";
                                echo "<td class='time-cell'>$time</td>";
                                if (isset($mergedMap[$rowIndex])) {
                                    if ($mergedMap[$rowIndex] !== 'skip') {
                                        $subject = $mergedMap[$rowIndex]['value'];
                                        if (!empty($subject)) {
                                            echo "<td class='class-item' rowspan='{$mergedMap[$rowIndex]['rowspan']}'>$subject</td>";
                                            $hasSchedule = true;
                                        } else {
                                            echo "<td rowspan='{$mergedMap[$rowIndex]['rowspan']}'>-</td>";
                                        }
                                    }
                                } else {
                                    $subject = $worksheet->getCell("$dayColumn$rowIndex")->getValue();
                                    if (!empty($subject)) {
                                        echo "<td class='class-item'>$subject</td>";
                                        $hasSchedule = true;
                                    } else {
                                        echo "<td>-</td>";
                                    }
                                }
                                echo "</tr>";
                            }
                            if (!$hasSchedule) {
                                echo "<tr><td colspan='2' class='text-center no-schedule'><i class='fa-coffee fas me-2'></i>No classes scheduled for today. Enjoy your free time!</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Weekly Schedule -->
        <div class="card">
            <div class="card-header">
                <i class="fa-calendar-week fas me-2"></i> Weekly Timetable
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-weekly mb-0">
                        <?php
                        $mergedWeeklyMap = [];

                        // Track merged cell positions across the entire sheet
                        foreach ($mergedCells as $range) {
                            [$startCell, $endCell] = explode(":", $range);
                            [$startCol, $startRow] = Coordinate::coordinateFromString($startCell);
                            [$endCol, $endRow] = Coordinate::coordinateFromString($endCell);

                            $startColIndex = Coordinate::columnIndexFromString($startCol);
                            $endColIndex = Coordinate::columnIndexFromString($endCol);

                            $colSpan = $endColIndex - $startColIndex + 1;
                            $rowSpan = $endRow - $startRow + 1;

                            // Store the value only for the top-left cell of the merged range
                            $mergedWeeklyMap["$startRow-$startColIndex"] = [
                                'value' => $worksheet->getCell($startCell)->getValue(),
                                'colspan' => $colSpan,
                                'rowspan' => $rowSpan
                            ];

                            // Mark all other cells in the merged area as 'skip'
                            for ($r = $startRow; $r <= $endRow; $r++) {
                                for ($c = $startColIndex; $c <= $endColIndex; $c++) {
                                    if ($r !== $startRow || $c !== $startColIndex) {
                                        $mergedWeeklyMap["$r-$c"] = 'skip';
                                    }
                                }
                            }
                        }

                        foreach ($worksheet->getRowIterator() as $row) {
                            echo "<tr>";
                            $rowIndex = $row->getRowIndex();
                            $isHeaderRow = ($rowIndex == 1);
                            
                            foreach ($row->getCellIterator() as $cell) {
                                $colIndex = Coordinate::columnIndexFromString($cell->getColumn());
                                $isHeaderCol = ($cell->getColumn() == 'A');
                                $key = "$rowIndex-$colIndex";
                                $value = $cell->getValue() ?: "&nbsp;";
                                $cellClass = '';
                                
                                // Add classes for header row/column and highlight today's column
                                if ($isHeaderRow || $isHeaderCol) {
                                    $cellClass = 'table-light fw-bold';
                                }
                                
                                // Highlight today's column
                                if (!$isHeaderRow && !$isHeaderCol && $cell->getColumn() == $dayColumn) {
                                    $cellClass .= ' bg-light';
                                }
                                
                                // Add classes for cells with content
                                if (!$isHeaderRow && !$isHeaderCol && !empty($value) && $value != "&nbsp;") {
                                    $cellClass .= ' class-item';
                                }

                                // Check if the cell is part of a merged range
                                if (isset($mergedWeeklyMap[$key])) {
                                    if ($mergedWeeklyMap[$key] !== 'skip') {
                                        echo "<td class='$cellClass' rowspan='{$mergedWeeklyMap[$key]['rowspan']}' 
                                              colspan='{$mergedWeeklyMap[$key]['colspan']}'>{$mergedWeeklyMap[$key]['value']}</td>";
                                    }
                                } else {
                                    // Regular cell (not merged)
                                    echo "<td class='$cellClass'>$value</td>";
                                }
                            }
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>


        <?php include '../assets/footer.php'; ?>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>