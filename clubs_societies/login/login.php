<?php
include '../../db_connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    try {
        $stmt = $pdo->prepare("SELECT * FROM club_admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin) {
            $error = "Wrong username.";
        } else if ($admin['is_active'] == 0) {
            $error = "Your account is not approved yet. Please wait for admin approval.";
        } else if (!password_verify($password, $admin['password'])) {
            $error = "Invalid password.";
        } else {
            $_SESSION['club_admin_uid'] = $admin['id'];
            header("Location: ../club_dashboard.php");
            exit();
        }
    } catch (PDOException $e) {
        $error = "Something went wrong. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #5e35b1;
            --primary-light: #7e57c2;
            --primary-light-transparent: rgba(126, 87, 194, 0.2);
            --primary-dark: #4527a0;
            --accent-purple: #00bcd4;
            --accent-light: #4dd0e1;
            --neutral-light: #f5f7fa;
            --neutral-medium: #e9ecef;
            --neutral-dark: #6c757d;
            --success-green: #10b981;
            --warning-orange: #f59e0b;
            --error-red: #ef4444;
            --white: #ffffff;
            --border-radius: 1rem;
            --box-shadow: 0 10px 25px rgba(67, 97, 238, 0.07);
            --transition: all 0.3s ease;
        }

        body {
            background-color: var(--neutral-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 0 15px;
        }

        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }

        .card-header {
            background-color: var(--primary-blue);
            color: var(--white);
            text-align: center;
            padding: 1.5rem 1rem;
            border-bottom: none;
        }

        .card-title {
            margin-bottom: 0;
            font-weight: 600;
            font-size: 1.75rem;
        }

        .card-body {
            padding: 2rem;
            background-color: var(--white);
        }

        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .form-control {
            padding: 0.75rem 1rem 0.75rem 3rem;
            border-radius: 0.5rem;
            border: 1px solid var(--neutral-medium);
            transition: var(--transition);
            height: auto;
        }

        .form-control:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 0.25rem var(--primary-light-transparent);
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-light);
            z-index: 10;
        }

        .btn-primary {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: var(--transition);
        }

        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(94, 53, 177, 0.3);
        }

        .alert {
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.15);
            border: 1px solid var(--success-green);
            color: var(--success-green);
        }

        .alert-danger {
            background-color: rgba(239, 68, 68, 0.15);
            border: 1px solid var(--error-red);
            color: var(--error-red);
        }

        .registration-link {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--neutral-dark);
        }

        .registration-link a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .registration-link a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .brand-logo {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .brand-logo img {
            height: 80px;
            width: auto;
        }
        
        .card-footer {
            background-color: var(--white);
            border-top: 1px solid var(--neutral-medium);
            padding: 1.5rem;
            text-align: center;
        }

        @media (max-width: 576px) {
            .card-body {
                padding: 1.5rem;
            }
        }

        /* Custom animation for the success alert */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="card">
        <div class="card-header">
            <div class="brand-logo">
                <!-- Replace with your club logo -->
                <i class="fas fa-users-cog fa-4x" style="color: var(--white);"></i>
            </div>
            <h3 class="card-title">Club Admin Login</h3>
        </div>
        <div class="card-body">
            <?php if (isset($_GET['registered'])): ?>
                <div class="alert alert-success fade-in">
                    <i class="fas fa-check-circle me-2"></i>
                    Registered successfully! Waiting for admin approval.
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger fade-in">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="input-group">
                    <span class="input-icon">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" name="username" class="form-control" placeholder="Username" required autofocus>
                </div>
                <div class="input-group">
                    <span class="input-icon">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </button>
            </form>
        </div>
        <div class="card-footer registration-link">
            Don't have an account? <a href="signup.php">Register here</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>