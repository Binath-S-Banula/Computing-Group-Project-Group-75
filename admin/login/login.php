<?php
session_start();
require '../../db_connection.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {    
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header("Location: ../admin_dashboard.php");
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            background-color: var(--primary-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-card {
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            width: 380px;
            padding: 2.5rem;
            transition: var(--transition);
        }
        
        .login-card:hover {
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
        }
        
        .brand-logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .brand-circle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background-color: var(--primary-green);
            border-radius: 50%;
            color: var(--white);
            font-size: 1.8rem;
        }
        
        .login-title {
            color: var(--primary-dark);
            font-weight: 600;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .form-control {
            padding: 0.7rem 1rem;
            border: 1px solid var(--neutral-medium);
            border-radius: var(--border-radius);
            transition: var(--transition);
        }
        
        .form-control:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.25rem rgba(30, 142, 78, 0.25);
        }
        
        .form-label {
            color: var(--neutral-dark);
            font-weight: 500;
        }
        
        .input-group-text {
            background-color: var(--white);
            border: 1px solid var(--neutral-medium);
            border-right: none;
            border-radius: var(--border-radius) 0 0 var(--border-radius);
            color: var(--neutral-dark);
        }
        
        .password-input {
            border-left: none;
            border-radius: 0 var(--border-radius) var(--border-radius) 0;
        }
        
        .btn-primary {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
            border-radius: var(--border-radius);
            padding: 0.7rem 1rem;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        .alert {
            border-radius: var(--border-radius);
            border-left: 4px solid #842029;
        }
        
        .back-link {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: var(--primary-green);
            text-decoration: none;
            font-size: 0.9rem;
            transition: var(--transition);
        }
        
        .back-link:hover {
            color: var(--primary-dark);
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="login-card">
        <div class="brand-logo">
            <div class="brand-circle">
                <i class="fas fa-shield-alt"></i>
            </div>
        </div>
        <h4 class="login-title">Admin Login</h4>
        
        <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="mb-4">
                <label class="form-label" for="username">Username</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" id="username" name="username" class="form-control" required autofocus>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label" for="password">Password</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" id="password" name="password" class="form-control password-input" required>
                </div>
            </div>
            
            <div class="d-grid">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>