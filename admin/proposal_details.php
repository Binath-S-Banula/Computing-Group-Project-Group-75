<?php
// Validate proposal ID
if (!isset($_GET['id'])) {
    die('❌ Proposal ID not provided.');
}
require '../db_connection.php';

$proposal_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM timetable_proposals WHERE id = ?");
$stmt->execute([$proposal_id]);
$proposal = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$proposal) {
    die('❌ Proposal not found.');
}

?>
<link rel="stylesheet" href="css/proposal_details.css">

<?php include 'includes/header.php'; ?>

        <?php require 'sections/timetable.php'; ?>
        <?php require 'sections/handle_proposal.php'; ?>
        

<?php include 'includes/footer.php'; ?>
