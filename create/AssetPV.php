<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit();
}

// Database connection
$mysqli = new mysqli("localhost", "root", "", "dwp");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Function to generate a unique asset ID
function generateAssetId($mysqli) {
    $prefix = 'CZITAST';
    $start_number = 101;
    
    // Fetch the latest asset ID
    $query = "SELECT asset_id FROM assets ORDER BY asset_id DESC LIMIT 1";
    $result = $mysqli->query($query);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_id = $row['asset_id'];
        $last_number = (int)substr($last_id, strlen($prefix));
        $next_number = $last_number + 1;
    } else {
        $next_number = $start_number;
    }
    
    $new_asset_id = $prefix . str_pad($next_number, 5, '0', STR_PAD_LEFT);
    return $new_asset_id;
}

// Handle form submission
$success_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $asset_type = $_POST['asset_type'];
    $asset_name = $_POST['asset_name'];
    $description = $_POST['description'];
    $impact = $_POST['impact'];
    $urgency = $_POST['urgency'];
    $ci_id = $_POST['ci_id'];
    $status = $_POST['status'];
    $tag_number = $_POST['tag_number'];
    $serial_number = $_POST['serial_number'];
    $available_date = $_POST['available_date'];
    $installation_date = $_POST['installation_date'];
    $received_date = $_POST['received_date'];
    $return_date = $_POST['return_date'];
    $disposal_date = $_POST['disposal_date'];
    $purchase_date = $_POST['purchase_date'];
    $invoice_number = $_POST['invoice_number'];
    
    // Generate a unique asset ID
    $asset_id = generateAssetId($mysqli);

    // Handle file upload
    $attachment_path = '';
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/Assets/';
        $upload_file = $upload_dir . basename($_FILES['attachment']['name']);
        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $upload_file)) {
            $attachment_path = $upload_file;
        }
    }

    // Prepare the statement
    $stmt = $mysqli->prepare("INSERT INTO assets (asset_id, user_id, asset_type, asset_name, description, impact, urgency, ci_id, status, tag_number, serial_number, available_date, installation_date, received_date, return_date, disposal_date, purchase_date, invoice_number, attachment_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $mysqli->error);
    }
    
    // Bind the parameters
    $stmt->bind_param("sisssssssssssssssss", $asset_id, $_SESSION['user_id'], $asset_type, $asset_name, $description, $impact, $urgency, $ci_id, $status, $tag_number, $serial_number, $available_date, $installation_date, $received_date, $return_date, $disposal_date, $purchase_date, $invoice_number, $attachment_path);
    
    // Execute the statement
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    
    $stmt->close();

    // Set success message
    $success_message = $asset_id;
}

// Helper function to format date
function formatDate($date) {
    if ($date && $date !== '0000-00-00 00:00:00' && $date !== '0001-01-01 00:00:00') {
        $datetime = new DateTime($date);
        return $datetime->format('d F Y');
    }
    return 'N/A';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Asset</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>

<?php include '../header.php'; ?>

    <div class="container mt-4">
        <h2>Create new asset</h2>
        <hr>
        <?php if ($success_message): ?>
        <div class="alert alert-success" role="alert">
            Asset Created Successfully! <br> Asset ID: <strong><?php echo htmlspecialchars($success_message); ?></strong>
        </div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-section">
                <h4>Asset Details</h4>
                <hr>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="asset_type">Asset Type *</label>
                        <select id="asset_type" name="asset_type" class="form-control" required>
                            <option value="" selected disabled>Select Asset Type</option>
                            <option value="Accounts">Accounts</option>
                            <option value="Application">Application</option>
                            <option value="Card">Card</option>
                            <option value="CD-ROM Drive">CD-ROM Drive</option>
                            <option value="Chassis">Chassis</option>
                            <option value="Cluster">Cluster</option>
                            <option value="Computer System">Computer System</option>
                            <option value="Database">Database</option>
                            <option value="Storage">Storage</option>
                            <option value="Disk Drive">Disk Drive</option>
                            <option value="Inventory Location">Inventory Location</option>
                            <option value="Document">Document</option>
                            <option value="CPU">CPU</option>
                            <option value="Keyboard (Wired)">Keyboard (Wired)</option>
                            <option value="Keyboard (Wireless)">Keyboard (Wireless)</option>
                            <option value="Mouse (Wired)">Mouse (Wired)</option>
                            <option value="Mouse (Wireless)">Mouse (Wireless)</option>
                            <option value="LAN/WAN Cable">LAN/WAN Cable</option>
                            <option value="Memory (RAM)">Memory (RAM)</option>
                            <option value="Monitor">Monitor</option>
                            <option value="Operating System (OS)">Operating System (OS)</option>
                            <option value="Printer">Printer</option>
                            <option value="Processor">Processor</option>
                            <option value="RACK">RACK</option>
                            <option value="Software">Software</option>
                            <option value="UPS">UPS</option>
                            <option value="Router">Router</option>
                            <option value="WiFi - Endpoint">WiFi - Endpoint</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="asset_name">Asset Name *</label>
                        <input type="text" id="asset_name" name="asset_name" class="form-control" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="description">Description *</label>
                        <input type="text" id="description" name="description" class="form-control" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="impact">Impact</label>
                        <select id="impact" name="impact" class="form-control">
                            <option value="" selected disabled>Select Impact</option>
                            <option value="1-Extensive/Widespread">1-Extensive/Widespread</option>
                            <option value="2-Significant/Large">2-Significant/Large</option>
                            <option value="3-Moderate/Limited">3-Moderate/Limited</option>
                            <option value="4-Minor/Localized">4-Minor/Localized</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="urgency">Urgency</label>
                        <select id="urgency" name="urgency" class="form-control">
                            <option value="" selected disabled>Select Urgency</option>
                            <option value="1-Critical">1-Critical</option>
                            <option value="2-High">2-High</option>
                            <option value="3-Medium">3-Medium</option>
                            <option value="4-Low">4-Low</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="ci_id">CI ID or Type</label>
                        <input type="text" id="ci_id" name="ci_id" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="Ordered">Ordered</option>
                            <option value="Received">Received</option>
                            <option value="Being Assembled">Being Assembled</option>
                            <option value="Deployed">Deployed</option>
                            <option value="In Repair">In Repair</option>
                            <option value="Down">Down</option>
                            <option value="End Of Life">End Of Life</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="tag_number">Tag Number</label>
                        <input type="text" id="tag_number" name="tag_number" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="serial_number">Serial Number</label>
                        <input type="text" id="serial_number" name="serial_number" class="form-control">
                    </div>
                </div>
            </div>
            <div class="form-section">
                <h4>Lifecycle Dates</h4>
                <hr>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="available_date">Available Date</label>
                        <input type="text" id="available_date" name="available_date" class="form-control datepicker">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="installation_date">Installation Date</label>
                        <input type="text" id="installation_date" name="installation_date" class="form-control datepicker">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="received_date">Received Date</label>
                        <input type="text" id="received_date" name="received_date" class="form-control datepicker">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="return_date">Return Date</label>
                        <input type="text" id="return_date" name="return_date" class="form-control datepicker">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="disposal_date">Disposal Date</label>
                        <input type="text" id="disposal_date" name="disposal_date" class="form-control datepicker">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="purchase_date">Purchase Date</label>
                        <input type="text" id="purchase_date" name="purchase_date" class="form-control datepicker">
                    </div>
                </div>
            </div>
            <div class="form-section">
                <h4>Financials</h4>
                <hr>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="invoice_number">Invoice Number</label>
                        <input type="text" id="invoice_number" name="invoice_number" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="attachment">Attachment</label>
                        <input type="file" id="attachment" name="attachment" class="form-control-file">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        <br>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.datepicker').datepicker({
                format: 'dd MM yyyy',
                autoclose: true
            });
        });
    </script>
</body>
</html>
