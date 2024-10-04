<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
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

// Log user activity
function log_activity($user_id, $action, $description = '') {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, description) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $action, $description);
    $stmt->execute();
    $stmt->close();
}

// Example usage: Log a sample activity
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['log_activity'])) {
    $action = $_POST['action'];
    $description = $_POST['description'];
    log_activity($_SESSION['user_id'], $action, $description);
}

// Fetch activity logs
$query = "SELECT al.id, u.username, al.action, al.description, al.timestamp
          FROM activity_logs al
          JOIN users u ON al.user_id = u.id
          ORDER BY al.timestamp DESC";
$result = $conn->query($query);

$logs = [];
while ($row = $result->fetch_assoc()) {
    $logs[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .table thead th {
            background-color: #f8f9fa;
            color: #343a40;
        }
        .table-hover tbody tr:hover {
            background-color: #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mt-5 mb-4 text-center">Activity Logs</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="action">Action</label>
                <input type="text" class="form-control" id="action" name="action" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" name="log_activity">Log Activity</button>
        </form>
        <table class="table table-bordered table-hover mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= $log['id'] ?></td>
                        <td><?= htmlspecialchars($log['username']) ?></td>
                        <td><?= htmlspecialchars($log['action']) ?></td>
                        <td><?= htmlspecialchars($log['description']) ?></td>
                        <td><?= htmlspecialchars($log['timestamp']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
