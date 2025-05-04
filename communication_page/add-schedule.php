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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $lecturer_name = $_POST['lecturer_name'];
    $day_of_week = $_POST['day_of_week'];
    $time_range = $_POST['time_range'];
    $room = $_POST['room'];

    // Insert the schedule into the database
    $stmt = $conn->prepare("INSERT INTO lecturer_schedule (lecturer_name, day_of_week, time_range, room) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $lecturer_name, $day_of_week, $time_range, $room);

    if ($stmt->execute()) {
        echo "<div class='success-message'>Schedule added successfully!</div>";
        // Redirect back to the lecture schedule page
        header("Location: lecture-schedule.php");
        exit();
    } else {
        echo "<div class='error-message'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Lecturer Schedule</title>
    <link rel="stylesheet" href="lecture-schedule.css">
</head>
<body>

<main>
    <div class="form-container">
        <h2>Add New Lecturer Schedule</h2>
        
        <form action="add-schedule.php" method="POST">
            <div class="form-group">
                <label for="lecturer_name">Lecturer Name</label>
                <input type="text" id="lecturer_name" name="lecturer_name" placeholder="Enter Lecturer's Name" required>
            </div>

            <div class="form-group">
                <label for="day_of_week">Day of the Week</label>
                <select id="day_of_week" name="day_of_week" required>
                    <option value="">Select Day</option>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                </select>
            </div>

            <div class="form-group">
                <label for="time_range">Time Range</label>
                <input type="text" id="time_range" name="time_range" placeholder="e.g. 9:00 AM - 12:00 PM" required>
            </div>

            <div class="form-group">
                <label for="room">Room Number</label>
                <input type="text" id="room" name="room" placeholder="Enter Room Number" required>
            </div>

            <button type="submit" class="submit-btn">Add Schedule</button>
        </form>
    </div>
</main>

</body>
</html>
