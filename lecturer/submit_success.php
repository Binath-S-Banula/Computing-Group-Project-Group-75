<?php
// Get the proposal ID from the query string (if available)
$proposal_id = isset($_GET['id']) ? $_GET['id'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submission Successful</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Cloudflare Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            --danger: #dc3545;
            --warning: #ffc107;
            --border-radius: 0.75rem;
            --box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            background-color: #f9fafb;
            color: #333;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .page-container {
            max-width: 800px;
            width: 100%;
            padding: 1rem;
        }

        .success-card {
            background: var(--white);
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            text-align: center;
            transition: var(--transition);
            border-top: 5px solid var(--primary-green);
        }

        .success-icon {
            font-size: 5rem;
            color: var(--primary-green);
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }

        .success-title {
            font-size: 2rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 15px;
        }

        .success-message {
            font-size: 1.1rem;
            color: var(--neutral-dark);
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .proposal-id {
            background-color: var(--primary-light);
            color: var(--primary-dark);
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 50px;
            margin: 20px 0;
            display: inline-block;
        }

        .info-box {
            background-color: rgba(30, 142, 78, 0.08);
            border-radius: var(--border-radius);
            padding: 20px;
            margin: 25px 0;
            text-align: left;
        }

        .info-box ul {
            margin-bottom: 0;
            padding-left: 20px;
        }

        .info-box li {
            margin-bottom: 8px;
        }

        .info-box li:last-child {
            margin-bottom: 0;
        }

        .btn {
            padding: 12px 28px;
            border-radius: var(--border-radius);
            font-weight: 500;
            transition: var(--transition);
            border: none;
            margin: 5px;
        }

        .btn-primary {
            background-color: var(--primary-green);
            color: var(--white);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(30, 142, 78, 0.2);
        }

        .btn-outline {
            background-color: transparent;
            color: var(--primary-green);
            border: 2px solid var(--primary-green);
        }

        .btn-outline:hover {
            background-color: var(--primary-light);
            transform: translateY(-3px);
        }

        .button-group {
            margin-top: 30px;
        }

        .btn i {
            margin-right: 8px;
        }

        @media (max-width: 768px) {
            .success-card {
                padding: 25px;
            }
            
            .success-title {
                font-size: 1.75rem;
            }
            
            .button-group {
                display: flex;
                flex-direction: column;
            }
            
            .btn {
                margin: 5px 0;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="success-card">
            <div class="success-icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>

            <h1 class="success-title">Submission Successful!</h1>
            
            <p class="success-message">
                Your timetable change proposal has been successfully submitted for review.
                <?php if ($proposal_id): ?>
                    You can track your proposal using the ID below.
                <?php endif; ?>
            </p>

            <?php if ($proposal_id): ?>
                <div class="proposal-id">
                    <i class="fa-solid fa-tag"></i> Proposal ID: <?= htmlspecialchars($proposal_id) ?>
                </div>
            <?php endif; ?>

            <div class="info-box">
                <h4><i class="fa-solid fa-info-circle"></i> What happens next?</h4>
                <ul>
                    <li>Your proposal has been sent to the administration team for review.</li>
                    <li>You will receive an email notification once your proposal has been approved or rejected.</li>
                    <li>The typical review process takes 1-2 business days.</li>
                    <li>If you have any questions, please contact the timetable administration office.</li>
                </ul>
            </div>

            <div class="button-group">
                <a href="lecturer_timetable.php" class="btn btn-primary">
                    <i class="fa-solid fa-home"></i> Return to Dashboard
                </a>
                <a href="lecturer_timetable.php" class="btn btn-outline">
                    <i class="fa-solid fa-list"></i> View My Proposals
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>