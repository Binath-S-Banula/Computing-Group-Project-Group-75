<?php 
$host = "localhost";
$dbname = "nsbm_career_guidance";
$username = "root";
$password = "";

$conn = mysqli_connect($host, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$fullName = $_POST['FullName'];
$email = $_POST['EmailAddress'];
$phone = $_POST['phone'];
$message = $_POST['message'];

$insertQuery = "INSERT INTO cv_uploads (full_name, email, phone, message) VALUES ('$fullName', '$email', '$phone' , '$message')";    
mysqli_query($conn, $insertQuery);    

include 'thank-you.php'; // Include the thank you page after successful submission
mysqli_close($conn);
?>