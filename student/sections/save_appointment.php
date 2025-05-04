<?php
session_start();
require '../../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $doctorId = $_POST['doctor_id'];
        $studentId = $_POST['student_id'];
        $availabilityId = $_POST['doctor_availability_id'];
        $reason = $_POST['reason'];

        // Check if the availability ID exists for the doctor
        $stmt = $pdo->prepare("SELECT * FROM doctor_availability WHERE id = ? AND doctor_id = ?");
        $stmt->execute([$availabilityId, $doctorId]);
        $availability = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$availability) {
            throw new Exception("The availability slot doesn't exist or does not belong to the specified doctor.");
        }

        // Check if the slot is already booked
        if ($availability['student_id'] != null) {
            throw new Exception("The slot is already booked by another student.");
        }

        // Check if the student already has an active appointment with this doctor
        $stmt = $pdo->prepare("SELECT * FROM medical_appointments WHERE student_id = ? AND doctor_id = ? AND status IN ('pending', 'accepted')");
        $stmt->execute([$studentId, $doctorId]);
        $existingAppointment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingAppointment) {
            throw new Exception("You already have an active appointment with this doctor. Please cancel or wait for the current appointment to be resolved.");
        }

        // Get availability details
        $doctorIdFromAvailability = $availability['doctor_id'];
        $dayOfWeek = $availability['day_of_week']; 
        $slotStart = $availability['slot_start'];
        $slotEnd = $availability['slot_end'];

        // Calculate the next occurrence of the day_of_week (from today)
        $today = new DateTime();
        $targetDate = clone $today;

        $days = ['Sunday' => 0, 'Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3, 'Thursday' => 4, 'Friday' => 5, 'Saturday' => 6];
        $targetDayNum = $days[$dayOfWeek];
        $currentDayNum = (int) $today->format('w');

        $daysToAdd = ($targetDayNum - $currentDayNum + 7) % 7;
        // If the slot is today and still in the future, keep it as today
        $targetDate->modify("+$daysToAdd days");

        $slotDate = $targetDate->format('Y-m-d'); // Final appointment_date

        // Combine slot_start and slot_end as a time range
        $timeRange = $slotStart . ' - ' . $slotEnd;

        // Update doctor_availability table to mark the slot as booked by the student
        $stmt = $pdo->prepare("UPDATE doctor_availability SET student_id = ? WHERE id = ?");
        $stmt->execute([$studentId, $availabilityId]);

        if ($stmt->rowCount() === 0) {
            throw new Exception("Failed to update doctor availability. The slot may already be taken or not exist.");
        }

        // Insert into medical_appointments table
        $stmt = $pdo->prepare("INSERT INTO medical_appointments (student_id, doctor_availability_id, doctor_id, appointment_date, appointment_time_range, reason, created_at, status) VALUES (?, ?, ?, ?, ?, ?, NOW(), 'pending')");
        $stmt->execute([$studentId, $availabilityId, $doctorIdFromAvailability, $slotDate, $timeRange, $reason]);

        if ($stmt->rowCount() === 0) {
            throw new Exception("Failed to create medical appointment.");
        }

        // Success message
        $_SESSION['message'] = ['text' => 'Appointment booked successfully! It is now pending confirmation.', 'type' => 'success'];
        echo json_encode(['success' => true, 'message' => 'Appointment booked successfully!']);
    } catch (Exception $e) {
        // Error message
        $_SESSION['message'] = ['text' => 'An error occurred: ' . $e->getMessage(), 'type' => 'error'];
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
}
?>
