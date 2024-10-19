<?php
// Include database connection file
require_once("config.php");
include_once "functions-tena.php";

// Check if the user is logged in as admin or manager
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'manager')) {
    header("Location: login.php");
    exit();
}

// Store the user role in a variable
$user_role = $_SESSION['user_role'];

// Include the appropriate sidebar based on the user role
$sidebar_file = '';
if ($user_role == 'admin') {
    $sidebar_file = 'left-sidebar-admin.php';
} elseif ($user_role == 'manager') {
    $sidebar_file = 'left-sidebar-manager.php';
} elseif ($user_role == 'client') {
    $sidebar_file = 'left-sidebar-client.php';
} else {
    header("Location: login.php");
    exit();
}

// Fetch loan data
$query = "SELECT * FROM loan_info ORDER BY created_at DESC";
$result = $conn->query($query);
$loans_data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $loan_id = $row['loan_id'];
        $officer_email = $row['onboarding_officer'];
        $officer_name = 'ADMIN';

        // Fetch manager's name if the officer is not 'admin'
        if ($officer_email !== 'admin') {
            $manager_query = "SELECT first_name, last_name FROM managers WHERE email = '$officer_email'";
            $manager_result = $conn->query($manager_query);
            if ($manager_result->num_rows > 0) {
                $manager = $manager_result->fetch_assoc();
                $officer_name = $manager['first_name'] . ' ' . $manager['last_name'];
            }
        }

        // Get loan details
        $loanDetails = getLoanDetails($loan_id);
        $loans_data[] = [
            'officer_name' => $officer_name,
            'loan_id' => $loan_id,
            'loan_principal' => $loanDetails['loanPrincipal'],
            'loan_balance' => $loanDetails['loanBalance'],
            'payments' => $loanDetails['payments']
        ];
    }
}
?>

<!DOCTYPE html>
<html>
<?php require_once("head.php"); ?>
    <body>
        <?php require_once("header.php"); ?>
        <?php require_once("right-sidebar.php"); ?>
        <?php include($sidebar_file); ?>

        <div class="mobile-menu-overlay"></div>

        <div class="main-container">
            <div class="xs-pd-20-10 pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="title">
                                    <h4>Loan Reports</h4>
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Loan Reports</li>
                                    </ol>
                                </nav>
                            </div>
                            <div class="col-md-6 col-sm-12 text-right">
                                <div>
                                    <a class="btn btn-primary dropdown-toggle" href="#" role="button" data-toggle="dropdown">All Loans</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Export Datatable start -->
                    <div class="card-box mb-30">
                        <div class="pd-20">
                            <h4 class="text-blue h4">Officer Loan Reports</h4>
                        </div>
                        <div class="pb-20">
                            <table class="table hover multiple-select-row data-table-export nowrap">
                                <thead>
                                    <tr>
                                        <th class="table-plus datatable-nosort">Officer Name</th>
                                        <th>Loan Number</th>
                                        <th>Loan Principal</th>
                                        <th>Loan Balance</th>
                                        <th>Received Payments</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($loans_data) > 0): ?>
                                        <?php foreach ($loans_data as $loan): ?>
                                            <tr>
                                                <td class="table-plus"><?= htmlspecialchars($loan['officer_name']) ?></td>
                                                <td><?= htmlspecialchars($loan['loan_id']) ?></td>
                                                <td><?= number_format($loan['loan_principal'], 2) ?></td>
                                                <td><?= number_format($loan['loan_balance'], 2) ?></td>
                                                <td><?= number_format($loan['payments'], 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5">No loans found</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Export Datatable End -->

                </div>
            </div>
        </div>
        
        <?php require_once("footer.php"); ?>
        
        <!-- JS -->
        <script src="vendors/scripts/core.js"></script>
        <script src="vendors/scripts/script.min.js"></script>
        <script src="vendors/scripts/process.js"></script>
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
