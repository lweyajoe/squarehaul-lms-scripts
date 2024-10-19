<?php

include 'config.php';
require_once("functions-tena.php");

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['admin', 'manager'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user_role'];

$clientExists = null;
$clientData = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize the input
    $national_id = htmlspecialchars($_POST['national_id']);

    // Query to check if the national ID exists
    $sql = "SELECT * FROM clients WHERE national_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $national_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $clientExists = true;
        // Fetch the client's data to pre-fill the form
        $clientData = $result->fetch_assoc();

        // Check if required fields are present in POST
        if (isset($_POST['first_name'], $_POST['last_name'], $_POST['email'])) {
            $phone_number = htmlspecialchars($_POST['phone_number']);
            // Validate phone number to ensure it starts with '254' and contains exactly 12 digits
            if (!preg_match("/^254\d{9}$/", $phone_number)) {
                $_SESSION['error'] = "Invalid phone number. Please validate the ID and fill out the form again.";
                header("Location: edit-client.php"); // Redirect to the form page with error
                exit(); // Stop further execution if phone number is invalid
            }

            // Collect form data
            $firstName = htmlspecialchars($_POST['first_name']);
            $lastName = htmlspecialchars($_POST['last_name']);
            $email = htmlspecialchars($_POST['email']);
            $county = htmlspecialchars($_POST['county']);
            $town = htmlspecialchars($_POST['town']); // Corrected field name
            $id_photo_front = htmlspecialchars($_FILES['id_photo_front']['name']);
            $id_photo_back = htmlspecialchars($_FILES['id_photo_back']['name']);
            $client_passport_photo = htmlspecialchars($_FILES['client_passport_photo']['name']);
            $work_economic_activity = htmlspecialchars($_POST['work_economic_activity']);
            $residence_nearest_building = htmlspecialchars($_POST['residence_nearest_building']);
            $residence_nearest_road = htmlspecialchars($_POST['residence_nearest_road']);

            // Upload file paths for ID photos if uploaded
            $id_photo_front_path = $clientData['id_photo_front'];
            $id_photo_back_path = $clientData['id_photo_back'];
            $client_passport_photo_path = $clientData['client_passport_photo'];

            if ($_FILES['id_photo_front']['tmp_name']) {
                $id_photo_front_path = "uploads/" . basename($_FILES['id_photo_front']['name']);
                move_uploaded_file($_FILES['id_photo_front']['tmp_name'], $id_photo_front_path);
            }
            if ($_FILES['id_photo_back']['tmp_name']) {
                $id_photo_back_path = "uploads/" . basename($_FILES['id_photo_back']['name']);
                move_uploaded_file($_FILES['id_photo_back']['tmp_name'], $id_photo_back_path);
            }
            if ($_FILES['client_passport_photo']['tmp_name']) {
                $client_passport_photo_path = "uploads/" . basename($_FILES['client_passport_photo']['name']);
                move_uploaded_file($_FILES['client_passport_photo']['tmp_name'], $client_passport_photo_path);
            }

            // Update the existing client record
            $sql_update = "UPDATE clients 
                SET first_name = ?, last_name = ?, email = ?, phone_number = ?, county = ?, town_centre = ?, id_photo_front = ?, id_photo_back = ?, client_passport_photo = ?, work_economic_activity = ?, residence_nearest_building = ?, residence_nearest_road = ?
                WHERE national_id = ?";

            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param(
                "sssssssssssss",
                $firstName,
                $lastName,
                $email,
                $phone_number,
                $county,
                $town,
                $id_photo_front_path,
                $id_photo_back_path,
                $client_passport_photo_path,
                $work_economic_activity,
                $residence_nearest_building,
                $residence_nearest_road,
                $national_id
            );

            if ($stmt_update->execute()) {
                $noted = "Client details updated successfully!";
                // Redirect or show success message
            } else {
                $noted = "Error updating client: " . $stmt_update->error;
            }
        }
    } else {
        $clientExists = false;
        $noted = "Client with this National ID does not exist.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <!-- Basic Page Info -->
    <meta charset="utf-8" />
    <title>Omabra Credit - Loan Management System</title>

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="vendors/styles/core.css" />
    <link rel="stylesheet" type="text/css" href="vendors/styles/style.css" />
</head>
<body class="login-page">
<div class="login-header box-shadow">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div class="brand-logo">
            <a href="">
                <img src="vendors/images/omabra_logo.png" alt="" />
            </a>
        </div>
        <div class="login-menu">
            <ul>
                <li><a href="admin_dashboard.php">Back to admin_dashboard</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="login-wrap d-flex align-items-center flex-wrap justify-content-center">
    <div class="container">
        <div class="row align-items-center">
            <div class="login-box bg-white box-shadow border-radius-10">
                <div class="login-title">
                    <h2 class="text-center text-primary">Omabra Credit - LMS</h2>
                </div>

                <!-- Show only the validate section on the first load -->
                <?php if ($clientExists === null || $clientExists === false): ?>
                    <form method="post" action="">
                        <div class="input-group custom">
                            <input
                                type="text"
                                class="form-control form-control-lg"
                                placeholder="National ID"
                                name="national_id"
                                required
                            />
                            <div class="input-group-append custom">
                                <span class="input-group-text">
                                    <i class="icon-copy dw dw-user1"></i>
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="input-group mb-0">
                                    <button class="btn btn-primary btn-lg btn-block" type="submit">Validate</button>
                                </div>
                            </div>
                        </div>
                        <?php
        // Check if there's an error message stored in the session
        if (isset($_SESSION['error'])) {
            echo '<div class="error-message" style="color:red;">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']); // Clear error message after displaying it
        }
        ?>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        <?php 
                    if (!empty($noted)) {
                        echo '<p style="color:green;">' . htmlspecialchars($noted) . '</p>';
                    }
                    ?>

        <!-- Show the full form only after validation succeeds -->
            <?php if ($clientExists === true && !empty($clientData)): ?>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
    <div class="row align-items-center">
        <!-- Personal Info Section -->
        <div class="col-md-4 col-lg-4">
            <div class="login-box bg-white box-shadow border-radius-10">
                <h5 class="text-blue h4">Personal Info</h5>

                <div class="input-group custom">
                    <input
                        type="text"
                        class="form-control form-control-lg"
                        placeholder="First Name"
                        name="first_name"
                        value="<?php echo htmlspecialchars($clientData['first_name']); ?>"
                        required
                    />
                    <div class="input-group-append custom">
                        <span class="input-group-text">
                            <i class="icon-copy dw dw-user1"></i>
                        </span>
                    </div>
                </div>

                <div class="input-group custom">
                    <input
                        type="text"
                        class="form-control form-control-lg"
                        placeholder="Last Name"
                        name="last_name"
                        value="<?php echo htmlspecialchars($clientData['last_name']); ?>"
                        required
                    />
                    <div class="input-group-append custom">
                        <span class="input-group-text">
                            <i class="icon-copy dw dw-user1"></i>
                        </span>
                    </div>
                </div>

                <div class="input-group custom">
                    <input
                        type="email"
                        class="form-control form-control-lg"
                        placeholder="Email"
                        name="email"
                        value="<?php echo htmlspecialchars($clientData['email']); ?>"
                        required
                    />
                    <div class="input-group-append custom">
                        <span class="input-group-text">
                            <i class="icon-copy dw dw-user1"></i>
                        </span>
                    </div>
                </div>

                <div class="input-group custom">
                    <input
                        type="text"
                        class="form-control form-control-lg"
                        placeholder="Phone Number"
                        id="phone_number"
                        name="phone_number"
                        value="<?php echo htmlspecialchars($clientData['phone_number']); ?>"
                        required
                    />
                    <div class="input-group-append custom">
                        <span class="input-group-text">
                            <i class="icon-copy dw dw-user1"></i>
                        </span>
                    </div>
                </div>

                <div class="input-group custom">
                    <input
                        type="text"
                        class="form-control form-control-lg"
                        placeholder="County"
                        name="county"
                        value="<?php echo htmlspecialchars($clientData['county']); ?>"
                        required
                    />
                    <div class="input-group-append custom">
                        <span class="input-group-text">
                            <i class="icon-copy dw dw-user1"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- KYC Documents Section -->
        <?php
// Assuming you've fetched the client's existing data from the database into an associative array $clientData
// For example: $clientData['national_id'], $clientData['work_economic_activity'], etc.
?>
<!-- KYC Documents Section -->
<div class="col-md-4 col-lg-4">
    <div class="login-box bg-white box-shadow border-radius-10">
        <h5 class="text-blue h4">KYC Documents</h5>

        <!-- ID/Passport Number -->
        <div class="input-group custom">
            <input
                type="text"
                class="form-control form-control-lg"
                placeholder="ID/Passport Number"
                name="national_id"
                value="<?php echo isset($clientData['national_id']) ? htmlspecialchars($clientData['national_id']) : ''; ?>"
                required
            />
            <div class="input-group-append custom">
                <span class="input-group-text">
                    <i class="icon-copy dw dw-user1"></i>
                </span>
            </div>
        </div>

        <!-- ID Photo Front -->
        <div class="input-group custom">
            <input
                type="file"
                class="form-control-file form-control height-auto"
                name="id_photo_front"
                data-toggle="tooltip"
                data-placement="bottom"
                title="ID Photo Front"
                <?php echo empty($clientData['id_photo_front']) ? 'required' : ''; ?>
            />
            <?php if (!empty($clientData['id_photo_front'])): ?>
                <p>Current file: <a href="<?php echo htmlspecialchars($clientData['id_photo_front']); ?>" target="_blank">View ID Photo Front</a></p>
            <?php endif; ?>
        </div>

        <!-- ID Photo Back -->
        <div class="input-group custom">
            <input
                type="file"
                class="form-control-file form-control height-auto"
                name="id_photo_back"
                data-toggle="tooltip"
                data-placement="bottom"
                title="ID Photo Back"
                <?php echo empty($clientData['id_photo_back']) ? 'required' : ''; ?>
            />
            <?php if (!empty($clientData['id_photo_back'])): ?>
                <p>Current file: <a href="<?php echo htmlspecialchars($clientData['id_photo_back']); ?>" target="_blank">View ID Photo Back</a></p>
            <?php endif; ?>
        </div>

        <!-- Client Passport Photo -->
        <div class="input-group custom">
            <input
                type="file"
                class="form-control-file form-control height-auto"
                name="client_passport_photo"
                data-toggle="tooltip"
                data-placement="bottom"
                title="Client Passport Photo"
                <?php echo empty($clientData['client_passport_photo']) ? 'required' : ''; ?>
            />
            <?php if (!empty($clientData['client_passport_photo'])): ?>
                <p>Current file: <a href="<?php echo htmlspecialchars($clientData['client_passport_photo']); ?>" target="_blank">View Passport Photo</a></p>
            <?php endif; ?>
        </div>

        <!-- Place of Work/Economic Activity -->
        <div class="input-group custom">
            <input
                type="text"
                class="form-control form-control-lg"
                placeholder="Place of Work/Economic Activity"
                name="work_economic_activity"
                value="<?php echo isset($clientData['work_economic_activity']) ? htmlspecialchars($clientData['work_economic_activity']) : ''; ?>"
                required
            />
            <div class="input-group-append custom">
                <span class="input-group-text">
                    <i class="icon-copy dw dw-user1"></i>
                </span>
            </div>
        </div>

        <!-- Place of Residence (Nearest Building) -->
        <div class="input-group custom">
            <input
                type="text"
                class="form-control form-control-lg"
                placeholder="Place of Residence (Nearest Building)"
                name="residence_nearest_building"
                value="<?php echo isset($clientData['residence_nearest_building']) ? htmlspecialchars($clientData['residence_nearest_building']) : ''; ?>"
                required
            />
            <div class="input-group-append custom">
                <span class="input-group-text">
                    <i class="icon-copy dw dw-user1"></i>
                </span>
            </div>
        </div>

    </div>
</div>

<!-- Other sections here, e.g. Bio Data Section, similar pre-filling can be applied -->
<?php
// Assuming you've fetched the client's existing data into an associative array $clientData
// For example: $clientData['next_of_kin_name_1'], $clientData['next_of_kin_name_2'], etc.
?>
<div class="col-md-4 col-lg-4">
    <div class="login-box bg-white box-shadow border-radius-10">
        <h5 class="text-blue h4">Bio Data</h5>

        <!-- Next of Kin Contact 1 -->
        <div class="input-group custom">
            <input
                type="text"
                class="form-control form-control-lg"
                placeholder="CloseContactName1, PhoneNumber"
                name="next_of_kin_name-1"
                value="<?php echo isset($clientData['next_of_kin_name_1']) ? htmlspecialchars($clientData['next_of_kin_name_1']) : ''; ?>"
                required
            />
            <div class="input-group-append custom">
                <span class="input-group-text">
                    <i class="icon-copy dw dw-user1"></i>
                </span>
            </div>
        </div>

        <!-- Next of Kin Contact 2 -->
        <div class="input-group custom">
            <input
                type="text"
                class="form-control form-control-lg"
                placeholder="CloseContactName2, PhoneNumber"
                name="next_of_kin_name-2"
                value="<?php echo isset($clientData['next_of_kin_name_2']) ? htmlspecialchars($clientData['next_of_kin_name_2']) : ''; ?>"
                required
            />
            <div class="input-group-append custom">
                <span class="input-group-text">
                    <i class="icon-copy dw dw-user1"></i>
                </span>
            </div>
        </div>

        <!-- Next of Kin Contact 3 -->
        <div class="input-group custom">
            <input
                type="text"
                class="form-control form-control-lg"
                placeholder="CloseContactName3, PhoneNumber"
                name="next_of_kin_name-3"
                value="<?php echo isset($clientData['next_of_kin_name_3']) ? htmlspecialchars($clientData['next_of_kin_name_3']) : ''; ?>"
            />
            <div class="input-group-append custom">
                <span class="input-group-text">
                    <i class="icon-copy dw dw-user1"></i>
                </span>
            </div>
        </div>

        <!-- Next of Kin Contact 4 -->
        <div class="input-group custom">
            <input
                type="text"
                class="form-control form-control-lg"
                placeholder="CloseContactName4, PhoneNumber"
                name="next_of_kin_name-4"
                value="<?php echo isset($clientData['next_of_kin_name_4']) ? htmlspecialchars($clientData['next_of_kin_name_4']) : ''; ?>"
            />
            <div class="input-group-append custom">
                <span class="input-group-text">
                    <i class="icon-copy dw dw-user1"></i>
                </span>
            </div>
        </div>

        <!-- Next of Kin Contact 5 -->
        <div class="input-group custom">
            <input
                type="text"
                class="form-control form-control-lg"
                placeholder="CloseContactName5, PhoneNumber"
                name="next_of_kin_name-5"
                value="<?php echo isset($clientData['next_of_kin_name_5']) ? htmlspecialchars($clientData['next_of_kin_name_5']) : ''; ?>"
            />
            <div class="input-group-append custom">
                <span class="input-group-text">
                    <i class="icon-copy dw dw-user1"></i>
                </span>
            </div>
        </div>
        
    </div>
</div>


    </div>

    <div class="col-sm-12">
        <div class="input-group mb-0">
            <button class="btn btn-primary btn-lg btn-block" type="submit">Update Information</button>
        </div>
    </div>
</form>
<?php endif; ?>
    </div>
</div>

<!-- JS -->
<script src="vendors/scripts/core.js"></script>
<script src="vendors/scripts/script.min.js"></script>
</body>
</html>