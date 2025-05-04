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
<html>
<head>
    <title>Lecturer Login</title>
</head>
<body>
    <h2>Lecturer Login</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        <label>Lecturer ID:</label><br>
        <input type="text" name="lecturer_id" required><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="signup.php">Sign up here</a>.</p>
</body>
</html>
