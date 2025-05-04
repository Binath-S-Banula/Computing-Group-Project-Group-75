<?php
session_start();
require '../../db_connection.php';

if (!isset($_POST['event_id'], $_POST['club_id'])) {
    $_SESSION['flash_message'] = 'Invalid request.';
    $_SESSION['flash_type'] = 'danger';
    header("Location: ../view_club.php");
    exit();
}

$event_id = $_POST['event_id'];
$club_id = $_POST['club_id'];
$student_id = $_SESSION['student_uid'] ?? null;

if (!$student_id) {
    $_SESSION['flash_message'] = 'Login required.';
    $_SESSION['flash_type'] = 'danger';
    header("Location: ../view_club.php?id=$club_id");
    exit();
}

$stmt = $pdo->prepare("DELETE FROM club_event_registrations WHERE event_id = ? AND student_id = ?");
$stmt->execute([$event_id, $student_id]);

$_SESSION['flash_message'] = 'Registration cancelled.';
$_SESSION['flash_type'] = 'info';
header("Location: ../view_club.php?id=$club_id");
exit();
