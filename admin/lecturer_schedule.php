<?php
// Start session for displaying success message
session_start();
require '../db_connection.php';
// Initialize message variable
$message = '';
$messageType = '';

// When form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Extract data from form
    $name = $_POST['name'];
    $sheetLink = $_POST['sheet_link'];
    
    // Extract the sheet_id from the link
    preg_match('/\/d\/(.*)\/edit/', $sheetLink, $matches);
    $sheet_id = isset($matches[1]) ? $matches[1] : '';

    // Save the timetable in the database
    try {
        $stmt = $pdo->prepare("UPDATE lecturers SET sheet_id = :sheet_id WHERE name = :name");
        $stmt->execute(['sheet_id' => $sheet_id, 'name' => $name]);
        
        // Set success message
        $_SESSION['message'] = "Timetable saved successfully!";
        $_SESSION['messageType'] = 'success';

        // Redirect to prevent resubmission
        header("Location: lecturer_schedule.php");
        exit;
    } catch (PDOException $e) {
        // Set error message
        $_SESSION['message'] = "Failed to save timetable: " . $e->getMessage();
        $_SESSION['messageType'] = 'danger';
        header("Location: lecturer_schedule.php");
        exit;
    }
}
?>

    <?php include 'includes/header.php'; ?>

    <link rel="stylesheet" href="css/lecturer_schedule.css">

    <script>
        // Auto-hide the success message after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var message = document.getElementById('message');
                if (message) {
                    const bsAlert = new bootstrap.Alert(message);
                    bsAlert.close();
                }
            }, 5000);
        });
    </script>

    <!-- Dashboard Header -->
    <div class="dashboard-header">
        
        <!-- Display success or error message -->
        <?php if (isset($_SESSION['message'])): ?>
            <div id="message" class="alert alert-<?= $_SESSION['messageType'] ?> alert-dismissible fade show" role="alert">
                <?php if ($_SESSION['messageType'] === 'success'): ?>
                    <i class="fas fa-check-circle"></i>
                <?php else: ?>
                    <i class="fas fa-exclamation-circle"></i>
                <?php endif; ?>
                <?= $_SESSION['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['messageType']); ?>
        <?php endif; ?>

        <!-- Action Bar with Search and Drive Button -->
        <div class="action-bar d-flex justify-content-between align-items-center">
            <div>
                <h3 class="dashboard-title">Lecturer Schedule Management</h3>
                <p class="dashboard-subtitle">Manage and assign timetables to lecturers across different faculties</p>
            </div>
            <a href="https://drive.google.com/drive/folders/1HzQcJnm9OFMPZofN9ua0YKoJcFMUZqlq" target="_blank" class="btn btn-secondary">
                <i class="fas fa-folder-open"></i> Open Google Drive Folder
            </a>
        </div>

    </div>

    <!-- Lecturers Table Card -->
    <div class="card">
        <div class="table-responsive">
            <table class="table" id="lecturersTable">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="25%">Lecturer Name</th>
                        <th width="15%">Faculty</th>
                        <th width="25%">Status</th>
                        <th width="30%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loop through  lecturers and display them -->
                    <?php
                    // Fetch lecturers from the database and display
                    $stmt = $pdo->query("SELECT * FROM lecturers ORDER BY faculty_id ASC, name ASC");
                    $counter = 1; // Initialize counter for sequential numbering
                    $rowCount = $stmt->rowCount();
                    
                    if ($rowCount > 0) {
                        while ($lecturer = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            // Fetch the faculty name using faculty_id
                            $facultyStmt = $pdo->prepare("SELECT faculty_name FROM faculties WHERE id = :faculty_id");
                            $facultyStmt->execute(['faculty_id' => $lecturer['faculty_id']]);
                            $faculty = $facultyStmt->fetch(PDO::FETCH_ASSOC);
                            $facultyName = $faculty ? $faculty['faculty_name'] : 'N/A';

                            $googleSheetLink = $lecturer['sheet_id'] ? "https://docs.google.com/spreadsheets/d/{$lecturer['sheet_id']}/edit" : '';
                            echo "<tr>";
                            echo "<td>" . $counter++ . "</td>"; // Display sequential number
                            echo "<td>" . htmlspecialchars($lecturer['name']) . "</td>";
                            echo "<td><span class='faculty-badge'>" . htmlspecialchars($facultyName) . "</span></td>";
                            echo "<td>";
                            
                            if ($lecturer['sheet_id']) {
                                echo "<span class='status-indicator status-active'></span> Timetable Assigned";
                            } else {
                                echo "<span class='status-indicator status-warning'></span> No Timetable";
                            }
                            
                            echo "</td>";
                            echo "<td class='action-buttons'>";

                            if ($lecturer['sheet_id']) {
                                // If sheet_id exists, show "Open Schedule" button
                                echo "<a href='{$googleSheetLink}' class='btn btn-primary btn-sm' target='_blank'>";
                                echo "<i class='fas fa-calendar-alt'></i> View</a>";
                                
                                // Add "Edit" button
                                echo "<button class='btn btn-secondary btn-sm ms-1' data-bs-toggle='modal' data-bs-target='#addTimetableModal' ";
                                echo "data-lecturer-name='" . htmlspecialchars($lecturer['name']) . "' ";
                                echo "data-sheet-link='{$googleSheetLink}'>";
                                echo "<i class='fas fa-edit'></i> Edit</button>";
                            } else {
                                // If no sheet_id, show "Add Timetable" button to trigger modal
                                echo "<button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#addTimetableModal' ";
                                echo "data-lecturer-name='" . htmlspecialchars($lecturer['name']) . "'>";
                                echo "<i class='fas fa-plus-circle'></i> Add Timetable</button>";
                            }

                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        // If no lecturers found
                        echo "<tr><td colspan='5' class='empty-state'>";
                        echo "<i class='fas fa-user-slash'></i>";
                        echo "<p>No lecturers found in the database.</p>";
                        echo "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for adding timetable -->
    <div class="modal fade" id="addTimetableModal" tabindex="-1" aria-labelledby="addTimetableModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTimetableModalLabel">
                        <i class="fas fa-calendar-plus"></i> Upload Lecturer Timetable
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="lecturerName" class="form-label">Lecturer Name</label>
                            <input type="text" id="lecturerName" name="name" class="form-control" required readonly>
                        </div>
                        <div class="form-group mb-0">
                            <label for="sheetLink" class="form-label">Google Sheet Shareable Link</label>
                            <input type="text" id="sheetLink" name="sheet_link" class="form-control" placeholder="https://docs.google.com/spreadsheets/d/..." required>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Make sure the Google Sheet has edit permissions available.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Timetable
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>



<script>
    // Fill modal with lecturer name and sheet link (if exists)
    document.addEventListener('DOMContentLoaded', function() {
        var addTimetableModal = document.getElementById('addTimetableModal');
        if (addTimetableModal) {
            addTimetableModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var lecturerName = button.getAttribute('data-lecturer-name');
                var sheetLink = button.getAttribute('data-sheet-link') || '';
                
                var modalTitle = this.querySelector('.modal-title');
                var nameInput = document.getElementById('lecturerName');
                var linkInput = document.getElementById('sheetLink');
                
                nameInput.value = lecturerName;
                linkInput.value = sheetLink;
                
                if (sheetLink) {
                    modalTitle.innerHTML = '<i class="fas fa-edit"></i> Edit Lecturer Timetable';
                } else {
                    modalTitle.innerHTML = '<i class="fas fa-calendar-plus"></i> Upload Lecturer Timetable';
                }
            });
        }
        
        // Search functionality
        var searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                var filter = this.value.toLowerCase();
                var table = document.getElementById('lecturersTable');
                var rows = table.getElementsByTagName('tr');
                
                for (var i = 1; i < rows.length; i++) { // Start from 1 to skip header
                    var lecturerName = rows[i].getElementsByTagName('td')[1];
                    var facultyName = rows[i].getElementsByTagName('td')[2];
                    
                    if (lecturerName && facultyName) {
                        var txtLecturer = lecturerName.textContent || lecturerName.innerText;
                        var txtFaculty = facultyName.textContent || facultyName.innerText;
                        
                        if (txtLecturer.toLowerCase().indexOf(filter) > -1 || txtFaculty.toLowerCase().indexOf(filter) > -1) {
                            rows[i].style.display = "";
                        } else {
                            rows[i].style.display = "none";
                        }
                    }
                }
            });
        }
    });
</script>

<?php include 'includes/footer.php'; ?>