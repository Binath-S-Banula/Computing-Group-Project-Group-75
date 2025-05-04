<?php
session_start();
require '../../db_connection.php';

if (!isset($_POST['event_id'], $_POST['student_id'])) {
    $_SESSION['flash_message'] = 'Invalid request.';
    $_SESSION['flash_type'] = 'danger';
    header("Location: ../manage_events.php");
    exit();
}

$event_id = $_POST['event_id'];
$student_id = $_POST['student_id'];

// Delete registration
$stmt = $pdo->prepare("DELETE FROM club_event_registrations WHERE event_id = ? AND student_id = ?");
$stmt->execute([$event_id, $student_id]);

$_SESSION['flash_message'] = 'Student removed from event.';
$_SESSION['flash_type'] = 'success';
header("Location: ../view_event.php?id=$event_id");
exit();
