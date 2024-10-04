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

// Fetch company data
$sql = "SELECT * FROM company";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Profile</title>

    <!-- CSS Links -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/header.css">

    <style>
        .card {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            max-height: 400px;
        }
        .card-img-left {
            width: 150px;
            height: 150px;
            object-fit: cover;
        }
        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .modal-content {
            padding: 20px;
        }
        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-group {
            margin-top: 10px;
        }
        .modal-dialog {
            max-width: 80%;
        }
    </style>
</head>
<body>

<?php include '../header.php'; ?>

<div class="container mt-5">
    <h2>Company View</h2>
    <hr>
    <div class="row">
        <?php
        if ($result->num_rows > 0) {
            $colCount = 0; // Column counter to manage two-column layout
            while ($row = $result->fetch_assoc()) {
                if ($colCount % 2 == 0) {
                    if ($colCount > 0) echo '</div>'; // Close the previous row if not the first column
                    echo '<div class="row">'; // Start a new row
                }
                $logo = $row['company_logo'] ? '../uploads/RegCompany/' . basename($row['company_logo']) : 'https://via.placeholder.com/150';
                ?>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body d-flex">
                            <img src="<?php echo $logo; ?>" class="card-img-left" alt="<?php echo htmlspecialchars($row['company_name']); ?>">
                            <div class="details">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['company_name']); ?></h5>
                                <p class="card-text">
                                    <strong>Company ID:</strong> <?php echo htmlspecialchars($row['company_id']); ?><br>
                                    <strong>Domain:</strong> <?php echo htmlspecialchars($row['website']); ?><br>
                                    <strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a>
                                </p>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#detailsModal<?php echo $row['company_id']; ?>">
                                View Details
                            </button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#editModal<?php echo $row['company_id']; ?>">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal<?php echo $row['company_id']; ?>">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- View Details Modal -->
                <div class="modal fade" id="detailsModal<?php echo $row['company_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel<?php echo $row['company_id']; ?>" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="detailsModalLabel<?php echo $row['company_id']; ?>"><?php echo htmlspecialchars($row['company_name']); ?> Details</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <img src="<?php echo $logo; ?>" class="img-fluid mb-3" alt="<?php echo htmlspecialchars($row['company_name']); ?>">
                                <p><strong>Company ID:</strong> <?php echo htmlspecialchars($row['company_id']); ?></p>
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($row['company_name']); ?></p>
                                <p><strong>Address Line 1:</strong> <?php echo htmlspecialchars($row['address_line1']); ?></p>
                                <p><strong>Address Line 2:</strong> <?php echo htmlspecialchars($row['address_line2']); ?></p>
                                <p><strong>PIN Code:</strong> <?php echo htmlspecialchars($row['pin_code']); ?></p>
                                <p><strong>Country:</strong> <?php echo htmlspecialchars($row['country']); ?></p>
                                <p><strong>GST Number:</strong> <?php echo htmlspecialchars($row['gst_number']); ?></p>
                                <p><strong>GST Percentage:</strong> <?php echo htmlspecialchars($row['gst_percentage']); ?>%</p>
                                <p><strong>Registration Number:</strong> <?php echo htmlspecialchars($row['registration_number']); ?></p>
                                <p><strong>Currency:</strong> <?php echo htmlspecialchars($row['currency']); ?></p>
                                <p><strong>Website:</strong> <a href="<?php echo htmlspecialchars($row['website']); ?>" target="_blank"><?php echo htmlspecialchars($row['website']); ?></a></p>
                                <p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a></p>
                                <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($row['phone_number']); ?></p>
                                <p><strong>Fax:</strong> <?php echo htmlspecialchars($row['fax']); ?></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal<?php echo $row['company_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?php echo $row['company_id']; ?>" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel<?php echo $row['company_id']; ?>">Edit Company</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="update_company.php" method="post">
                                <div class="modal-body">
                                    <input type="hidden" name="company_id" value="<?php echo htmlspecialchars($row['company_id']); ?>">
                                    <div class="form-group">
                                        <label for="company_name">Name</label>
                                        <input type="text" class="form-control" id="company_name" name="company_name" value="<?php echo htmlspecialchars($row['company_name']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="address_line1">Address Line 1</label>
                                        <input type="text" class="form-control" id="address_line1" name="address_line1" value="<?php echo htmlspecialchars($row['address_line1']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="address_line2">Address Line 2</label>
                                        <input type="text" class="form-control" id="address_line2" name="address_line2" value="<?php echo htmlspecialchars($row['address_line2']); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="pin_code">PIN Code</label>
                                        <input type="text" class="form-control" id="pin_code" name="pin_code" value="<?php echo htmlspecialchars($row['pin_code']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="gst_percentage">GST Percentage</label>
                                        <input type="text" class="form-control" id="gst_percentage" name="gst_percentage" value="<?php echo htmlspecialchars($row['gst_percentage']); ?>%" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="website">Website</label>
                                        <input type="url" class="form-control" id="website" name="website" value="<?php echo htmlspecialchars($row['website']); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="phone_number">Phone Number</label>
                                        <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($row['phone_number']); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="fax">Fax</label>
                                        <input type="text" class="form-control" id="fax" name="fax" value="<?php echo htmlspecialchars($row['fax']); ?>">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Delete Modal -->
                <div class="modal fade" id="deleteModal<?php echo $row['company_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel<?php echo $row['company_id']; ?>" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel<?php echo $row['company_id']; ?>">Delete Company</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="delete_company.php" method="post">
                                <div class="modal-body">
                                    <p>Are you sure you want to delete this company?</p>
                                    <input type="hidden" name="company_id" value="<?php echo htmlspecialchars($row['company_id']); ?>">
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php
                $colCount++;
            }
            if ($colCount % 2 != 0) echo '</div>'; // Close the last row if needed
        } else {
            echo "<p>No companies found.</p>";
        }
        ?>
    </div>
</div>

<!-- JS Links -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="../js/scripts.js"></script>

</body>
</html>

<?php $conn->close(); ?>
