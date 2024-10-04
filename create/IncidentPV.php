<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

// Database connection
$host = 'localhost';
$db = 'dwp';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit();
}

// Handle customer search
if (isset($_GET['search'])) {
    $searchValue = '%' . $_GET['search'] . '%';
    $stmt = $pdo->prepare("SELECT employee_id, CONCAT(first_name, ' ', last_name) AS full_name, username, email FROM employees WHERE employee_id LIKE ? OR username LIKE ? OR email LIKE ? OR CONCAT(first_name, ' ', last_name) LIKE ?");
    $stmt->execute([$searchValue, $searchValue, $searchValue, $searchValue]);
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($employees as $employee) {
        echo "<div class='employee-item' data-id='{$employee['employee_id']}' data-username='{$employee['username']}' data-email='{$employee['email']}' data-full-name='{$employee['full_name']}'>";
        echo "{$employee['full_name']} ({$employee['employee_id']})";
        echo "</div>";
    }
    exit();
}

// Handle support group search
if (isset($_GET['search_support_group'])) {
    $searchValue = '%' . $_GET['search_support_group'] . '%';
    $stmt = $pdo->prepare("SELECT DISTINCT support_group FROM employees WHERE support_group LIKE ?");
    $stmt->execute([$searchValue]);
    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($groups as $group) {
        echo "<div class='support-group-item' data-support-group='{$group['support_group']}'>";
        echo "{$group['support_group']}";
        echo "</div>";
    }
    exit();
}

// Handle incident form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'];
    $summary = $_POST['summary'];
    $description = $_POST['description'];
    $impact = $_POST['impact'];
    $urgency = $_POST['urgency'];
    $status = $_POST['status'];
    $incident_type = $_POST['incident_type'];
    $reported_source = $_POST['reported_source'];
    $support_group = $_POST['support_group'];
    $assignee = $_POST['assignee'];
    $company = $_POST['company'];

    $stmt = $pdo->prepare("INSERT INTO incidents (customer_id, summary, description, impact, urgency, status, incident_type, reported_source, support_group, assignee, company, created_at, last_modified_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    $stmt->execute([$customer_id, $summary, $description, $impact, $urgency, $status, $incident_type, $reported_source, $support_group, $assignee, $company]);

    header('Location: incident_form.php'); // Redirect after submission
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incident Form</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/header.css">
    <style>
        .employee-item, .support-group-item {
            cursor: pointer;
            padding: 5px;
            border: 1px solid #ddd;
            margin-bottom: 5px;
        }
        .employee-item:hover, .support-group-item:hover {
            background-color: #f0f0f0;
        }
        .search-results {
            border: 1px solid #ddd;
            max-height: 150px;
            overflow-y: auto;
            position: absolute;
            background: #fff;
            z-index: 1000;
            width: calc(100% - 2px);
        }
    </style>
</head>
<body>

<?php include '../header.php'; ?>

<div class="container mt-5">
    <h2>Create Incident</h2>
    <hr>
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-section">
            <h4>Customer Details</h4>
            <hr>
            <div class="form-row">
                <div class="form-group col-md-4 position-relative">
                    <label for="customer_search">Customer Name</label>
                    <input type="text" id="customer_search" name="customer_search" class="form-control" placeholder="Search by name, employeeID, username" required>
                    <input type="hidden" id="customer_id" name="customer_id">
                    <div id="customer_results" class="search-results"></div>
                </div>
                <div class="form-group col-md-4">
                    <label for="employee_id">Employee ID</label>
                    <input type="text" id="employee_id" name="employee_id" class="form-control" disabled>
                </div>
                <div class="form-group col-md-4">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" disabled>
                </div>
                <div class="form-group col-md-4">
                    <label for="company">Company</label>
                    <input type="text" id="company" name="company" class="form-control" disabled>
                </div>
            </div>
        </div>
        <br>
        <div class="form-section" id="incidentDetailsSection">
            <h4>Incident Details</h4>
            <hr>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="summary">Summary *</label>
                    <input type="text" id="summary" name="summary" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control"></textarea>
                </div>
                <div class="form-group col-md-4">
                    <label for="impact">Impact</label>
                    <select id="impact" name="impact" class="form-control">
                        <option value="1-Extensive/Widespread">1-Extensive/Widespread</option>
                        <option value="2-Significant/Large">2-Significant/Large</option>
                        <option value="3-Moderate/Limited">3-Moderate/Limited</option>
                        <option value="4-Minor/Localize">4-Minor/Localize</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="urgency">Urgency</label>
                    <select id="urgency" name="urgency" class="form-control">
                        <option value="1-Critical">1-Critical</option>
                        <option value="2-High">2-High</option>
                        <option value="3-Medium">3-Medium</option>
                        <option value="4-Low">4-Low</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="status">Status *</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="New">New</option>
                        <option value="Assigned">Assigned</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Pending">Pending</option>
                        <option value="Resolved">Resolved</option>
                        <option value="Closed">Closed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="incident_type">Incident Type *</label>
                    <select id="incident_type" name="incident_type" class="form-control" required>
                        <option value="User Service Restoration">User Service Restoration</option>
                        <option value="User Service Request">User Service Request</option>
                        <option value="Infrastructure Restoration">Infrastructure Restoration</option>
                        <option value="Infrastructure Event">Infrastructure Event</option>
                        <option value="Security Incident">Security Incident</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="reported_source">Reported Source</label>
                    <select id="reported_source" name="reported_source" class="form-control">
                        <option value="Direct Input">Direct Input</option>
                        <option value="Email">Email</option>
                    </select>
                </div>
            </div>
        </div>
        <br>
        <div class="form-section">
            <h4>Support Group and Assignee</h4>
            <hr>
            <div class="form-row">
                <div class="form-group col-md-4 position-relative">
                    <label for="support_group_search">Support Group</label>
                    <input type="text" id="support_group_search" name="support_group_search" class="form-control" required>
                    <div id="support_group_results" class="search-results"></div>
                </div>
                <div class="form-group col-md-4">
                    <label for="assignee">Assignee</label>
                    <input type="text" id="assignee" name="assignee" class="form-control" required>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
<br>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script>
    function debounce(func, delay) {
        let timer;
        return function(...args) {
            clearTimeout(timer);
            timer = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // Live search for customer
    $('#customer_search').on('input', debounce(function() {
        let query = $(this).val();
        if (query.length > 2) {
            $.ajax({
                url: 'IncidentPV.php',
                method: 'GET',
                data: { search: query },
                success: function(data) {
                    $('#customer_results').html(data).fadeIn();
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                }
            });
        } else {
            $('#customer_results').fadeOut();
        }
    }, 300));

    // Select customer and populate the form
    $(document).on('click', '.employee-item', function() {
        $('#customer_id').val($(this).data('id'));
        $('#employee_id').val($(this).data('id'));
        $('#username').val($(this).data('username'));
        $('#company').val($(this).data('company'));
        $('#customer_results').fadeOut();
    });

    // Live search for support group
    $('#support_group_search').on('input', debounce(function() {
        let query = $(this).val();
        if (query.length > 2) {
            $.ajax({
                url: 'IncidentPV.php',
                method: 'GET',
                data: { search_support_group: query },
                success: function(data) {
                    $('#support_group_results').html(data).fadeIn();
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                }
            });
        } else {
            $('#support_group_results').fadeOut();
        }
    }, 300));

    // Select support group and populate the form
    $(document).on('click', '.support-group-item', function() {
        $('#support_group_search').val($(this).data('support-group'));
        $('#assignee').val($(this).data('assignee'));
        $('#support_group_results').fadeOut();
    });
</script>

</body>
</html>
