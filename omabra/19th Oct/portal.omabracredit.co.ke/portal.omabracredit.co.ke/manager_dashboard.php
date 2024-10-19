<?php
// Include database connection file and functions file
require_once("config.php");
include_once "functions-tena.php";

// Start the session to check user login status and role
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'manager')) {
    // Redirect to login page if the user is not logged in or not an admin/manager
    header("Location: login.php");
    exit();
}

// Get the email and role of the logged-in user
$user_role = $_SESSION['user_role'];

//call manager-specific functions

$onboardingOfficer = getOnboardingOfficer();

// Fetch all loan numbers and their statuses for the logged-in manager
$query = "SELECT loan_id, loan_status FROM loan_info WHERE onboarding_officer = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $onboardingOfficer);
$stmt->execute();
$result = $stmt->get_result();

$loanNumbers = [];
$activeLoanIds = [];
while ($row = $result->fetch_assoc()) {
    $loanNumbers[] = $row['loan_id']; // Collect all loans
    if ($row['loan_status'] === 'Active') {
        $activeLoanIds[] = $row['loan_id']; // Collect only active loans
    }
}

// Fetch transactions data for the logged-in manager
$transactions = getManagerTransactionsData($onboardingOfficer);
?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once("head.php"); // Include the head content ?>
</head>
<body>
    <?php require_once("header.php"); // Include the header ?>
    <?php require_once("right-sidebar.php"); // Include the right sidebar ?>

    <?php
    // Include the appropriate sidebar based on the user role
    if ($user_role == 'admin') {
        include('left-sidebar-admin.php');
    } elseif ($user_role == 'manager') {
        include('left-sidebar-manager.php');
    } else {
        // If the user role is neither admin nor manager, redirect to login or show an error
        header("Location: login.php");
        exit();
    }
    ?>
    
    <!-- Main content area -->
    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
    <div class="xs-pd-20-10 pd-ltr-20">

        <!-- Performance report titles -->
        <div class="title pb-20">
            <h2 class="h3 mb-0">My Performance Report</h2>
        </div>
        <div class="title pb-20">
            <h3 class="h3 mb-0">Since Inception</h3>
        </div>

        <!-- Performance metrics since inception -->
        <div class="row pb-10">
            <!-- Total Loans Disbursed -->
            <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                <div class="card-box height-100-p widget-style3">
                    <div class="d-flex flex-wrap">
                        <div class="widget-data">
                            <div class="weight-700 font-24 text-dark"><?php echo htmlspecialchars(countLoansByManager()); ?></div>
                            <div class="font-14 text-secondary weight-500">
                                No. of Total Loans Disbursed
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
            <!-- Total Loan Amount Disbursed -->
            <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                <div class="card-box height-100-p widget-style3">
                    <div class="d-flex flex-wrap">
                        <div class="widget-data">
                            <div class="weight-700 font-24 text-dark"><?php echo number_format(getTotalAmountDisbursedByManager(), 2); ?></div>
                            <div class="font-14 text-secondary weight-500">
                                Total Loan Amount Disbursed
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
            <!-- Total Interest Earned -->
            <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                <div class="card-box height-100-p widget-style3">
                    <div class="d-flex flex-wrap">
                        <div class="widget-data">
                            <div class="weight-700 font-24 text-dark"><?php echo number_format(getTotalAccruedInterestByManager(), 2); ?></div>
                            <div class="font-14 text-secondary weight-500">
                                Total Interest Accrued
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
            <!-- Total Payments Received -->
            <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                <div class="card-box height-100-p widget-style3">
                    <div class="d-flex flex-wrap">
                        <div class="widget-data">
                            <div class="weight-700 font-24 text-dark"><?php echo number_format(getTotalPaymentsByManager(), 2); ?></div>
                            <div class="font-14 text-secondary weight-500">
                                Total Payments Received
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

        <!-- Today's Performance Report -->
        <div class="title pb-20">
            <h3 class="h3 mb-0">Today</h3>
        </div>

        <div class="row pb-10">
            <!-- Loans Disbursed Today -->
            <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                <div class="card-box height-100-p widget-style3">
                    <div class="d-flex flex-wrap">
                        <div class="widget-data">
                            <div class="weight-700 font-24 text-dark"><?php echo htmlspecialchars(getTotalLoansDisbursedTodayForManager()); ?></div>
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
            <!-- Loan Amounts Disbursed Today -->
            <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                <div class="card-box height-100-p widget-style3">
                    <div class="d-flex flex-wrap">
                        <div class="widget-data">
                            <div class="weight-700 font-24 text-dark"><?php echo number_format(getTotalAmountDisbursedTodayForManager(), 2); ?></div>
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
            <!-- Interest Earned Today -->
            <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                <div class="card-box height-100-p widget-style3">
                    <div class="d-flex flex-wrap">
                        <div class="widget-data">
                            <div class="weight-700 font-24 text-dark"><?php echo number_format(getTotalInterestEarnedTodayForManager(), 2); ?></div>
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
            <!-- Payments Received Today -->
            <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                <div class="card-box height-100-p widget-style3">
                    <div class="d-flex flex-wrap">
                        <div class="widget-data">
                            <div class="weight-700 font-24 text-dark"><?php echo number_format(getTotalPaymentsReceivedTodayForManager(), 2); ?></div>
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


        <div class="bg-white pd-20 card-box mb-30">
<div class="clearfix">
							<div class="pull-left">
								<h4 class="text-blue h4">Repayments Chart</h4>
							</div>
						</div>
                        <form>
    <div class="row">
        <div class="col-md-4 col-sm-12">
            <div class="form-group">
                <label>Select Chart You Want To View</label>
                <select id="loanSelector" class="selectpicker form-control" data-style="btn-outline-primary" data-size="5">
                    <optgroup label="Loan IDs" data-max-options="2">
                        <?php 
                        // Loop through the loanNumbers array to generate the options
                        foreach ($loanNumbers as $loan_id) {
                            echo "<option value='$loan_id'>$loan_id</option>";
                        }
                        ?>
                    </optgroup>
                </select>
            </div>
        </div>
    </div>
</form>
<div id="chart3"></div>
</div>


        <!-- Transactions Data Table Start -->
        <div class="card-box mb-30">
            <div class="pd-20">
                <h4 class="text-blue h4">Transactions Data Table</h4>
            </div>
            <div class="pb-20">
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
        <!-- Transactions Data Table End -->

        <?php require_once("footer.php"); // Include the footer ?>
    </div>
</div>


    <!-- Include JS files -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="src/plugins/apexcharts/apexcharts.min.js"></script>
	<script src="vendors/scripts/apexcharts-setting.js"></script>
    <script src="vendors/scripts/core.js"></script>
    <script src="vendors/scripts/script.min.js"></script>
    <script src="vendors/scripts/process.js"></script>
    <script src="vendors/scripts/layout-settings.js"></script>
    <script src="src/plugins/jQuery-Knob-master/jquery.knob.min.js"></script>
    <script src="vendors/scripts/dashboard2.js"></script>
    <script src="src/plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script src="src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
    <script src="src/plugins/datatables/js/dataTables.responsive.min.js"></script>
    <script src="src/plugins/datatables/js/responsive.bootstrap4.min.js"></script>
    <!-- Datatable Setting js -->
    <script src="vendors/scripts/datatable-setting.js"></script>
</body>
</html>