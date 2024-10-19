<?php
// Include database connection and utility functions
require_once("config.php");
include_once "functions-tena.php";

// Start session and check user authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'manager') {
    // If the user is not logged in or not a manager, redirect to the login page
    header("Location: login.php");
    exit();
}

// Retrieve logged-in manager's email from session
$email = $_SESSION['email'];
$manager_data = getManagerData($email);
$managerTotalLoanData = getManagerTotalLoanData();


// Fetch performance data using the existing function
if ($manager_data) {
    $profile_found = true;
}


?>




<!DOCTYPE html>
<html>
<head>
    <?php require_once("head.php"); // Include head section (CSS, meta tags, etc.) ?>
</head>
<body>
    <?php require_once("header.php"); // Include header section ?>
    <?php require_once("right-sidebar.php"); // Include right sidebar ?>

    <?php
    // Include the appropriate sidebar based on the user role
    if ($_SESSION['user_role'] == 'admin') {
        include('left-sidebar-admin.php');
    } elseif ($_SESSION['user_role'] == 'manager') {
        include('left-sidebar-manager.php');
    } else {
        header("Location: login.php");
        exit();
    }
    ?>

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
                                    <li class="breadcrumb-item active" aria-current="page">Officer Profile</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-8 col-sm-12 mb-30">
                        <div class="pd-20 card-box height-100-p">
                            <?php if ($profile_found): ?>
                                <h5 class="text-center h5 mb-0"><?php echo htmlspecialchars($manager_data['first_name']) . ' ' . htmlspecialchars($manager_data['last_name']); ?></h5>
                                <p class="text-center text-muted font-14">
                                    Here are your details. If any details change please contact your employer.
                                </p>
                                <div class="profile-info-container" style="display: flex; justify-content: space-between; gap: 20px;">
                                    <!-- Performance Information -->
                                    <div class="profile-info" style="width: 30%">
                                    <h5 class="mb-20 h5 text-blue">Performance Information</h5>
                                    <ul>
                                        <li><span>Total Loans Disbursed:</span> <?php echo htmlspecialchars($managerTotalLoanData['total_loans'] ?? 0); ?></li>
                                        <li><span>Total No. of Clients:</span> <?php echo htmlspecialchars($managerTotalLoanData['total_clients'] ?? 0); ?></li>
                                        <li><span>Total Amount Disbursed:</span> <?php echo htmlspecialchars($managerTotalLoanData['total_amount_disbursed'] ?? 0); ?></li>
                                        <li><span>Total Interest Earned:</span> <?php echo htmlspecialchars($managerTotalLoanData['total_earned_interest'] ?? 0); ?></li>
                                        <li><span>Total Payments Received:</span> <?php echo htmlspecialchars($managerTotalLoanData['total_payments_received'] ?? 0); ?></li>
                                    </ul>
                                    </div>
                                    <!-- Contact Information -->
                                    <div class="profile-info" style="width: 30%">
                                        <h5 class="mb-20 h5 text-blue">Contact Information</h5>
                                        <ul>
                                            <li><span>Email Address:</span> <?php echo htmlspecialchars($manager_data['email']); ?></li>
                                            <li><span>Phone Number:</span> <?php echo htmlspecialchars($manager_data['phone_number']); ?></li>
                                            <li><span>County:</span> <?php echo htmlspecialchars($manager_data['county']); ?></li>
                                            <li><span>Place of Residence:</span> <?php echo nl2br(htmlspecialchars($manager_data['residence_nearest_building'] . ', ' . $manager_data['residence_nearest_road'])); ?></li>
                                        </ul>
                                    </div>
                                    <!-- KYC Information -->
                                    <div class="profile-info" style="width: 30%">
                                        <h5 class="mb-20 h5 text-blue">KYC Information</h5>
                                        <ul>
                                            <li><span>On-Boarding Date:</span> <?php echo htmlspecialchars($manager_data['date_of_onboarding']); ?></li>
                                            <li><span>Identification Number:</span> <?php echo htmlspecialchars($manager_data['national_id']); ?></li>
                                            <li><span>KRA PIN:</span> <?php echo htmlspecialchars($manager_data['kra_pin']); ?></li>
                                            <li><span>NSSF Number:</span> <?php echo htmlspecialchars($manager_data['nssf']); ?></li>
                                            <li><span>NHIF Number:</span> <?php echo htmlspecialchars($manager_data['nhif']); ?></li>
                                            <li><span>Next of Kin Number:</span> <?php echo htmlspecialchars($manager_data['next_of_kin_phone_number']); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning" role="alert">
                                    Profile not found.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript files -->
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
    <script src="src/plugins/datatables/js/dataTables.buttons.min.js"></script>
    <script src="src/plugins/datatables/js/buttons.bootstrap4.min.js"></script>
    <script src="src/plugins/datatables/js/buttons.print.min.js"></script>
    <script src="src/plugins/datatables/js/buttons.html5.min.js"></script>
    <script src="src/plugins/datatables/js/buttons.flash.min.js"></script>
    <script src="src/plugins/datatables/js/pdfmake.min.js"></script>
    <script src="src/plugins/datatables/js/vfs_fonts.js"></script>
    <script src="vendors/scripts/datatable-setting.js"></script>
</body>
</html>