<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="css/student_favorites.css">

<?php
// Fetch all events
$events = $pdo->query("SELECT * FROM career_events ORDER BY event_date ASC")->fetchAll(PDO::FETCH_ASSOC);
?>


    <div class="page-header">
        <h3 class="page-title">
            <i class="fas fa-star me-2"></i>
            Student Favorites per Event
        </h3>
    </div>

    <div class="row">
        <?php if (empty($events)): ?>
            <div class="col-12">
                <div class="empty-state">
                    <i class="fas fa-calendar-xmark"></i>
                    <p>No events found. Please add events to view student favorites.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($events as $event): ?>
                <?php
                    // Fetch students who favorited this event
                    $stmt = $pdo->prepare("
                        SELECT s.id, s.student_id, s.name 
                        FROM favorite_events fe 
                        JOIN students s ON fe.student_id = s.id 
                        WHERE fe.event_id = ?
                    ");
                    $stmt->execute([$event['id']]);
                    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $studentCount = count($students);
                    
                    // Format date if available
                    $eventDate = !empty($event['event_date']) ? date('F j, Y', strtotime($event['event_date'])) : 'Date TBD';
                ?>
                <div class="col-lg-6 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <span><?= htmlspecialchars($event['event_name']) ?></span>
                            <span class="card-date"><?= $eventDate ?></span>
                        </div>
                        <div class="card-body">
                            <?php if (empty($students)): ?>
                                <div class="empty-state">
                                    <i class="fas fa-user-slash"></i>
                                    <p>No students have favorited this event yet.</p>
                                </div>
                            <?php else: ?>
                                <div class="student-count">
                                    <i class="fas fa-users me-2"></i>
                                    <?= $studentCount ?> Student<?= $studentCount !== 1 ? 's' : '' ?>
                                </div>
                                <div class="student-list">
                                    <ul class="list-group">
                                        <?php foreach ($students as $student): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong><?= htmlspecialchars($student['name']) ?></strong>
                                                <span class="student-id">ID: <?= htmlspecialchars($student['student_id']) ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    

    <?php include 'includes/footer.php'; ?>