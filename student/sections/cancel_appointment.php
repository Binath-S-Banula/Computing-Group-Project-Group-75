<?php
session_start();
require '../../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get POST data
        $doctorId = $_POST['doctor_id'];
        $studentId = $_POST['student_id'];
        $slotId = $_POST['slot_id']; 

        // Check if the appointment exists for the given student and doctor
        $stmt = $pdo->prepare("SELECT * FROM medical_appointments WHERE doctor_availability_id = ? AND student_id = ? AND status IN ('pending', 'approved')");
        $stmt->execute([$slotId, $studentId]);
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$appointment) {
            throw new Exception("Appointment not found or cannot be canceled.");
        }

        // Update the appointment status to 'cancelled'
        $stmt = $pdo->prepare("UPDATE medical_appointments SET status = 'cancelled' WHERE id = ?");
        $stmt->execute([$appointment['id']]);

        if ($stmt->rowCount() === 0) {
            throw new Exception("Failed to cancel the appointment.");
        }

        // Also update the doctor availability to nullify the student ID
        $stmt = $pdo->prepare("UPDATE doctor_availability SET student_id = NULL WHERE id = ?");
        $stmt->execute([$slotId]);

        if ($stmt->rowCount() === 0) {
            throw new Exception("Failed to update the doctor availability.");
        }

        echo json_encode(['success' => true, 'message' => 'Appointment cancelled successfully.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
