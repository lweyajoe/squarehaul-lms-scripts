<?php
require_once 'config.php';
include_once 'functions.php';

// Start session if not already started (handled by config.php)
// session_start() not needed here if included in config.php

// Check if the user is logged in. Page can only be viewed when logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user role and email from session
$user_role = $_SESSION['user_role'] ?? null;
$user_email = $_SESSION['email'] ?? null;

if (!$user_role || !$user_email) {
    header("Location: login.php");
    exit();
}

// Fetch national ID for clients
$national_id = null;
if ($user_role == 'client') {
    $national_id = getClientNationalId($user_email, $conn);
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

    <div class="main-container">
        <div class="xs-pd-20-10 pd-ltr-20">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="title">
                                <h4>DataTable</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Cleared Loans DataTable</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <!-- Simple Datatable start -->
                <div class="card-box mb-30">
                    <div class="pd-20">
                        <h4 class="text-blue h4">Cleared Loans</h4>
                    </div>
                    <div class="pb-20">
                    <div class="table-responsive">
                        <table class="data-table table stripe hover nowrap">
                            <thead>
                                <tr>
                                    <th class="table-plus datatable-nosort">Loan ID</th>
                                    <th>Name</th>
                                    <th>N. ID</th>
                                    <th>Officer</th>
                                    <th>Loan Amount</th>
                                    <th>Security</th>
                                    <th>Payments</th>
                                    <th>Profit</th>
                                    <th>Loan Start</th>
                                    <th>Loan Cleared</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Construct SQL query based on user role
                                if ($user_role == 'admin') {
                                    $sql = "SELECT 
                                                a.loan_id, 
                                                c.first_name, 
                                                a.national_id, 
                                                a.onboarding_officer, 
                                                a.requested_amount, 
                                                a.collateral_value, 
                                                IFNULL(SUM(p.amount), 0) as total_payments,
                                                (IFNULL(SUM(p.amount), 0) - a.requested_amount) as surplus_balance,
                                                a.created_at,
                                                a.status_change
                                            FROM 
                                                loan_info a
                                            JOIN 
                                                clients c ON a.client_id = c.client_id
                                            LEFT JOIN 
                                                payments p ON a.loan_id = p.loan_id
                                            WHERE 
                                                a.loan_status = 'Cleared'
                                            GROUP BY 
                                                a.loan_id, c.first_name, a.national_id, a.onboarding_officer, 
                                                a.requested_amount, a.collateral_value, a.created_at, a.status_change";
                                } elseif ($user_role == 'client' && $national_id) {
                                    $sql = "SELECT 
                                                a.loan_id, 
                                                c.first_name, 
                                                a.national_id, 
                                                a.onboarding_officer, 
                                                a.requested_amount, 
                                                a.collateral_value, 
                                                IFNULL(SUM(p.amount), 0) as total_payments,
                                                (IFNULL(SUM(p.amount), 0) - a.requested_amount) as surplus_balance,
                                                a.created_at,
                                                a.status_change
                                            FROM 
                                                loan_info a
                                            JOIN 
                                                clients c ON a.client_id = c.client_id
                                            LEFT JOIN 
                                                payments p ON a.loan_id = p.loan_id
                                            WHERE 
                                                a.loan_status = 'Cleared' AND a.national_id = ?
                                            GROUP BY 
                                                a.loan_id, c.first_name, a.national_id, a.onboarding_officer, 
                                                a.requested_amount, a.collateral_value, a.created_at, a.status_change";
                                } elseif ($user_role == 'manager') {
                                    $sql = "SELECT 
                                                a.loan_id, 
                                                c.first_name, 
                                                a.national_id, 
                                                a.onboarding_officer, 
                                                a.requested_amount, 
                                                a.collateral_value, 
                                                IFNULL(SUM(p.amount), 0) as total_payments,
                                                (IFNULL(SUM(p.amount), 0) - a.requested_amount) as surplus_balance,
                                                a.created_at,
                                                a.status_change
                                            FROM 
                                                loan_info a
                                            JOIN 
                                                clients c ON a.client_id = c.client_id
                                            LEFT JOIN 
                                                payments p ON a.loan_id = p.loan_id
                                            WHERE 
                                                a.loan_status = 'Cleared' AND a.onboarding_officer = ?
                                            GROUP BY 
                                                a.loan_id, c.first_name, a.national_id, a.onboarding_officer, 
                                                a.requested_amount, a.collateral_value, a.created_at, a.status_change";
                                }

                                $stmt = $conn->prepare($sql);
                                if ($user_role == 'client' && $national_id) {
                                    $stmt->bind_param("s", $national_id);
                                } elseif ($user_role == 'manager') {
                                    $stmt->bind_param("s", $user_email);
                                }
                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td class='table-plus'>" . $row['loan_id'] . "</td>";
                                        echo "<td>" . $row['first_name'] . "</td>";
                                        echo "<td>" . $row['national_id'] . "</td>";
                                        echo "<td>" . $row['onboarding_officer'] . "</td>";
                                        echo "<td>" . number_format($row['requested_amount'], 2) . "</td>";
                                        echo "<td>" . number_format($row['collateral_value'], 2) . "</td>";
                                        echo "<td>" . number_format($row['total_payments'], 2) . "</td>";
                                        echo "<td>" . number_format($row['surplus_balance'], 2) . "</td>";
                                        echo "<td>" . $row['created_at'] . "</td>";
                                        echo "<td>" . $row['status_change'] . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='8'>No cleared loans found</td></tr>";
                                }
                                $stmt->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>
                <!-- Simple Datatable End -->
            </div>
        </div>
    </div>

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

<?php
$conn->close();
?>
