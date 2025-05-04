<?php
session_start();
require '../../db_connection.php';

if (!isset($_SESSION['student_uid']) || !isset($_POST['club_id'])) {
    header('Location: ../clubs_societies.php');
    exit();
}

$student_id = $_SESSION['student_uid'];
$club_id = $_POST['club_id'];

// Check if already a member of the club
$stmt = $pdo->prepare("SELECT status FROM club_members WHERE club_id = ? AND student_id = ?");
$stmt->execute([$club_id, $student_id]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);

if ($member) {
    // If the user is blacklisted prevent joining 
    if ($member['status'] === 'blacklisted') {
        $_SESSION['flash_message'] = "You are blacklisted from this club and cannot join it.";
        $_SESSION['flash_type'] = "danger";
    } else {
        $_SESSION['flash_message'] = "You are already a member of this club.";
        $_SESSION['flash_type'] = "info";
    }
} else {
    // Insert new membership 
    $stmt = $pdo->prepare("INSERT INTO club_members (club_id, student_id, status) VALUES (?, ?, 'active')");
    if ($stmt->execute([$club_id, $student_id])) {
        $_SESSION['flash_message'] = "Successfully joined the club!";
        $_SESSION['flash_type'] = "success";
    } else {
        $_SESSION['flash_message'] = "Failed to join the club.";
        $_SESSION['flash_type'] = "danger";
    }
}

header('Location: ../clubs_societies.php');
exit();
?>
