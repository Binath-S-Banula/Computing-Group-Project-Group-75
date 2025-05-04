<div class="section-card">
    
    <?php
    // Get real lecturer info using the auto-increment lecturer ID from proposal
    $lecturerInfo = null;
    if (!empty($proposal['lecturer_id'])) {
        $stmt = $pdo->prepare("SELECT lecturer_id, name FROM lecturers WHERE id = ?");
        $stmt->execute([$proposal['lecturer_id']]);
        $lecturerInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    ?>

    <div class="page-header-timetable">
        <h1 class="page-title">Proposal #<?= htmlspecialchars($proposal['id']) ?></h1>
        <?php if ($lecturerInfo): ?>
            <span class="lecturer-badge">
                Lecturer <?= htmlspecialchars($lecturerInfo['lecturer_id']) ?> - <?= htmlspecialchars($lecturerInfo['name']) ?>
            </span>
        <?php else: ?>
            <span class="lecturer-badge text-danger">Lecturer not found</span>
        <?php endif; ?>
    </div>



    <div class="timetable-container">
        <iframe src="https://docs.google.com/spreadsheets/d/<?= $proposal['sheet_id'] ?>/edit?rm=minimal" allowfullscreen></iframe>
    </div>

</div>