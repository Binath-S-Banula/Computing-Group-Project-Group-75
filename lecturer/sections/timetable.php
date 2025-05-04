<?php
// Get the lecturer's timetable sheet_id
$stmt = $pdo->prepare("SELECT sheet_id FROM lecturers WHERE id = ?");
$stmt->execute([$lecturer_uid]);
$sheet_id = $stmt->fetchColumn();
?>

<div class="section-card">
    <div class="page-header">
        <h1 class="page-title">Lecturer Timetable</h1>
        <span class="lecturer-badge">Lecturer ID: <?php echo $lecturer_id; ?></span>
    </div>

    <!-- Timetable iframe -->
    <div class="timetable-container">
    <?php if ($sheet_id): ?>
        <iframe src="https://docs.google.com/spreadsheets/d/<?= $sheet_id ?>/preview"></iframe>
    <?php else: ?>
        <p>No timetable found for your account.</p>
    <?php endif; ?>
    </div> 
</div>
