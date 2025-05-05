<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../db_connection.php';
date_default_timezone_set('Asia/Colombo');

if (!isset($_SESSION['lecturer_uid'])) {
    header("Location: login/login.php");
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);

$lecturer_uid = $_SESSION['lecturer_uid'];
$lecturer_id = $_SESSION['lecturer_id'];  
$lecturer_name = $_SESSION['lecturer_name'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons (Cloudflare CDN) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/lecturer_dashboard.css">
    <link rel="stylesheet" href="css/lecturer_timetable.css">
    
    
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <i class="bi bi-mortarboard-fill me-2"></i> Lecturer Portal
            </div>

            <ul class="nav nav-pills flex-column mb-auto px-2">
                <li class="nav-item">
                    <a href="lecturer_dashboard.php" class="nav-link <?= $current_page == 'lecturer_dashboard.php' ? 'active' : '' ?>">
                        <i class="bi bi-speedometer2"></i>Dashboard
                    </a>
                </li>
                <li>
                    <a href="lecturer_timetable.php" class="nav-link <?= $current_page == 'lecturer_timetable.php' ? 'active' : '' ?>">
                        <i class="bi bi-calendar3"></i>Timetable
                    </a>
                </li>
                <li>
                    <a href="lecturer_set_reminders.php" class="nav-link <?= $current_page == 'lecturer_set_reminders.php' ? 'active' : '' ?>">
                        <i class="bi bi-person-lines-fill"></i>Set Reminders
                    </a>
                </li>
                <li>
                    <a href="announcements.php" class="nav-link <?= $current_page == 'announcements.php' ? 'active' : '' ?>">
                        <i class="bi bi-calendar-check"></i>Announcements
                    </a>
                </li>
                <li>
                    <a href="student_requests.php" class="nav-link <?= $current_page == 'student_requests.php' ? 'active' : '' ?>">
                        <i class="bi bi-person-lines-fill"></i>Student Requests
                    </a>
                </li>
                <li>
                    <a href="lecturer_notifications.php" class="nav-link <?= $current_page == 'lecturer_notifications.php' ? 'active' : '' ?>">
                        <i class="bi bi-bell"></i>Notifications
                    </a>
                </li>
                <li>
                    <a href="lecturer_allocations.php" class="nav-link <?= $current_page == 'lecturer_allocations.php' ? 'active' : '' ?>">
                        <i class="bi bi-person-circle"></i>My Profile
                    </a>
                </li>
            </ul>

            <hr>
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
                        <?= strtoupper(substr($lecturer_name, 0, 1)) ?>
                    </div>
                    <div class="user-name"><?= htmlspecialchars($lecturer_name) ?></div>
                </div>
            </header>


            <!-- Dashboard Content starts from here -->
            <div class="dashboard-content">