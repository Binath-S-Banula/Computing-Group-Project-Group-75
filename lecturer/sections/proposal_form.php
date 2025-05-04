<?php
// Get unique allocated subjects for this lecturer
$query = "
    SELECT DISTINCT s.id, s.name 
    FROM subjects s
    JOIN subject_allocations sa ON sa.subject_id = s.id
    WHERE sa.lecturer_id = :lecturer_id
";
$stmt = $pdo->prepare($query);
$stmt->execute(['lecturer_id' => $lecturer_uid]);
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<div class="section-card">
    <h2 class="section-title">Propose a Change to Timetable</h2>
    <form method="POST" action="sections/handle_proposal_submit.php" class="row g-3">
        <div class="col-md-4">
            <label for="date" class="form-label">Date</label>
            <input type="date" class="form-control" id="date" name="date" required>
        </div>

        <div class="col-md-4">
            <label for="start_time" class="form-label">Start Time</label>
            <input type="time" class="form-control" id="start_time" name="start_time" required>
        </div>

        <div class="col-md-4">
            <label for="end_time" class="form-label">End Time</label>
            <input type="time" class="form-control" id="end_time" name="end_time" required>
        </div>

        <div class="col-md-6">
            <label for="subject" class="form-label">Subject</label>
            <select class="form-select" id="subject" name="subject_id" required>
                <option value="">-- Select Subject --</option>
                <?php foreach ($subjects as $subject): ?>
                    <option value="<?php echo htmlspecialchars($subject['id']); ?>">
                        <?php echo htmlspecialchars($subject['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>


        <div class="col-md-6">
            <label for="reason" class="form-label">Reason for Change</label>
            <textarea class="form-control" id="reason" name="reason" rows="3" required placeholder="Explain why you need this change..."></textarea>
        </div>

        <div class="col-12 mt-4">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="send" class="me-2"></i>
                Submit Change for Approval
            </button>
        </div>
    </form>
</div>
