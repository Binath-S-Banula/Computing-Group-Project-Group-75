<?php
session_start();
require '../../db_connection.php';

if (!isset($_SESSION['lecturer_uid'])) {
    header("Location: ../lecturer_login/login.php");
    exit;
}

$lecturer_uid = $_SESSION['lecturer_uid'];

$stmt = $pdo->prepare("SELECT sheet_id FROM lecturers WHERE id = ?");
$stmt->execute([$lecturer_uid]);
$sheet_id = $stmt->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['date'])) {
    $date = $_POST['date'];
    $start_time = date("g:i A", strtotime($_POST['start_time']));
    $end_time = date("g:i A", strtotime($_POST['end_time']));
    $subject_id = $_POST['subject_id'];
    $reason = $_POST['reason'];
    $day = date("l", strtotime($date));

    $query = "INSERT INTO timetable_proposals 
        (lecturer_id, sheet_id, date, day, start_time, end_time, subject_id, reason, status) 
        VALUES (:lecturer_uid, :sheet_id, :date, :day, :start_time, :end_time, :subject_id, :reason, 'pending')";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':lecturer_uid', $lecturer_uid);
    $stmt->bindParam(':sheet_id', $sheet_id);
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':day', $day);
    $stmt->bindParam(':start_time', $start_time);
    $stmt->bindParam(':end_time', $end_time);
    $stmt->bindParam(':subject_id', $subject_id);
    $stmt->bindParam(':reason', $reason);
    $stmt->execute();

    header("Location: ../submit_success.php");
    exit();
}
?>
