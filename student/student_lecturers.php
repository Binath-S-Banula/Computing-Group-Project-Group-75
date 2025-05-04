<?php include 'includes/header.php'; ?>

<!-- style sheet  -->
 <link rel="stylesheet" href="css/student_lecturers.css">


<?php
// Fetch student degree and batch
$stmt = $pdo->prepare("
    SELECT degree_id, batch_id 
    FROM students 
    WHERE id = :id
");
$stmt->execute([':id' => $student_uid]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

$lecturers = [];

if ($student) {
    $sql = "
    SELECT 
        l.id AS lecturer_id,
        l.name AS lecturer_name,
        s.name AS subject_name
    FROM subject_allocations sa
    JOIN lecturers l ON sa.lecturer_id = l.id
    JOIN subjects s ON sa.subject_id = s.id
    WHERE sa.degree_id = :degree_id AND sa.batch_id = :batch_id
";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':degree_id' => $student['degree_id'],
        ':batch_id' => $student['batch_id']
    ]);
    $lecturers = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>


        <div class="lecturers-card">
            <div class="lecturers-header">
                <h3>Your Lecturers</h3>
            </div>

            <div class="table-container">
                <?php if (!empty($lecturers)): ?>
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Subject</th>
                                    <th>Lecturer Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lecturers as $index => $lecturer): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($lecturer['subject_name']) ?></td>
                                        <td><?= htmlspecialchars($lecturer['lecturer_name']) ?></td>
                                        <td>
                                            <a href="meet_lecturer.php?lecturer_id=<?= $lecturer['lecturer_id'] ?>" class="btn-meet">
                                                <i class="fas fa-video"></i> Meet Lecturer
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert-custom">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>No lecturers assigned for your degree and batch yet.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>



<?php include 'includes/footer.php'; ?>