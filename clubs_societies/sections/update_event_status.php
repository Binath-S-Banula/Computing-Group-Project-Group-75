<?php
session_start();
require '../../db_connection.php'; 

date_default_timezone_set('Asia/Colombo'); 
$now = date('Y-m-d H:i:s');

$stmt = $pdo->prepare("UPDATE club_events SET status = 'ended' 
WHERE status = 'upcoming' AND CONCAT(event_date, ' ', end_time) < ?");
$success = $stmt->execute([$now]);

if ($success) {
    $_SESSION['flash_message'] = "Expired Events updated successfully.";
    $_SESSION['flash_type'] = "success";
} else {
    $_SESSION['flash_message'] = "Failed to update event statuses.";
    $_SESSION['flash_type'] = "danger";
}

header("Location: ../manage_events.php");
exit();
