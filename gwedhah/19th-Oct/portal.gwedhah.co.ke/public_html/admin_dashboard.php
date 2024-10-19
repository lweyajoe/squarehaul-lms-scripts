<?php
// Include database connection file
require_once("config.php");
include_once "functions-tena.php";


// Check if the user is logged in. Page can only be viewed when logged in.

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user_role'];

// Assuming these functions are already defined in functions.php
$allAccruedInterest = getTotalAccruedInterest();
$payments = getTotalPayments();
$loansDisbursed = getTotalLoansDisbursed();
$countLoans = countLoans();
$countClients = countClients();
$loanBookStatus = getLoanBookStatus();

// Assuming these functions are defined to get today's data
$todayReportData = [
    'total_loans_today' => getTotalLoansDisbursedToday(),
    'total_amount_disbursed_today' => getTotalAmountDisbursedToday(),
    'total_interest_earned_today' => getTotalInterestEarnedToday(),
    'total_payments_received_today' => getTotalPaymentsReceivedToday(),
];

$transactions = getTransactionsData($conn);

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
        <h2 class="h3 mb-0">QuickStart</h2>
    </div>
            <div class="row clearfix">

            <div class="col-md-4 col-sm-12 mb-30">
							<div class="pd-20 card-box height-100-p">
								<h4 class="mb-15 text-blue h4">Loans & Balances Management Module</h4>
								<p>
									Access different aspects of loan management here.
								</p>
								<div class="btn-list">
                                <a href="payments-handler.php">
									<button
										type="button"
										class="btn btn-success btn-lg btn-block"
									>
										Update Payment
									</button>
                                </a>
                                <a href="apply-loan.php">
									<button
										type="button"
										class="btn btn-secondary btn-lg btn-block"
									>
										Apply Loan
									</button>
                                </a>
								</div>
                                <div class="btn-list">
                                <a href="loan-approval.php">
									<button
										type="button"
										class="btn btn-success btn-lg btn-block"
									>
										Approve Loan Applied
									</button>
                                </a>
                                <a href="loan-clearance.php">
									<button
										type="button"
										class="btn btn-secondary btn-lg btn-block"
									>
										Declare Loan as Cleared
									</button>
                                </a>
								</div>
                                <div class="btn-list">
                                <a href="loan-defaults.php">
									<button
										type="button"
										class="btn btn-success btn-lg btn-block"
									>
										Declare Loan as Defaulted
									</button>
                                </a>
								</div>


							</div>
						</div>


            <div class="col-md-4 col-sm-12 mb-30">
							<div class="pd-20 card-box height-100-p">
								<h4 class="mb-15 text-blue h4">Human Resource Management Module</h4>
								<p>
                                Access different aspects of hr management here.
								</p>
                                <div class="btn-list">
                                <a href="onboard-client.php">
									<button
										type="button"
										class="btn btn-success btn-lg btn-block"
									>
                                    Onboard New Client
									</button>
                                </a>
                                <a href="https://mail.gwedhah.co.ke">
                                    <button
										type="button"
										class="btn btn-secondary btn-lg btn-block"
									>
										Access Staff Webmail
									</button>
                                </a>
                                <a href="onboard-manager.php">
									<button
										type="button"
										class="btn btn-success btn-lg btn-block"
									>
                                    Onboard New Officer
									</button>
                                </a>
                                <a href="register-new-admin.php">
                                    <button
										type="button"
										class="btn btn-secondary btn-lg btn-block"
									>
										Create New Admin Account
									</button>
                                </a>
                                <a href="officer-loan-reports.php">
									<button
										type="button"
										class="btn btn-success btn-lg btn-block"
									>
                                    See Officers' Loan Reports
									</button>
                                </a>
								</div>

							</div>
						</div>


            <div class="col-md-4 col-sm-12 mb-30">
							<div class="pd-20 card-box height-100-p">
								<h4 class="mb-15 text-blue h4">Search Module</h4>
								<p>
                                    Access different aspects of search function for website mobility here.
								</p>
                                <div class="btn-list">
                                <a href="search-national-id.php">
									<button
										type="button"
										class="btn btn-success btn-lg btn-block"
									>
                                    Search Loan Details: Use National ID
									</button>
                                </a>
								</div>
                                <div class="btn-list">
                                <a href="search-client-profile.php">
									<button
										type="button"
										class="btn btn-success btn-lg btn-block"
									>
                                    Search Client Profile: Use National ID
									</button>
                                </a>
                                <a href="search-manager-profile.php">
                                    <button
										type="button"
										class="btn btn-secondary btn-lg btn-block"
									>
										Search Officer Profile: Use Manager ID
									</button>
                                </a>
                                <a href="search-collateral.php">
									<button
										type="button"
										class="btn btn-success btn-lg btn-block"
									>
                                    Search Collateral Profile: Use Loan ID
									</button>
                                </a>

								</div>


							</div>
						</div>
    </div>


			<div class="title pb-20">
        <h2 class="h3 mb-0">My Business Overview</h2>
    </div>
    <div class="title pb-20">
        <h3 class="h3 mb-0">Since Inception</h3>
    </div>

    <div class="row pb-10">
    <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
        <div class="card-box height-100-p widget-style3">
            <div class="d-flex flex-wrap">
                <div class="widget-data">
                    <div class="weight-700 font-24 text-dark"><?php echo $countLoans; ?></div>
                    <div class="font-14 text-secondary weight-500">
                        Total No. of Loans Disbursed
                    </div>
                </div>
                <div class="widget-icon">
                    <div class="icon" data-color="#00eccf">
                        <i class="icon-copy dw dw-calendar1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
        <div class="card-box height-100-p widget-style3">
            <div class="d-flex flex-wrap">
                <div class="widget-data">
                    <div class="weight-700 font-24 text-dark"><?php echo $countClients; ?></div>
                    <div class="font-14 text-secondary weight-500">
                        Total No. of Clients in Clientbook
                    </div>
                </div>
                <div class="widget-icon">
                    <div class="icon" data-color="#ff5b5b">
                        <span class="icon-copy ti-heart"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
        <div class="card-box height-100-p widget-style3">
            <div class="d-flex flex-wrap">
                <div class="widget-data">
                    <div class="weight-700 font-24 text-dark"><?php echo number_format($allAccruedInterest, 2); ?></div>
                    <div class="font-14 text-secondary weight-500">
                        Total Accrued Interest
                    </div>
                </div>
                <div class="widget-icon">
                    <div class="icon">
                        <i class="icon-copy fa fa-stethoscope" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
        <div class="card-box height-100-p widget-style3">
            <div class="d-flex flex-wrap">
                <div class="widget-data">
                    <div class="weight-700 font-24 text-dark"><?php echo number_format($payments, 2); ?></div>
                    <div class="font-14 text-secondary weight-500">Total Payments Received</div>
                </div>
                <div class="widget-icon">
                    <div class="icon" data-color="#09cc06">
                        <i class="icon-copy fa fa-money" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="title pb-20">
    <h3 class="h3 mb-0">Today</h3>
</div>

<div class="row pb-10">
    <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
        <div class="card-box height-100-p widget-style3">
            <div class="d-flex flex-wrap">
                <div class="widget-data">
                    <div class="weight-700 font-24 text-dark"><?php echo $todayReportData['total_loans_today']; ?></div>
                    <div class="font-14 text-secondary weight-500">
                        No. of Loans Disbursed Today
                    </div>
                </div>
                <div class="widget-icon">
                    <div class="icon" data-color="#00eccf">
                        <i class="icon-copy dw dw-calendar1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
        <div class="card-box height-100-p widget-style3">
            <div class="d-flex flex-wrap">
                <div class="widget-data">
                    <div class="weight-700 font-24 text-dark"><?php echo number_format($todayReportData['total_amount_disbursed_today'], 2); ?></div>
                    <div class="font-14 text-secondary weight-500">
                        Loan Amounts Disbursed Today
                    </div>
                </div>
                <div class="widget-icon">
                    <div class="icon" data-color="#ff5b5b">
                        <span class="icon-copy ti-heart"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
        <div class="card-box height-100-p widget-style3">
            <div class="d-flex flex-wrap">
                <div class="widget-data">
                    <div class="weight-700 font-24 text-dark"><?php echo number_format($todayReportData['total_interest_earned_today'], 2); ?></div>
                    <div class="font-14 text-secondary weight-500">
                        Total Interest Earned Today
                    </div>
                </div>
                <div class="widget-icon">
                    <div class="icon">
                        <i class="icon-copy fa fa-stethoscope" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
        <div class="card-box height-100-p widget-style3">
            <div class="d-flex flex-wrap">
                <div class="widget-data">
                    <div class="weight-700 font-24 text-dark"><?php echo number_format($todayReportData['total_payments_received_today'], 2); ?></div>
                    <div class="font-14 text-secondary weight-500">
                        Total Payments Received Today
                    </div>
                </div>
                <div class="widget-icon">
                    <div class="icon" data-color="#09cc06">
                        <i class="icon-copy fa fa-money" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Transactions Data Table Start -->
    <div class="card-box mb-30">
                    <div class="pd-20">
                        <h4 class="text-blue h4">Transactions Data Table</h4>
                    </div>
                    <div class="pb-20">
                    <div class="table-container">
    <table class="data-table table stripe hover nowrap">
        <thead>
            <tr>
                <th>Name</th>
                <th>Loan ID No.</th>
                <th>Transaction Date</th>
                <th>Received Payment</th>
                <th>Transaction Reference</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?php echo htmlspecialchars($transaction['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($transaction['loan_id']); ?></td>
                    <td><?php echo htmlspecialchars($transaction['transaction_date']); ?></td>
                    <td><?php echo htmlspecialchars($transaction['received_payment']); ?></td>
                    <td><?php echo htmlspecialchars($transaction['transaction_reference']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
                    </div>
                </div>
                <!-- Transactions Data Table End -->

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
