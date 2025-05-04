<?php
// Fetch submitted proposals with subject name
$stmt = $pdo->prepare("
    SELECT tp.*, s.name AS subject_name
    FROM timetable_proposals tp
    LEFT JOIN subjects s ON tp.subject_id = s.id
    WHERE tp.lecturer_id = :lecturer_uid
    ORDER BY tp.created_at DESC
");
$stmt->execute(['lecturer_uid' => $lecturer_uid]);
$my_proposals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!-- section_my_proposals -->
<div class="section-card">
    <h2 class="section-title">My Submitted Proposals</h2>

    <?php if (count($my_proposals) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Subject</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($my_proposals as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['date']) ?></td>
                            <td><?= htmlspecialchars($p['start_time']) ?> - <?= htmlspecialchars($p['end_time']) ?></td>
                            <td><?= htmlspecialchars($p['subject_name']) ?></td>
                            <td><?= nl2br(htmlspecialchars($p['reason'])) ?></td>
                            <td><span class="status-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
                            <td><?= htmlspecialchars($p['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i data-lucide="calendar-x" class="mb-3" style="width: 48px; height: 48px;"></i>
            <p>No proposals submitted yet.</p>
        </div>
    <?php endif; ?>
</div>
