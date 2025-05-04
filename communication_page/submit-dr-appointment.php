<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nlink_cp";

// DB connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check DB connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$student_id = $_POST['student_id'];
$student_name = $_POST['student_name'];
$batch_number = $_POST['batch_number'];
$faculty = $_POST['faculty'];
$doctor = $_POST['doctor']; // Using doctor field
$reason = $_POST['reason'];

// Insert into doctor_appointments table
$sql = "INSERT INTO doctor_appointments (student_id, student_name, batch_number, faculty, doctor, reason)
        VALUES ('$student_id', '$student_name', '$batch_number', '$faculty', '$doctor', '$reason')";

if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Doctor appointment submitted successfully!'); window.location.href='view-doctor-appointments.php';</script>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
