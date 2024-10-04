<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Database connection
$servername = "localhost";
$dbusername = "root";
$password = "";
$dbname = "dwp";

$conn = new mysqli($servername, $dbusername, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch employee records
$sql = "SELECT e.employee_id, e.first_name, e.last_name, e.username, e.email
        FROM employees e
        ORDER BY e.employee_id ASC";
$result = $conn->query($sql);

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['employee_id'])) {
    // Fetch detailed employee information
    $employee_id = $_GET['employee_id'];
    $stmt = $conn->prepare("SELECT e.*, o.company_name AS organization, sg.name AS support_group, rm.name AS reporting_manager
                            FROM employees e
                            LEFT JOIN company o ON e.organization = o.id
                            LEFT JOIN support_groups sg ON e.support_group = sg.id
                            LEFT JOIN reporting_managers rm ON e.reporting_manager = rm.id
                            WHERE e.employee_id = ?");
    $stmt->bind_param("s", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $details = "
            <p><strong>Employee ID:</strong> {$row['employee_id']}</p>
            <p><strong>Full Name:</strong> {$row['first_name']} {$row['last_name']}</p>
            <p><strong>Username:</strong> {$row['username']}</p>
            <p><strong>Email:</strong> {$row['email']}</p>
            <p><strong>Gender:</strong> {$row['gender']}</p>
            <p><strong>Phone Number:</strong> {$row['phone_number']}</p>
            <p><strong>Date of Birth:</strong> " . date("d M Y", strtotime($row['dob'])) . "</p>
            <p><strong>Address:</strong> {$row['address']}</p>
            <p><strong>City:</strong> {$row['city']}</p>
            <p><strong>Postal Code:</strong> {$row['postal_code']}</p>
            <p><strong>Country:</strong> {$row['country']}</p>
            <p><strong>Organization:</strong> {$row['organization']}</p>
            <p><strong>Support Group:</strong> {$row['support_group']}</p>
            <p><strong>Reporting Manager:</strong> {$row['reporting_manager']}</p>
            <p><strong>Documents:</strong></p>
            <ul>";
        foreach (['passport_photo', 'govt_id_proof', 'tenth_certificate', 'twelfth_certificate', 'graduation_certificate', 'post_graduation_certificate', 'other_qualification', 'passport'] as $field) {
            if (!empty($row[$field])) {
                $details .= "<li><a href='../uploads/Employees/{$row[$field]}' target='_blank'>" . ucfirst(str_replace('_', ' ', $field)) . "</a></li>";
            }
        }
        $details .= "</ul>";
    } else {
        $details = "Employee not found.";
    }
    $stmt->close();
    $conn->close();
    echo $details;
    exit();
}
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
    <title>Employee Records</title>
    <style>
        .card {
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: box-shadow 0.3s ease;
        }
        .card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .card-body {
            padding: 15px;
        }
        .card-title {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }
        .card-text {
            font-size: 0.875rem;
            color: #6c757d;
        }
        .list-item {
            margin-bottom: 10px;
        }
        .modal-body {
            max-height: calc(100vh - 210px);
            overflow-y: auto;
        }
    </style>
</head>
<body>

<?php include '../header.php'; ?>

<div class="container mt-5">
    <h2>Employee Records</h2>
    <hr>

    <div class="form-group">
        <input type="text" id="search" class="form-control" placeholder="Search by Employee ID, Name, Username, or Email">
    </div>

    <div class="row" id="employee-list">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card" data-id="<?php echo $row['employee_id']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row['first_name'] . " " . $row['last_name']; ?></h5>
                            <p class="card-text"><strong>Employee ID:</strong> <?php echo $row['employee_id']; ?></p>
                            <p class="card-text"><strong>Username:</strong> <?php echo $row['username']; ?></p>
                            <p class="card-text"><strong>Email:</strong> <?php echo $row['email']; ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <p>No employee records found.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="employeeModal" tabindex="-1" role="dialog" aria-labelledby="employeeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="employeeModalLabel">Employee Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="employee-details">
                <!-- Detailed information will be loaded here via AJAX -->
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        // Live search
        $('#search').on('input', function() {
            var query = $(this).val().toLowerCase();
            $('#employee-list .card').each(function() {
                var name = $(this).find('.card-title').text().toLowerCase();
                var empId = $(this).find('.card-text').eq(0).text().toLowerCase();
                var username = $(this).find('.card-text').eq(1).text().toLowerCase();
                var email = $(this).find('.card-text').eq(2).text().toLowerCase();
                $(this).toggle(name.indexOf(query) > -1 || empId.indexOf(query) > -1 || username.indexOf(query) > -1 || email.indexOf(query) > -1);
            });
        });

        // Load employee details in modal
        $('#employee-list').on('click', '.card', function() {
            var employeeId = $(this).data('id');
            $.ajax({
                url: '',
                method: 'GET',
                data: { employee_id: employeeId },
                success: function(response) {
                    $('#employeeModal').find('#employee-details').html(response);
                    $('#employeeModal').modal('show');
                }
            });
        });
    });
</script>

</body>
</html>
