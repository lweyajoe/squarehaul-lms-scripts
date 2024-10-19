<?php
// Include database connection file
require_once("config.php");
include_once "functions.php";

// Check if the user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user_role'];

$accruedInterestData = getAccruedInterestData($conn);

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
				
			<div class="title pb-20">
        <h2 class="h3 mb-0">Accrued Interest Overview</h2>
    </div>
    <div class="title pb-20">
        <h3 class="h3 mb-0">Active Loans</h3>
    </div>

    <div class="row pb-10">
        <div class="col-xl-12 col-lg-12 col-md-12 mb-20">
            <div class="card-box height-100-p widget-style3">
                <div class="d-flex flex-wrap">
                    <div class="widget-data">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Loan ID</th>
                                    <th>Client ID</th>
                                    <th>Requested Amount</th>
                                    <th>Interest Rate</th>
                                    <th>Interest Rate Period</th>
                                    <th>Start Date</th>
                                    <th>Accrued Interest</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($accruedInterestData as $data) : ?>
                                    <tr>
                                        <td><?php echo $data['loan_id']; ?></td>
                                        <td><?php echo $data['client_id']; ?></td>
                                        <td><?php echo number_format($data['requested_amount'], 2); ?></td>
                                        <td><?php echo $data['interest_rate']; ?>%</td>
                                        <td><?php echo $data['interest_rate_period']; ?></td>
                                        <td><?php echo $data['created_at']; ?></td>
                                        <td><?php echo number_format($data['accrued_interest'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="widget-icon">
                        <div class="icon" data-color="#00eccf">
                            <i class="icon-copy dw dw-calendar1"></i>
                        </div>
                    </div>
                </div>
            </div>
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
