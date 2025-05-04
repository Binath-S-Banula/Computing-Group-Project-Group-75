<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nlink_cp";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch available lecturer schedules from the database
$sql = "SELECT * FROM doctor_schedule ORDER BY doctor_name, day_of_week, time_range";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Booking Form</title>
    <link rel="stylesheet" href="appointment-form.css">
</head>
<body>

<main>
    <div class="form-container">
        <h2>Submit Your Doctor Appointment Booking Details</h2>
        <form action="submit-dr-appointment.php" method="POST">
            <div class="form-group">
                <label for="student-id">Student ID</label>
                <input type="text" id="student-id" name="student_id" placeholder="Enter your Student ID" required>
            </div>
            
            <div class="form-group">
                <label for="student-name">Student Name</label>
                <input type="text" id="student-name" name="student_name" placeholder="Enter your full name" required>
            </div>
            
            <div class="form-group">
                <label for="batch-number">Batch Number</label>
                <input type="text" id="batch-number" name="batch_number" placeholder="Enter your batch number" required>
            </div>

            <div class="form-group">
                <label for="faculty">Faculty</label>
                <select id="faculty" name="faculty" required>
                    <option value="">Select your Faculty</option>
                    <option value="Faculty of Business">Faculty of Business</option>
                    <option value="Faculty of Computing">Faculty of Computing</option>
                    <option value="Faculty of Engineering">Faculty of Engineering</option>
                    <option value="Faculty of Design">Faculty of Design</option>
                </select>
            </div>

            <div class="form-group">
                <label for="lecturer">Select Doctor</label>
                <select id="lecturer" name="lecturer" required>
                    <option value="">Choose a Doctor</option>
                    <?php
                    // Populate the select options with available doctor schedules
                    while ($row = $result->fetch_assoc()) {
                        // Combine doctor name, schedule day, time, and room into one string
                        $lecturer_info = $row['doctor_name'] . ' - ' . $row['day_of_week'] . ' ' . $row['time_range'] . ' - Room: ' . $row['room'];
                        echo '<option value="' . $lecturer_info . '">' . $lecturer_info . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="reason">Reason for Appointment</label>
                <textarea id="reason" name="reason" placeholder="Enter the reason for your appointment" required></textarea>
            </div>

            <button type="submit" class="submit-btn">Submit Appointment</button>
        </form>
    </div>
</main>

</body>
</html>

<?php
$conn->close();
?>
