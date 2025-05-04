<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nlink_cp";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data from the form
$student_id = $_POST['student_id'];
$student_name = $_POST['student_name'];
$batch_number = $_POST['batch_number'];
$faculty = $_POST['faculty'];
$lecturer = $_POST['lecturer'];
$reason = $_POST['reason'];

// Insert into appointments table
$sql = "INSERT INTO appointments (student_id, student_name, batch_number, faculty, lecturer, reason)
        VALUES ('$student_id', '$student_name', '$batch_number', '$faculty', '$lecturer', '$reason')";

if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Appointment submitted successfully!'); window.location.href='view-appointments.php';</script>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
