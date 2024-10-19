<?php
// Include database connection file
require_once("config.php");
include_once "functions-tena.php";

// Enable error reporting
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

// Check if the user is logged in as admin or manager
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'admin')) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user_role'];

// Check if a loan ID is provided via POST (from loan-approval.php)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loan_id'])) {
    $loan_id = $_POST['loan_id'];

    // Fetch loan data from loan_info for the provided loan_id
    $stmt = $conn->prepare("SELECT * FROM loan_applications WHERE loan_id = ?");
    $stmt->bind_param("s", $loan_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $loan = $result->fetch_assoc();  // Loan data fetched successfully
    } else {
        echo "No loan found for this ID.";
        exit();
    }
}

// Process form submission to update loan info
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_loan'])) {
    // Capture the updated data from the form
    $national_id = $_POST['national_id'];
    $requested_amount = $_POST['requested_amount'];
    $loan_purpose = $_POST['loan_purpose'];
    $duration = $_POST['duration'];
    $duration_period = $_POST['duration_period'];
    $date_applied = $_POST['date_applied'];
    $collateral_name = $_POST['collateral_name'];
    $collateral_value = $_POST['collateral_value'];
    $loan_status = $_POST['loan_status'];
    $onboarding_officer = $_POST['onboarding_officer'];

    // Update the loan_info table with the new data
    $stmt = $conn->prepare("UPDATE loan_applications SET national_id = ?, requested_amount = ?, loan_purpose = ?, duration = ?, duration_period = ?, date_applied = ?, collateral_name = ?, collateral_value = ?, loan_status = ?, onboarding_officer = ? WHERE loan_id = ?");
    $stmt->bind_param("sssssssssss", $national_id, $requested_amount, $loan_purpose, $duration, $duration_period, $date_applied, $collateral_name, $collateral_value, $loan_status, $onboarding_officer, $loan_id);

    if ($stmt->execute()) {
        echo "Loan updated successfully!";
        header("Location: loan-approval.php");
    } else {
        echo "Error updating loan.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php require_once("head.php"); ?>
<body>
    <?php require_once("header.php"); ?>
    <?php require_once("right-sidebar.php"); ?>

    <?php
    // Include the appropriate sidebar based on the user role
    if ($user_role == 'admin') {
        include('left-sidebar-admin.php');
    } elseif ($user_role == 'manager') {
        include('left-sidebar-manager.php');
    } elseif ($user_role == 'client') {
        include('left-sidebar-client.php');
    } else {
        // If the user role is neither admin, manager, nor client, redirect or show an error
        header("Location: login.php");
        exit();
    }
    ?>

    <!-- Add your form and other HTML content here -->

        <!-- Your content goes here -->
        <div class="main-container">
        <div class="xs-pd-20-10 pd-ltr-20">
            <!-- HTML content here -->
            <!-- Modify the HTML form to include PHP where necessary -->
            <div class="page-header">
					<div class="row">
						<div class="col-md-6 col-sm-12">
							<div class="title">
								<h4>Loan Application</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="index.html">Home</a></li>
									<li class="breadcrumb-item active" aria-current="page">
										This Page: To edit loan application before approval
									</li>
								</ol>
							</nav>
						</div>
						<div class="col-md-6 col-sm-12 text-right">
						</div>
					</div>
				</div>

                <div class="pd-20 card-box mb-30">
					<div class="clearfix">
						<h4 class="text-blue h4">Change/Edit the Loan Application Before Approval</h4>
						<p class="mb-30">All fields required</p>
					</div>
					<div class="wizard-content">
                    
                    <form class="tab-wizard wizard-circle wizard" method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

    <input type="hidden" name="loan_id" value="<?php echo htmlspecialchars($loan['loan_id']); ?>" />

    <div class="row align-items-center">

    <!-- Left Column -->
    <div class="col-md-6">
        <!-- Loan Details Info -->
        <h5 class="text-blue h4">Loan Details Info</h5>
        <section>
            <div class="form-group">
                <label>ID/Passport Number:</label>
                <input type="text" class="form-control" name="national_id" readonly value="<?php echo htmlspecialchars($loan['national_id']); ?>" />
            </div>

            <div class="form-group">
                <label>Requested Loan:</label>
                <input type="text" class="form-control" name="requested_amount" value="<?php echo htmlspecialchars($loan['requested_amount']); ?>" />
            </div>

            <div class="form-group">
                <label>Loan Purpose:</label>
                <input type="text" class="form-control" name="loan_purpose" value="<?php echo htmlspecialchars($loan['loan_purpose']); ?>" />
            </div>

            <div class="form-group">
                <label>Duration (Number only):</label>
                <select class="custom-select col-12" name="duration">
                    <?php for ($i = 1; $i <= 52; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php if ($loan['duration'] == $i) echo 'selected'; ?>><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Duration period in:</label>
                <select class="custom-select col-12" name="duration_period">
                    <option value="Week" <?php if ($loan['duration_period'] == 'Week') echo 'selected'; ?>>Week(s)</option>
                    <option value="Month" <?php if ($loan['duration_period'] == 'Month') echo 'selected'; ?>>Month(s)</option>
                    <option value="Year" <?php if ($loan['duration_period'] == 'Year') echo 'selected'; ?>>Year(s)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Date of Applying :</label>
                <input type="text" class="form-control date-picker" name="date_applied" value="<?php echo htmlspecialchars($loan['date_applied']); ?>" />
            </div>
        </section>
    </div>

    <!-- Right Column -->
    <div class="col-md-6">
        <h5 class="text-blue h4">Security Info</h5>
        <section>
            <div class="form-group">
                <label>Collateral Name :</label>
                <input type="text" class="form-control" name="collateral_name" value="<?php echo htmlspecialchars($loan['collateral_name']); ?>" />
            </div>

            <div class="form-group">
                <label>Collateral Value :</label>
                <input type="text" class="form-control" name="collateral_value" value="<?php echo htmlspecialchars($loan['collateral_value']); ?>" />
            </div>

            <div class="form-group">
                <label>Loan Status :</label>
                <select class="custom-select col-12" name="loan_status">
                    <option value="Pending" <?php if ($loan['loan_status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                </select>
            </div>

            <div class="form-group">
                <label>OnBoarding Officer</label>
                <input class="form-control" type="text" readonly value="<?php echo htmlspecialchars($loan['onboarding_officer']); ?>" name="onboarding_officer" />
            </div>
        </section>
    </div>

    </div>

    <div class="text-center mt-4 form-group mb-0">
        <button type="submit" name="update_loan" class="btn btn-primary">Update Loan Information</button>
    </div>
</form>

					</div>
				</div>
				<?php require_once("footer.php"); ?>
			</div>
    </div>
    <!-- js -->
		<script src="vendors/scripts/core.js"></script>
		<script src="vendors/scripts/script.min.js"></script>
		<script src="vendors/scripts/process.js"></script>
		<script src="vendors/scripts/layout-settings.js"></script>
		<script src="src/plugins/jquery-steps/jquery.steps.js"></script>
		<!-- <script src="vendors/scripts/steps-setting.js"></script> -->

</body>
</html>
