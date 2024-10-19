<?php
// Include database connection file
require_once("config.php");
include_once "functions.php";

// Start session and check user authentication

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['admin', 'manager'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user_role'];



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
                        <h4>Collateral Information</h4>
                    </div>
                    <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Collateral Search</li>
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
                    placeholder="Search Collateral by Loan ID..."
                    title="Type in the loan ID"
                    required
                />
                <i class="search_icon dw dw-search" onclick="this.closest('form').submit();"></i>
            </form>
        </div>

        <?php
        // Check if search form is submitted
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $loan_id = $_POST['loan_id'];

            // Query to fetch collateral information from loan_info table
            $query = "SELECT collateral_name, collateral_value, collateral_pic1, collateral_pic2 FROM loan_info WHERE loan_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $loan_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $collateral_info = $result->fetch_assoc();
                ?>

                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-8 col-sm-12 mb-30">
                        <div class="pd-20 card-box height-100-p">
                            <h5 class="text-center h5 mb-0">LOAN ID: <?php echo htmlspecialchars($loan_id); ?></h5>
                            <p class="text-center text-muted font-14">Here are the collateral details for this loan.</p>
                            <div class="profile-info-container">
                                <!-- Collateral Details -->
                                <div class="profile-info">
                                    <h5 class="mb-20 h5 text-blue">Collateral Information</h5>
                                    <ul>
                                        <li><span>Collateral Name:</span> <?php echo htmlspecialchars($collateral_info['collateral_name']); ?></li>
                                        <li><span>Collateral Value:</span> <?php echo htmlspecialchars($collateral_info['collateral_value']); ?></li>
                                        <li><span>Collateral Pic 1:</span> <a href="<?php echo htmlspecialchars($collateral_info['collateral_pic1']); ?>" target="_blank">View Picture 1</a></li>
                                        <li><span>Collateral Pic 2:</span> <a href="<?php echo htmlspecialchars($collateral_info['collateral_pic2']); ?>" target="_blank">View Picture 2</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
            } else {
                ?>
                <div class="alert alert-danger" role="alert">
                    No collateral found for the given Loan ID.
                </div>
                <?php
            }
        }
        ?>

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




