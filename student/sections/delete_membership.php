<?php
session_start();
require '../../db_connection.php';

if (!isset($_SESSION['student_uid']) || !isset($_POST['club_member_id'])) {
    header('Location: ../clubs_societies.php');
    exit();
}

$club_member_id = $_POST['club_member_id'];

$stmt = $pdo->prepare("DELETE FROM club_members WHERE id = ?");
if ($stmt->execute([$club_member_id])) {
    $_SESSION['flash_message'] = "Successfully left the club.";
    $_SESSION['flash_type'] = "success";
} else {
    $_SESSION['flash_message'] = "Failed to leave the club.";
    $_SESSION['flash_type'] = "danger";
}

header('Location: ../clubs_societies.php');
exit();
?>
