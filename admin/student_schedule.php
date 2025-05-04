<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="css/student_schedule.css">

<?php
// Handle form submission for adding new timetable
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_schedule'])) {
    $degree_id = $_POST['degree_id'];
    $batch_id = $_POST['batch_id'];
    $sheetLink = $_POST['sheet_link'];

    // Extract the sheet_id from the link
    preg_match('/\/d\/(.*)\/edit/', $sheetLink, $matches);
    $sheet_id = isset($matches[1]) ? $matches[1] : '';

    try {
        // Insert into student_timetables
        $stmt = $pdo->prepare("INSERT INTO student_timetables (degree_id, batch_id, sheet_id) VALUES (:degree_id, :batch_id, :sheet_id)");
        $stmt->execute(['degree_id' => $degree_id, 'batch_id' => $batch_id, 'sheet_id' => $sheet_id]);
        $_SESSION['message'] = 'Schedule added successfully!';
        $_SESSION['messageType'] = 'success';
        header('Location: student_schedule.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Error: ' . $e->getMessage();
        $_SESSION['messageType'] = 'danger';
    }
}

// Handle form submission for updating timetable
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_schedule'])) {
    $timetable_id = $_POST['timetable_id'];
    $degree_id = $_POST['degree_id'];
    $batch_id = $_POST['batch_id'];
    $sheetLink = $_POST['sheet_link'];

    // Extract the sheet_id from the link
    preg_match('/\/d\/(.*)\/edit/', $sheetLink, $matches);
    $sheet_id = isset($matches[1]) ? $matches[1] : '';

    try {
        // Update the timetable
        $stmt = $pdo->prepare("UPDATE student_timetables SET degree_id = :degree_id, batch_id = :batch_id, sheet_id = :sheet_id WHERE id = :id");
        $stmt->execute(['degree_id' => $degree_id, 'batch_id' => $batch_id, 'sheet_id' => $sheet_id, 'id' => $timetable_id]);
        $_SESSION['message'] = 'Schedule updated successfully!';
        $_SESSION['messageType'] = 'success';
        header('Location: student_schedule.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Error: ' . $e->getMessage();
        $_SESSION['messageType'] = 'danger';
    }
}

// Handle delete timetable
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $timetable_id = $_GET['delete'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM student_timetables WHERE id = :id");
        $stmt->execute(['id' => $timetable_id]);
        $_SESSION['message'] = 'Schedule deleted successfully!';
        $_SESSION['messageType'] = 'success';
        header('Location: student_schedule.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Error: ' . $e->getMessage();
        $_SESSION['messageType'] = 'danger';
    }
}

// Get timetable information for editing
$edit_timetable = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $timetable_id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM student_timetables WHERE id = :id");
    $stmt->execute(['id' => $timetable_id]);
    $edit_timetable = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>



    <!-- dashboard header  -->
    <div class="dashboard-header d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="dashboard-title m-0">Student Timetable Management</h3>
            <p class="dashboard-subtitle">Manage and assign timetables to Students across different faculties</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn-drive  " onclick="window.open('https://drive.google.com/drive/folders/16qZPlLlYLZKK45Ef6OeWXy7wZy7YDSro', '_blank')">
                <i class="fa-brands fa-google-drive btn-icon"></i> Open Google Drive
            </button>
            <button class="btn-add " data-bs-toggle="modal" data-bs-target="#addTimetableModal">
                <i class="fas fa-plus btn-icon"></i> Add Timetable
            </button>
        </div>
    </div>


    <!-- Display success or error message -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['messageType'] ?> alert-dismissible fade show" role="alert" id="autoDismissAlert">
            <?php if ($_SESSION['messageType'] === 'success'): ?>
                <i class="fas fa-check-circle me-2"></i>
            <?php else: ?>
                <i class="fas fa-exclamation-circle me-2"></i>
            <?php endif; ?>
            <?= $_SESSION['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['messageType']); ?>
    <?php endif; ?>

    <script>
        setTimeout(function() {
            let alertMessage = document.querySelector('.alert');
            if (alertMessage) {
                alertMessage.classList.add('fade-out');
                setTimeout(function() {
                    alertMessage.style.display = 'none'; 
                }, 500);
            }
        }, 3000); // for 3 seconds 

    </script>


    <!-- Timetables Card -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="10%">#</th>
                            <th width="30%">Degree</th>
                            <th width="25%">Batch</th>
                            <th width="35%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch student timetables
                        $stmt = $pdo->query("SELECT student_timetables.id, degrees.degree_name, batches.batch_name, student_timetables.sheet_id 
                                            FROM student_timetables 
                                            JOIN degrees ON student_timetables.degree_id = degrees.id 
                                            JOIN batches ON student_timetables.batch_id = batches.id");
                        $timetableExists = false;
                        while ($schedule = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $timetableExists = true;
                            $googleSheetLink = "https://docs.google.com/spreadsheets/d/{$schedule['sheet_id']}/edit";
                            echo "<tr>";
                            echo "<td>" . $schedule['id'] . "</td>";
                            echo "<td>" . htmlspecialchars($schedule['degree_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($schedule['batch_name']) . "</td>";
                            echo "<td>
                                    <div class='d-flex gap-2'>
                                        <a href='{$googleSheetLink}' target='_blank' class='btn btn-sm btn-success'>
                                            <i class='fas fa-external-link-alt'></i> View
                                        </a>
                                        <a href='?edit=" . $schedule['id'] . "' class='btn btn-sm btn-warning'>
                                            <i class='fas fa-edit'></i> Edit
                                        </a>
                                        <button class='btn btn-sm btn-danger' onclick='confirmDelete(" . $schedule['id'] . ", \"" . htmlspecialchars($schedule['degree_name']) . " - " . htmlspecialchars($schedule['batch_name']) . "\")'>
                                            <i class='fas fa-trash-alt'></i> Delete
                                        </button>
                                    </div>
                                  </td>";
                            echo "</tr>";
                        }
                        
                        if (!$timetableExists) {
                            echo "<tr><td colspan='4'>
                                <div class='empty-state'>
                                    <i class='fas fa-calendar-times'></i>
                                    <p>No timetables found. Click on 'Add Timetable' to create one.</p>
                                </div>
                            </td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- Modal for adding student timetable -->
    <div class="modal fade" id="addTimetableModal" tabindex="-1" aria-labelledby="addTimetableModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTimetableModalLabel">
                        <i class="fas fa-plus-circle me-2"></i> Add Student Timetable
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="degree_id" class="form-label">Degree Program</label>
                            <select name="degree_id" class="form-select" required>
                                <option value="">Select Degree</option>
                                <?php
                                // Fetch degrees from the database
                                $stmt = $pdo->query("SELECT * FROM degrees");
                                while ($degree = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='" . $degree['id'] . "'>" . htmlspecialchars($degree['degree_name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="batch_id" class="form-label">Batch</label>
                            <select name="batch_id" class="form-select" required>
                                <option value="">Select Batch</option>
                                <?php
                                // Fetch batches from the database
                                $stmt = $pdo->query("SELECT * FROM batches");
                                while ($batch = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='" . $batch['id'] . "'>" . htmlspecialchars($batch['batch_name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group mb-0">
                            <label for="sheet_link" class="form-label">Google Sheet Link</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fab fa-google"></i></span>
                                <input type="text" name="sheet_link" class="form-control" placeholder="https://docs.google.com/spreadsheets/d/..." required>
                            </div>
                            <small class="text-muted mt-2">
                                <i class="fas fa-info-circle"></i> Paste the shareable link to your Google Sheet
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_schedule" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Save Timetable
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for editing student timetable -->
    <div class="modal fade" id="editTimetableModal" tabindex="-1" aria-labelledby="editTimetableModalLabel" aria-hidden="true" <?php echo isset($_GET['edit']) ? 'data-bs-backdrop="static"' : ''; ?>>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTimetableModalLabel">
                        <i class="fas fa-edit me-2"></i> Edit Student Timetable
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="window.location.href='student_schedule.php'"></button>
                </div>
                <?php if ($edit_timetable): 
                    // Fetch the sheet link from the sheet_id
                    $googleSheetLink = "https://docs.google.com/spreadsheets/d/{$edit_timetable['sheet_id']}/edit";
                    
                    // Get degree and batch info for this timetable
                    $stmt = $pdo->prepare("SELECT degrees.id AS degree_id, batches.id AS batch_id 
                                        FROM student_timetables 
                                        JOIN degrees ON student_timetables.degree_id = degrees.id 
                                        JOIN batches ON student_timetables.batch_id = batches.id 
                                        WHERE student_timetables.id = :id");
                    $stmt->execute(['id' => $edit_timetable['id']]);
                    $timetable_info = $stmt->fetch(PDO::FETCH_ASSOC);
                ?>
                <form method="post">
                    <input type="hidden" name="timetable_id" value="<?= $edit_timetable['id'] ?>">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="degree_id" class="form-label">Degree Program</label>
                            <select name="degree_id" class="form-select" required>
                                <option value="">Select Degree</option>
                                <?php
                                // Fetch degrees from the database
                                $stmt = $pdo->query("SELECT * FROM degrees");
                                while ($degree = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $selected = ($degree['id'] == $edit_timetable['degree_id']) ? 'selected' : '';
                                    echo "<option value='" . $degree['id'] . "' " . $selected . ">" . htmlspecialchars($degree['degree_name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="batch_id" class="form-label">Batch</label>
                            <select name="batch_id" class="form-select" required>
                                <option value="">Select Batch</option>
                                <?php
                                // Fetch batches from the database
                                $stmt = $pdo->query("SELECT * FROM batches");
                                while ($batch = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $selected = ($batch['id'] == $edit_timetable['batch_id']) ? 'selected' : '';
                                    echo "<option value='" . $batch['id'] . "' " . $selected . ">" . htmlspecialchars($batch['batch_name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group mb-0">
                            <label for="sheet_link" class="form-label">Google Sheet Link</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fab fa-google"></i></span>
                                <input type="text" name="sheet_link" class="form-control" value="<?= $googleSheetLink ?>" placeholder="https://docs.google.com/spreadsheets/d/..." required>
                            </div>
                            <small class="text-muted mt-2">
                                <i class="fas fa-info-circle"></i> Paste the shareable link to your Google Sheet
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="student_schedule.php" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" name="update_schedule" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Update Timetable
                        </button>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Confirmation Dialog for Delete -->
    <div class="confirm-dialog" id="deleteConfirmDialog">
        <div class="confirm-dialog-content">
            <div class="confirm-dialog-header">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i> Confirm Delete</h5>
            </div>
            <div class="confirm-dialog-body">
                <p>Are you sure you want to delete the timetable for:</p>
                <p><strong id="deleteTimetableName"></strong>?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="confirm-dialog-footer">
                <button type="button" class="btn btn-outline-secondary" onclick="cancelDelete()">Cancel</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">
                    <i class="fas fa-trash-alt me-2"></i> Delete
                </a>
            </div>
        </div>
    </div>

    <script>
        // Show the edit modal automatically if edit parameter exists in URL
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_GET['edit'])): ?>
                var editModal = new bootstrap.Modal(document.getElementById('editTimetableModal'));
                editModal.show();
            <?php endif; ?>
        });

        // Delete confirmation functions
        function confirmDelete(id, name) {
            document.getElementById('deleteTimetableName').textContent = name;
            document.getElementById('confirmDeleteBtn').href = '?delete=' + id;
            document.getElementById('deleteConfirmDialog').classList.add('show');
        }

        function cancelDelete() {
            document.getElementById('deleteConfirmDialog').classList.remove('show');
        }
    </script>

<?php include 'includes/footer.php'; ?>