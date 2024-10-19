<?php
require_once("config.php");
include_once "functions-tena.php";

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user_role'];

// Fetch company settings from the database
$sql = "SELECT * FROM company_settings WHERE id = 1"; // Assuming you have a single record
$result = $conn->query($sql);
$companySettings = $result->fetch_assoc(); // Fetch as associative array

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
		} elseif ($user_role == 'client') {
			include('left-sidebar-client.php');
		} else {
			// If the user role is neither admin, manager, nor client, redirect or show an error
			header("Location: login.php");
			exit();
		}
		?>

	<div class="main-container">
    <div class="xs-pd-20-10 pd-ltr-20">
        <div class="page-header">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <!-- Page title and breadcrumb -->
                    <div class="title">
                        <h4>Settings</h4>
                    </div>
                    <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                            Company Settings
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 col-sm-12 text-right">
                </div>
            </div>
        </div>

        					<!-- Default Basic Forms Start -->
					<div class="pd-20 card-box mb-30">
						<div class="clearfix">
							<div class="pull-left">
								<h4 class="text-blue h4">Set Up Your Company</h4>
								<p class="mb-30">Add details/Edit Changes</p>
							</div>
						</div>

<form method="POST" action="update_company_settings.php">
    <div class="row align-items-center">
<!-- Company Info Section -->
<div class="col-md-6 col-lg-6">
    <div class="login-box bg-white box-shadow border-radius-10">
        <h5 class="text-blue h4">Company Info</h5>

        <div class="input-group custom">
            <input
                type="text"
                class="form-control form-control-lg"
                placeholder="Company Name"
                name="company_name"
                value="<?php echo htmlspecialchars($companySettings['company_name'] ?? ''); ?>"
                data-toggle="tooltip"
                data-placement="bottom"
                title="Company Name"
            />
            <div class="input-group-append custom">
                <span class="input-group-text"><i class="icon-copy dw dw-user1"></i></span>
            </div>
        </div>

        <div class="input-group custom">
            <input
                type="text"
                class="form-control form-control-lg"
                placeholder="Company Address"
                name="company_address"
                value="<?php echo htmlspecialchars($companySettings['company_address'] ?? ''); ?>"
                data-toggle="tooltip"
                data-placement="bottom"
                title="Company Address"
            />
            <div class="input-group-append custom">
                <span class="input-group-text"><i class="icon-copy dw dw-home"></i></span>
            </div>
        </div>

        <div class="input-group custom">
            <input
                type="email"
                class="form-control form-control-lg"
                placeholder="Email"
                name="company_email"
                value="<?php echo htmlspecialchars($companySettings['company_email'] ?? ''); ?>"
                data-toggle="tooltip"
                data-placement="bottom"
                title="Email"
            />
            <div class="input-group-append custom">
                <span class="input-group-text"><i class="icon-copy dw dw-email1"></i></span>
            </div>
        </div>

        <div class="input-group custom">
            <input
                type="url"
                class="form-control form-control-lg"
                placeholder="Website"
                name="company_website"
                value="<?php echo htmlspecialchars($companySettings['company_website'] ?? ''); ?>"
                data-toggle="tooltip"
                data-placement="bottom"
                title="Website"
            />
            <div class="input-group-append custom">
                <span class="input-group-text"><i class="icon-copy dw dw-globe"></i></span>
            </div>
        </div>

        <div class="input-group custom">
            <input
                type="tel"
                class="form-control form-control-lg"
                placeholder="Telephone"
                name="company_phone"
                value="<?php echo htmlspecialchars($companySettings['company_phone'] ?? ''); ?>"
                data-toggle="tooltip"
                data-placement="bottom"
                title="Telephone"
            />
            <div class="input-group-append custom">
                <span class="input-group-text"><i class="icon-copy dw dw-phone"></i></span>
            </div>
        </div>

        <div class="input-group custom">
            <input
                type="text"
                class="form-control form-control-lg"
                placeholder="Tax Rate"
                name="tax_rate"
                value="<?php echo htmlspecialchars($companySettings['tax_rate'] ?? ''); ?>"
                data-toggle="tooltip"
                data-placement="bottom"
                title="Tax Rate"
            />
            <div class="input-group-append custom">
                <span class="input-group-text"><i class="icon-copy dw dw-money"></i></span>
            </div>
        </div>
    </div>
</div>

<!-- Bank Info Section -->
<div class="col-md-6 col-lg-6">
    <div class="login-box bg-white box-shadow border-radius-10">
        <h5 class="text-blue h4">Bank Info</h5>

        <div class="input-group custom">
            <input
                type="text"
                class="form-control form-control-lg"
                placeholder="Bank Name"
                name="bank_name"
                value="<?php echo htmlspecialchars($companySettings['bank_name'] ?? ''); ?>"
                data-toggle="tooltip"
                data-placement="bottom"
                title="Bank Name"
            />
            <div class="input-group-append custom">
                <span class="input-group-text"><i class="icon-copy dw dw-bank"></i></span>
            </div>
        </div>

        <div class="input-group custom">
            <input
                type="text"
                class="form-control form-control-lg"
                placeholder="Account Number"
                name="account_number"
                value="<?php echo htmlspecialchars($companySettings['account_number'] ?? ''); ?>"
                data-toggle="tooltip"
                data-placement="bottom"
                title="Account Number"
            />
            <div class="input-group-append custom">
                <span class="input-group-text"><i class="icon-copy dw dw-bank"></i></span>
            </div>
        </div>

        <div class="input-group custom">
            <input
                type="text"
                class="form-control form-control-lg"
                placeholder="Account Reference"
                name="account_reference"
                value="<?php echo htmlspecialchars($companySettings['account_reference'] ?? ''); ?>"
                data-toggle="tooltip"
                data-placement="bottom"
                title="Account Reference"
            />
            <div class="input-group-append custom">
                <span class="input-group-text"><i class="icon-copy dw dw-bank"></i></span>
            </div>
        </div>

        <div class="input-group custom">
            <input
                type="text"
                class="form-control form-control-lg"
                placeholder="Payee Name"
                name="payee_name"
                value="<?php echo htmlspecialchars($companySettings['payee_name'] ?? ''); ?>"
                data-toggle="tooltip"
                data-placement="bottom"
                title="Payee Name"
            />
            <div class="input-group-append custom">
                <span class="input-group-text"><i class="icon-copy dw dw-user1"></i></span>
            </div>
        </div>

        <div class="input-group custom">
            <input
                type="text"
                class="form-control form-control-lg"
                placeholder="Interest Rate"
                name="interest_rate"
                value="<?php echo htmlspecialchars($companySettings['interest_rate'] ?? ''); ?>"
                data-toggle="tooltip"
                data-placement="bottom"
                title="Interest Rate"
            />
            <div class="input-group-append custom">
                <span class="input-group-text"><i class="icon-copy dw dw-money"></i></span>
            </div>
        </div>

        <div class="input-group custom">
            <select class="form-control form-control-lg" name="interest_billing_period" data-toggle="tooltip" data-placement="bottom" title="Select Interest Billing Period">
                <option value="">Select Interest Billing Period</option>
                <option value="Month" <?php echo (isset($companySettings['interest_billing_period']) && $companySettings['interest_billing_period'] == 'Month') ? 'selected' : ''; ?>>Monthly</option>
            </select>
        </div>
    </div>
</div>
    </div>

    <div class="row align-items-center">
<!-- Company Info Section -->
<div class="col-md-6 col-lg-6">
    <div class="login-box bg-white box-shadow border-radius-10">
        <h5 class="text-blue h4">Processing Fee Charges on Loan</h5>

        <div class="input-group custom">
            <input
                type="text"
                class="form-control form-control-lg"
                placeholder="Processing Fees"
                name="processing_fees"
                value="<?php echo htmlspecialchars($companySettings['processing_fees'] ?? ''); ?>"
                data-toggle="tooltip"
                data-placement="bottom"
                title="Processing Fees"
            />
            <div class="input-group-append custom">
                <span class="input-group-text"><i class="icon-copy dw dw-user1"></i></span>
            </div>
        </div>
    </div>
</div>

<!-- Bank Info Section -->
<div class="col-md-6 col-lg-6">
    <div class="login-box bg-white box-shadow border-radius-10">
        <h5 class="text-blue h4">Insurance Charge on Loan</h5>

        <div class="input-group custom">
            <input
                type="text"
                class="form-control form-control-lg"
                placeholder="Insurance Fees Percent eg 10 per cent"
                name="insurance_fees"
                value="<?php echo htmlspecialchars($companySettings['insurance_fees'] ?? ''); ?>"
                data-toggle="tooltip"
                data-placement="bottom"
                title="Insurance Fees Percent eg 10 per cent"
            />
            <div class="input-group-append custom">
                <span class="input-group-text"><i class="icon-copy dw dw-bank"></i></span>
            </div>
        </div>
    </div>
</div>
    </div>


    <div class="text-center mt-4 form-group mb-0">
        <button type="submit" class="btn btn-primary">Submit: Update Company Details</button>
    </div>
</form>

							</div>
						</div>
					</div>
					<!-- Default Basic Forms End -->

        <div class="footer-wrap pd-20 mb-20 card-box">
            Dev. <a href="http://www.squarehaul.online" target="_blank">Squarehaul</a>
        </div>
    </div>
    <!-- js -->
		<script src="vendors/scripts/core.js"></script>
		<script src="vendors/scripts/script.min.js"></script>
		<script src="vendors/scripts/process.js"></script>
		<script src="vendors/scripts/layout-settings.js"></script>
		<script src="src/plugins/jquery-steps/jquery.steps.js"></script>
</body>
</html>
