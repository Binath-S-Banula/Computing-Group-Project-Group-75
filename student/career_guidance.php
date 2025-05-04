<?php include 'includes/header.php'; ?>

<?php
// Get all events
$events = $pdo->query("SELECT * FROM career_events ORDER BY event_date ASC")->fetchAll(PDO::FETCH_ASSOC);

// Get student's favorite events
$fav_stmt = $pdo->prepare("SELECT event_id FROM favorite_events WHERE student_id = ?");
$fav_stmt->execute([$student_uid]);
$fav_events = array_column($fav_stmt->fetchAll(PDO::FETCH_ASSOC), 'event_id');
?>

    <style>
        .days-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: rgba(0, 123, 255, 0.9);
            color: white;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 0.9rem;
        }
    </style>

    <h3 class="mb-4">Career Events</h3>

    <?php if (empty($events)): ?>
        <p>No events found.</p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($events as $event): 
                $eventDate = new DateTime($event['event_date']);
                $today = new DateTime();
                $daysLeft = (int)$today->diff($eventDate)->format('%r%a');
                ?>
                <div class="col-md-6">
                    <div class="card mb-4 shadow-sm position-relative">
                        <?php if (!empty($event['image']) && file_exists('../career_guidance/images/' . $event['image'])): ?>
                            <div style="position: relative;">
                                <img src="../career_guidance/images/<?= $event['image'] ?>" class="card-img-top" style="height: 250px; width: 100%; object-fit: cover;">
                                <div class="days-badge">
                                    <?php if ($daysLeft > 0): ?>
                                        <?= $daysLeft ?> day<?= $daysLeft > 1 ? 's' : '' ?> left
                                    <?php elseif ($daysLeft === 0): ?>
                                        Today
                                    <?php else: ?>
                                        Event passed
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($event['event_name']) ?></h5>
                            <p class="card-text"><?= nl2br(htmlspecialchars($event['description'])) ?></p>
                            <p class="card-text">
                                <strong>Date:</strong> <?= htmlspecialchars($event['event_date']) ?><br>
                                <strong>Time:</strong>
                                <?= date("g:i A", strtotime($event['start_time'])) . " - " . date("g:i A", strtotime($event['end_time'])) ?><br>
                                <strong>Location:</strong> <?= htmlspecialchars($event['location']) ?>
                            </p>

                            <form action="sections/toggle_favorite.php" method="post">
                                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                <?php if (in_array($event['id'], $fav_events)): ?>
                                    <button type="submit" class="btn btn-danger btn-sm">Remove from Favorites</button>
                                <?php else: ?>
                                    <button type="submit" class="btn btn-outline-primary btn-sm">Add to Favorites</button>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php include 'includes/footer.php'; ?>