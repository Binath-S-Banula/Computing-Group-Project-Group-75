<?php
require_once('../db_connection.php');

// 1. Most Favorited Events
$eventStmt = $pdo->query("
    SELECT e.event_name, COUNT(f.id) AS favorite_count
    FROM career_events e
    LEFT JOIN favorite_events f ON e.id = f.event_id
    GROUP BY e.id
    ORDER BY favorite_count DESC
");
$eventFavorites = $eventStmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Favorites Over Time (by exact date)
$dateStmt = $pdo->query("
    SELECT DATE(created_at) AS date, COUNT(*) AS count
    FROM favorite_events
    GROUP BY DATE(created_at)
    ORDER BY DATE(created_at)
");
$favoritesOverTime = $dateStmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Events Per Month
$eventsPerMonthStmt = $pdo->query("
    SELECT DATE_FORMAT(event_date, '%Y-%m') AS month, COUNT(*) AS count
    FROM career_events
    GROUP BY month
    ORDER BY month
");
$eventsPerMonth = $eventsPerMonthStmt->fetchAll(PDO::FETCH_ASSOC);

// 4. Favorite Trends Per Month
$favPerMonthStmt = $pdo->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS count
    FROM favorite_events
    GROUP BY month
    ORDER BY month
");
$favoriteTrends = $favPerMonthStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Career Guidance Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="mb-4">Career Guidance Reports</h2>

    <!-- Most Favorited Events -->
    <div class="card mb-5">
        <div class="card-body">
            <h5 class="card-title">Most Favorited Events</h5>
            <canvas id="eventFavoritesChart" height="100"></canvas>
        </div>
    </div>

    <!-- Favorites Over Time -->
    <div class="card mb-5">
        <div class="card-body">
            <h5 class="card-title">Favorites Over Time (Daily)</h5>
            <canvas id="favoritesOverTimeChart" height="100"></canvas>
        </div>
    </div>

    <!-- Events Per Month -->
    <div class="card mb-5">
        <div class="card-body">
            <h5 class="card-title">Events Per Month</h5>
            <canvas id="eventsPerMonthChart" height="100"></canvas>
        </div>
    </div>

    <!-- Favorite Trends Per Month -->
    <div class="card mb-5">
        <div class="card-body">
            <h5 class="card-title">Favorite Trends Per Month</h5>
            <canvas id="favoriteTrendsChart" height="100"></canvas>
        </div>
    </div>
</div>

<script>
    // Data from PHP
    const eventFavorites = <?= json_encode($eventFavorites) ?>;
    const favoritesOverTime = <?= json_encode($favoritesOverTime) ?>;
    const eventsPerMonth = <?= json_encode($eventsPerMonth) ?>;
    const favoriteTrends = <?= json_encode($favoriteTrends) ?>;

    // 1. Most Favorited Events
    new Chart(document.getElementById('eventFavoritesChart'), {
        type: 'bar',
        data: {
            labels: eventFavorites.map(e => e.event_name),
            datasets: [{
                label: 'Favorites',
                data: eventFavorites.map(e => e.favorite_count),
                backgroundColor: 'rgba(54, 162, 235, 0.7)'
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });

    // 2. Favorites Over Time (Daily)
    new Chart(document.getElementById('favoritesOverTimeChart'), {
        type: 'line',
        data: {
            labels: favoritesOverTime.map(e => e.date),
            datasets: [{
                label: 'Daily Favorites',
                data: favoritesOverTime.map(e => e.count),
                borderColor: 'rgba(255, 99, 132, 0.7)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                fill: true,
                tension: 0.3
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });

    // 3. Events Per Month
    new Chart(document.getElementById('eventsPerMonthChart'), {
        type: 'bar',
        data: {
            labels: eventsPerMonth.map(e => e.month),
            datasets: [{
                label: 'Events',
                data: eventsPerMonth.map(e => e.count),
                backgroundColor: 'rgba(75, 192, 192, 0.7)'
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });

    // 4. Favorite Trends Per Month
    new Chart(document.getElementById('favoriteTrendsChart'), {
        type: 'line',
        data: {
            labels: favoriteTrends.map(e => e.month),
            datasets: [{
                label: 'Monthly Favorites',
                data: favoriteTrends.map(e => e.count),
                borderColor: 'rgba(153, 102, 255, 0.7)',
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                fill: true,
                tension: 0.3
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });
</script>

</body>
</html>
