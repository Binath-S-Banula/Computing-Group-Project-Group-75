<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nlink_cp";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $sql = "DELETE FROM doctor_appointments WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Doctor appointment deleted.'); window.location.href='view-doctor-appointments.php';</script>";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

$conn->close();
?>
