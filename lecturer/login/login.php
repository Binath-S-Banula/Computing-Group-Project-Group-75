<?php
require '../../db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lecturer_id = $_POST['lecturer_id'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM lecturers WHERE lecturer_id = :lecturer_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':lecturer_id' => $lecturer_id]);
    $lecturer = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($lecturer && password_verify($password, $lecturer['password'])) {
        $_SESSION['lecturer_uid'] = $lecturer['id']; // internal ID
        $_SESSION['lecturer_id'] = $lecturer['lecturer_id']; // actual visible ID
        $_SESSION['lecturer_name'] = $lecturer['name'];
        header("Location: ../lecturer_dashboard.php"); 
        exit;
    } else {
        $error = "Invalid Lecturer ID or Password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Login</title>
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--neutral-light);
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 2rem;
        }

        .container {
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            width: 100%;
            max-width: 500px;
            padding: 2.5rem;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .header h2 {
            color: var(--primary-dark);
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: var(--neutral-dark);
            font-size: 0.9rem;
        }

        .error-message {
            background-color: #ffe6e6;
            color: #d32f2f;
            padding: 0.75rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--neutral-dark);
            font-weight: 500;
            font-size: 0.9rem;
        }

        input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--neutral-medium);
            border-radius: var(--border-radius);
            background-color: var(--white);
            font-size: 1rem;
            transition: var(--transition);
        }

        input:focus {
            outline: none;
            border-color: var(--accent-green);
            box-shadow: 0 0 0 3px rgba(52, 199, 116, 0.2);
        }

        button {
            background-color: var(--primary-green);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            padding: 0.85rem;
            width: 100%;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        button:hover {
            background-color: var(--primary-dark);
        }

        .signup-link {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--neutral-dark);
            font-size: 0.9rem;
        }

        .signup-link a {
            color: var(--primary-green);
            text-decoration: none;
            font-weight: 600;
        }

        .signup-link a:hover {
            text-decoration: underline;
            color: var(--primary-dark);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Lecturer Login</h2>
            <p>Please enter your credentials to continue</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="lecturer_id">Lecturer ID</label>
                <input type="text" id="lecturer_id" name="lecturer_id" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Login</button>
        </form>
        
        <div class="signup-link">
            Don't have an account? <a href="signup.php">Sign up here</a>
        </div>
    </div>
</body>
</html>