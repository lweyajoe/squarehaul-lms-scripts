<?php
// Include database connection file
require_once("config.php");
include_once "functions-tena.php";


// Enable error reporting
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

$onboardingOfficer = 'admin'; // Default initialization, just in case the condition isn't met

// Check if the user is logged in as admin or manager
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'manager')) {
    $onboardingOfficer = 'admin';
}


// Define onboarding officer variable


// Initialize error and success messages
$clientExistError = $insertQueryError = $insertQuerySuccess = $error = "";

// Form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get client's national ID from form input
    $clientNationalId = $_POST['national_id'];

    // Query to check if the client exists in the database
    $checkClientQuery = "SELECT client_id, email FROM clients WHERE national_id = ?";
    $stmt = $conn->prepare($checkClientQuery);    

    // Check if prepare statement was successful
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    // Bind parameters to the SQL query
    $stmt->bind_param("s", $clientNationalId);
    $stmt->execute();
    $stmt->store_result();

    // Check if the client does not exist
    if ($stmt->num_rows == 0) {
        // Client does not exist, show error message and prompt to onboard/register client first
        $clientExistError = "Client does not exist. Please onboard/register first.";
    } else {
        // Client exists, fetch client_id and email
        $stmt->bind_result($clientId, $email);
        $stmt->fetch();

		if (is_null($clientId)) {
			die("Error: Retrieved client_id is null");
		}		

		// Debugging: Print clientId
        //echo "Client ID: " . htmlspecialchars($clientId) . "<br>";
		// Or use var_dump for more detailed output
        //var_dump($clientId);

        // File upload handling
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $collateralPic1 = "";
        $collateralPic2 = "";

        if (isset($_FILES['collateral_pic1']) && $_FILES['collateral_pic1']['error'] === UPLOAD_ERR_OK) {
            if ($_FILES['collateral_pic1']['size'] <= 10 * 1024 * 1024) {
                $fileExt = pathinfo($_FILES['collateral_pic1']['name'], PATHINFO_EXTENSION);
                $validExt = ['jpg', 'jpeg', 'png', 'pdf'];
                if (in_array(strtolower($fileExt), $validExt)) {
                    $collateralPic1 = $uploadDir . 'pic1_' . uniqid() . '.' . $fileExt;
                    move_uploaded_file($_FILES['collateral_pic1']['tmp_name'], $collateralPic1);
                } else {
                    $error = "Invalid file type for collateral pic 1 photo.";
                }
            } else {
                $error = "Pic 1 photo exceeds the 10MB limit.";
            }
        }

        if (isset($_FILES['collateral_pic2']) && $_FILES['collateral_pic2']['error'] === UPLOAD_ERR_OK) {
            if ($_FILES['collateral_pic2']['size'] <= 10 * 1024 * 1024) {
                $fileExt = pathinfo($_FILES['collateral_pic2']['name'], PATHINFO_EXTENSION);
                $validExt = ['jpg', 'jpeg', 'png', 'pdf'];
                if (in_array(strtolower($fileExt), $validExt)) {
                    $collateralPic2 = $uploadDir . 'pic2_' . uniqid() . '.' . $fileExt;
                    move_uploaded_file($_FILES['collateral_pic2']['tmp_name'], $collateralPic2);
                } else {
                    $error = "Invalid file type for collateral pic 2 photo.";
                }
            } else {
                $error = "Pic 2 photo exceeds the 10MB limit.";
            }
        }

		// Proceed with the loan application insert
        $loanId = null; // Set this to null to trigger the trigger to generate the ID

        // SQL query to insert loan application data into the database
        $insertQuery = "INSERT INTO loan_applications (national_id, client_id, requested_amount, loan_purpose, duration, duration_period, date_applied, collateral_name, collateral_value, collateral_pic1, collateral_pic2, guarantor1_name, guarantor1_phone, guarantor2_name, guarantor2_phone, onboarding_officer)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($insertQuery);
        
        // Check if prepare statement for insertion was successful
        if ($stmtInsert === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }

        // Bind parameters to the SQL query for insertion
		// Assign $_POST data and the date() to variables first
$requested_amount = $_POST['requested_amount'];
$loan_purpose = $_POST['loan_purpose'];
$duration = $_POST['duration'];
$duration_period = $_POST['duration_period'];
$date_applied = date("Y-m-d");
$collateral_name = $_POST['collateral_name'];
$collateral_value = $_POST['collateral_value'];
$guarantor1_name = $_POST['guarantor1_name'];
$guarantor1_phone = $_POST['guarantor1_phone'];
$guarantor2_name = $_POST['guarantor2_name'];
$guarantor2_phone = $_POST['guarantor2_phone'];

// Now bind the variables to the prepared statement
$stmtInsert->bind_param("ssssssssssssssss",
    $clientNationalId, 
    $clientId, 
    $requested_amount,
    $loan_purpose, 
    $duration, 
    $duration_period,
    $date_applied, 
    $collateral_name, 
    $collateral_value, 
    $collateralPic1, 
    $collateralPic2, 
    $guarantor1_name, 
    $guarantor1_phone, 
    $guarantor2_name, 
    $guarantor2_phone, 
    $onboardingOfficer
);

        // Execute the insert statement
        if ($stmtInsert->execute()) {
            // Send email to client with details of the loan
			$to = $email;
			$subject = "Loan Status: Pending";
			$message = "Dear Client,\n\n";
			$message .= "Thank you for applying for a loan with us. Below are the details:\n";
			$message .= "Loan Amount: " . $_POST['requested_amount'] . "\n";
			$message .= "Manager: " . $onboardingOfficer . "\n";
			$message .= "Please wait for approval. If approved, your loan will be sent via MPESA to your registered number. Please log into your portal to update your number with us\n\n";
			$message .= "Best regards,\nLoan Administrator";
			
			sendEmail($to, $subject, $message);

			$clientName = getClientAllNames($clientId);
			// Insert notification for the new client
			$clientQuery = "SELECT user_id FROM users WHERE file_no = '$clientId'";
			$clientResult = $conn->query($clientQuery);
			if ($clientResult && $clientResult->num_rows > 0) {
				$clientRow = $clientResult->fetch_assoc();
				$clientUserId = $clientRow['user_id'];
                                $clientNotificationHeading = "Loan Application for $clientName" ;
                                $clientNotificationMessage = "Hi, $clientName! Your application has been received and is awaiting approval.";
                                addNotification($clientUserId, $clientNotificationHeading, $clientNotificationMessage);
                            }                                

                            // Insert notifications for all admin users
                            $adminQuery = "SELECT user_id FROM users WHERE role = 'admin'";
                            $adminResult = $conn->query($adminQuery);
                            while ($admin = $adminResult->fetch_assoc()) {
                                $adminUserId = $admin['user_id'];
                                $adminNotificationHeading = "Loan Application for $clientName";
                                $adminNotificationMessage = "Loan application has been received and is awaiting approval. Please APPROVE.";
                                addNotification($adminUserId, $adminNotificationHeading, $adminNotificationMessage);
                            }

			
			$insertQuerySuccess = "Loan application submitted successfully.";
		
		} else {
            $insertQueryError = "Error: " . $stmtInsert->error;
        }

        // Close the insert statement
        $stmtInsert->close();
    }

    // Close the select statement
    $stmt->close();

    // Close the database connection
}
?>

<!DOCTYPE html>
<html>
<head>
    <!-- Basic Page Info -->
    <meta charset="utf-8" />
    <title>Omabra Credit -  Loan Management System</title>

    <!-- Site favicon -->
    <link
            rel="apple-touch-icon"
            sizes="180x180"
            href="vendors/images/apple-touch-icon.png"
    />
    <link
            rel="icon"
            type="image/png"
            sizes="32x32"
            href="vendors/images/favicon-32x32.png"
    />
    <link
            rel="icon"
            type="image/png"
            sizes="16x16"
            href="vendors/images/favicon-16x16.png"
    />

    <!-- Mobile Specific Metas -->
    <meta
            name="viewport"
            content="width=device-width, initial-scale=1, maximum-scale=1"
    />

    <!-- Google Font -->
    <link
            href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
            rel="stylesheet"
    />
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="vendors/styles/core.css" />
    <link
            rel="stylesheet"
            type="text/css"
            href="vendors/styles/icon-font.min.css"
    />
    <link rel="stylesheet" type="text/css" href="vendors/styles/style.css" />

</head>
<body class="login-page">
<div class="login-header box-shadow">
    <div
            class="container-fluid d-flex justify-content-between align-items-center"
    >
        <div class="brand-logo">
            <a href="">
                <img src="vendors/images/omabra_logo.png" alt="" />
            </a>
        </div>
        <div class="login-menu">
            <ul>
                <li><a href="login.php">Login</a></li>
            </ul>
        </div>
    </div>
</div>
<div
        class="login-wrap d-flex align-items-center flex-wrap justify-content-center"
>
    <div class="container">
    <div class="row align-items-center">
    <div class="login-box bg-white box-shadow border-radius-10">
    <div class="login-title">
                        <h2 class="text-center text-primary">Omabra Credit - LMS</h2>
                    </div>
					<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div class="select-role">
        <div class="btn-group btn-group-toggle" data-toggle="buttons">


            <label class="btn text-center">
                
                <div class="icon">
                    <img src="vendors/images/person.svg" class="svg" alt="Client Icon" />
                </div>
                <span>Not registered with us? Register first to apply for a loan online today!</span>
            </label>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="input-group mb-0">
            <a href="register.php" class="btn btn-primary btn-lg btn-block">Register here!</a>
            </div>
        </div>
    </div>

    </div>

</div>



<div class="pd-20 card-box mb-30">
					<div class="wizard-content">
					<?php
    if (!empty($clientExistError)) {
        echo '<p style="color:red;">' . htmlspecialchars($clientExistError) . '</p>';
    }
    if (!empty($insertQueryError)) {
        echo '<p style="color:red;">' . htmlspecialchars($insertQueryError) . '</p>';
    }
    if (!empty($insertQuerySuccess)) {
        echo '<p style="color:green;">' . htmlspecialchars($insertQuerySuccess) . '</p>';
    }
    ?>
                    <form class="tab-wizard wizard-circle wizard" method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

<!-- Two column layout starts here -->
<div class="row align-items-start">
	<!-- Left Column -->
	<div class="col-md-6">
		<!-- Loan Details Info -->
		<h5 class="text-blue h4">Loan Details Info</h5>
		<section>
			<div class="form-group">
				<label>ID/Passport Number:</label>
				<input type="text" class="form-control" name="national_id" />
			</div>

			<div class="form-group">
				<label>Requested Loan:</label>
				<input type="text" class="form-control" name="requested_amount" placeholder="500000" />
			</div>

			<div class="form-group">
				<label>Loan Purpose:</label>
				<input type="text" class="form-control" name="loan_purpose" placeholder="School Fees" />
			</div>

			<div class="form-group">
				<label>Duration (Number only):</label>
				<select class="custom-select col-12" name="duration">
					<option selected="">Choose...</option>
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
					<option value="6">6</option>
					<option value="7">7</option>
					<option value="8">8</option>
					<option value="9">9</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="12">12</option>
				</select>
			</div>

			<div class="form-group">
				<label>Duration period in:</label>
				<select class="custom-select col-12" name="duration_period">
					<option selected="">Choose...</option>
					<option value="1">Week(s)</option>
					<option value="2">Month(s)</option>
					<option value="3">Year(s)</option>
				</select>
			</div>

		</section>

		<!-- Loan Status Info -->
		<h5 class="text-blue h4 d-none">Loan Status Info</h5>
		<section class="form-group d-none">
			<div class="form-group">
				<label>Loan Status :</label>
				<select class="custom-select col-12" name="loan_status">
					<option selected="">Choose...</option>
					<option value="1">Pending</option>
				</select>
			</div>

			<div class="form-group">
				<label>OnBoarding Officer</label>
				<input class="form-control" type="text" readonly value="admin" name="onboarding_officer" />
			</div>
		</section>
	</div>

	<!-- Right Column -->
	<div class="col-md-6">
		<!-- Security Info -->
		<h5 class="text-blue h4">Security Info</h5>
		<section>
			<div class="form-group">
				<label>Collateral Name :</label>
				<input type="text" class="form-control" name="collateral_name" placeholder="please leave blank if not applicable for this product" />
			</div>

			<div class="form-group">
				<label>Collateral Value :</label>
				<input type="text" class="form-control" name="collateral_value" placeholder="please leave blank if not applicable for this product" />
			</div>

			<div class="form-group">
				<label>Attach Collateral Pic 1 :</label>
				<input type="file" class="form-control-file form-control height-auto" name="collateral_pic1" placeholder="please leave blank if not applicable for this product" />
			</div>

			<div class="form-group">
				<label>Attach Collateral Pic 2 :</label>
				<input type="file" class="form-control-file form-control height-auto" name="collateral_pic2" placeholder="please leave blank if not applicable for this product" />
			</div>

			<div class="form-group d-none">
				<label>Guarantor 1 Name :</label>
				<input type="text" class="form-control" name="guarantor1_name" placeholder="please leave blank if not applicable for this product" />
			</div>

			<div class="form-group d-none">
				<label>Guarantor 1 Phone Number :</label>
				<input type="text" class="form-control" name="guarantor1_phone" placeholder="please leave blank if not applicable for this product" />
			</div>

			<div class="form-group d-none">
				<label>Guarantor 2 Name :</label>
				<input type="text" class="form-control" name="guarantor2_name" placeholder="please leave blank if not applicable for this product" />
			</div>

			<div class="form-group d-none">
				<label>Guarantor 2 Phone Number :</label>
				<input type="text" class="form-control" name="guarantor2_phone" placeholder="please leave blank if not applicable for this product" />
			</div>
            <div class="form-group">
				<label>Date of Applying (Enter today's date) :</label>
				<input
					type="text"
					class="form-control date-picker"
					placeholder="Select Date"
					name="date_applied"
				/>
			</div>

		</section>
	</div>
</div>

<div class="text-center mt-4 form-group mb-0">
	<button type="submit" class="btn btn-primary">Submit: Loan Application</button>
</div>
</form>

					</div>
				</div>


    </div>
</div>
<!-- js -->
<script src="vendors/scripts/core.js"></script>
<script src="vendors/scripts/script.min.js"></script>
<script src="vendors/scripts/process.js"></script>
<script src="vendors/scripts/layout-settings.js"></script>
</body>
</html>
