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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['new_support_group'])) {
        // Adding a new support group
        $new_support_group = $_POST['new_support_group'];

        // Get the next auto-increment value
        $result = $conn->query("SHOW TABLE STATUS LIKE 'support_groups'");
        $row = $result->fetch_assoc();
        $next_id = $row['Auto_increment'];

        // Generate unique ID
        $unique_id = 'CZSUPGRP' . str_pad($next_id, 5, '0', STR_PAD_LEFT);

        $insert_group_query = "INSERT INTO support_groups (name, unique_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_group_query);
        $stmt->bind_param("ss", $new_support_group, $unique_id);

        if ($stmt->execute()) {
            echo "<script>alert('Support group added successfully');</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    } elseif (isset($_POST['edit_support_group'])) {
        // Editing a support group
        $id = $_POST['edit_support_group_id'];
        $name = $_POST['edit_support_group_name'];

        $update_group_query = "UPDATE support_groups SET name = ? WHERE id = ?";
        $stmt = $conn->prepare($update_group_query);
        $stmt->bind_param("si", $name, $id);

        if ($stmt->execute()) {
            echo "<script>alert('Support group updated successfully');</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    } elseif (isset($_POST['delete_support_group'])) {
        // Deleting a support group
        $id = $_POST['delete_support_group_id'];

        $delete_group_query = "DELETE FROM support_groups WHERE id = ?";
        $stmt = $conn->prepare($delete_group_query);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "<script>alert('Support group deleted successfully');</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    }
}

// Fetch support groups for display and search
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $support_groups_query = "SELECT id, unique_id, name FROM support_groups WHERE name LIKE ?";
    $stmt = $conn->prepare($support_groups_query);
    $search_param = "%$search%";
    $stmt->bind_param("s", $search_param);
    $stmt->execute();
    $result = $stmt->get_result();

    $support_groups = [];
    while ($row = $result->fetch_assoc()) {
        $support_groups[] = $row;
    }

    $stmt->close();
    $conn->close();

    // Output the table rows for AJAX
    foreach ($support_groups as $group) {
        echo "<tr>";
        echo "<td>{$group['id']}</td>";
        echo "<td>{$group['unique_id']}</td>";
        echo "<td>{$group['name']}</td>";
        echo "<td class='table-actions'>";
        echo "<i class='fas fa-edit action-icons text-primary' data-toggle='modal' data-target='#editGroupModal' data-id='{$group['id']}' data-name='{$group['name']}'></i>";
        echo "<i class='fas fa-trash action-icons text-danger' data-toggle='modal' data-target='#deleteGroupModal' data-id='{$group['id']}'></i>";
        echo "</td>";
        echo "</tr>";
    }
    exit();
}

// Fetch support groups for initial page load
$support_groups_query = "SELECT id, unique_id, name FROM support_groups";
$result = $conn->query($support_groups_query);

$support_groups = [];
while ($row = $result->fetch_assoc()) {
    $support_groups[] = $row;
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
    <title>Support Groups</title>
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
    </style>
</head>
<body>

<?php include '../header.php'; ?>

<div class="container mt-5">
    <h2 class="mt-5 mb-4 text-center">Support Groups</h2>
    <button type="button" class="btn btn-success mb-3" data-toggle="modal" data-target="#addGroupModal">
        <i class="fas fa-plus"></i> Add Support Group
    </button>
    <input type="text" id="search" class="form-control mb-3" placeholder="Search for support groups...">
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Unique ID</th>
                <th>Name</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody id="support_groups_table">
            <?php foreach ($support_groups as $group): ?>
                <tr>
                    <td><?= $group['id'] ?></td>
                    <td><?= $group['unique_id'] ?></td>
                    <td><?= $group['name'] ?></td>
                    <td class="table-actions">
                        <i class="fas fa-edit action-icons text-primary" data-toggle="modal" data-target="#editGroupModal" data-id="<?= $group['id'] ?>" data-name="<?= $group['name'] ?>"></i>
                        <i class="fas fa-trash action-icons text-danger" data-toggle="modal" data-target="#deleteGroupModal" data-id="<?= $group['id'] ?>"></i>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add Support Group Modal -->
<div class="modal fade" id="addGroupModal" tabindex="-1" aria-labelledby="addGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addGroupModalLabel">Add Support Group</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="new_support_group">Support Group Name</label>
                        <input type="text" class="form-control" id="new_support_group" name="new_support_group" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Support Group Modal -->
<div class="modal fade" id="editGroupModal" tabindex="-1" aria-labelledby="editGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editGroupModalLabel">Edit Support Group</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" id="edit_support_group_id" name="edit_support_group_id">
                    <div class="form-group">
                        <label for="edit_support_group_name">Support Group Name</label>
                        <input type="text" class="form-control" id="edit_support_group_name" name="edit_support_group_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" name="edit_support_group">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Support Group Modal -->
<div class="modal fade" id="deleteGroupModal" tabindex="-1" aria-labelledby="deleteGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteGroupModalLabel">Delete Support Group</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" id="delete_support_group_id" name="delete_support_group_id">
                    <p>Are you sure you want to delete this support group?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" name="delete_support_group">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    function fetchSupportGroups(search = '') {
        $.ajax({
            url: 'index.php',
            method: 'GET',
            data: { search: search },
            success: function(data) {
                $('#support_groups_table').html(data);
            }
        });
    }

    $(document).ready(function() {
        $('#search').on('keyup', function() {
            const search = $(this).val();
            fetchSupportGroups(search);
        });

        $('#editGroupModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const name = button.data('name');

            const modal = $(this);
            modal.find('#edit_support_group_id').val(id);
            modal.find('#edit_support_group_name').val(name);
        });

        $('#deleteGroupModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');

            const modal = $(this);
            modal.find('#delete_support_group_id').val(id);
        });
    });
</script>
</body>
</html>
