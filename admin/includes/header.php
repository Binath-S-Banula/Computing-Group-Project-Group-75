<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../db_connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login/login.php");
    exit;
}

$admin_username = $_SESSION['admin_username'];

// Get the current file name
$currentPage = basename($_SERVER['PHP_SELF']);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons (Cloudflare CDN) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin_dashboard.css">
    <link rel="stylesheet" href="css/manage_proposals.css">
    <link rel="stylesheet" href="css/available_lecturers.css">
    
    
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <i class="bi bi-shield-lock-fill me-2"></i> Admin Portal
            </div>

            <ul class="nav nav-pills flex-column mb-auto px-2">
                <li class="nav-item">
                    <a href="admin_dashboard.php" class="nav-link <?= $currentPage == 'admin_dashboard.php' ? 'active' : '' ?>">
                        <i class="bi bi-speedometer2"></i>Dashboard
                    </a>
                </li>
                <li>
                    <a href="course_management.php" class="nav-link <?= $currentPage == 'course_management.php' ? 'active' : '' ?>">
                        <i class="bi bi-book"></i>Course Management
                    </a>
                </li>
                <li>
                    <a href="subject_allocations.php" class="nav-link <?= $currentPage == 'subject_allocations.php' ? 'active' : '' ?>">
                        <i class="bi bi-people"></i>Subject Allocations
                    </a>
                </li>
                <li>
                    <a href="student_schedule.php" class="nav-link <?= $currentPage == 'student_schedule.php' ? 'active' : '' ?>">
                        <i class="bi bi-calendar3"></i>Student Schedules
                    </a>
                </li>
                <li>
                    <a href="lecturer_schedule.php" class="nav-link <?= $currentPage == 'lecturer_schedule.php' ? 'active' : '' ?>">
                        <i class="bi bi-calendar3"></i>Lecturer Schedules 
                    </a>
                </li>
                <li>
                    <a href="manage_proposals.php" class="nav-link <?= $currentPage == 'manage_proposals.php' ? 'active' : '' ?>">
                        <i class="bi bi-file-earmark-text"></i>Timetable Changes
                    </a>
                </li>
                <li>
                    <a href="manage_clubs.php" class="nav-link <?= $currentPage == 'manage_clubs.php' ? 'active' : '' ?>">
                    <i class="bi bi-people"></i></i>Clubs Mangement
                    </a>
                </li>

            </ul>

            <hr class="bg-white opacity-25">
            <div class="px-3 py-2">
                <a href="login/logout.php" class="btn btn-outline-light w-100">
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
                        <?= strtoupper(substr($admin_username, 0, 1)) ?>
                    </div>
                    <div class="user-name"><?= htmlspecialchars($admin_username) ?></div>
                </div>
            </header>

            <!-- Dashboard Content strarts here -->
            <div class="dashboard-content">