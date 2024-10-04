<?php
session_start();
require 'dbconfig.php'; // Ensure this file initializes $mysqli

// Check if $mysqli is set
if (!isset($mysqli)) {
    die('Database connection failed.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        // Login logic
        $username = $_POST['login_username'];
        $password = $_POST['login_password'];

        $stmt = $mysqli->prepare("SELECT user_id, password_hash FROM users WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($user_id, $password_hash);
                $stmt->fetch();
                if (password_verify($password, $password_hash)) {
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $username;
                    header('Location: index.php');
                    exit();
                } else {
                    $login_error = "Invalid password.";
                }
            } else {
                $login_error = "Username not found.";
            }

            $stmt->close();
        } else {
            $login_error = "Database query failed.";
        }
    } elseif (isset($_POST['signup'])) {
        // Signup logic
        $username = $_POST['signup_username'];
        $password = $_POST['signup_password'];
        $email = $_POST['signup_email'];

        $stmt = $mysqli->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
        if ($stmt) {
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $signup_error = "Username or email already exists.";
            } else {
                $password_hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $mysqli->prepare("INSERT INTO users (username, password_hash, email) VALUES (?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("sss", $username, $password_hash, $email);
                    if ($stmt->execute()) {
                        $_SESSION['user_id'] = $stmt->insert_id;
                        $_SESSION['username'] = $username;
                        header('Location: index.php');
                        exit();
                    } else {
                        $signup_error = "Signup failed. Please try again.";
                    }
                    $stmt->close();
                } else {
                    $signup_error = "Database query failed.";
                }
            }

            $stmt->close();
        } else {
            $signup_error = "Database query failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auth</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .auth-container {
            max-width: 600px;
            margin: 50px auto;
        }
        .form-container {
            display: none;
        }
        .form-container.active {
            display: block;
        }
        .nav-tabs .nav-item {
            margin-right: 10px;
        }
        .nav-tabs .nav-item .nav-link {
            padding: 10px 15px;
        }
        .nav-tabs .nav-item .nav-link i {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="container auth-container">
        <ul class="nav nav-tabs" id="authTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="login-tab" data-toggle="tab" href="#login-form" role="tab">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="signup-tab" data-toggle="tab" href="#signup-form" role="tab">
                    <i class="fas fa-user-plus"></i> Sign Up
                </a>
            </li>
        </ul>
        <div class="tab-content mt-4">
            <!-- Login Form -->
            <div class="tab-pane fade show active form-container" id="login-form" role="tabpanel">
                <h2>Login</h2>
                <?php if (isset($login_error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($login_error); ?></div>
                <?php endif; ?>
                <form method="POST" action="auth.php">
                    <div class="form-group">
                        <label for="login_username">Username</label>
                        <input type="text" class="form-control" id="login_username" name="login_username" required>
                    </div>
                    <div class="form-group">
                        <label for="login_password">Password</label>
                        <input type="password" class="form-control" id="login_password" name="login_password" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary">Login</button>
                </form>
            </div>
            <!-- Sign Up Form -->
            <div class="tab-pane fade form-container" id="signup-form" role="tabpanel">
                <h2>Sign Up</h2>
                <?php if (isset($signup_error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($signup_error); ?></div>
                <?php endif; ?>
                <form method="POST" action="auth.php">
                    <div class="form-group">
                        <label for="signup_username">Username</label>
                        <input type="text" class="form-control" id="signup_username" name="signup_username" required>
                    </div>
                    <div class="form-group">
                        <label for="signup_password">Password</label>
                        <input type="password" class="form-control" id="signup_password" name="signup_password" required>
                    </div>
                    <div class="form-group">
                        <label for="signup_email">Email</label>
                        <input type="email" class="form-control" id="signup_email" name="signup_email" required>
                    </div>
                    <button type="submit" name="signup" class="btn btn-primary">Sign Up</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // JavaScript to activate tab functionality
        $(document).ready(function(){
            $('a[data-toggle="tab"]').on('click', function (e) {
                e.preventDefault();
                $(this).tab('show');
            });
        });
    </script>
</body>
</html>
