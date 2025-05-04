<?php
session_start();
require '../../db_connection.php';

if (!isset($_SESSION['club_admin_uid'])) {
    header('Location: ../login/login.php');
    exit();
}

$event_id = $_POST['event_id'] ?? '';
$club_id = $_SESSION['club_admin_uid'];

if ($event_id) {
    $stmt = $pdo->prepare("UPDATE club_events SET status = 'cancelled' WHERE id = ? AND club_id = ?");
    $success = $stmt->execute([$event_id, $club_id]);

    $_SESSION['flash_message'] = $success ? "Event cancelled successfully." : "Failed to cancel event.";
    $_SESSION['flash_type'] = $success ? "success" : "danger";
} else {
    $_SESSION['flash_message'] = "Invalid event ID.";
    $_SESSION['flash_type'] = "danger";
}

header("Location: ../manage_events.php");
exit();
