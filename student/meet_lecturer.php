<?php
require '../db_connection.php';
session_start();

if (!isset($_GET['lecturer_id'])) {
    echo "Lecturer not specified.";
    exit;
}

$lecturer_id = $_GET['lecturer_id'];

$stmt = $pdo->prepare("SELECT name FROM lecturers WHERE id = :id");
$stmt->execute([':id' => $lecturer_id]);
$lecturer = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Meet Lecturer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <?php if ($lecturer): ?>
        <h2 class="text-success mb-3">Meet <?= htmlspecialchars($lecturer['name']) ?></h2>
        <!-- You can add more info or meeting form here -->
        <p>This is where you can view details or request a meeting with your lecturer.</p>
    <?php else: ?>
        <div class="alert alert-danger">Lecturer not found.</div>
    <?php endif; ?>
</div>
</body>
</html>
