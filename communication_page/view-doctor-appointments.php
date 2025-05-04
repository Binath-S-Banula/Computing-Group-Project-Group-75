<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nlink_cp";

// DB connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM doctor_appointments ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Appointments</title>
    <link rel="stylesheet" href="appointment-form.css">
</head>
<body>

<main>
    <div class="form-container">
        <h2>Submitted Doctor Appointments</h2>
        <a href="appointment-form.php" class="go-back-btn">← Back to Form</a>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Batch</th>
                    <th>Faculty</th>
                    <th>Doctor</th>
                    <th>Reason</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['student_id']}</td>
                            <td>{$row['student_name']}</td>
                            <td>{$row['batch_number']}</td>
                            <td>{$row['faculty']}</td>
                            <td>{$row['doctor']}</td>
                            <td>{$row['reason']}</td>
                            <td>
                                <form action='delete-doctor-appointment.php' method='POST' onsubmit='return confirm(\"Delete this appointment?\");'>
                                    <input type='hidden' name='id' value='{$row['id']}'>
                                    <button type='submit' class='delete-btn'>Delete</button>
                                </form>
                            </td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No doctor appointments found.</td></tr>";
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
