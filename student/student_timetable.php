<?php include 'includes/header.php'; ?>

<!-- style sheet  -->
<link rel="stylesheet" href="css/student_timetable.css">

<?php
// Fetch student's degree_id and batch_id
$sql = "
    SELECT 
        s.degree_id, s.batch_id, 
        d.degree_name, 
        b.batch_name 
    FROM students s
    JOIN degrees d ON s.degree_id = d.id
    JOIN batches b ON s.batch_id = b.id
    WHERE s.id = :id
";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $student_uid]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

$sheet_id = null;

if ($student) {
    $sql = "SELECT sheet_id FROM student_timetables WHERE degree_id = :degree_id AND batch_id = :batch_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':degree_id' => $student['degree_id'],
        ':batch_id' => $student['batch_id']
    ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $sheet_id = $result['sheet_id'] ?? null;
}
?>

        <div class="timetable-card">
            <?php if ($student): ?>
                <div class="info-header">
                    <h5><strong>Degree:</strong> <?= htmlspecialchars($student['degree_name']) ?></h5>
                    <h5><strong>Batch:</strong> <?= htmlspecialchars($student['batch_name']) ?></h5>
                </div>
            <?php endif; ?>

            <div class="timetable-container">
                <?php if ($sheet_id): ?>
                    <div class="timetable-frame">
                        <div class="ratio ratio-16x9 h-100">
                            <iframe 
                                src="https://docs.google.com/spreadsheets/d/<?= htmlspecialchars($sheet_id) ?>/preview"
                                allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert-custom">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>Timetable not found for your degree and batch.</div>
                    </div>
                <?php endif; ?>
            </div>


        </div>


 <?php include 'includes/footer.php'; ?>