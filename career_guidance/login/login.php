<?php
session_start();
require_once('../../db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    try {
        $stmt = $pdo->prepare("SELECT * FROM career_admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin["password"])) {
            $_SESSION["career_admin_uid"] = $admin["id"]; // <-- use 'career_admin_uid'
            $_SESSION["career_admin_username"] = $admin["username"];
            header("Location: ../career_guidance.php"); // <-- redirect to dashboard
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } catch (PDOException $e) {
        $error = "Login error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - Career Guidance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .container {
            max-width: 540px;
        }

        h3 {
            color: var(--primary-dark);
            font-weight: 600;
            margin-bottom: 1.5rem;
            text-align: center;
            position: relative;
            padding-bottom: 0.75rem;
        }

        h3:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background-color: var(--primary-green);
            border-radius: 2px;
        }

        form {
            background-color: var(--white);
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .form-label {
            color: var(--primary-dark);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 1px solid var(--neutral-medium);
            border-radius: var(--border-radius);
            padding: 0.75rem 1rem;
            transition: var(--transition);
        }

        .form-control:focus {
            box-shadow: none;
            border-color: var(--primary-green);
            background-color: var(--primary-light);
        }

        .mb-3 {
            margin-bottom: 1.5rem !important;
        }

        .btn-success {
            background-color: var(--primary-green);
            border: none;
            border-radius: var(--border-radius);
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-success:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-link {
            color: var(--primary-green);
            text-decoration: none;
            transition: var(--transition);
        }

        .btn-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .alert {
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background-color: var(--primary-light);
            border-color: var(--primary-green);
            color: var(--primary-dark);
        }

        .alert-danger {
            border-left: 4px solid #dc3545;
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5 col-md-6">
    <h3 class="mb-4">Career Guidance Admin Login</h3>
    <?php
    if (isset($_GET['signup']) && $_GET['signup'] === 'success') {
        echo "<div class='alert alert-success'>Signup successful! Please log in.</div>";
    }
    if (isset($error)) echo "<div class='alert alert-danger'>$error</div>";
    ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" required class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" required class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Login</button>
    </form>
</div>
</body>
</html>