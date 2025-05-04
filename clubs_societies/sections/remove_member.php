<?php
session_start();
require '../../db_connection.php';

if (isset($_POST['club_member_id'])) {
    $stmt = $pdo->prepare("DELETE FROM club_members WHERE id = ?");
    $stmt->execute([$_POST['club_member_id']]);

    $_SESSION['flash_message'] = "Member removed successfully.";
    $_SESSION['flash_type'] = "success";
}
header('Location: ../view_members.php');
exit();
?>
