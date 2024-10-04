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

// Fetch Reporting Managers
$reporting_managers = [];
$sql = "SELECT id, name FROM reporting_managers";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $reporting_managers[] = $row;
}

// Fetch Support Groups
$support_groups = [];
$sql = "SELECT id, name FROM support_groups";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $support_groups[] = $row;
}

// Fetch Organizations
$organizations = [];
$sql = "SELECT id, company_name FROM company";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $organizations[] = $row;
}

// Define the documents array for file uploads
$documents = [
    'passport_photo' => 'Passport Photo',
    'govt_id_proof' => 'Government ID Proof',
    'tenth_certificate' => '10th Marksheet/Certificate',
    'twelfth_certificate' => '12th Certificate',
    'graduation_certificate' => 'Graduation Certificate',
    'post_graduation_certificate' => 'Post-Graduation Certificate',
    'other_qualification' => 'Other Qualification Certificate',
    'passport' => 'Passport'
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Generate Employee ID
    $employee_id = generateEmployeeId($conn);

    // Collect form data
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $gender = $_POST['gender'];
    $personal_email = $_POST['personal_email'];
    $phone_number = $_POST['phone_number'];
    $country_code = isset($_POST['country_code']) ? $_POST['country_code'] : '';
    $dob = $_POST['dob'];
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $postal_code = $_POST['postal_code'] ?? '';
    $country = $_POST['country'];
    $custom_country = $_POST['custom_country'] ?? '';
    $country = $country === 'OTHER' ? $custom_country : $country;
    $email = generateEmployeeEmail($conn, $first_name, $last_name);
    $organization = $_POST['organization'] ?: 0;
    $support_group = $_POST['support_group'] ?: 0;
    $reporting_manager = $_POST['reporting_manager'] ?: null;

    // Generate unique username
    $username = generateUsername($conn, $first_name, $last_name);

    // File upload logic for documents
    $uploaded_files = [];
    foreach ($documents as $field_name => $display_name) {
        if (isset($_FILES[$field_name]) && $_FILES[$field_name]['error'] == 0) {
            $file = $_FILES[$field_name];
            $target_dir = "../uploads/Employees/";
            $target_file = $target_dir . basename($file['name']);
            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                $uploaded_files[$field_name] = $target_file;
            } else {
                $uploaded_files[$field_name] = null;
            }
        } else {
            $uploaded_files[$field_name] = null;
        }
    }

    // Insert data into the database
    $sql = "INSERT INTO employees (employee_id, first_name, last_name, gender, email, phone_number, dob, address, city, postal_code, country, username, organization, support_group, reporting_manager) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssssssssssssi', $employee_id, $first_name, $last_name, $gender, $email, $phone_number, $dob, $address, $city, $postal_code, $country, $username, $organization, $support_group, $reporting_manager);

    if ($stmt->execute()) {
        echo "<script>
                alert('Employee record created successfully.\\nEmployee ID: $employee_id\\nEmail: $email\\nUsername: $username\\nSupport Group: " . ($support_group ?: 'Labour') . "\\nCompany: " . ($organization ?: 'ConZex Global Private Limited') . "');
              </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();

// Function to generate a new employee ID
function generateEmployeeId($conn) {
    $prefix = "CZEMP";
    $sql = "SELECT MAX(CAST(SUBSTRING(employee_id, 6) AS UNSIGNED)) AS max_id FROM employees";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        $max_id = $row['max_id'] ? (int)$row['max_id'] : 100;
        $new_id = $max_id + 1;
    } else {
        $new_id = 101; // Start from 101 if no records are found
    }

    return $prefix . str_pad($new_id, 5, "0", STR_PAD_LEFT);
}

// Function to generate a unique email
function generateEmployeeEmail($conn, $first_name, $last_name) {
    $base_email = strtolower($first_name . "_" . $last_name . "@conzex.com");
    $email = $base_email;
    $suffix = 1;

    while (true) {
        $sql = "SELECT COUNT(*) AS count FROM employees WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count == 0) {
            break;
        }

        $email = $base_email . $suffix;
        $suffix++;
    }

    return $email;
}

// Function to generate a unique username
function generateUsername($conn, $first_name, $last_name) {
    $base_username = strtolower(substr($first_name, 0, 1) . $last_name);
    $username = $base_username;
    $suffix = 1;

    while (true) {
        $sql = "SELECT COUNT(*) AS count FROM employees WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count == 0) {
            break;
        }

        $username = $base_username . $suffix;
        $suffix++;
    }

    return $username;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/header.css">
    <title>Employee Record Form</title>
    <style>
        .custom-file-input ~ .custom-file-label::after {
            content: "Browse";
        }
        #custom-country {
            display: none;
        }
    </style>
</head>
<body>

<?php include '../header.php'; ?>

<div class="container mt-5">
    <h2>Employee Record</h2>
    <hr>
    <form method="POST" action="" enctype="multipart/form-data">
        
        <!-- Personal Information -->
        <div class="form-section">
            <h4>Personal Information</h4>
            <hr>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" class="form-control" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" class="form-control" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender" class="form-control" required>
                        <option value="" disabled selected>Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>

            <!-- Date of Birth and Email -->
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="dob">Date of Birth</label>
                    <input type="text" id="dob" name="dob" class="form-control datepicker" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="personal_email">Personal Email</label>
                    <input type="email" id="personal_email" name="personal_email" class="form-control" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="phone_number">Phone Number</label>
                    <input type="tel" id="phone_number" name="phone_number" class="form-control" required>
                </div>
            </div>
        </div>

        <!-- Address Information -->
        <div class="form-section">
            <h4>Address Information</h4>
            <hr>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" class="form-control">
                </div>
                <div class="form-group col-md-3">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" class="form-control">
                </div>
                <div class="form-group col-md-3">
                    <label for="postal_code">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" class="form-control">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="country">Country</label>
                        <select id="country" name="country" class="form-control" required>
                            <!-- Example countries with code and currency -->
                            <option value="IN" data-currency="₹ - INR" data-code="+91" selected>India</option>
                            <option value="US" data-currency="$ - USD" data-code="+1">United States</option>
                            <option value="GB" data-currency="£ - GBP" data-code="+44">United Kingdom</option>
                            <option value="AU" data-currency="$ - AUD" data-code="+61">Australia</option>
                            <option value="CA" data-currency="$ - CAD" data-code="+1">Canada</option>
                            <option value="DE" data-currency="€ - EUR" data-code="+49">Germany</option>
                            <option value="FR" data-currency="€ - EUR" data-code="+33">France</option>
                            <option value="IT" data-currency="€ - EUR" data-code="+39">Italy</option>
                            <option value="JP" data-currency="¥ - JPY" data-code="+81">Japan</option>
                            <option value="CN" data-currency="¥ - CNY" data-code="+86">China</option>
                            <option value="ZA" data-currency="R - ZAR" data-code="+27">South Africa</option>
                            <option value="BR" data-currency="R$ - BRL" data-code="+55">Brazil</option>
                            <option value="OTHER">Other</option>
                        </select>
                        <br>
                    <input type="text" id="custom_country" name="custom_country" class="form-control custom-country" placeholder="Enter custom country name">
                </div>
                <div class="form-group col-md-6" id="custom-country">
                    <label for="custom_country">Custom Country</label>
                    <input type="text" id="custom_country" name="custom_country" class="form-control">
                </div>
            </div>
        </div>

        <!-- Documents Upload -->
        <div class="form-section">
            <h4>Documents Upload</h4>
            <hr>
            <?php foreach ($documents as $field_name => $display_name): ?>
                <div class="form-group">
                    <label for="<?php echo $field_name; ?>"><?php echo $display_name; ?></label>
                    <input type="file" id="<?php echo $field_name; ?>" name="<?php echo $field_name; ?>" class="form-control-file">
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Organizational Details -->
        <div class="form-section">
            <h4>Organizational Details</h4>
            <hr>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="organization">Organization</label>
                    <select id="organization" name="organization" class="form-control">
                        <option value="0" selected>Select Organization</option>
                        <?php foreach ($organizations as $org): ?>
                            <option value="<?php echo $org['id']; ?>"><?php echo $org['company_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="support_group">Support Group</label>
                    <select id="support_group" name="support_group" class="form-control">
                        <option value="0" selected>Select Support Group</option>
                        <?php foreach ($support_groups as $group): ?>
                            <option value="<?php echo $group['id']; ?>"><?php echo $group['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="reporting_manager">Reporting Manager</label>
                    <select id="reporting_manager" name="reporting_manager" class="form-control">
                        <option value="" selected>Select Reporting Manager</option>
                        <?php foreach ($reporting_managers as $manager): ?>
                            <option value="<?php echo $manager['id']; ?>"><?php echo $manager['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
<br>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script>
    $(document).ready(function() {
        $('#dob').datepicker({
            format: 'dd MM yyyy',
            autoclose: true
        });
    });
</script>
<script>
    $(document).ready(function() {
        // Show/Hide custom country field based on the selected option
        $('#custom_country').hide();
        $('#country').change(function() {
            if ($(this).val() === 'OTHER') {
                $('#custom_country').show();
            } else {
                $('#custom_country').hide();
            }
        });

        // Update currency and country code based on the selected country
        $('#country').change(function() {
            var selectedCountry = $(this).find(':selected');
            var currency = selectedCountry.data('currency');
            var code = selectedCountry.data('code');
            $('.currency').val(currency);
            $('.country-code').text(code);
        });

        // Initialize the currency and country code fields
        var initialCountry = $('#country').find(':selected');
        var initialCurrency = initialCountry.data('currency');
        var initialCode = initialCountry.data('code');
        $('.currency').val(initialCurrency);
        $('.country-code').text(initialCode);
    });
</script>
</body>
</html>
