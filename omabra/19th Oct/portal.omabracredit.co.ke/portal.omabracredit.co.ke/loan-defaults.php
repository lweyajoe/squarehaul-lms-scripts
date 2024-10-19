<?php
require_once("config.php");
include_once "functions.php";
include_once "approve_loan_defaulter.php";

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'manager')) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user_role'];

// Fetch all active loans
$active_loans_query = $conn->prepare("SELECT * FROM loan_info WHERE loan_status = 'Active'");
if (!$active_loans_query->execute()) {
    die("Error fetching active loans: " . $conn->error);
}
$active_loans_result = $active_loans_query->get_result();

$loans_found = false;
$loan_profiles = [];

while ($loan_info = $active_loans_result->fetch_assoc()) {
    // Get the loan status info using your function
    $loan_status_info = getLoanStatusInfo($conn, $loan_info);

    // Check if the loan is eligible for default
    $total_payments = floatval($loan_status_info['Total Payments']);
    $total_amount_due = floatval($loan_status_info['Total EIs']); 
    // If no commas are involved, floatval() is simpler

    $duration = $loan_info['duration'];
    $activation_date = $loan_info['created_at'];
    $duration_period = $loan_info['duration_period'];
    $periodPassed = calculatePeriodsPassed($activation_date, $duration_period);

    if ($periodPassed >= $duration) {
        if ($total_amount_due >= $total_payments) {
            // Loan is eligible for clearance, so include it in the $loan_profiles array
            $loans_found = true;
            $loan_details_info = [
                'ID/Passport Number' => $loan_info['national_id'],
                'Requested Loan' => number_format($loan_info['requested_amount'], 2),
                'Loan Purpose' => $loan_info['loan_purpose'],
                'Duration' => $loan_info['duration'],
                'Duration period in' => $loan_info['duration_period'],
                'Date of Applying' => $loan_info['date_applied'],
            ];
            $loan_security_info = [
                'Collateral Name' => $loan_info['collateral_name'],
                'Collateral Value' => number_format($loan_info['collateral_value'], 2),
                'Collateral Pic 1' => $loan_info['collateral_pic1'],
                'Collateral Pic 2' => $loan_info['collateral_pic2'],
                'Guarantor 1 Name' => $loan_info['guarantor1_name'],
                'Guarantor 1 Phone Number' => $loan_info['guarantor1_phone'],
                'Guarantor 2 Name' => $loan_info['guarantor2_name'],
                'Guarantor 2 Phone Number' => $loan_info['guarantor2_phone'],
                'Onboarding Officer' => $loan_info['onboarding_officer'],
            ];
            $loan_profiles[] = [
                'loan_info' => $loan_info,
                'loan_status_info' => $loan_status_info,
                'loan_details_info' => $loan_details_info,
                'loan_security_info' => $loan_security_info,
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html>
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
		} else {
			// If the user role is neither admin nor manager, redirect or show an error
			header("Location: login.php");
			exit();
		}
		?>
        
        <!-- Your content goes here -->

		<div class="mobile-menu-overlay"></div>

        <div class="main-container">
			<div class="xs-pd-20-10 pd-ltr-20">

			<div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <h4>Profile</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Loan Profiles Of Loans Ready For Default Status</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <?php if ($loans_found): ?>
                    <?php foreach ($loan_profiles as $profile): ?>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-8 col-sm-12 mb-30">
                                <div class="pd-20 card-box height-100-p">
                                    <h5 class="text-center h5 mb-0">LOAN ID: <?php echo htmlspecialchars($profile['loan_info']['loan_id']); ?></h5>
                                    <p class="text-center text-muted font-14">Here are the specifics of this loan.</p>
                                    <div class="profile-info-container" style="display: flex; justify-content: space-between; gap: 20px;">
                                        <!-- Loan Status Info -->
                                        <div class="profile-info" style="width: 30%">
                                            <h5 class="mb-20 h5 text-blue">Loan Status Info</h5>
                                            <?php
                                            if (!empty($loanStatusUpdateFail)) {
                                                echo '<p style="color:red;">' . htmlspecialchars($loanStatusUpdateFail) . '</p>';
                                            }
                                            if (!empty($loanStatusUpdateSuccess)) {
                                                echo '<p style="color:green;">' . htmlspecialchars($loanStatusUpdateSuccess) . '</p>';
                                            }
                                            ?>
                                            <ul>
                                                <?php foreach ($profile['loan_status_info'] as $label => $value): ?>
                                                    <li><span><?php echo htmlspecialchars($label); ?>:</span> <?php echo htmlspecialchars($value); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                        <!-- Loan Details Info -->
                                        <div class="profile-info" style="width: 30%">
                                            <h5 class="mb-20 h5 text-blue">Loan Details Info</h5>
                                            <ul>
                                                <?php foreach ($profile['loan_details_info'] as $label => $value): ?>
                                                    <li><span><?php echo htmlspecialchars($label); ?>:</span> <?php echo htmlspecialchars($value); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                        <!-- Loan Security Info -->
                                        <div class="profile-info" style="width: 30%">
                                            <h5 class="mb-20 h5 text-blue">Loan Security Info</h5>
                                            <ul>
                                                <?php foreach ($profile['loan_security_info'] as $label => $value): ?>
                                                    <li><span><?php echo htmlspecialchars($label); ?>:</span> <?php echo htmlspecialchars($value); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- Approve Loan Clearance Button -->
                                     <div class="text-center mt-4">
                                        <form method="POST" action="approve_loan_defaulter.php" class="approve-loan-form">
                                            <input type="hidden" name="loan_id" value="<?php echo htmlspecialchars($profile['loan_info']['loan_id']); ?>">
                                            <div class="form-group mb-0">
                                                <button type="submit" class="btn btn-success">Approve Loan As Defaulted</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center">No loans available for default.</p>
                        <?php endif; ?>
                    </div>

        <?php require_once("footer.php"); ?>
        </div>
		</div>
        <!-- js -->
		<script src="vendors/scripts/core.js"></script>
		<script src="vendors/scripts/script.min.js"></script>
		<script src="vendors/scripts/process.js"></script>
		<script src="vendors/scripts/layout-settings.js"></script>
		<script src="vendors/scripts/dashboard2.js"></script>
    </body>
    </html>
