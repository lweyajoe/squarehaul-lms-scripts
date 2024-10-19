<?php
// Include database connection file
require_once("config.php");
//include_once "functions.php";

// Check if the user is logged in as admin or manager
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'manager')) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user_role'];

function calculateEMI($principal, $interest_rate, $duration, $interest_rate_period, $duration_period) {
    if ($interest_rate_period === 'Yearly') {
        $interest = $principal * ($interest_rate / 100);
    } elseif ($interest_rate_period === 'Monthly') {
        $interest = $principal * ($interest_rate / 100);
    } elseif ($interest_rate_period === 'Weekly') {
        $interest = $principal * ($interest_rate / 100);
    }

    if ($duration_period === 'Year') {
        $emi = ($principal / $duration) + $interest;
    } elseif ($duration_period === 'Month') {
        $emi = ($principal / $duration) + $interest;
    } elseif ($duration_period === 'Week') {
        $emi = ($principal / $duration) + $interest;
    }

    return $emi;
}

$sql = "
    SELECT
        loan_id,
        requested_amount AS principal,
        interest_rate,
        duration,
        duration_period,
        interest_rate_period,
        created_at AS activation_date
    FROM
        loan_info
    WHERE
        loan_status = 'Active';
";

$result = $conn->query($sql);
$loans = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $loan_id = $row['loan_id'];
        $emi = calculateEMI($row['principal'], $row['interest_rate'], $row['duration'], $row['interest_rate_period'], $row['duration_period']);
        
        // Fetch all payments made for this loan (including payment dates and amounts)
        $payments_sql = "SELECT amount, payment_date FROM payments WHERE loan_id = '$loan_id' ORDER BY payment_date ASC";
        $payments_result = $conn->query($payments_sql);
        $payments = [];
        while ($payment_row = $payments_result->fetch_assoc()) {
            $payments[] = [
                'amount' => $payment_row['amount'],
                'date' => new DateTime($payment_row['payment_date'])
            ];
        }

        // Calculate the number of periods based on duration and duration_period
        $num_periods = $row['duration'];
        
        // Calculate the total EMIs
        $total_emis = $emi * $num_periods;

        // Calculate EMIs due to date
        $activation_date = new DateTime($row['activation_date']);
        $now = new DateTime();
        $interval = $now->diff($activation_date);
        $periods_due = 0;

        if ($row['duration_period'] === 'Year') {
            $periods_due = $interval->y;
        } elseif ($row['duration_period'] === 'Month') {
            $periods_due = $interval->m + ($interval->y * 12);
        } elseif ($row['duration_period'] === 'Week') {
            $periods_due = floor($interval->days / 7);
        }

        // Generate an array of all expected payment due dates
        $due_dates = [];
        $due_date = clone $activation_date;
        $interval_spec = match($row['duration_period']) {
            'Year' => new DateInterval('P1Y'),
            'Month' => new DateInterval('P1M'),
            'Week' => new DateInterval('P1W'),
        };
        for ($i = 0; $i < $num_periods; $i++) {
            $due_dates[] = clone $due_date;
            $due_date->add($interval_spec);
        }

        // Initialize the due amount logic
        $nextDueAmount = $emi;
        $outstanding_balance = 0; // Tracks unpaid amounts
        $overpayment = 0; // Tracks overpayments

        // Go through each due date and payments to adjust for overpayments/underpayments
        foreach ($due_dates as $index => $due_date) {
            if ($due_date > $now) {
                // Stop processing if we've reached a future due date
                break;
            }

            // Sum up payments made before this due date
            $paid_towards_this_due = 0;
            foreach ($payments as $payment) {
                if ($payment['date'] <= $due_date) {
                    $paid_towards_this_due += $payment['amount'];
                }
            }

            // Handle underpayment or overpayment logic
            if ($paid_towards_this_due >= ($emi + $outstanding_balance)) {
                // Overpayment: calculate excess to apply to future installments
                $overpayment = $paid_towards_this_due - ($emi + $outstanding_balance);
                $outstanding_balance = 0;
            } else {
                // Underpayment: accumulate the unpaid balance
                $outstanding_balance = ($emi + $outstanding_balance) - $paid_towards_this_due;
                $overpayment = 0;
            }
        }

        // Calculate the next due date and amount
        foreach ($due_dates as $due_date) {
            if ($due_date > $now) {
                // If overpayment exists, skip the fully paid installments
                if ($overpayment >= $emi) {
                    // Deduct overpayment for this installment and move to the next
                    $overpayment -= $emi;
                    continue; // Move to the next due date
                }

                // Set the next due date and amount
                $nextDueDate = $due_date;
                $nextDueAmount = $emi + $outstanding_balance - $overpayment;
                
                // If the next due amount is less than zero, set it to zero (overpayment handling)
                if ($nextDueAmount < 0) {
                    $nextDueAmount = 0;
                }

                break;
            }
        }

        // Add the loan info to the array
        $loans[] = [
            'loan_id' => $loan_id,
            'emi_per_period' => $emi,
            'num_periods' => $num_periods,
            'total_emis' => $total_emis,
            'total_payments' => array_sum(array_column($payments, 'amount')), // Total payments made
            'balance' => $total_emis - array_sum(array_column($payments, 'amount')), // Balance remaining
            'activation_date' => $row['activation_date'],
            'next_due_date' => isset($nextDueDate) ? $nextDueDate->format('Y-m-d') : null,
            'next_due_amount' => $nextDueAmount
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
            <div class="col-md-6 col-sm-12">
                <div class="title">
                    <h4>DataTable</h4>
                </div>
                <nav aria-label="breadcrumb" role="navigation">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="index.html">Home</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            DataTable
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 col-sm-12 text-right">
                <div class="dropdown">
                    <a class="btn btn-primary dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                        January 2018
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#">Export List</a>
                        <a class="dropdown-item" href="#">Policies</a>
                        <a class="dropdown-item" href="#">View Assets</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="pd-20">
        <h4 class="text-blue h4">Loan Data Summary</h4>
    </div>

<!-- Simple 4-Column Table Start -->
<?php foreach ($loans as $loan): ?>
<div class="card-box mb-30">
    <div class="pb-20">
        <table class="data-table table stripe hover profile-info-container-columned">
            <tbody class="profile-info-columned">
                <tr class="info-row">
                    <th>Loan ID</th>
                    <td><?php echo htmlspecialchars($loan['loan_id']); ?></td>
                    <th>EI per period</th>
                    <td><?php echo number_format($loan['emi_per_period'], 2); ?></td>
                </tr>
                <tr class="info-row">
                    <th>No. of periods</th>
                    <td><?php echo htmlspecialchars($loan['num_periods']); ?></td>
                    <th>Total EIs</th>
                    <td><?php echo number_format($loan['total_emis'], 2); ?></td>
                </tr>
                <tr class="info-row">
                    <th>EIs due to date</th>
                    <td><?php echo number_format($loan['emis_due_to_date'], 2); ?></td>
                    <th>Total Payments</th>
                    <td><?php echo number_format($loan['total_payments'], 2); ?></td>
                </tr>
                <tr class="info-row">
                    <th>Balance</th>
                    <td><?php echo number_format($loan['balance'], 2); ?></td>
                    <th>Activation Date</th>
                    <td><?php echo htmlspecialchars($loan['activation_date']); ?></td>
                </tr>
                <tr class="info-row">
                    <th>Next Due Date</th>
                    <td><?php echo htmlspecialchars($loan['next_due_date']); ?></td>
                    <th>Next Due Amount</th>
                    <td><?php echo number_format($loan['next_due_amount'], 2); ?></td>
                </tr>
                <tr>
                    <!-- Empty row for spacing -->
                    <td colspan="4" style="border: none;"></td>
                </tr>
                <tr>
                    <!-- Button row spanning columns 2 to 3 -->
                    <td colspan="2" style="border: none;">
                        <button type="button" class="btn-columned btn-primary w-100 center">
                            Pay Next Due Amount Now
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php endforeach; ?>
<!-- Simple 4-Column Table End -->


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
