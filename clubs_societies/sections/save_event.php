<?php
session_start();
require '../../db_connection.php';

if (!isset($_SESSION['club_admin_uid'])) {
    header('Location: ../login/login.php');
    exit();
}

$club_id = $_SESSION['club_admin_uid'];
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$event_date = $_POST['event_date'] ?? '';
$start_time = $_POST['start_time'] ?? '';
$end_time = $_POST['end_time'] ?? '';
$location = $_POST['location'] ?? '';
$access_type = $_POST['access_type'] ?? '';
$event_id = $_POST['event_id'] ?? '';
$max_attendees = isset($_POST['max_attendees']) && $_POST['max_attendees'] !== '' 
    ? intval($_POST['max_attendees']) 
    : null;

// Basic validation
if (empty($title) || empty($description) || empty($event_date) || empty($location) || empty($access_type)) {
    $_SESSION['flash_message'] = "All fields are required.";
    $_SESSION['flash_type'] = "danger";

    // Redirect accordingly
    $redirect = !empty($event_id) ? "../view_event.php?id=$event_id" : "../manage_events.php";
    header("Location: $redirect");
    exit();
}

if (!in_array($access_type, ['members', 'public'])) {
    $_SESSION['flash_message'] = "Invalid access type.";
    $_SESSION['flash_type'] = "danger";
    $redirect = !empty($event_id) ? "../view_event.php?id=$event_id" : "../manage_events.php";
    header("Location: $redirect");
    exit();
}

// Check if it's an update or new insert
if (!empty($event_id)) {
    // UPDATE existing event
    $stmt = $pdo->prepare("UPDATE club_events 
        SET title = ?, description = ?, event_date = ?, start_time = ?, end_time = ?, location = ?, access_type = ?, max_attendees = ?
        WHERE id = ? AND club_id = ?");
    $success = $stmt->execute([$title, $description, $event_date, $start_time, $end_time, $location, $access_type, $max_attendees, $event_id, $club_id]);

    $_SESSION['flash_message'] = $success ? "Event updated successfully." : "Failed to update event.";
    $_SESSION['flash_type'] = $success ? "success" : "danger";

    header("Location: ../view_event.php?id=$event_id");
    exit();

} else {
    // INSERT new event
    $stmt = $pdo->prepare("INSERT INTO club_events 
        (club_id, title, description, event_date, start_time, end_time, location, access_type, max_attendees) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $success = $stmt->execute([$club_id, $title, $description, $event_date, $start_time, $end_time, $location, $access_type, $max_attendees]);

    $_SESSION['flash_message'] = $success ? "Event added successfully." : "Failed to add event.";
    $_SESSION['flash_type'] = $success ? "success" : "danger";

    header("Location: ../manage_events.php");
    exit();
}
?>
