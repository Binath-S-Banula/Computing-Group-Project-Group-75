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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
                :root {
            --primary-blue: #4361ee;
            --primary-light: #eef2ff;
            --primary-dark: #3145c9;
            --accent-purple: #7209b7;
            --accent-light: #f1e6ff;
            --neutral-light: #f9fafb;
            --neutral-medium: #e5e7eb;
            --neutral-dark: #4b5563;
            --success-green: #10b981;
            --warning-orange: #f59e0b;
            --error-red: #ef4444;
            --white: #ffffff;
            --border-radius: 1rem;
            --box-shadow: 0 10px 25px rgba(67, 97, 238, 0.07);
            --transition: all 0.3s ease;
        }

        body {
            background-color: var(--primary-light);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--neutral-dark);
            line-height: 1.6;
        }

        .signup-container {
            max-width: 500px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--primary-blue);
            font-weight: 600;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--primary-light);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--neutral-dark);
        }

        .form-control, .form-select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--neutral-medium);
            border-radius: 0.5rem;
            background-color: var(--neutral-light);
            transition: var(--transition);
        }

        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        }

        .btn-primary {
            background-color: var(--primary-blue);
            color: var(--white);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            width: 100%;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--error-red);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.95rem;
        }

        .login-link a {
            color: var(--accent-purple);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .login-link a:hover {
            color: var(--primary-blue);
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="signup-container">
            <h2 class="form-header">Student Registration</h2>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Student ID</label>
                    <input type="text" name="student_id" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Faculty</label>
                    <select name="faculty_id" class="form-select" required>
                        <option value="">Select Faculty</option>
                        <?php foreach ($faculties as $faculty): ?>
                            <option value="<?= $faculty['id'] ?>"><?= $faculty['faculty_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Degree Program</label>
                    <select name="degree_id" class="form-select" required>
                        <option value="">Select Degree</option>
                        <?php foreach ($degrees as $degree): ?>
                            <option value="<?= $degree['id'] ?>"><?= $degree['degree_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Batch</label>
                    <select name="batch_id" class="form-select" required>
                        <option value="">Select Batch</option>
                        <?php foreach ($batches as $batch): ?>
                            <option value="<?= $batch['id'] ?>"><?= $batch['batch_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn-primary">Create Account</button>
            </form>
            
            <div class="login-link">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </div>
    </div>
</body>
</html>