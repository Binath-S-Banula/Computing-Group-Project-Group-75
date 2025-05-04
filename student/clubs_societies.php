<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="css/clubs_societies.css">


<?php
if (!isset($_SESSION['student_uid'])) {
    header('Location: ../login/login.php');
    exit();
}

// Handle flash message
$flash_message = '';
$flash_type = '';
if (isset($_SESSION['flash_message'])) {
    $flash_message = $_SESSION['flash_message'];
    $flash_type = $_SESSION['flash_type'] ?? 'info';
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
}

// Fetch joined clubs, excluding blacklisted members
$stmt = $pdo->prepare("
    SELECT cm.id as club_member_id, ca.id as club_id, ca.club_name, cm.status, ca.description
    FROM club_members cm
    JOIN club_admins ca ON cm.club_id = ca.id
    WHERE cm.student_id = ?
    AND ca.is_active = 1
    AND cm.status != 'blacklisted' 
");

$stmt->execute([$_SESSION['student_uid']]);
$joined_clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);



// Fetch available clubs or blacklisted clubs
$stmt2 = $pdo->prepare("
    SELECT ca.id, ca.club_name, ca.description, cm.status
    FROM club_admins ca
    LEFT JOIN club_members cm ON ca.id = cm.club_id AND cm.student_id = ?
    WHERE ca.is_active = 1
    AND (
        cm.student_id IS NULL OR cm.status = 'blacklisted'
    )
");
$stmt2->execute([$_SESSION['student_uid']]);
$available_clubs = $stmt2->fetchAll(PDO::FETCH_ASSOC);

?>



    <div class="dashboard-title">
        <h2>Club Registration</h2>
        <p>Explore and join clubs that match your interests</p>
    </div>

    <?php if (!empty($flash_message)): ?>
        <div id="flashMessage" class="alert alert-<?= htmlspecialchars($flash_type) ?> alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <?php if ($flash_type === 'success'): ?>
                    <i class="fas fa-check-circle me-2"></i>
                <?php elseif ($flash_type === 'danger'): ?>
                    <i class="fas fa-exclamation-circle me-2"></i>
                <?php elseif ($flash_type === 'warning'): ?>
                    <i class="fas fa-exclamation-triangle me-2"></i>
                <?php else: ?>
                    <i class="fas fa-info-circle me-2"></i>
                <?php endif; ?>
                <?= htmlspecialchars($flash_message) ?>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <section id="my-clubs" class="mb-5">
        <h2 class="section-title"><i class="fas fa-star"></i> My Clubs</h2>

        <?php if (count($joined_clubs) > 0): ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($joined_clubs as $club): ?>
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column">
                                <h3 class="card-title"><?= htmlspecialchars($club['club_name']) ?></h3>
                                
                                <span class="status-badge <?= $club['status'] === 'active' ? 'status-active' : 'status-inactive' ?>">
                                    <i class="fas fa-<?= $club['status'] === 'active' ? 'check-circle' : 'pause-circle' ?>"></i>
                                    <?= ucfirst(htmlspecialchars($club['status'])) ?>
                                </span>
                                
                                <?php if (!empty($club['description'])): ?>
                                    <p class="card-description"><?= htmlspecialchars(substr($club['description'], 0, 100)) ?><?= strlen($club['description']) > 100 ? '...' : '' ?></p>
                                <?php endif; ?>

                                <div class="card-actions mt-auto">
                                    <a href="view_club.php?id=<?= htmlspecialchars($club['club_id']) ?>" class="btn btn-primary w-100 mb-2 btn-icon">
                                        <i class="fas fa-info-circle"></i> View Details
                                    </a>
                                    <form action="sections/club_status.php" method="POST" class="form-action">
                                        <input type="hidden" name="club_member_id" value="<?= $club['club_member_id'] ?>">
                                        <input type="hidden" name="new_status" value="<?= $club['status'] == 'active' ? 'inactive' : 'active' ?>">
                                        <button type="submit" class="btn btn-secondary w-100 btn-icon">
                                            <i class="fas fa-<?= $club['status'] == 'active' ? 'pause' : 'play' ?>"></i>
                                            <?= $club['status'] == 'active' ? 'Pause Membership' : 'Activate Membership' ?>
                                        </button>
                                    </form>

                                    <button type="button" class="btn btn-danger w-100 btn-icon" 
                                           data-bs-toggle="modal" 
                                           data-bs-target="#leaveClubModal-<?= $club['club_member_id'] ?>"
                                           data-club-name="<?= htmlspecialchars($club['club_name']) ?>"
                                           data-club-id="<?= $club['club_member_id'] ?>">
                                        <i class="fas fa-sign-out-alt"></i> Leave Club
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Leave Club Modal for each club -->
                    <div class="modal fade" id="leaveClubModal-<?= $club['club_member_id'] ?>" tabindex="-1" aria-labelledby="leaveClubModalLabel-<?= $club['club_member_id'] ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="leaveClubModalLabel-<?= $club['club_member_id'] ?>">Leave Club Confirmation</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <i class="fas fa-exclamation-triangle modal-icon leave-icon"></i>
                                    <h4>Are you sure you want to leave</h4>
                                    <h3 class="text-primary mb-4"><?= htmlspecialchars($club['club_name']) ?>?</h3>
                                    <p>Leaving this club will remove you from all club activities and communications. You can always join again later if you change your mind.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <form action="sections/delete_membership.php" method="POST">
                                        <input type="hidden" name="club_member_id" value="<?= $club['club_member_id'] ?>">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-sign-out-alt"></i> Yes, Leave Club
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-user-friends"></i>
                <h3>No Clubs Joined Yet</h3>
                <p>You haven't joined any clubs. Browse the available clubs below and find one that interests you!</p>
            </div>
        <?php endif; ?>
    </section>

    <hr class="section-divider">

    <section id="available-clubs">
        <h2 class="section-title"><i class="fas fa-search"></i> Available Clubs to Join</h2>

        <?php if (count($available_clubs) > 0): ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($available_clubs as $club): ?>
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column">
                                <h3 class="card-title"><?= htmlspecialchars($club['club_name']) ?></h3>
                                
                                <?php if (!empty($club['description'])): ?>
                                    <p class="card-description"><?= htmlspecialchars(substr($club['description'], 0, 100)) ?><?= strlen($club['description']) > 100 ? '...' : '' ?></p>
                                <?php endif; ?>

                                <div class="card-actions mt-auto">
                                    <a href="view_club.php?id=<?= $club['id'] ?>" class="btn btn-primary w-100 mb-2 btn-icon">
                                        <i class="fas fa-info-circle"></i> View Details
                                    </a>

                                    <button type="button" class="btn btn-success w-100 btn-icon" 
                                           data-bs-toggle="modal" 
                                           data-bs-target="#joinClubModal-<?= $club['id'] ?>"
                                           data-club-name="<?= htmlspecialchars($club['club_name']) ?>"
                                           data-club-id="<?= $club['id'] ?>">
                                        <i class="fas fa-plus-circle"></i> Join Club
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Join Club Modal for each club -->
                    <div class="modal fade" id="joinClubModal-<?= $club['id'] ?>" tabindex="-1" aria-labelledby="joinClubModalLabel-<?= $club['id'] ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="joinClubModalLabel-<?= $club['id'] ?>">Join Club Confirmation</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <i class="fas fa-handshake modal-icon join-icon"></i>
                                    <h4>You're about to join</h4>
                                    <h3 class="text-primary mb-4"><?= htmlspecialchars($club['club_name']) ?></h3>
                                    <p>By joining this club, you agree to participate in club activities and receive communications from club administrators.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <form action="sections/join_club.php" method="POST">
                                        <input type="hidden" name="club_id" value="<?= $club['id'] ?>">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-plus-circle"></i> Yes, Join Club
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-clipboard-list"></i>
                <h3>No Available Clubs</h3>
                <p>There are no available clubs to join at the moment. Check back later!</p>
            </div>
        <?php endif; ?>
    </section>

<script>
    // Auto-dismiss flash messages after 5 seconds
    document.addEventListener('DOMContentLoaded', () => {
        const flashMessage = document.getElementById('flashMessage');
        if (flashMessage) {
            setTimeout(() => {
                const closeButton = flashMessage.querySelector('.btn-close');
                if (closeButton) {
                    closeButton.click();
                }
            }, 3000);
        }
    });
</script>



<?php include 'includes/footer.php'; ?>