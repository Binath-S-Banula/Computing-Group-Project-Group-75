<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="css/lecturer_allocations.css">

<?php
// Fetch lecturer info
$lecturer_query = "SELECT name FROM lecturers WHERE id = :lecturer_id";
$lecturer_stmt = $pdo->prepare($lecturer_query);
$lecturer_stmt->execute(['lecturer_id' => $lecturer_uid]);
$lecturer = $lecturer_stmt->fetch(PDO::FETCH_ASSOC);

// Fetch subject allocations with joins to get degree, batch, subject info
$query = "
    SELECT 
        s.code AS subject_code,
        s.name AS subject_name,
        d.degree_name,
        b.batch_name
    FROM subject_allocations sa
    JOIN subjects s ON sa.subject_id = s.id
    JOIN degrees d ON sa.degree_id = d.id
    JOIN batches b ON sa.batch_id = b.id
    WHERE sa.lecturer_id = :lecturer_id
";
$stmt = $pdo->prepare($query);
$stmt->execute(['lecturer_id' => $lecturer_uid]);
$allocations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <h2 class="dashboard-title">Subject Allocations</h2>
        <p class="dashboard-subtitle">View all subjects assigned to you</p>
    </div>
    
    
    <!-- Allocations Card -->
    <div class="card">
        <div class="card-body">
            <h4 class="mb-4"><i class="fas fa-book-open me-2 text-success"></i>Your Subject Allocations</h4>
            
            <?php if (empty($allocations)): ?>
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <h5>No Subjects Assigned</h5>
                    <p>You don't have any subjects assigned to you yet.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Degree</th>
                                <th>Subject</th>
                                <th>Batch</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allocations as $index => $row): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($row['degree_name']) ?></td>
                                    <td>
                                        <span class="subject-badge"><?= htmlspecialchars($row['subject_code']) ?></span>
                                        <?= htmlspecialchars($row['subject_name']) ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['batch_name']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>