<?php
// Include database connection and utility functions
require_once("config.php");
include_once "functions-tena.php";

// Define constants
define('DEFAULT_EMAIL', 'default_email@example.com');

// Start session and check user authentication
session_start(); // Make sure session is started
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['admin', 'manager'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user_role'];
$profile_found = false;
$manager_data = [];
$performance_data = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $manager_id = trim($_POST['manager_id']);

    if (!empty($manager_id)) {
        // Fetch data from the managers table
        $manager_query = $conn->prepare("SELECT * FROM managers WHERE manager_id = ?");
        $manager_query->bind_param("s", $manager_id);
        
        if (!$manager_query->execute()) {
            die("Query failed: " . $manager_query->error);
        }
        
        $manager_result = $manager_query->get_result();
        $manager_data = $manager_result->fetch_assoc();
        
        // Function to fetch manager's email
        function getCustomOnboardingOfficerEmail($manager_id) {
            global $conn;
            
            // Prepare and execute the query to get the manager's email based on the manager_id
            $stmt = $conn->prepare("SELECT email FROM managers WHERE manager_id = ?");
            $stmt->bind_param("s", $manager_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Fetch the result
            $manager_data_enough = $result->fetch_assoc();
            
            // If email is found, return it, otherwise return a default email
            return $manager_data_enough['email'] ?? DEFAULT_EMAIL;
        }

        // Store original session values
        $original_email = $_SESSION['email'];
        $original_role = $_SESSION['user_role'];

        // Temporarily override session values
        $_SESSION['user_role'] = 'manager';
        $_SESSION['email'] = getCustomOnboardingOfficerEmail($manager_id);

        // Fetch performance data using the existing function
        $performance_data = getManagerTotalLoanData();

        // Restore original session values
        $_SESSION['user_role'] = $original_role;
        $_SESSION['email'] = $original_email;

        if ($manager_data && $performance_data) {
            $profile_found = true;
        }
    }
}
?>



<!DOCTYPE html>
<html>
<head>
    <?php require_once("head.php"); ?>
</head>
<body>
    <?php require_once("header.php"); ?>
    <?php require_once("right-sidebar.php"); ?>

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

                <div class="search-icon-box card-box mb-30">
                <form method="post" action="">
                    <input
                        type="text"
                        class="border-radius-10"
                        name="manager_id"
                        id="filter_input"
                        placeholder="Search Manager Profile: Enter Manager ID..."
                        title="Type in THE MANAGER'S ID"
                        required
                    />
                    <i class="search_icon dw dw-search" onclick="this.closest('form').submit();"></i>
                </form>
            </div>

                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-8 col-sm-12 mb-30">
                        <div class="pd-20 card-box height-100-p">
                            <?php if ($profile_found): ?>
                                <h5 class="text-center h5 mb-0"><?php echo htmlspecialchars($manager_data['first_name']) . ' ' . htmlspecialchars($manager_data['last_name']); ?></h5>
                                <p class="text-center text-muted font-14">
                                    Here are your details. If any details change please contact your employer.
                                </p>
                                <div class="profile-info-container">
                                    <div class="profile-info">
                                        <h5 class="mb-20 h5 text-blue">Performance Information</h5>
                                        <ul>
                                            <li><span>Total Loans Disbursed:</span> <?php echo htmlspecialchars($performance_data['total_loans']); ?></li>
                                            <li><span>Total Amount Disbursed:</span> <?php echo htmlspecialchars($performance_data['total_amount_disbursed']); ?></li>
                                            <li><span>Total Interest Earned:</span> <?php echo htmlspecialchars($performance_data['total_earned_interest']); ?></li>
                                            <li><span>Total Payments Received:</span> <?php echo htmlspecialchars($performance_data['total_payments']); ?></li>
                                        </ul>
                                    </div>
                                    <div class="profile-info">
                                        <h5 class="mb-20 h5 text-blue">Contact Information</h5>
                                        <ul>
                                            <li><span>Email Address:</span> <?php echo htmlspecialchars($manager_data['email']); ?></li>
                                            <li><span>Phone Number:</span> <?php echo htmlspecialchars($manager_data['phone_number']); ?></li>
                                            <li><span>County:</span> <?php echo htmlspecialchars($manager_data['county']); ?></li>
                                            <li><span>Place of Residence:</span> <?php echo nl2br(htmlspecialchars($manager_data['residence_nearest_building'] . ', ' . $manager_data['residence_nearest_road'])); ?></li>
                                        </ul>
                                    </div>
                                    <div class="profile-info">
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
