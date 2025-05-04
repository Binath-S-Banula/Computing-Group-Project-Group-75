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

// Fetch all lecture schedules from the database
$sql = "SELECT * FROM lecturer_schedule ORDER BY day_of_week, time_range";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecture Schedule</title>
    <link rel="stylesheet" href="lecture-schedule.css">
</head>
<body>

<main>
    <div class="schedule-container">
        <h2>Lecture Schedule</h2>
        
        <!-- Add Schedule Button -->
        <div class="add-schedule-btn-container">
            <a href="add-schedule.php" class="add-schedule-btn">Add New Schedule</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Lecturer Name</th>
                    <th>Day</th>
                    <th>Time</th>
                    <th>Room</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    // Output data for each row
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row['lecturer_name'] . "</td>
                                <td>" . $row['day_of_week'] . "</td>
                                <td>" . $row['time_range'] . "</td>
                                <td>" . $row['room'] . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No schedules available</td></tr>";
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
