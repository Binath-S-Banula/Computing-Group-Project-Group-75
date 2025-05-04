<?php
require '../../db_connection.php';

$faculties = $pdo->query("SELECT id, faculty_name FROM faculties")->fetchAll();
$degrees = $pdo->query("SELECT id, degree_name FROM degrees")->fetchAll();
$batches = $pdo->query("SELECT id, batch_name FROM batches")->fetchAll();

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $faculty_id = $_POST['faculty_id'];
    $degree_id = $_POST['degree_id'];
    $batch_id = $_POST['batch_id'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if student_id already exists
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE student_id = ?");
    $checkStmt->execute([$student_id]);
    if ($checkStmt->fetchColumn() > 0) {
        $error = "Student ID already exists. Please try another.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO students (student_id, name, email, faculty_id, degree_id, batch_id, password) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$student_id, $name, $email, $faculty_id, $degree_id, $batch_id, $password]);
        header("Location: login.php?success=1");
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Signup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="card mx-auto mt-5 p-3" style="width: 30rem;">
    <h2 class="mb-4">Student Signup</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3" >
            <label class="form-label">Student ID</label>
            <input type="text" name="student_id" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Faculty</label>
            <select name="faculty_id" class="form-select" required>
                <option value="">Select Faculty</option>
                <?php foreach ($faculties as $faculty): ?>
                    <option value="<?= $faculty['id'] ?>"><?= $faculty['faculty_name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Degree</label>
            <select name="degree_id" class="form-select" required>
                <option value="">Select Degree</option>
                <?php foreach ($degrees as $degree): ?>
                    <option value="<?= $degree['id'] ?>"><?= $degree['degree_name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Batch</label>
            <select name="batch_id" class="form-select" required>
                <option value="">Select Batch</option>
                <?php foreach ($batches as $batch): ?>
                    <option value="<?= $batch['id'] ?>"><?= $batch['batch_name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button class="btn btn-primary w-100">Sign Up</button>
    </form>
    <p class="mt-3">Already have an account? <a href="login.php">Login here</a></p>
</div>
</body>
</html>
