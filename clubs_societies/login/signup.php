<?php
include '../../db_connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $club_name = trim($_POST['club_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    try {
        $stmt = $pdo->prepare("SELECT id FROM club_admins WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {
            $error = "Username already taken. Please choose another.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO club_admins (club_name, username, email, password, is_approved, is_active) VALUES (?, ?, ?, ?, 0, 0)");
            $stmt->execute([$club_name, $username, $email, $hashed_password]);

            header("Location: login.php?registered=1");
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
    <title>Club Admin Signup</title>
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

        .signup-container {
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

        .alert-danger {
            background-color: rgba(239, 68, 68, 0.15);
            border: 1px solid var(--error-red);
            color: var(--error-red);
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--neutral-dark);
        }

        .login-link a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .login-link a:hover {
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

        /* Custom animation for alerts */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
        
        /* Password strength indicator */
        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 5px;
            transition: var(--transition);
        }
        
        .password-feedback {
            font-size: 0.8rem;
            margin-top: 5px;
            color: var(--neutral-dark);
        }
    </style>
</head>
<body>

<div class="signup-container">
    <div class="card">
        <div class="card-header">
            <div class="brand-logo">
                <!-- Replace with your club logo -->
                <i class="fas fa-users-cog fa-4x" style="color: var(--white);"></i>
            </div>
            <h3 class="card-title">Club Admin Signup</h3>
        </div>
        <div class="card-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger fade-in">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="post" id="signupForm">
                <div class="input-group">
                    <span class="input-icon">
                        <i class="fas fa-trophy"></i>
                    </span>
                    <input type="text" name="club_name" class="form-control" placeholder="Club Name" required autofocus>
                </div>
                <div class="input-group">
                    <span class="input-icon">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <span class="input-icon">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
                <div class="input-group">
                    <span class="input-icon">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                </div>
                <div>
                    <div class="password-strength"></div>
                    <div class="password-feedback"></div>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-user-plus me-2"></i>Create Account
                </button>
            </form>
        </div>
        <div class="card-footer login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Simple password strength checker
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const strengthBar = document.querySelector('.password-strength');
        const feedback = document.querySelector('.password-feedback');
        
        // Calculate strength
        let strength = 0;
        if (password.length >= 8) strength += 1;
        if (password.match(/[A-Z]/)) strength += 1;
        if (password.match(/[a-z]/)) strength += 1;
        if (password.match(/[0-9]/)) strength += 1;
        if (password.match(/[^A-Za-z0-9]/)) strength += 1;
        
        // Update UI
        switch(strength) {
            case 0:
                strengthBar.style.width = '0%';
                strengthBar.style.backgroundColor = '';
                feedback.textContent = '';
                break;
            case 1:
                strengthBar.style.width = '20%';
                strengthBar.style.backgroundColor = '#ef4444';
                feedback.textContent = 'Very weak password';
                feedback.style.color = '#ef4444';
                break;
            case 2:
                strengthBar.style.width = '40%';
                strengthBar.style.backgroundColor = '#f59e0b';
                feedback.textContent = 'Weak password';
                feedback.style.color = '#f59e0b';
                break;
            case 3:
                strengthBar.style.width = '60%';
                strengthBar.style.backgroundColor = '#eab308';
                feedback.textContent = 'Moderate password';
                feedback.style.color = '#eab308';
                break;
            case 4:
                strengthBar.style.width = '80%';
                strengthBar.style.backgroundColor = '#84cc16';
                feedback.textContent = 'Strong password';
                feedback.style.color = '#84cc16';
                break;
            case 5:
                strengthBar.style.width = '100%';
                strengthBar.style.backgroundColor = '#10b981';
                feedback.textContent = 'Very strong password';
                feedback.style.color = '#10b981';
                break;
        }
    });
</script>
</body>
</html>