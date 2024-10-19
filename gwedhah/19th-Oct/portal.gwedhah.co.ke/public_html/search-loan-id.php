<?php
// Include database connection file
require_once("config.php");
include_once "functions-tena.php";

// Start session and check user authentication
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user_role'];
$client_id = ''; // Initialize client ID

// Check if the user is a client
if ($user_role == 'client') {
    // Retrieve the client ID based on the logged-in user
    $email = $_SESSION['email'];
    $client_query = $conn->prepare("SELECT client_id FROM clients WHERE email = ?");
    $client_query->bind_param("s", $email);
    if ($client_query->execute()) {
        $client_result = $client_query->get_result();
        if ($client_result->num_rows > 0) {
            $client_data = $client_result->fetch_assoc();
            $client_id = $client_data['client_id'];
        } else {
            // If client data not found, redirect to login
            header("Location: login.php");
            exit();
        }
    }
}

// Initialize variables
$loans_found = false;
$loan_id = '';
$loan_profiles = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $loan_id = $_POST['loan_id'];

    // Retrieve loans by loan ID
    $loan_query = $conn->prepare("SELECT * FROM loan_info WHERE loan_id = ?");
    $loan_query->bind_param("s", $loan_id);
    
    // For clients, restrict the search to their own loan profiles
    if ($user_role == 'client') {
        $loan_query->bind_param("s", $loan_id);
    }
    
    if ($loan_query->execute()) {
        $loan_result = $loan_query->get_result();
        while ($loan_info = $loan_result->fetch_assoc()) {
            // For clients, check if the loan belongs to them
            if ($user_role == 'client' && $loan_info['client_id'] != $client_id) {
                // If the loan does not belong to the client, skip it
                continue;
            }

            // Get loan status info
            $loan_status_info = getLoanStatusInfo($loan_info);

            // Handle null or empty activation date for this page only
            $loan_status_info['Activation Date'] = !empty($loan_status_info['Activation Date']) ? 
                $loan_status_info['Activation Date'] : "No Activation Date Available";

            // Defaulting to 0.00 for numerical values and 'N/A' for other fields if empty
            $loan_details_info = [
                'ID/Passport Number' => !empty($loan_info['national_id']) ? $loan_info['national_id'] : 'N/A',
                'Requested Loan' => !empty($loan_info['requested_amount']) ? number_format($loan_info['requested_amount'], 2) : '0.00',
                'Loan Purpose' => !empty($loan_info['loan_purpose']) ? $loan_info['loan_purpose'] : 'N/A',
                'Duration' => !empty($loan_info['duration']) ? $loan_info['duration'] : 'N/A',
                'Duration period in' => !empty($loan_info['duration_period']) ? $loan_info['duration_period'] : 'N/A',
                'Date of Applying' => !empty($loan_info['date_applied']) ? $loan_info['date_applied'] : 'N/A',
            ];

            $loan_security_info = [
                'Collateral Name' => !empty($loan_info['collateral_name']) ? $loan_info['collateral_name'] : 'N/A',
                'Collateral Value' => !empty($loan_info['collateral_value']) ? number_format($loan_info['collateral_value'], 2) : '0.00',
                'Collateral Pic 1' => !empty($loan_info['collateral_pic1']) ? $loan_info['collateral_pic1'] : 'N/A',
                'Collateral Pic 2' => !empty($loan_info['collateral_pic2']) ? $loan_info['collateral_pic2'] : 'N/A',
                'Guarantor 1 Name' => !empty($loan_info['guarantor1_name']) ? $loan_info['guarantor1_name'] : 'N/A',
                'Guarantor 1 Phone Number' => !empty($loan_info['guarantor1_phone']) ? $loan_info['guarantor1_phone'] : 'N/A',
                'Guarantor 2 Name' => !empty($loan_info['guarantor2_name']) ? $loan_info['guarantor2_name'] : 'N/A',
                'Guarantor 2 Phone Number' => !empty($loan_info['guarantor2_phone']) ? $loan_info['guarantor2_phone'] : 'N/A',
                'Onboarding Officer' => !empty($loan_info['onboarding_officer']) ? $loan_info['onboarding_officer'] : 'N/A',
            ];

            $loan_profiles[] = [
                'loan_info' => $loan_info,
                'loan_status_info' => $loan_status_info,
                'loan_details_info' => $loan_details_info,
                'loan_security_info' => $loan_security_info,
            ];

            $loans_found = true;
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
		} elseif ($user_role == 'client') {
			include('left-sidebar-client.php');
		} else {
			// If the user role is neither admin, manager, nor client, redirect or show an error
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
                                    <li class="breadcrumb-item active" aria-current="page">Loan Profiles</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="search-icon-box card-box mb-30">
                    <form method="post" action="">
                        <input
                            type="text"
                            class="border-radius-10"
                            name="loan_id"
                            id="filter_input"
                            placeholder="Search Loan Profiles: Enter Loan ID..."
                            title="Type in the loan ID"
                            required
                        />
                        <i class="search_icon dw dw-search" onclick="this.closest('form').submit();"></i>
                    </form>
                </div>

                <?php if ($loans_found): ?>
                    <?php foreach ($loan_profiles as $profile): ?>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-8 col-sm-12 mb-30">
                                <div class="pd-20 card-box height-100-p">
                                    <h5 class="text-center h5 mb-0">LOAN ID: <?php echo htmlspecialchars($profile['loan_info']['loan_id']); ?></h5>
                                    <p class="text-center text-muted font-14">Here are the specifics of this loan.</p>
                                    <div class="profile-info-container">
                                        <!-- Loan Status Info -->
                                        <div class="profile-info">
                                            <h5 class="mb-20 h5 text-blue">Loan Status Info</h5>
                                            <ul>
                                                <?php foreach ($profile['loan_status_info'] as $label => $value): ?>
                                                    <li><span><?php echo htmlspecialchars($label); ?>:</span> <?php echo htmlspecialchars($value); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                        <!-- Loan Details Info -->
                                        <div class="profile-info">
                                            <h5 class="mb-20 h5 text-blue">Loan Details Info</h5>
                                            <ul>
                                                <?php foreach ($profile['loan_details_info'] as $label => $value): ?>
                                                    <li><span><?php echo htmlspecialchars($label); ?>:</span> <?php echo htmlspecialchars($value); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                        <!-- Loan Security Info -->
                                        <div class="profile-info">
                                            <h5 class="mb-20 h5 text-blue">Loan Security Info</h5>
                                            <ul>
                                                <?php foreach ($profile['loan_security_info'] as $label => $value): ?>
                                                    <li><span><?php echo htmlspecialchars($label); ?>:</span> <?php echo htmlspecialchars($value); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                        
                                    </div>
                                    <a href="printify.php?loan_id=<?php echo htmlspecialchars($profile['loan_info']['loan_id']); ?>">
                                    <button
										type="button"
										class="btn btn-secondary btn-lg btn-block"
									>
										View Statement
									</button>
                                </a>
                                </div>
                            </div>
                            
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
                        <div class="alert alert-danger" role="alert">
                            No loans found for the given Loan ID.
                        </div>
                    <?php endif; ?>
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
		<script src="src/plugins/jQuery-Knob-master/jquery.knob.min.js"></script>
		<script src="src/plugins/highcharts-6.0.7/code/highcharts.js"></script>
		<script src="src/plugins/highcharts-6.0.7/code/highcharts-more.js"></script>
		<script src="src/plugins/jvectormap/jquery-jvectormap-2.0.3.min.js"></script>
		<script src="src/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
		<script src="vendors/scripts/dashboard2.js"></script>
		<script src="src/plugins/datatables/js/jquery.dataTables.min.js"></script>
		<script src="src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
		<script src="src/plugins/datatables/js/dataTables.responsive.min.js"></script>
		<script src="src/plugins/datatables/js/responsive.bootstrap4.min.js"></script>
				<!-- buttons for Export datatable -->
		<script src="src/plugins/datatables/js/dataTables.buttons.min.js"></script>
		<script src="src/plugins/datatables/js/buttons.bootstrap4.min.js"></script>
		<script src="src/plugins/datatables/js/buttons.print.min.js"></script>
		<script src="src/plugins/datatables/js/buttons.html5.min.js"></script>
		<script src="src/plugins/datatables/js/buttons.flash.min.js"></script>
		<script src="src/plugins/datatables/js/pdfmake.min.js"></script>
		<script src="src/plugins/datatables/js/vfs_fonts.js"></script>
		<!-- Datatable Setting js -->
		<script src="vendors/scripts/datatable-setting.js"></script>
    </body>
</html>
