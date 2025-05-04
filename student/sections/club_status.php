<?php
session_start();
require '../../db_connection.php';

if (!isset($_SESSION['student_uid']) || !isset($_POST['club_member_id']) || !isset($_POST['new_status'])) {
    header('Location: ../clubs_societies.php');
    exit();
}

$club_member_id = $_POST['club_member_id'];
$new_status = $_POST['new_status'];

$stmt = $pdo->prepare("UPDATE club_members SET status = ? WHERE id = ?");
if ($stmt->execute([$new_status, $club_member_id])) {
    $_SESSION['flash_message'] = "Status updated successfully!";
    $_SESSION['flash_type'] = "success";
} else {
    $_SESSION['flash_message'] = "Failed to update status.";
    $_SESSION['flash_type'] = "danger";
}

header('Location: ../clubs_societies.php');
exit();
?>
