<?php include 'includes/header.php'; ?>

<!-- custom styles  -->
<link rel="stylesheet" href="css/course_management.css">

<?php

$degreeCount = $pdo->query("SELECT COUNT(*) FROM degrees")->fetchColumn();
$batchCount = $pdo->query("SELECT COUNT(*) FROM batches")->fetchColumn();
$subjectCount = $pdo->query("SELECT COUNT(*) FROM subjects")->fetchColumn();
?>

    <!-- Dashboard Header -->
    <div class="dashboard-header text-center">
        <h2 class="dashboard-title">Course Management Dashboard</h2>
        <p class="dashboard-subtitle">Manage your educational resources efficiently</p>
    </div>

    <div class="row g-4">
        <!-- Degrees Card -->
        <div class="col-md-4">
            <div class="card feature-card">
                <div class="icon-box degrees-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h3 class="card-title">Degrees</h3>
                <div class="stat-number degrees-number"><?= $degreeCount ?></div>
                <p class="card-text">Add, edit, or remove degree programs in your institution</p>
                <a href="manage_degrees.php" class="btn btn-card btn-degrees">
                    <i class="fas fa-arrow-right me-2"></i>Manage Degrees
                </a>
            </div>
        </div>

        <!-- Batches Card -->
        <div class="col-md-4">
            <div class="card feature-card">
                <div class="icon-box batches-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <h3 class="card-title">Batches</h3>
                <div class="stat-number batches-number"><?= $batchCount ?></div>
                <p class="card-text">Organize and maintain academic batch information</p>
                <a href="manage_batches.php" class="btn btn-card btn-batches">
                    <i class="fas fa-arrow-right me-2"></i>Manage Batches
                </a>
            </div>
        </div>

        <!-- Subjects Card -->
        <div class="col-md-4">
            <div class="card feature-card">
                <div class="icon-box subjects-icon">
                    <i class="fas fa-book"></i>
                </div>
                <h3 class="card-title">Subjects</h3>
                <div class="stat-number subjects-number"><?= $subjectCount ?></div>
                <p class="card-text">Create and organize subjects and assign faculties</p>
                <a href="manage_subjects.php" class="btn btn-card btn-subjects">
                    <i class="fas fa-arrow-right me-2"></i>Manage Subjects
                </a>
            </div>
        </div>
    </div>


<?php include 'includes/footer.php'; ?>