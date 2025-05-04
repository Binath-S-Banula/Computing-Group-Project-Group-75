<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="css/manage_events.css">

<?php
// Handle flash messages
$flash_message = $_SESSION['flash_message'] ?? null;
$flash_type = $_SESSION['flash_type'] ?? null;
unset($_SESSION['flash_message'], $_SESSION['flash_type']);

// Fetch events created by the admin
$admin_id = $_SESSION['club_admin_uid'];
$stmt = $pdo->prepare("SELECT * FROM club_events WHERE club_id = ? AND status = 'upcoming' ORDER BY event_date ASC");
$stmt->execute([$admin_id]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (isset($_SESSION['flash_message'])): ?>
    <div class="alert alert-<?php echo $_SESSION['flash_type']; ?> alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['flash_message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
<?php endif; ?>



    <div class="dashboard-title d-flex" style="justify-content:space-between;">
        <h2>Upcoming Club Events</h2>
        <div class="d-flex">
        <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#eventModal" onclick="openAddEventModal()">
            <i class="bi bi-plus-circle"></i> Add Event
        </button>
        <form action="sections/update_event_status.php" method="POST" style="z-index:1;">
            <button type="submit" class="btn btn-warning  update-btn" >
                <i class="fa fa-refresh me-1"></i> Update Expired Events
            </button>
        </form>
        </div>
    </div>

    <?php if ($flash_message): ?>
        <div class="alert alert-<?= $flash_type ?>">
            <?php if ($flash_type === 'success'): ?>
                <i class="bi bi-check-circle-fill"></i>
            <?php elseif ($flash_type === 'danger'): ?>
                <i class="bi bi-exclamation-circle-fill"></i>
            <?php elseif ($flash_type === 'warning'): ?>
                <i class="bi bi-exclamation-triangle-fill"></i>
            <?php endif; ?>
            <?= $flash_message ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <?php if (count($events) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Event Details</th>
                            <th>Date & Time</th>
                            <th>Location</th>
                            <th>Max Attendees</th>
                            <th>Access Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php foreach ($events as $event): ?>
        <tr>
            <td>
                <div class="event-title"><?= htmlspecialchars($event['title']) ?></div>
            </td>
            <td>
                <span class="badge-date">
                    <i class="bi bi-calendar3"></i> 
                    <?= date('M d, Y', strtotime($event['event_date'])) ?>
                </span>
                <div class="event-meta mt-1">
                    <i class="bi bi-clock"></i> 
                    <?= date('h:i A', strtotime($event['start_time'])) ?> - 
                    <?= date('h:i A', strtotime($event['end_time'])) ?>
                </div>
            </td>
            <td>
                <div class="event-meta">
                    <i class="bi bi-geo-alt"></i> 
                    <?= htmlspecialchars($event['location']) ?>
                </div>
            </td>
            <td>
                <?= htmlspecialchars($event['max_attendees']) ?? 'Unlimited' ?>
            </td>
            <td>
                <?php if ($event['access_type'] === 'public'): ?>
                    <span class="badge bg-success">Public</span>
                <?php else: ?>
                    <span class="badge bg-secondary">Members Only</span>
                <?php endif; ?>
            </td>
            <td>
                <div class="action-buttons">
                    <a href="view_event.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-info">View</a>
                    <button class="btn btn-action btn-edit" onclick="openEditEventModal(<?= htmlspecialchars(json_encode($event)) ?>)">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    <button class="btn btn-action btn-delete" onclick="openDeleteModal(<?= $event['id'] ?>)">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>

                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="bi bi-calendar-x"></i>
                </div>
                <h4>No Events Found</h4>
                <p class="empty-text">You haven't created any events yet. Click the "Add Event" button to get started.</p>
                <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#eventModal" onclick="openAddEventModal()">
                    <i class="bi bi-plus-circle"></i> Add Your First Event
                </button>
            </div>
        <?php endif; ?>
    </div>


<!-- Add/Edit Modal -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" action="sections/save_event.php">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalTitle">Add Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="event_id" id="event_id">
                <div class="mb-3">
                    <label class="form-label">Event Title</label>
                    <input type="text" class="form-control" name="title" id="title" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" id="description" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Event Date</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                        <input type="date" class="form-control" name="event_date" id="event_date" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_time" class="form-label">Start Time</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-clock"></i></span>
                            <input type="time" class="form-control" name="start_time" id="start_time" required>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="end_time" class="form-label">End Time</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-clock"></i></span>
                            <input type="time" class="form-control" name="end_time" id="end_time" required>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Location</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                        <input type="text" class="form-control" name="location" id="location" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Max Attendees</label>
                    <input type="number" class="form-control" name="max_attendees" id="max_attendees" min="1">
                </div>
                <div class="mb-3">
                    <label for="access_type" class="form-label">Event Access</label>
                    <select name="access_type" id="access_type" class="form-select" required>
                        <option value="public">Public (All Students)</option>
                        <option value="members">Members Only</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-modal-cancel" type="button" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-modal-save" type="submit">Save Event</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="sections/delete_event.php">
            <input type="hidden" name="event_id" id="delete_event_id">
            <input type="hidden" name="action" value="delete">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <p class="text-center">Are you sure you want to delete this event?</p>
                    <p class="text-center text-muted">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-modal-delete">Delete Event</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddEventModal() {
        // Set default date to today
        const today = new Date().toISOString().split('T')[0];
        
        document.getElementById('eventModalTitle').innerText = 'Add Event';
        document.getElementById('event_id').value = '';
        document.getElementById('title').value = '';
        document.getElementById('description').value = '';
        document.getElementById('event_date').value = today;
        document.getElementById('start_time').value = '';
        document.getElementById('end_time').value = '';
        document.getElementById('location').value = '';
        document.getElementById('max_attendees').value = '';
    }

    function openEditEventModal(event) {
    document.getElementById('eventModalTitle').innerText = 'Edit Event';
    document.getElementById('event_id').value = event.id;
    document.getElementById('title').value = event.title;
    document.getElementById('description').value = event.description;
    document.getElementById('event_date').value = event.event_date;
    document.getElementById('start_time').value = event.start_time;
    document.getElementById('end_time').value = event.end_time;
    document.getElementById('location').value = event.location;  
    document.getElementById('max_attendees').value = event.max_attendees ?? '';
    document.getElementById('access_type').value = event.access_type;

    new bootstrap.Modal(document.getElementById('eventModal')).show();
}


    function openDeleteModal(eventId) {
        document.getElementById('delete_event_id').value = eventId;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
</script>

<?php include 'includes/footer.php'; ?>