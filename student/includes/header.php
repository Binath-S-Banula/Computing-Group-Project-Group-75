<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../db_connection.php';

if (!isset($_SESSION['student_uid'])) {
    header("Location: login/login.php");
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);

$student_uid = $_SESSION['student_uid'];
$student_id = $_SESSION['student_id'];  
$student_name = $_SESSION['student_name'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>student Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons (Cloudflare CDN) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/student_dashboard.css">
    
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <i class="bi bi-mortarboard-fill me-2"></i> student Portal
            </div>
            <div class="p-3">
                <div class="d-flex align-items-center">
                    <div class="user-avatar">
                        <?= strtoupper(substr($student_name, 0, 1)) ?>
                    </div>
                    <div>
                        <div class="user-name"><?= htmlspecialchars($student_name) ?></div>
                        <small class="text-muted">ID: <?= htmlspecialchars($student_id) ?></small>
                    </div>
                </div>
            </div>
            <hr class="my-2">
            <ul class="nav nav-pills flex-column mb-auto px-2">
                <li class="nav-item">
                    <a href="student_dashboard.php" class="nav-link <?= $current_page == 'student_dashboard.php' ? 'active' : '' ?>">
                        <i class="bi bi-speedometer2"></i>Dashboard
                    </a>
                </li>
                <li>
                    <a href="student_timetable.php" class="nav-link <?= $current_page == 'student_timetable.php' ? 'active' : '' ?>">
                        <i class="bi bi-calendar3"></i>Timetable
                    </a>
                </li>
                <li>
                    <a href="student_lecturers.php" class="nav-link <?= $current_page == 'student_lecturers.php' ? 'active' : '' ?>">
                        <i class="bi bi-calendar-check"></i>Meet Lecturers
                    </a>
                </li>
                <li>
                    <a href="medical_center.php" class="nav-link <?= ($current_page == 'medical_center.php' || $current_page == 'medical_appointment.php') ? 'active' : '' ?>">
                        <i class="bi bi-shield-plus"></i> Medical Center
                    </a>
                </li>

                <li>
                    <a href="clubs_societies.php" class="nav-link <?= $current_page == 'clubs_societies.php' ? 'active' : '' ?>">
                        <i class="bi bi-people"></i>Clubs / Societies
                    </a>
                </li>
                <li>
                    <a href="career_guidance.php" class="nav-link <?= $current_page == 'career_guidance.php' ? 'active' : '' ?>">
                        <i class="bi bi-bell"></i>Career Guidance
                    </a>
                </li>
            </ul>

            <div class="px-3 py-2">
                <a href="login/logout.php" class="btn btn-outline-danger w-100">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </a>
            </div>
        </div>

        <!-- Overlay for mobile view -->
        <div class="overlay" id="overlay"></div>

        <!-- Main Content Area -->
        <div class="content-area" id="content">
            <!-- Header -->
            <header class="main-header sticky-top">
                <button class="toggle-sidebar" id="toggleSidebar">
                    <i class="bi bi-list"></i>
                </button>
                <div class="user-profile">
                    <div class="user-avatar">
                        <?= strtoupper(substr($student_name, 0, 1)) ?>
                    </div>
                    <div class="user-name"><?= htmlspecialchars($student_name) ?></div>
                </div>
            </header>


            <!-- Dashboard Content starts from here -->
            <div class="dashboard-content">