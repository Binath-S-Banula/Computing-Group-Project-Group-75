<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="css/view_members.css">


<?php
// Fetch club_id of this club admin
$stmt = $pdo->prepare("SELECT id FROM club_admins WHERE id = ?");
$stmt->execute([$_SESSION['club_admin_uid']]);
$club = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$club) {
    die("Club not found.");
}
$club_id = $club['id'];

// Handle flash message
$flash_message = '';
$flash_type = '';
if (isset($_SESSION['flash_message'])) {
    $flash_message = $_SESSION['flash_message'];
    $flash_type = $_SESSION['flash_type'];
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
}

// Fetch members
$stmt = $pdo->prepare("
    SELECT cm.id as club_member_id, s.name, s.email, cm.status
    FROM club_members cm
    JOIN students s ON cm.student_id = s.id
    WHERE cm.club_id = ?
    ORDER BY FIELD(cm.status, 'active', 'inactive', 'blacklisted')
");
$stmt->execute([$club_id]);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

    <!-- Page Header -->
    <div class="dashboard-title">
        <h2>Club Members</h2>
        <p>Manage members of the club </p>
    </div>

    <?php if (!empty($flash_message)): ?>
        <div id="flashMessage" class="alert alert-<?= htmlspecialchars($flash_type) ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flash_message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="content-card">
        <!-- Search and Filter -->
        <div class="row mb-3 align-items-center">
    <div class="col-md-8">
        <div class="search-wrapper d-flex align-items-center">
            <i class="fas fa-search search-icon me-2"></i>
            <input type="text" id="memberSearch" class="search-input" placeholder="Search members..." style="max-width: 300px; width: 100%;">
        </div>
    </div>
    <div class="col-md-4 text-end mt-2 mt-md-0">
        <div class="status-filter d-flex justify-content-end">
            <button class="filter-btn active" data-filter="all">All</button>
            <button class="filter-btn" data-filter="active">Active</button>
            <button class="filter-btn" data-filter="inactive">Inactive</button>
            <button class="filter-btn" data-filter="blacklisted">Blacklisted</button>
        </div>
    </div>
</div>



        <?php if (count($members) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $modals = ''; ?>
                        <?php foreach ($members as $member): ?>
                            <tr class="member-row" data-status="<?= htmlspecialchars($member['status']) ?>">
                                <td>
                                    <div class="member-info">
                                        <div class="avatar"><?= strtoupper(substr($member['name'], 0, 1)) ?></div>
                                        <?= htmlspecialchars($member['name']) ?>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($member['email']) ?></td>
                                <td>
                                    <span class="badge badge-<?= htmlspecialchars($member['status']) ?>">
                                        <?= ucfirst(htmlspecialchars($member['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($member['status'] !== 'blacklisted'): ?>
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#blacklistModal<?= $member['club_member_id'] ?>">
                                            <i class="fas fa-ban"></i> Blacklist
                                        </button>
                                    <?php else: ?>
                                        <form action="sections/remove_blacklist.php" method="POST" style="display: inline;">
                                            <input type="hidden" name="club_member_id" value="<?= $member['club_member_id'] ?>">
                                            <button class="btn btn-secondary btn-sm">
                                                <i class="fas fa-user-check"></i> Remove Blacklist
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-danger btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#removeModal<?= $member['club_member_id'] ?>">
                                        <i class="fas fa-trash-alt"></i> Remove
                                    </button>
                                </td>
                            </tr>

                            <?php
                            $modals .= '
                            <!-- Blacklist Modal -->
                            <div class="modal fade" id="blacklistModal'.$member['club_member_id'].'" tabindex="-1" aria-labelledby="blacklistModalLabel'.$member['club_member_id'].'" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="blacklistModalLabel'.$member['club_member_id'].'">
                                                <i class="fas fa-ban me-2"></i> Confirm Blacklist
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Are you sure you want to blacklist this member?</p>
                                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                                <div class="avatar">'.strtoupper(substr($member['name'], 0, 1)).'</div>
                                                <div>
                                                    <strong>'.htmlspecialchars($member['name']).'</strong><br>
                                                    <small class="text-muted">'.htmlspecialchars($member['email']).'</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <form action="sections/blacklist_member.php" method="POST" style="display: inline;">
                                                <input type="hidden" name="club_member_id" value="'.$member['club_member_id'].'">
                                                <button type="submit" class="btn btn-warning">
                                                    <i class="fas fa-ban me-1"></i> Yes, Blacklist
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Remove Modal -->
                            <div class="modal fade" id="removeModal'.$member['club_member_id'].'" tabindex="-1" aria-labelledby="removeModalLabel'.$member['club_member_id'].'" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="removeModalLabel'.$member['club_member_id'].'">
                                                <i class="fas fa-trash-alt me-2"></i> Confirm Remove
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Are you sure you want to permanently remove this member?</p>
                                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                                <div class="avatar">'.strtoupper(substr($member['name'], 0, 1)).'</div>
                                                <div>
                                                    <strong>'.htmlspecialchars($member['name']).'</strong><br>
                                                    <small class="text-muted">'.htmlspecialchars($member['email']).'</small>
                                                </div>
                                            </div>
                                            <div class="alert alert-danger mt-3">
                                                <i class="fas fa-exclamation-triangle me-2"></i> This action cannot be undone.
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <form action="sections/remove_member.php" method="POST" style="display: inline;">
                                                <input type="hidden" name="club_member_id" value="'.$member['club_member_id'].'">
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="fas fa-trash-alt me-1"></i> Yes, Remove
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ';
                            ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?= $modals ?>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> No members found for your club.
            </div>
        <?php endif; ?>
    </div>

<script>
// Search and Filter
document.addEventListener('DOMContentLoaded', function() {
    var flash = document.getElementById('flashMessage');
    if (flash) {
        setTimeout(function() {
            flash.classList.add('fade');
            flash.classList.remove('show');
            setTimeout(function() {
                if (flash) flash.remove();
            }, 500);
        }, 3000);
    }

    const searchInput = document.getElementById('memberSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.member-row');
            rows.forEach(row => {
                const name = row.querySelector('.member-info').textContent.toLowerCase();
                const email = row.querySelectorAll('td')[1].textContent.toLowerCase();
                if (name.includes(searchTerm) || email.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    const filterButtons = document.querySelectorAll('.filter-btn');
    if (filterButtons.length) {
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                const filter = this.getAttribute('data-filter');
                const rows = document.querySelectorAll('.member-row');
                rows.forEach(row => {
                    if (filter === 'all' || row.getAttribute('data-status') === filter) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>
