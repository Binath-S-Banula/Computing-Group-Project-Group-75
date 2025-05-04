<?php
require '../../db_connection.php';

header('Content-Type: text/plain');

try {
    // Get current date and time
    date_default_timezone_set('Asia/Colombo');
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');
    
    // Fetch appointments that need to be updated
    $stmt = $pdo->prepare("
        SELECT ma.id, ma.doctor_availability_id, ma.appointment_date, da.slot_end
        FROM medical_appointments ma
        JOIN doctor_availability da ON ma.doctor_availability_id = da.id
        WHERE ma.status IN ('pending', 'approved')
        AND (
            ma.appointment_date < :currentDate OR
            (ma.appointment_date = :currentDate AND da.slot_end <= :currentTime)
        )
    ");
    $stmt->execute([
        'currentDate' => $currentDate,
        'currentTime' => $currentTime
    ]);

    // Fetch all appointments that need to be updated
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($appointments) === 0) {
        echo 'No appointments need updating.'; 
        exit;
    }

    // Loop through the appointments and update them
    foreach ($appointments as $appt) {
        $appointmentId = $appt['id'];
        $availabilityId = $appt['doctor_availability_id'];

        // Update the status to disabled
        $updateStmt = $pdo->prepare("UPDATE medical_appointments SET status = 'disabled' WHERE id = ?");
        $updateStmt->execute([$appointmentId]);

        //  Set student_id = NULL in doctor_availability
        if ($availabilityId) {
            $clearStmt = $pdo->prepare("UPDATE doctor_availability SET student_id = NULL WHERE id = ?");
            $clearStmt->execute([$availabilityId]);
        }
    }

 //success message
    echo 'Appointments updated successfully.'; 
    exit;
} catch (PDOException $e) {
    echo 'Database Error: ' . $e->getMessage();
    exit;
} catch (Exception $e) {
    echo 'General Error: ' . $e->getMessage();
    exit;
}
?>
