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

// Function to generate a new company ID
function generateCompanyId($conn) {
    $prefix = "CZORG";
    $sql = "SELECT MAX(SUBSTRING(company_id, 6)) AS max_id FROM company";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $max_id = (int)$row['max_id'] ?? 101;
    $new_id = $max_id + 1;
    return $prefix . str_pad($new_id, 5, "0", STR_PAD_LEFT);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $company_id = generateCompanyId($conn);
    $company_name = $_POST['company_name'];
    $address_line1 = $_POST['address_line1'];
    $address_line2 = $_POST['address_line2'];
    $pin_code = $_POST['pin_code'];
    $country = $_POST['country'];
    $custom_country = $_POST['custom_country'] ?? '';
    $gst_number = $_POST['gst_number'];
    $gst_percentage = $_POST['gst_percentage'];
    $registration_number = $_POST['registration_number'];
    $currency = $_POST['currency'];
    $website = $_POST['website'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $fax = $_POST['fax'];

    // File upload logic for company logo
    if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] == 0) {
        $logo_file = $_FILES['company_logo'];
        $target_dir = "../uploads/RegCompany/";
        $target_file = $target_dir . basename($logo_file['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if the file is an image
        $check = getimagesize($logo_file['tmp_name']);
        if ($check !== false) {
            // Move the file to the target directory
            if (move_uploaded_file($logo_file['tmp_name'], $target_file)) {
                // File upload success
                $company_logo = $target_file;
            } else {
                // File upload failed
                $company_logo = null;
            }
        } else {
            // Not an image
            $company_logo = null;
        }
    } else {
        $company_logo = null;
    }

    // Insert data into the database
    $sql = "INSERT INTO company (company_id, company_name, address_line1, address_line2, pin_code, country, custom_country, gst_number, gst_percentage, registration_number, currency, website, email, phone_number, fax, company_logo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssssssss", $company_id, $company_name, $address_line1, $address_line2, $pin_code, $country, $custom_country, $gst_number, $gst_percentage, $registration_number, $currency, $website, $email, $phone_number, $fax, $company_logo);

    if ($stmt->execute()) {
        echo "<script>alert('Company registered successfully. Company ID: $company_id');</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
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
</head>
<body>

<?php include '../header.php'; ?>

    <div class="container mt-5">
        <h2>Company Registration</h2>
        <hr>
        <form method="POST" action="" enctype="multipart/form-data">
            
            <!-- Company Details -->
            <div class="form-section">
                <h4>Company Details</h4>
            <hr>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="company_logo">Brand Logo</label>
                        <input type="file" id="company_logo" name="company_logo" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="company_name">Brand Name</label>
                        <input type="text" id="company_name" name="company_name" class="form-control" required>
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div class="form-section">
                <h4>Address</h4>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="address_line1">Address Line 1</label>
                        <input type="text" id="address_line1" name="address_line1" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="address_line2">Address Line 2</label>
                        <input type="text" id="address_line2" name="address_line2" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="pin_code">PIN Code</label>
                        <input type="text" id="pin_code" name="pin_code" class="form-control" required>
                    </div>
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
                </div>
            </div>

            <!-- Financial Details -->
            <div class="form-section">
                <h4>Financial Details</h4>
            <hr>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="gst_number">GST/VAT Number</label>
                        <input type="text" id="gst_number" name="gst_number" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="gst_percentage">GST/VAT Percentage</label>
                        <input type="number" id="gst_percentage" name="gst_percentage" class="form-control" step="0.01">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="registration_number">Registration Number</label>
                        <input type="text" id="registration_number" name="registration_number" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="currency">Currency</label>
                        <input type="text" id="currency" name="currency" class="form-control currency" readonly>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="form-section">
                <h4>Contact Information</h4>
            <hr>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="website">Website</label>
                        <input type="text" id="website" name="website" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="phone_number">Phone Number</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text country-code">+91</span>
                            </div>
                            <input type="text" id="phone_number" name="phone_number" class="form-control">
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="fax">Fax</label>
                        <input type="text" id="fax" name="fax" class="form-control">
                    </div>
                </div>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary">Register Company</button>
        </form>
        <br>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
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
