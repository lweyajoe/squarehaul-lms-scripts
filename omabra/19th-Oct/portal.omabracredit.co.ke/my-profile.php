<?php
// Include database connection file
require_once("config.php");
include_once "functions-tena.php";

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in. Page can only be viewed when logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user_role'];
$user_email = $_SESSION['email'];

$clientId = getClientId();
$clientData = getClientData($clientId);
$totalClientLoanData = getTotalClientLoanData($clientId);

$profile_found = $clientData ? true : false;

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
                                    <li class="breadcrumb-item">
                                        <a href="index.html">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        Customer Profile
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <?php if ($profile_found): ?>

                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-8 col-sm-12 mb-30">
                        <div class="pd-20 card-box height-100-p">
                            <h5 class="text-center h5 mb-0"><?php echo htmlspecialchars($clientData['first_name']) . ' ' . htmlspecialchars($clientData['last_name']); ?></h5>
                            <p class="text-center text-muted font-14">
                                Here are your details. If any details change please contact your loan officer or <a href="edit-my-profile.php" >edit here</a>.
                            </p>
                            <div class="profile-info-container">
                                <div class="profile-info">
                                    <h5 class="mb-20 h5 text-blue">Loan Information</h5>
                                    <ul>
                                        <li>
                                            <span>Total Loan Principle:</span>
                                            <?php echo htmlspecialchars($totalClientLoanData['total_principle']); ?>
                                        </li>
                                        <li>
                                            <span>Total Payments:</span>
                                            <?php echo htmlspecialchars($totalClientLoanData['total_payments']); ?>
                                        </li>
                                        <li>
                                            <span>Total Loan Balance:</span>
                                            <?php echo htmlspecialchars($totalClientLoanData['loan_balance']); ?>
                                        </li>
                                    </ul>
                                </div>
                                <div class="profile-info">
                                    <h5 class="mb-20 h5 text-blue">Contact Information</h5>
                                    <ul>
                                        <li>
                                            <span>Email Address:</span>
                                            <?php echo htmlspecialchars($clientData['email']); ?>
                                        </li>
                                        <li>
                                            <span>Phone Number:</span>
                                            <?php echo htmlspecialchars($clientData['phone_number']); ?>
                                        </li>
                                        <li>
                                            <span>County:</span>
                                            <?php echo htmlspecialchars($clientData['county']); ?>
                                        </li>
                                        <li>
                                            <span>Place of Residence:</span>
                                            <?php echo nl2br(htmlspecialchars($clientData['residence_nearest_building'])); ?>
                                        </li>
                                    </ul>
                                </div>
                                <div class="profile-info">
                                    <h5 class="mb-20 h5 text-blue">KYC Information</h5>
                                    <ul>
                                        <li>
                                            <span>On-Boarding Date:</span>
                                            <?php echo htmlspecialchars($clientData['date_of_onboarding']); ?>
                                        </li>
                                        <li>
                                            <span>Identification Number:</span>
                                            <?php echo htmlspecialchars($clientData['national_id']); ?>
                                        </li>
                                        <li>
                                            <span>Next of Kin Name:</span>
                                            <?php echo htmlspecialchars($clientData['next_of_kin_name']); ?>
                                        </li>
                                        <li>
                                            <span>Next of Kin Number:</span>
                                            <?php echo htmlspecialchars($clientData['next_of_kin_phone_number']); ?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="alert alert-warning" role="alert">
                            No profile data found.
                        </div>
                    </div>
                </div>
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
