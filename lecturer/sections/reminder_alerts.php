<?php
session_start();
require '../../db_connection.php';
date_default_timezone_set('Asia/Colombo');


if (!isset($_SESSION['lecturer_uid'])) {
    header("Location: ../login/login.php"); 
    exit();
}

$lecturer_id = $_SESSION['lecturer_uid'];
$today = date('Y-m-d');

// Get today's reminders ordered by time
$stmt = $pdo->prepare("SELECT * FROM lecturer_reminders WHERE lecturer_id = ? AND reminder_date = ? ORDER BY reminder_time ASC");
$stmt->execute([$lecturer_id, $today]);
$reminders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Reminders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-green: #1e8e4e;
            --primary-light: #e6f7ed;
            --primary-dark: #146c3c;
            --accent-green: #34c774;
            --neutral-light: #f8f9fa;
            --neutral-medium: #e2e3e5;
            --neutral-dark: #6c757d;
            --white: #ffffff;
            --border-radius: 0.75rem;
            --box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }
        
        body {
            background-color: var(--neutral-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
        }
        
        .reminder-container {
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }
        
        .reminder-header {
            background-color: var(--primary-green);
            color: var(--white);
            padding: 0.75rem 1.25rem;
            font-weight: 500;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .reminder-body {
            padding: 1rem;
        }
        
        .reminder-date {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .reminder-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .reminder-item {
            background-color: var(--primary-light);
            border-radius: 10px;
            padding: 1rem;
            border-bottom: 5px solid var(--white);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: var(--transition);
        }
        
        .reminder-item:last-child {
            border-bottom: none;
        }
        
        .reminder-note {
            font-size: 1rem;
            color: #333;
        }
        
        .reminder-time {
            background-color: var(--white);
            color: var(--primary-dark);
            padding: 0.4rem 0.8rem;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .empty-message {
            padding: 2rem;
            text-align: center;
            color: var(--neutral-dark);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="reminder-container">
            <div class="reminder-header">
                <div>Today's Reminders</div>
                <div class="reminder-date"><?= date('F j, Y') ?></div>
            </div>
            <div class="reminder-body">
                <?php if (!empty($reminders)): ?>
                    <ul class="reminder-list">
                        <?php foreach ($reminders as $rem): ?>
                            <li class="reminder-item">
                                <div class="reminder-note"><?= htmlspecialchars($rem['note']) ?></div>
                                <div class="reminder-time"><?= date('h:i A', strtotime($rem['reminder_time'])) ?></div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="empty-message">You have no reminders for today.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
