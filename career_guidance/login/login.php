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
        <a href="signup.php" class="btn btn-link">Create an account</a>
    </form>
</div>
</body>
</html>
