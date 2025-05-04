<?php
session_start();
require_once('../../db_connection.php');

$student_id = $_SESSION['student_uid'] ?? null;
$event_id = $_POST['event_id'] ?? null;

if (!$student_id || !$event_id) {
    header("Location: ../career_guidance.php");
    exit();
}

// Check if already favorited
$check = $pdo->prepare("SELECT id FROM favorite_events WHERE student_id = ? AND event_id = ?");
$check->execute([$student_id, $event_id]);

if ($check->rowCount() > 0) {
    // Remove from favorites
    $delete = $pdo->prepare("DELETE FROM favorite_events WHERE student_id = ? AND event_id = ?");
    $delete->execute([$student_id, $event_id]);
} else {
    // Add to favorites
    $insert = $pdo->prepare("INSERT INTO favorite_events (student_id, event_id) VALUES (?, ?)");
    $insert->execute([$student_id, $event_id]);
}

header("Location: ../career_guidance.php");
exit();
