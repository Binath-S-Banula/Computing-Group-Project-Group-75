<?php
require '../../db_connection.php';

// Fetch faculties
$faculties = [];
$stmt = $pdo->query("SELECT id, faculty_name FROM faculties");
$faculties = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lecturer_id = $_POST['lecturer_id'];
    $name = $_POST['name'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $faculty_id = $_POST['faculty'];

    $sql = "INSERT INTO lecturers (lecturer_id, name, password, faculty_id) 
            VALUES (:lecturer_id, :name, :password, :faculty_id)";
    
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([
            ':lecturer_id' => $lecturer_id,
            ':name' => $name,
            ':password' => $password,
            ':faculty_id' => $faculty_id
        ]);
        header("Location: login.php");
        exit;
    } catch (PDOException $e) {
        $error = "Signup failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lecturer Signup</title>
</head>
<body>
    <h2>Lecturer Signup</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        <label>Lecturer ID:</label><br>
        <input type="text" name="lecturer_id" required><br>

        <label>Name:</label><br>
        <input type="text" name="name" required><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br>

        <label>Faculty:</label><br>
        <select name="faculty" required>
            <option value="">Select Faculty</option>
            <?php foreach ($faculties as $faculty): ?>
                <option value="<?= $faculty['id'] ?>"><?= htmlspecialchars($faculty['faculty_name']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Sign Up</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a>.</p>
</body>
</html>
