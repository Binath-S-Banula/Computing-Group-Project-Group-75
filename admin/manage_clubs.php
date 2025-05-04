<?php
include '../db_connection.php';
session_start();

// Handle Approve and Inactivate
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($_GET['action'] == 'approve') {
        $stmt = $pdo->prepare("UPDATE club_admins SET is_approved = 1, is_active = 1 WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($_GET['action'] == 'inactivate') {
        $stmt = $pdo->prepare("UPDATE club_admins SET is_active = 0 WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($_GET['action'] == 'activate') {
        $stmt = $pdo->prepare("UPDATE club_admins SET is_active = 1 WHERE id = ?");
        $stmt->execute([$id]);
    }
    header("Location: manage_clubs.php");
    exit();
}


$stmt = $pdo->query("SELECT * FROM club_admins");
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Club Admins</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h3 class="mb-4 text-center">Manage Club Admins</h3>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Club Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Approved</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($admins as $admin): ?>
                <tr>
                    <td><?php echo htmlspecialchars($admin['club_name']); ?></td>
                    <td><?php echo htmlspecialchars($admin['username']); ?></td>
                    <td><?php echo htmlspecialchars($admin['email']); ?></td>
                    <td>
                        <?php if ($admin['is_approved']): ?>
                            <span class="badge bg-success">Approved</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">Pending</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($admin['is_active']): ?>
                            <span class="badge bg-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!$admin['is_approved']): ?>
                            <a href="?action=approve&id=<?php echo $admin['id']; ?>" class="btn btn-success btn-sm">Approve</a>
                        <?php endif; ?>
                        
                        <?php if ($admin['is_active']): ?>
                            <a href="?action=inactivate&id=<?php echo $admin['id']; ?>" class="btn btn-danger btn-sm">Inactivate</a>
                        <?php else: ?>
                            <a href="?action=activate&id=<?php echo $admin['id']; ?>" class="btn btn-primary btn-sm">Activate</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
