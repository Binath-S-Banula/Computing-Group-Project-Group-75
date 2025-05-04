<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nlink_cp";

// Create DB connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check DB connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get appointments
$sql = "SELECT * FROM appointments ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Appointments</title>
    <link rel="stylesheet" href="appointment-form.css">
</head>
<body>

<main>
    <div class="form-container">
        <h2>All Submitted Appointments</h2>

        <!-- Go Back Button -->
        <a href="appointment-form.php" class="go-back-btn">← Go Back to Booking Form</a>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Batch</th>
                    <th>Faculty</th>
                    <th>Lecturer</th>
                    <th>Reason</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td data-label='ID'>{$row['id']}</td>
                                <td data-label='Student ID'>{$row['student_id']}</td>
                                <td data-label='Student Name'>{$row['student_name']}</td>
                                <td data-label='Batch'>{$row['batch_number']}</td>
                                <td data-label='Faculty'>{$row['faculty']}</td>
                                <td data-label='Lecturer'>{$row['lecturer']}</td>
                                <td data-label='Reason'>{$row['reason']}</td>
                                <td data-label='Actions'>
                                    <form method='POST' action='delete-appointment.php' onsubmit='return confirm(\"Are you sure you want to delete this appointment?\");'>
                                        <input type='hidden' name='id' value='{$row['id']}'>
                                        <button type='submit' class='delete-btn'>Delete</button>
                                    </form>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No appointments found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</main>

</body>
</html>

<?php
$conn->close();
?>
