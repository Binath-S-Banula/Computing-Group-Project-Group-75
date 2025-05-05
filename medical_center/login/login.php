<?php
session_start();
include '../../db_connection.php'; 

$login_error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_id = $_POST['admin_id'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT id, password FROM medical_admins WHERE admin_id = ?");
        $stmt->execute([$admin_id]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['medical_admin_uid'] = $admin['id'];
            header("Location: ../medical_dashboard.php");
            exit();
        } else {
            $login_error = "Invalid admin ID or password.";
        }
    } catch (PDOException $e) {
        $login_error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Center Admin Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            /* Medical center color palette */
            --primary-blue: #2C7ABB;
            --primary-light: #E6F0F9;
            --primary-dark: #1A5A8F;
            --accent-teal: #20B2AA;
            --accent-light: #E6F7F6;
            --neutral-light: #F9FAFB;
            --neutral-medium: #E5E7EB;
            --neutral-dark: #4B5563;
            --success-green: #38A169;
            --warning-orange: #ED8936;
            --error-red: #E53E3E;
            --white: #FFFFFF;
            --border-radius: 1rem;
            --box-shadow: 0 10px 25px rgba(44, 122, 187, 0.07);
            --transition: all 0.3s ease;
        }
        
        body {
            background-color: var(--primary-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 1rem;
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
        
        .header-logo {
            width: 60px;
            height: 60px;
            background-color: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: var(--primary-blue);
            font-size: 1.8rem;
        }
        
        .card-body {
            padding: 2rem;
            background-color: var(--white);
        }
        
        .form-label {
            color: var(--neutral-dark);
            font-weight: 500;
        }
        
        .form-control {
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            border: 1px solid var(--neutral-medium);
            transition: var(--transition);
        }
        
        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(44, 122, 187, 0.15);
        }
        
        .input-group-text {
            background-color: var(--neutral-light);
            border: 1px solid var(--neutral-medium);
            border-right: none;
            color: var(--neutral-dark);
        }
        
        .btn-primary {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .alert {
            border-radius: 0.5rem;
            border-left: 4px solid var(--error-red);
        }
        
        .card-footer {
            background-color: var(--neutral-light);
            border-top: 1px solid var(--neutral-medium);
            text-align: center;
            padding: 1rem;
        }
        
        .card-footer a {
            color: var(--primary-blue);
            text-decoration: none;
            transition: var(--transition);
        }
        
        .card-footer a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        /* Password visibility toggle */
        .password-toggle {
            cursor: pointer;
            color: var(--neutral-dark);
        }

        .password-toggle:hover {
            color: var(--primary-blue);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="card shadow">
            <div class="card-header">
                <div class="header-logo">
                    <i class="fas fa-hospital-user"></i>
                </div>
                <h4 class="mb-0">Medical Center Admin</h4>
            </div>
            <div class="card-body">
                <?php if ($login_error): ?>
                    <div class="alert alert-danger d-flex align-items-center mb-4">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= $login_error ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-4">
                        <label for="admin_id" class="form-label">Admin ID</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user-md"></i></span>
                            <input type="text" name="admin_id" id="admin_id" class="form-control" placeholder="Enter your admin ID" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                            <span class="input-group-text password-toggle" onclick="togglePasswordVisibility()">
                                <i class="fas fa-eye" id="togglePassword"></i>
                            </span>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer">
                <a href="#" class="forgot-password">Forgot Password?</a>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePassword');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>