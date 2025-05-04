<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../db_connection.php';

if (!isset($_SESSION['medical_admin_uid'])) {
    header("Location: login/login.php");
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);

date_default_timezone_set('Asia/Colombo');

// Fetch admin_id from DB
$admin_id = "";
try {
    $stmt = $pdo->prepare("SELECT admin_id FROM medical_admins WHERE id = ?");
    $stmt->execute([$_SESSION['medical_admin_uid']]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    $admin_id = $admin ? $admin['admin_id'] : "Unknown Admin";
} catch (PDOException $e) {
    $admin_id = "Error";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Medical Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/medical_dashboard.css">
</head>
<body>
<div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <i class="bi bi-hospital-fill me-2"></i> Medical Admin
        </div>
        <div class="p-3">
            <div class="d-flex align-items-center">
                <div class="user-avatar">
                    <?= strtoupper(substr($admin_id, 0, 1)) ?>
                </div>
                <div>
                    <div class="user-name">Admin</div>
                    <small class="text-muted">ID: <?= htmlspecialchars($admin_id) ?></small>
                </div>
            </div>
        </div>
        <hr class="my-2">
        <ul class="nav nav-pills flex-column mb-auto px-2">
            <li class="nav-item">
                <a href="medical_dashboard.php" class="nav-link <?= $current_page == 'medical_dashboard.php' ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="current_appointments.php" class="nav-link <?= ($current_page == 'current_appointments.php' || $current_page == 'view_appointments.php') ? 'active' : '' ?>">
                    <i class="bi bi-journal-check"></i> Appointments
                </a>
            </li>
            <li>
                <a href="manage_doctors.php" class="nav-link <?= ($current_page == 'manage_doctors.php' || $current_page == 'doctor_hours.php') ? 'active' : '' ?>">
                    <i class="bi bi-clock-history"></i> Doctor Hours
                </a>
            </li>
            <li>
                <a href="medical_reports.php" class="nav-link <?= $current_page == 'medical_reports.php' ? 'active' : '' ?>">
                    <i class="bi bi-file-earmark-medical"></i> Reports
                </a>
            </li>
        </ul>

        <div class="px-3 py-2">
            <a href="login/logout.php" class="btn btn-outline-danger w-100">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
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
                    <?= strtoupper(substr($admin_id, 0, 1)) ?>
                </div>
                <div class="user-name"><?= htmlspecialchars($admin_id) ?></div>
            </div>
        </header>

        <!-- Dashboard Content starts here -->
        <div class="dashboard-content">
