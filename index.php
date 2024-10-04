<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit();
}

// Fetch activity logs
$logs = [];
$mysqli = new mysqli("localhost", "root", "", "dwp");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
$result = $mysqli->query("SELECT * FROM activity_logs WHERE user_id = " . $_SESSION['user_id']);
if ($result) {
    $logs = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITSM Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #f8f9fa;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header .logo img {
            max-height: 40px;
        }
        .dropdown-menu {
            min-width: 250px;
        }
        .dropdown-item i {
            margin-right: 10px;
        }
        .nav-link {
            margin-right: 15px;
        }
        .nav-link.active {
            font-weight: bold;
        }
    </style>
</head>
<body>

<header class="header sticky-top bg-light shadow-sm">
        <div class="logo">
            <img src="./images/logo.png" alt="ITSM Logo">
        </div>
        <nav class="navbar navbar-expand-lg navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="./index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="consoleDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-cogs"></i> Console
                    </a>
                    <div class="dropdown-menu" aria-labelledby="consoleDropdown">
                        <a class="dropdown-item" href="./console/AssetPV.php"><i class="fas fa-box"></i> Asset Console</a>
                        <a class="dropdown-item" href="./console/IncidentPV.php"><i class="fas fa-ticket-alt"></i> Ticket Console</a>                        
                        <a class="dropdown-item" href="./console/EmployeeView.php"><i class="fas fa-user"></i> Employee Console</a>
                        <a class="dropdown-item" href="./console/CompanyProfile.php"><i class="fas fa-building"></i> Company Console</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="createNewDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-plus"></i> Create New
                    </a>
                    <div class="dropdown-menu" aria-labelledby="createNewDropdown">
                        <a class="dropdown-item" href="./create/AssetPV.php"><i class="fas fa-box"></i> Asset</a>
                        <a class="dropdown-item" href="./create/IncidentPV.php"><i class="fas fa-exclamation-circle"></i> Incident</a>
                        <a class="dropdown-item" href="./create/ReleasePV.php"><i class="fas fa-calendar-check"></i> Release</a>
                        <a class="dropdown-item" href="./create/WorkorderPV.php"><i class="fas fa-tasks"></i> Work Order</a>
                        <a class="dropdown-item" href="./create/KnowledgePV.php"><i class="fas fa-book"></i> Knowledge</a>
                        <a class="dropdown-item" href="./create/RegCompanyPV.php"><i class="fas fa-building"></i> Register Company</a>
                        <a class="dropdown-item" href="./create/RegEmployeePV.php"><i class="fas fa-user-plus"></i> Register Employee</a>
                        <a class="dropdown-item" href="./create/SupportGroupPV.php"><i class="fas fa-users-cog"></i> Support Group</a>
                        <a class="dropdown-item" href="./create/ReportingMgrPV.php"><i class="fas fa-user-tie"></i> Reporting Manager</a>
                    </div>
                </li>
            </ul>
        </nav>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center dropdown-toggle" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-user-circle fa-2x"></i>
                <span class="ml-2"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#updateProfileModal">
                    <i class="fas fa-user-edit"></i> Update Profile
                </a>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#activityLogModal">
                    <i class="fas fa-list"></i> Activity Log
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </header>

    <div class="container mt-4">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <p>You are now logged in.</p>
    </div>

    <!-- Update Profile Modal -->
    <div class="modal fade" id="updateProfileModal" tabindex="-1" role="dialog" aria-labelledby="updateProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateProfileModalLabel">Update Profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="updateProfileForm">
                        <div class="form-group">
                            <label for="currentPassword">Current Password</label>
                            <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
                        </div>
                        <div class="form-group">
                            <label for="newPassword">New Password</label>
                            <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Log Modal -->
    <div class="modal fade" id="activityLogModal" tabindex="-1" role="dialog" aria-labelledby="activityLogModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="activityLogModalLabel">Activity Log</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        <?php if (!empty($logs)): ?>
                            <?php foreach ($logs as $log): ?>
                                <li class="list-group-item">
                                    <?php echo htmlspecialchars($log['activity']); ?> <small class="text-muted"><?php echo htmlspecialchars($log['timestamp']); ?></small>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="list-group-item">No activity logs found.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#updateProfileForm').on('submit', function(e) {
                e.preventDefault();
                // Add AJAX code to submit form data to the server
                alert('Profile update functionality is not yet implemented.');
            });
        });
    </script>
</body>
</html>
