<?php
session_start();
require '../db_connection.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='alert alert-danger'>Event not specified.</div>";
    exit();
}

$event_id = $_GET['id'];

// Get event details
$stmt = $pdo->prepare("SELECT e.*, c.club_name 
                       FROM club_events e 
                       JOIN club_admins c ON e.club_id = c.id 
                       WHERE e.id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    echo "<div class='alert alert-danger'>Event not found.</div>";
    exit();
}

// Get registered students
$stmt = $pdo->prepare("
    SELECT s.id, s.student_id, s.name, s.email 
    FROM club_event_registrations r
    JOIN students s ON r.student_id = s.id
    WHERE r.event_id = ?
");
$stmt->execute([$event_id]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Flash messages
if (isset($_SESSION['flash_message'])) {
    echo "<div class='alert alert-{$_SESSION['flash_type']} p-2'>" . $_SESSION['flash_message'] . "</div>";
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Details - <?= htmlspecialchars($event['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><?= htmlspecialchars($event['title']) ?></h2>
        <div>
            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editEventModal">Edit</button>
            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteEventModal">Delete</button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <p><strong>Event: <?= htmlspecialchars($event['title']) ?></strong> </p>
            <p><strong>Description: </strong> <?= nl2br(htmlspecialchars($event['description'])) ?></p>
            <p><strong>Date: </strong> <?= date('F j, Y', strtotime($event['event_date'])) ?></p>
            <p><strong>Start: </strong> <?= date('h:i A', strtotime($event['start_time'])) ?> |
             <strong>End:</strong> <?= date('h:i A', strtotime($event['end_time'])) ?></p>
            <p><strong>Location: </strong> <?= htmlspecialchars($event['location']) ?></p>
            <p><strong>Access Type: </strong> <?= ucfirst($event['access_type']) ?> </p>
            <p> <strong>Max Attendees: </strong> <?= $event['max_attendees'] ?></p>
            
        </div>
    </div>

    <hr>

    <h4>Registered Students (<?= count($students) ?>)</h4>

    <?php if (empty($students)): ?>
        <div class="alert alert-info">No students have registered yet.</div>
    <?php else: ?>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= htmlspecialchars($student['student_id']) ?></td>
                        <td><?= htmlspecialchars($student['name']) ?></td>
                        <td><?= htmlspecialchars($student['email']) ?></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteStudentModal" data-student-id="<?= $student['id'] ?>" data-event-id="<?= $event_id ?>">Remove</button>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="manage_events.php" class="btn btn-secondary mt-3">Back to Events</a>
</div>

<!-- Edit Event Modal -->
<div class="modal fade" id="editEventModal" tabindex="-1" aria-labelledby="editEventModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="sections/save_event.php" method="POST">
      <input type="hidden" name="event_id" value="<?= $event['id'] ?>">

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editEventModalLabel">Edit Event</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        
        <div class="modal-body">
          <!-- Event Title -->
          <div class="mb-3">
            <label for="title" class="form-label">Event Title</label>
            <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($event['title']) ?>" required>
          </div>

          <!-- Event Description -->
          <div class="mb-3">
            <label for="description" class="form-label">Event Description</label>
            <textarea class="form-control" name="description" required><?= htmlspecialchars($event['description']) ?></textarea>
          </div>

          <!-- Event Date -->
          <div class="mb-3">
            <label for="event_date" class="form-label">Event Date</label>
            <input type="date" class="form-control" name="event_date" value="<?= htmlspecialchars($event['event_date']) ?>" required>
          </div>

          <!-- Event Start Time -->
          <div class="mb-3">
            <label for="start_time" class="form-label">Start Time</label>
            <input type="time" class="form-control" name="start_time" value="<?= htmlspecialchars($event['start_time']) ?>" required>
          </div>

          <!-- Event End Time -->
          <div class="mb-3">
            <label for="end_time" class="form-label">End Time</label>
            <input type="time" class="form-control" name="end_time" value="<?= htmlspecialchars($event['end_time']) ?>" required>
          </div>

          <!-- Event Location -->
          <div class="mb-3">
            <label for="location" class="form-label">Event Location</label>
            <input type="text" class="form-control" name="location" value="<?= htmlspecialchars($event['location']) ?>" required>
          </div>

          <!-- Event Access Type -->
          <div class="mb-3">
            <label for="access_type" class="form-label">Access Type</label>
            <select class="form-select" name="access_type" required>
              <option value="public" <?= $event['access_type'] == 'public' ? 'selected' : '' ?>>Public</option>
              <option value="members" <?= $event['access_type'] == 'members' ? 'selected' : '' ?>>Members Only</option>
            </select>
          </div>

            <!-- Max Attendees -->
            <div class="mb-3">
                <label for="max_attendees" class="form-label">Max Attendees</label>
                <input type="number" class="form-control" name="max_attendees" value="<?= htmlspecialchars($event['max_attendees']) ?>" min="1" required>
            </div>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Delete Event Modal -->
<div class="modal fade" id="deleteEventModal" tabindex="-1" aria-labelledby="deleteEventModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" action="sections/delete_event.php">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="deleteEventModalLabel">Confirm Delete</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="event_id" value="<?= $event_id ?>">
          <p>Are you sure you want to delete the event "<strong><?= htmlspecialchars($event['title']) ?></strong>"?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Delete</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Delete Student Modal -->
<div class="modal fade" id="deleteStudentModal" tabindex="-1" aria-labelledby="deleteStudentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" action="sections/remove_student.php">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="deleteStudentModalLabel">Confirm Removal</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="event_id" id="delete_event_id">
          <input type="hidden" name="student_id" id="delete_student_id">
          <p>Are you sure you want to remove this student from the event?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Remove</button>
        </div>
      </div>
    </form>
  </div>

</div>


</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // When a "Remove" button is clicked, pass the student ID and event ID to the modal
  const deleteStudentModal = document.getElementById('deleteStudentModal');
  deleteStudentModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget; // Button that triggered the modal
    const studentId = button.getAttribute('data-student-id');
    const eventId = button.getAttribute('data-event-id');
    
    const modalStudentIdField = deleteStudentModal.querySelector('#delete_student_id');
    const modalEventIdField = deleteStudentModal.querySelector('#delete_event_id');
    
    modalStudentIdField.value = studentId;
    modalEventIdField.value = eventId;
  });
</script>


</html>
