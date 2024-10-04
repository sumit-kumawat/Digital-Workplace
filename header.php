<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-PnFHSBH/w1BXLs94qZ9tQpaH02z1aI7ZJDCP9NHhjA25xFZ/4NQ8AYiiJ5+J4a8YI8XaAFvh8P+X2p26PL+e4A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Your custom styles -->
    <link rel="stylesheet" href="styles.css">

    <title></title>
</head>
<body>
    <header class="header sticky-top bg-light shadow-sm">
        <div class="logo">
            <img src="../images/logo.png" alt="Logo">
        </div>
        <nav class="navbar navbar-expand-lg navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="../index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="consoleDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-cogs"></i> Console
                    </a>
                    <div class="dropdown-menu" aria-labelledby="consoleDropdown">
                        <a class="dropdown-item" href="../console/AssetPV.php"><i class="fas fa-box"></i> Asset Console</a>
                        <a class="dropdown-item" href="../console/IncidentPV.php"><i class="fas fa-ticket-alt"></i> Ticket Console</a>                        
                        <a class="dropdown-item" href="../console/EmployeeView.php"><i class="fas fa-user"></i> Employee Console</a>
                        <a class="dropdown-item" href="../console/CompanyProfile.php"><i class="fas fa-building"></i> Company Console</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="createNewDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-plus"></i> Create New
                    </a>
                    <div class="dropdown-menu" aria-labelledby="createNewDropdown">
                        <a class="dropdown-item" href="../create/AssetPV.php"><i class="fas fa-box"></i> Asset</a>
                        <a class="dropdown-item" href="../create/IncidentPV.php"><i class="fas fa-exclamation-circle"></i> Incident</a>
                        <a class="dropdown-item" href="../create/ReleasePV.php"><i class="fas fa-calendar-check"></i> Release</a>
                        <a class="dropdown-item" href="../create/WorkorderPV.php"><i class="fas fa-tasks"></i> Work Order</a>
                        <a class="dropdown-item" href="../create/KnowledgePV.php"><i class="fas fa-book"></i> Knowledge</a>
                        <a class="dropdown-item" href="../create/RegCompanyPV.php"><i class="fas fa-building"></i> Register Company</a>
                        <a class="dropdown-item" href="../create/RegEmployeePV.php"><i class="fas fa-user-plus"></i> Register Employee</a>
                        <a class="dropdown-item" href="../create/SupportGroupPV.php"><i class="fas fa-users-cog"></i> Support Group</a>
                        <a class="dropdown-item" href="../create/ReportingMgrPV.php"><i class="fas fa-user-tie"></i> Reporting Manager</a>
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

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-r6U4lBOjN+1msyZDHRlOzxRSKsH7FdTjW0HePcmI2tSNjfF9i04pbrEVR0e+Q8vJ" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-b4gt1jrGC7Jh4AgTPSdUtOBvfO8sh+xr1+0nq9v9sga3gpxtK1WI7f5/K2JRM2y8" crossorigin="anonymous"></script>
</body>
</html>
