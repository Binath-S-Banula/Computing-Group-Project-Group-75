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

    $sql = "DELETE FROM appointments WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Appointment deleted successfully'); window.location.href='view-appointments.php';</script>";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

$conn->close();
?>
