<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../db_connection.php';

if (!isset($_SESSION['career_admin_uid'])) {
    header("Location: login/login.php");
    exit;
}

$admin_username = $_SESSION['career_admin_username'];
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Career Admin Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons & Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/career_guidance.css">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <i class="bi bi-person-workspace me-2"></i> Career Admin
            </div>

            <ul class="nav nav-pills flex-column mb-auto px-2">
                <li class="nav-item">
                    <a href="career_guidance.php" class="nav-link <?= $currentPage == 'career_guidance.php' ? 'active' : '' ?>">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="career_events.php" class="nav-link <?= in_array($currentPage, ['career_events.php', 'view_event.php']) ? 'active' : '' ?>">
                        <i class="bi bi-calendar-event"></i> Manage Events
                    </a>
                </li>
                <li>
                    <a href="student_favorites.php" class="nav-link <?= $currentPage == 'student_favorites.php' ? 'active' : '' ?>">
                        <i class="bi bi-journal-check"></i> Event Overview
                    </a>
                </li>
            </ul>

            <hr class="bg-white opacity-25">
            <div class="px-3 py-2">
                <a href="login/logout.php" class="btn btn-outline-light w-100">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </a>
            </div>
        </div>

        <!-- Overlay -->
        <div class="overlay" id="overlay"></div>

        <!-- Main Content -->
        <div class="content-area" id="content">
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

            <!-- Dashboard Content -->
            <div class="dashboard-content">

  
