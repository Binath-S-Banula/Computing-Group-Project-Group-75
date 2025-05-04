<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="css/view_event.css">

<?php
if (!isset($_GET['id'])) {
    header("Location: career_events.php");
    exit();
}

$id = $_GET['id'];
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);

// Fetch event
$stmt = $pdo->prepare("SELECT * FROM career_events WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    $_SESSION['error'] = "Event not found.";
    header("Location: career_events.php");
    exit();
}

// Update Event
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['edit_event'])) {
    $event_name = $_POST['event_name'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $location = $_POST['location'];

    try {
        $stmt = $pdo->prepare("UPDATE career_events SET event_name=?, description=?, event_date=?, start_time=?, end_time=?, location=? WHERE id=?");
        $stmt->execute([$event_name, $description, $event_date, $start_time, $end_time, $location, $id]);

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_image_name = $id . '.' . $ext;
            $target = "images/" . $new_image_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $update = $pdo->prepare("UPDATE career_events SET image=? WHERE id=?");
                $update->execute([$new_image_name, $id]);
            }
        }

        $_SESSION['success'] = "Event updated successfully.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Update failed: " . $e->getMessage();
    }

    header("Location: view_event.php?id=$id");
    exit();
}

// Delete
if (isset($_POST['delete_event'])) {
    if (!empty($event['image']) && file_exists("images/" . $event['image'])) {
        unlink("images/" . $event['image']);
    }

    $stmt = $pdo->prepare("DELETE FROM career_events WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['success'] = "Event deleted successfully.";
    header("Location: career_events.php");
    exit();
}

// Format date and times for display
$formatted_date = date("F j, Y", strtotime($event['event_date']));
$formatted_start = date("g:i A", strtotime($event['start_time']));
$formatted_end = date("g:i A", strtotime($event['end_time']));
$formatted_time_period = $formatted_start . ' - ' . $formatted_end;

// Calculate days remaining
$event_timestamp = strtotime($event['event_date']);
$today = strtotime(date('Y-m-d'));
$days_remaining = ceil(($event_timestamp - $today) / (60 * 60 * 24));
?>


<style>
    .countdown-badge {
    background-color: <?= $days_remaining > 0 ? 'var(--accent-green)' : 'var(--neutral-dark)' ?>;
    color: var(--white);
    border-radius: 30px;
    padding: 0.25rem 0.75rem;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-block;
    margin-top: 0.5rem;
    white-space: nowrap;
    }
</style>

    <header class="page-header">
        <div class="container">
            <h1 class="h3 mb-0 d-flex align-items-center">
                <i class="fas fa-calendar-alt me-2"></i>
                <?= htmlspecialchars($event['event_name']) ?>
                <span class="admin-badge">Admin View</span>
            </h1>
        </div>
    </header>

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

        <div class="event-card">
            <div class="event-banner">
                <?php if (!empty($event['image']) && file_exists("images/" . $event['image'])): ?>
                    <img src="images/<?= $event['image'] ?>" alt="<?= htmlspecialchars($event['event_name']) ?>">
                <?php else: ?>
                    <div class="d-flex align-items-center justify-content-center h-100 bg-gradient">
                        <i class="fas fa-calendar-alt fa-5x text-white-50"></i>
                    </div>
                <?php endif; ?>
                
                <div class="event-date-badge">
                    <div class="day-number"><?= date("d", strtotime($event['event_date'])) ?></div>
                    <div class="month-name"><?= date("M", strtotime($event['event_date'])) ?></div>
                    <?php if ($days_remaining > 0): ?>
                        <div class="countdown-badge">
                            <i class="fas fa-clock me-1"></i>
                            <?= $days_remaining ?> day<?= $days_remaining != 1 ? 's' : '' ?> left
                        </div>
                    <?php else: ?>
                        <div class="countdown-badge">
                            <i class="fas fa-check-circle me-1"></i>
                            Completed
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="event-banner-overlay">
                    <h2 class="h3 mb-2"><?= htmlspecialchars($event['event_name']) ?></h2>
                </div>
            </div>
            
            <div class="event-content">
                <div class="event-description">
                    <?= nl2br(htmlspecialchars($event['description'])) ?>
                </div>
                
                <div class="event-details">
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="detail-text">
                            <div class="detail-label">Date</div>
                            <div class="detail-value"><?= $formatted_date ?></div>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="detail-text">
                            <div class="detail-label">Time</div>
                            <div class="detail-value"><?= $formatted_time_period ?></div>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="detail-text">
                            <div class="detail-label">Location</div>
                            <div class="detail-value"><?= htmlspecialchars($event['location']) ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex flex-wrap gap-2">
                    <a href="career_events.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left btn-icon"></i>Back to Events
                    </a>
                    
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal">
                        <i class="fas fa-edit btn-icon"></i>Edit Event
                    </button>
                    
                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash-alt btn-icon"></i>Delete Event
                    </button>
                </div>
            </div>
        </div>
        
        <div class="alert alert-secondary bg-white border-start border-4 border-primary-dark" role="alert">
            <div class="d-flex">
                <div class="me-3">
                    <i class="fas fa-info-circle fa-2x text-primary"></i>
                </div>
                <div>
                    <h5 class="alert-heading mb-1">Admin Preview Mode</h5>
                    <p class="mb-0">This is how the event will appear to students. Use the edit button to make changes to the event details.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">
                        <i class="fas fa-edit me-2"></i>Edit Event
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="edit_event" value="1">
                    
                    <div class="mb-3">
                        <label for="event_name" class="form-label">Event Name</label>
                        <input type="text" id="event_name" name="event_name" value="<?= htmlspecialchars($event['event_name']) ?>" required class="form-control">
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="5" required><?= htmlspecialchars($event['description']) ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="event_date" class="form-label">Date</label>
                            <input type="date" id="event_date" name="event_date" value="<?= htmlspecialchars($event['event_date']) ?>" required class="form-control">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="start_time" class="form-label">Start Time</label>
                            <input type="time" id="start_time" name="start_time" value="<?= htmlspecialchars($event['start_time']) ?>" class="form-control" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="end_time" class="form-label">End Time</label>
                            <input type="time" id="end_time" name="end_time" value="<?= htmlspecialchars($event['end_time']) ?>" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" id="location" name="location" value="<?= htmlspecialchars($event['location']) ?>" required class="form-control">
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" id="image" name="image" accept="image/*" class="form-control">
                        <?php if (!empty($event['image']) && file_exists("images/" . $event['image'])): ?>
                            <div class="mt-2">
                                <div class="d-flex align-items-center">
                                    <img src="images/<?= $event['image'] ?>" alt="Current image" style="height: 60px; width: auto; object-fit: cover; border-radius: 4px;" class="me-2">
                                    <span class="text-muted">Current image (upload a new one to replace)</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-save btn-icon"></i>Save Changes
                    </button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">
                        <i class="fas fa-times btn-icon"></i>Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <input type="hidden" name="delete_event" value="1">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirm Deletion
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to delete <strong><?= htmlspecialchars($event['event_name']) ?></strong>? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" type="submit">
                        <i class="fas fa-trash-alt btn-icon"></i>Yes, Delete
                    </button>
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
                        <i class="fas fa-times btn-icon"></i>Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Auto show modal if form has error or success after redirect -->
    <?php if ($success || $error): ?>
    <script>
        const editModal = new bootstrap.Modal(document.getElementById('editModal'));
        <?php if ($success && !empty($_POST['edit_event'])): ?>
            editModal.hide();
        <?php elseif (!empty($_POST['edit_event'])): ?>
            editModal.show();
        <?php endif; ?>
    </script>
    <?php endif; ?>


    <?php include 'includes/footer.php'; ?>