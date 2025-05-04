<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="css/medical_center.css">
<?php
// Fetch active doctors
$stmt = $pdo->prepare("SELECT id, name, specialization FROM doctors WHERE is_active = 1");
$stmt->execute();
$doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Define placeholder specialization icons
$specialization_icons = [
    'Cardiology' => 'bi-heart-pulse',
    'Neurology' => 'bi-brain',
    'Pediatrics' => 'bi-emoji-smile',
    'Orthopedics' => 'bi-bandaid',
    'Dermatology' => 'bi-shield-plus',
    'Ophthalmology' => 'bi-eye',
    'Psychiatry' => 'bi-chat-square-heart',
    'Dentistry' => 'bi-emoji-smile',
    'General Medicine' => 'bi-clipboard2-pulse',
    'default' => 'bi-clipboard2-pulse'
];
?>


    <div class="dashboard-title">
        <h2>Meet The Doctor</h2>
        <p >Schedule an appointment with the Doctor and see your appointment history</p>
    </div>

    <?php if (count($doctors) === 0): ?>
        <div class="no-doctors-alert">
            <i class="bi bi-exclamation-circle fs-2 d-block mb-3"></i>
            <p class="mb-0">No active doctors available at the moment. Please check back later.</p>
        </div>


    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($doctors as $doc): ?>
                <?php
                // Get icon for the doctor's specialization
                $icon = $specialization_icons['default'];
                foreach ($specialization_icons as $spec => $spec_icon) {
                    if (stripos($doc['specialization'], $spec) !== false) {
                        $icon = $spec_icon;
                        break;
                    }
                }
                ?>
                <div class="col-lg-4 col-md-6">
                    <div class="doctor-card">
                        
                        <div class="card-body">
                            <div>
                                <h3 class="doctor-name">Dr. <?= htmlspecialchars($doc['name']) ?></h3>
                            </div>
                            <div class="specialization-badge">
                                <i class="bi bi-star-fill"></i>
                                <?= htmlspecialchars($doc['specialization']) ?>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <a href="medical_appointment.php?doctor_id=<?= $doc['id'] ?>" class="btn btn-appointment">
                                <i class="bi bi-calendar-check me-2"></i>Book Appointment
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php include 'sections/appointment_history.php'; ?>

<?php include 'includes/footer.php'; ?>