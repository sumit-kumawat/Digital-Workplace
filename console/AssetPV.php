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

// Fetch assets for display
$assets_query = "SELECT * FROM assets ORDER BY created_at DESC";
$result = $mysqli->query($assets_query);

if (!$result) {
    die("Query failed: " . $mysqli->error);
}

$assets = [];
while ($row = $result->fetch_assoc()) {
    $assets[] = $row;
}

$mysqli->close();

// Helper function to format date and time
function formatDate($date) {
    if ($date && $date !== '0000-00-00 00:00:00' && $date !== '0001-01-01 00:00:00') {
        $datetime = new DateTime($date);
        return $datetime->format('d F Y, H:i:s');
    }
    return ''; // Return empty string for unfilled or placeholder dates
}

// Helper function to get Impact and Urgency text
function getImpactText($value) {
    switch ($value) {
        case 1: return '1-Extensive/Widespread';
        case 2: return '2-Significant/Large';
        case 3: return '3-Moderate/Limited';
        case 4: return '4-Minor/Localize';
        default: return 'N/A';
    }
}

function getUrgencyText($value) {
    switch ($value) {
        case 1: return '1-Critical';
        case 2: return '2-High';
        case 3: return '3-Medium';
        case 4: return '4-Low';
        default: return 'N/A';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assets Console</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>

<?php include '../header.php'; ?>

    <div class="container mt-4">
        <h1>Assets Console</h1>
        <div class="search-bar">
            <input type="text" id="search" class="form-control" placeholder="Search assets...">
        </div>
        <hr>
        <div class="row">
            <?php foreach ($assets as $asset): ?>
                <div class="col-md-6 mb-3">
                    <div class="list-group-item">
                        <div>
                            <h5><?php echo htmlspecialchars($asset['asset_id']); ?></h5>
                            <p><?php echo htmlspecialchars($asset['asset_type']); ?></p>
                        </div>
                        <div class="actions">
                            <a href="#" class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewModal<?php echo $asset['id']; ?>">View</a>
                        </div>
                    </div>
                </div>

                <!-- View Modal -->
                <div class="modal fade" id="viewModal<?php echo $asset['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">View Asset</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Asset ID:</strong> <?php echo htmlspecialchars($asset['asset_id']); ?></p>
                                <p><strong>Asset Type:</strong> <?php echo htmlspecialchars($asset['asset_type']); ?></p>
                                <p><strong>Asset Name:</strong> <?php echo htmlspecialchars($asset['asset_name']); ?></p>
                                <p><strong>Description:</strong> <?php echo htmlspecialchars($asset['description']); ?></p>
                                <p><strong>Impact:</strong> <?php echo getImpactText($asset['impact']); ?></p>
                                <p><strong>Urgency:</strong> <?php echo getUrgencyText($asset['urgency']); ?></p>
                                <p><strong>Status:</strong> <?php echo htmlspecialchars($asset['status']); ?></p>
                                <p><strong>Tag Number:</strong> <?php echo htmlspecialchars($asset['tag_number']); ?></p>
                                <p><strong>Serial Number:</strong> <?php echo htmlspecialchars($asset['serial_number']); ?></p>
                                <p><strong>Available Date:</strong> <?php echo formatDate($asset['available_date']); ?></p>
                                <p><strong>Installation Date:</strong> <?php echo formatDate($asset['installation_date']); ?></p>
                                <p><strong>Received Date:</strong> <?php echo formatDate($asset['received_date']); ?></p>
                                <p><strong>Return Date:</strong> <?php echo formatDate($asset['return_date']); ?></p>
                                <p><strong>Disposal Date:</strong> <?php echo formatDate($asset['disposal_date']); ?></p>
                                <p><strong>Purchase Date:</strong> <?php echo formatDate($asset['purchase_date']); ?></p>
                                <p><strong>Invoice Number:</strong> <?php echo htmlspecialchars($asset['invoice_number']); ?></p>
                                <p><strong>Attachment Path:</strong> 
                                    <?php if (!empty($asset['attachment_path'])): ?>
                                        <a href="../uploads/Assets/<?php echo htmlspecialchars(basename($asset['attachment_path'])); ?>" download>Download Attachment</a>
                                    <?php else: ?>
                                        No Attachment
                                    <?php endif; ?>
                                </p>
                                <p><strong>Created At:</strong> <?php echo formatDate($asset['created_at']); ?></p>
                                <p><strong>Updated At:</strong> <?php echo !empty($asset['updated_at']) ? formatDate($asset['updated_at']) : 'N/A'; ?></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#search').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('.list-group-item').each(function() {
                    var itemText = $(this).text().toLowerCase();
                    $(this).toggle(itemText.indexOf(value) > -1);
                });
            });
        });
    </script>
</body>
</html>
