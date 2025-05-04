<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="css/career_events.css">

<?php
// Flash messages
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);

// Add Event
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_event'])) {
    $event_name = $_POST['event_name'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $location = $_POST['location'];

    try {
        // Insert event first
        $stmt = $pdo->prepare("INSERT INTO career_events (event_name, description, event_date, start_time, end_time, location) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$event_name, $description, $event_date, $start_time, $end_time, $location]);
        $event_id = $pdo->lastInsertId();

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_image_name = $event_id . '.' . $ext;
            $target = "images/" . $new_image_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $update = $pdo->prepare("UPDATE career_events SET image = ? WHERE id = ?");
                $update->execute([$new_image_name, $event_id]);
                $_SESSION['success'] = "Event added successfully.";
            } else {
                $_SESSION['error'] = "Event added but image upload failed.";
            }
        } else {
            $_SESSION['success'] = "Event added successfully (no image).";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error adding event: " . $e->getMessage();
    }

    // Redirect to avoid resubmission
    header("Location: career_events.php");
    exit();
}

// Delete Event
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    try {
        $stmt = $pdo->prepare("SELECT image FROM career_events WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if ($row && !empty($row['image']) && file_exists("images/" . $row['image'])) {
            unlink("images/" . $row['image']);
        }

        $stmt = $pdo->prepare("DELETE FROM career_events WHERE id = ?");
        $stmt->execute([$id]);

        $_SESSION['success'] = "Event deleted successfully.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error deleting event: " . $e->getMessage();
    }

    header("Location: career_events.php");
    exit();
}

// Fetch events
$stmt = $pdo->query("SELECT * FROM career_events ORDER BY event_date ASC");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group events by upcoming and past
$upcoming_events = [];
$past_events = [];
$today = date('Y-m-d');

foreach ($events as $event) {
    if ($event['event_date'] >= $today) {
        $upcoming_events[] = $event;
    } else {
        $past_events[] = $event;
    }
}
?>

    <div class="container">
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i><?= $success ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between welcome-section">
            <h2 class="h4 mb-0">All Events</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
                <i class="fas fa-plus btn-icon"></i>Add New Event
            </button>
        </div>

        <div class="card">
            <div class="card-header p-0">
                <ul class="nav nav-tabs" id="eventTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button" role="tab" aria-controls="upcoming" aria-selected="true">
                            <i class="fas fa-calendar-day me-2"></i>Upcoming Events
                            <?php if (count($upcoming_events) > 0): ?>
                                <span class="badge bg-white text-primary ms-2"><?= count($upcoming_events) ?></span>
                            <?php endif; ?>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="past-tab" data-bs-toggle="tab" data-bs-target="#past" type="button" role="tab" aria-controls="past" aria-selected="false">
                            <i class="fas fa-history me-2"></i>Past Events
                            <?php if (count($past_events) > 0): ?>
                                <span class="badge bg-white text-primary ms-2"><?= count($past_events) ?></span>
                            <?php endif; ?>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="tab-content" id="eventTabsContent">
                <div class="tab-pane fade show active" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
                    <?php if (count($upcoming_events) > 0): ?>
                        <ul class="event-list">
                            <?php foreach ($upcoming_events as $event): ?>
                                <li class="event-item">
                                    <div class="event-content">
                                        <div class="event-date">
                                            <div class="event-date-day"><?= date("d", strtotime($event['event_date'])) ?></div>
                                            <div class="event-date-month"><?= date("M", strtotime($event['event_date'])) ?></div>
                                        </div>
                                        
                                        <?php if (!empty($event['image']) && file_exists("images/" . $event['image'])): ?>
                                            <img src="images/<?= $event['image'] ?>" alt="<?= htmlspecialchars($event['event_name']) ?>" class="event-image">
                                        <?php else: ?>
                                            <div class="event-image d-flex align-items-center justify-content-center bg-primary-light">
                                                <i class="fas fa-calendar-alt fa-2x text-primary"></i>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="event-info">
                                            <h3 class="event-title"><?= htmlspecialchars($event['event_name']) ?></h3>
                                            <div class="event-details">
                                                <span class="me-3"><i class="fas fa-clock me-1"></i><?= date("g:i A", strtotime($event['start_time'])) . ' - ' . date("g:i A", strtotime($event['end_time'])) ?></span>
                                                <span><i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($event['location']) ?></span>
                                                <span class="badge badge-upcoming ms-2">Upcoming</span>
                                            </div>
                                        </div>
                                        
                                        <div class="event-actions">
                                            <a href="view_event.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                View <i class="fas fa-eye"></i> 
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal<?= $event['id'] ?>">
                                                 Delete <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-plus empty-state-icon"></i>
                            <h3 class="h5">No Upcoming Events</h3>
                            <p class="mb-3">You haven't added any upcoming events yet.</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
                                <i class="fas fa-plus btn-icon"></i>Add New Event
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="tab-pane fade" id="past" role="tabpanel" aria-labelledby="past-tab">
                    <?php if (count($past_events) > 0): ?>
                        <ul class="event-list">
                            <?php foreach ($past_events as $event): ?>
                                <li class="event-item">
                                    <div class="event-content">
                                        <div class="event-date">
                                            <div class="event-date-day"><?= date("d", strtotime($event['event_date'])) ?></div>
                                            <div class="event-date-month"><?= date("M", strtotime($event['event_date'])) ?></div>
                                        </div>
                                        
                                        <?php if (!empty($event['image']) && file_exists("images/" . $event['image'])): ?>
                                            <img src="images/<?= $event['image'] ?>" alt="<?= htmlspecialchars($event['event_name']) ?>" class="event-image">
                                        <?php else: ?>
                                            <div class="event-image d-flex align-items-center justify-content-center bg-primary-light">
                                                <i class="fas fa-calendar-alt fa-2x text-primary"></i>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="event-info">
                                            <h3 class="event-title"><?= htmlspecialchars($event['event_name']) ?></h3>
                                            <div class="event-details">
                                                <span class="me-3"><i class="fas fa-clock me-1"></i><?= date("g:i A", strtotime($event['start_time'])) . ' - ' . date("g:i A", strtotime($event['end_time'])) ?></span>
                                                <span><i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($event['location']) ?></span>
                                                <span class="badge badge-past ms-2">Past</span>
                                            </div>
                                        </div>
                                        
                                        <div class="event-actions">
                                            <a href="view_event.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                View <i class="fas fa-eye"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal<?= $event['id'] ?>">
                                                Delete <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-history empty-state-icon"></i>
                            <h3 class="h5">No Past Events</h3>
                            <p>There are no past events in the system.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Event Modal -->
    <div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEventModalLabel">
                        <i class="fas fa-plus-circle me-2"></i>Add New Event
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="event_name" class="form-label">Event Name</label>
                            <input type="text" name="event_name" id="event_name" class="form-control" required>
                        </div>
                        
                        <div class="col-md-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="4" required></textarea>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="event_date" class="form-label">Date</label>
                            <input type="date" name="event_date" id="event_date" class="form-control" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="start_time" class="form-label">Start Time</label>
                            <input type="time" name="start_time" id="start_time" class="form-control" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="end_time" class="form-label">End Time</label>
                            <input type="time" name="end_time" id="end_time" class="form-control" required>
                        </div>
                        
                        <div class="col-md-12">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" name="location" id="location" class="form-control" required>
                        </div>
                        
                        <div class="col-md-12">
                            <label for="image" class="form-label">Event Image</label>
                            <input type="file" name="image" id="image" accept="image/*" class="form-control">
                            <div class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Recommended image size: 1200 x 600 pixels
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_event" class="btn btn-primary btn-save">
                        <i class="fas fa-save btn-icon"></i>Save Event
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times btn-icon"></i>Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modals -->
    <?php foreach ($events as $event): ?>
    <div class="modal fade" id="confirmDeleteModal<?= $event['id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $event['id'] ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel<?= $event['id'] ?>">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to delete <strong><?= htmlspecialchars($event['event_name']) ?></strong>? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <a href="?delete=<?= $event['id'] ?>" class="btn btn-danger">
                        <i class="fas fa-trash-alt btn-icon"></i>Yes, Delete
                    </a>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times btn-icon"></i>Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <?php endforeach; ?>

<?php include 'includes/footer.php'; ?>