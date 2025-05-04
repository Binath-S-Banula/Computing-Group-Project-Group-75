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
<html>
<head>
    <title>Medical Center Admin Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="col-md-4 mx-auto">
            <div class="card shadow">
                <div class="card-body">
                    <h4 class="card-title text-center mb-4">Medical Center Admin Login</h4>
                    <?php if ($login_error): ?>
                        <div class="alert alert-danger"><?= $login_error ?></div>
                    <?php endif; ?>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="admin_id" class="form-label">Admin ID</label>
                            <input type="text" name="admin_id" id="admin_id" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
