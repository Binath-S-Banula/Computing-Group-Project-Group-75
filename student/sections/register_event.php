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
    $_SESSION['flash_message'] = 'Please log in to register.';
    $_SESSION['flash_type'] = 'danger';
    header("Location: ../view_club.php?id=$club_id");
    exit();
}

// Check if already registered
$stmt = $pdo->prepare("SELECT * FROM club_event_registrations WHERE event_id = ? AND student_id = ?");
$stmt->execute([$event_id, $student_id]);
if ($stmt->fetch()) {
    $_SESSION['flash_message'] = 'Already registered.';
    $_SESSION['flash_type'] = 'warning';
    header("Location: ../view_club.php?id=$club_id");
    exit();
}

// Check if max attendees reached
$stmt = $pdo->prepare("SELECT max_attendees FROM club_events WHERE id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch();

if (!$event) {
    $_SESSION['flash_message'] = 'Event not found.';
    $_SESSION['flash_type'] = 'danger';
    header("Location: ../view_club.php?id=$club_id");
    exit();
}

$stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM club_event_registrations WHERE event_id = ?");
$stmt->execute([$event_id]);
$count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

if ($count >= $event['max_attendees']) {
    $_SESSION['flash_message'] = 'Event is full.';
    $_SESSION['flash_type'] = 'danger';
    header("Location: ../view_club.php?id=$club_id");
    exit();
}

// Register student
$stmt = $pdo->prepare("INSERT INTO club_event_registrations (event_id, student_id) VALUES (?, ?)");
$stmt->execute([$event_id, $student_id]);

$_SESSION['flash_message'] = 'Successfully registered!';
$_SESSION['flash_type'] = 'success';
header("Location: ../view_club.php?id=$club_id");
exit();
