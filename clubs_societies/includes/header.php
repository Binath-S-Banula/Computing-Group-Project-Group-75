<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../db_connection.php';

$current_page = basename($_SERVER['PHP_SELF']);

if (!isset($_SESSION['club_admin_uid'])) {
    header("Location: login/login.php");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT club_name FROM club_admins WHERE id = ?");
    $stmt->execute([$_SESSION['club_admin_uid']]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        session_destroy();
        header("Location: login/login.php");
        exit();
    }

    $club_name = $admin['club_name'];

} catch (PDOException $e) {
    echo "Something went wrong.";
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club Admin Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="css/club_dashboard.css">
   
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="p-3 top-left">
                <div class="d-flex align-items-center">
                    <div class="user-avatar">
                        <?= strtoupper(substr($club_name, 0, 1)) ?>
                    </div>
                    <div class="user-info">
                        <div class="user-name"><?= htmlspecialchars($club_name) ?></div>
                        <div class="user-role">Administrator</div>
                    </div>
                </div>
            </div>
            <hr class="my-2">

            <div class="menu-category">Main Menu</div>
            <ul class="nav nav-pills flex-column mb-auto px-2">
                <li class="nav-item">
                    <a href="club_dashboard.php" class="nav-link <?= ($current_page == 'club_dashboard.php') ? 'active' : '' ?>">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="manage_events.php" class="nav-link <?= ($current_page == 'manage_events.php') ? 'active' : '' ?>">
                        <i class="bi bi-calendar-event"></i> Manage Events
                    </a>
                </li>
                <li>
                    <a href="view_members.php" class="nav-link <?= ($current_page == 'view_members.php') ? 'active' : '' ?>">
                        <i class="bi bi-people"></i> Club Members
                    </a>
                </li>
            </ul>


            <div class="menu-category">Settings</div>
            <ul class="nav nav-pills flex-column px-2">
                <li>
                    <a href="#" class="nav-link">
                        <i class="bi bi-gear"></i> Club Settings
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link">
                        <i class="bi bi-person-gear"></i> My Profile
                    </a>
                </li>
            </ul>

            <hr class="bg-white opacity-25 mt-4">
            <div class="px-3 py-3">
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
            <header class="main-header">
                <button class="toggle-sidebar" id="toggleSidebar">
                    <i class="bi bi-list"></i>
                </button>
                <div class="user-profile">
                    <div class="user-avatar">
                        <?= strtoupper(substr($club_name, 0, 1)) ?>
                    </div>
                    <div class="user-name"><?= htmlspecialchars($club_name) ?></div>
                </div>
            </header>

            <div class="dashboard-content">
                <!-- Dashboard Content starts here -->

           