<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dwp";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assign_reporting_manager'])) {
    $employee_id = $_POST['employee_id'];
    $reporting_manager_id = $_POST['reporting_manager_id'];

    $update_query = "UPDATE employees SET reporting_manager = ? WHERE employee_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ii", $reporting_manager_id, $employee_id);

    if ($stmt->execute()) {
        // Fetch the new reporting manager's name
        $manager_query = "SELECT CONCAT(first_name, ' ', last_name) AS manager_name FROM employees WHERE employee_id = ?";
        $stmt = $conn->prepare($manager_query);
        $stmt->bind_param("i", $reporting_manager_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $manager = $result->fetch_assoc();

        echo "<script>
                alert('Reporting manager assigned successfully');
                document.getElementById('current_manager_name').innerText = '{$manager['manager_name']}';
              </script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

// Fetch employees for live search
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $query = "SELECT employee_id, CONCAT(first_name, ' ', last_name) AS full_name, email FROM employees WHERE CONCAT(employee_id, ' ', first_name, ' ', last_name, ' ', email) LIKE ?";
    $stmt = $conn->prepare($query);
    $search_param = "%$search%";
    $stmt->bind_param("s", $search_param);
    $stmt->execute();
    $result = $stmt->get_result();

    $employees = [];
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }

    $stmt->close();
    $conn->close();

    // Output the table rows for AJAX
    foreach ($employees as $employee) {
        echo "<tr>";
        echo "<td>{$employee['employee_id']}</td>";
        echo "<td>{$employee['full_name']}</td>";
        echo "<td>{$employee['email']}</td>";
        echo "<td class='table-actions'>";
        echo "<i class='fas fa-edit action-icons text-primary' data-toggle='modal' data-target='#assignManagerModal' data-id='{$employee['employee_id']}' data-full-name='{$employee['full_name']}' data-email='{$employee['email']}'></i>";
        echo "</td>";
        echo "</tr>";
    }
    exit();
}

// Fetch reporting managers for live search
if (isset($_GET['search_reporting_manager'])) {
    $search = $_GET['search_reporting_manager'];
    $query = "SELECT employee_id, CONCAT(first_name, ' ', last_name) AS name FROM employees WHERE CONCAT(first_name, ' ', last_name) LIKE ?";
    $stmt = $conn->prepare($query);
    $search_param = "%$search%";
    $stmt->bind_param("s", $search_param);
    $stmt->execute();
    $result = $stmt->get_result();

    $managers = [];
    while ($row = $result->fetch_assoc()) {
        $managers[] = $row;
    }

    $stmt->close();
    $conn->close();

    // Output the search results for AJAX
    foreach ($managers as $manager) {
        echo "<div class='reporting-manager-item' data-id='{$manager['employee_id']}'>{$manager['name']}</div>";
    }
    exit();
}

// Fetch employees for initial page load
$query = "SELECT employee_id, CONCAT(first_name, ' ', last_name) AS full_name, email, reporting_manager FROM employees";
$result = $conn->query($query);

$employees = [];
while ($row = $result->fetch_assoc()) {
    $employees[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/header.css">
    <title>Assign Reporting Manager</title>
    <style>
        .action-icons {
            cursor: pointer;
            margin: 0 5px;
        }
        .modal-content {
            border-radius: 5px;
        }
        .modal-header,
        .modal-footer {
            border: none;
        }
        .table thead th {
            background-color: #f8f9fa;
            color: #343a40;
        }
        .table-hover tbody tr:hover {
            background-color: #e9ecef;
        }
        .table-actions {
            display: flex;
            justify-content: center;
        }
        .reporting-manager-item {
            cursor: pointer;
            padding: 5px;
            border-bottom: 1px solid #ddd;
        }
        .reporting-manager-item:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>

<?php include '../header.php'; ?>

<div class="container mt-5">
    <h2 class="mt-5 mb-4 text-center">Assign Reporting Manager</h2>
    <input type="text" id="search" class="form-control mb-3" placeholder="Search for employees...">
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Employee ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody id="employees_table">
            <?php foreach ($employees as $employee): ?>
            <tr>
                <td><?php echo $employee['employee_id']; ?></td>
                <td><?php echo $employee['full_name']; ?></td>
                <td><?php echo $employee['email']; ?></td>
                <td class="table-actions">
                    <i class="fas fa-edit action-icons text-primary" data-toggle="modal" data-target="#assignManagerModal" data-id="<?php echo $employee['employee_id']; ?>" data-full-name="<?php echo $employee['full_name']; ?>" data-email="<?php echo $employee['email']; ?>" data-reporting-manager="<?php echo $employee['reporting_manager']; ?>"></i>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Assign Reporting Manager Modal -->
<div class="modal fade" id="assignManagerModal" tabindex="-1" aria-labelledby="assignManagerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignManagerModalLabel">Assign Reporting Manager</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" id="employee_id" name="employee_id">
                    <div class="form-group">
                        <label for="employee_name">Employee Name</label>
                        <input type="text" class="form-control" id="employee_name" disabled>
                    </div>
                    <div class="form-group">
                        <label for="employee_email">Email</label>
                        <input type="email" class="form-control" id="employee_email" disabled>
                    </div>
                    <div class="form-group">
                        <label for="current_manager_name">Current Reporting Manager</label>
                        <p id="current_manager_name">None</p>
                    </div>
                    <div class="form-group">
                        <label for="reporting_manager_search">Search New Reporting Manager</label>
                        <input type="text" class="form-control" id="reporting_manager_search" placeholder="Search...">
                        <div id="reporting_manager_list"></div>
                        <input type="hidden" id="reporting_manager_id" name="reporting_manager_id">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="assign_reporting_manager" class="btn btn-primary">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        // Handle search input
        $('#search').on('input', function() {
            var search = $(this).val();
            $.ajax({
                url: 'ReportingMgrPV.php',
                type: 'GET',
                data: { search: search },
                success: function(data) {
                    $('#employees_table').html(data);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                }
            });
        });

        // Handle search for reporting managers
        $('#reporting_manager_search').on('input', function() {
            var search = $(this).val();
            $.ajax({
                url: 'ReportingMgrPV.php',
                type: 'GET',
                data: { search_reporting_manager: search },
                success: function(data) {
                    $('#reporting_manager_list').html(data);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                }
            });
        });

        // Populate modal with employee details
        $('#assignManagerModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var employeeId = button.data('id');
            var fullName = button.data('full-name');
            var email = button.data('email');
            var reportingManager = button.data('reporting-manager');

            var modal = $(this);
            modal.find('#employee_id').val(employeeId);
            modal.find('#employee_name').val(fullName);
            modal.find('#employee_email').val(email);

            // Fetch current manager details
            $.ajax({
                url: 'ReportingMgrPV.php',
                type: 'GET',
                data: { reporting_manager_id: reportingManager },
                success: function(data) {
                    $('#current_manager_name').text(data || 'None');
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                }
            });
        });

        // Set reporting manager ID on selection
        $(document).on('click', '.reporting-manager-item', function() {
            var managerId = $(this).data('id');
            var managerName = $(this).text();
            $('#reporting_manager_id').val(managerId);
            $('#current_manager_name').text(managerName);
        });
    });
</script>
</body>
</html>
