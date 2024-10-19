<?php
require_once("config.php");


// Before the code block - remove in production
//$startMemory = memory_get_usage();
//$startPeakMemory = memory_get_peak_usage();

include 'functions-tena.php';

// Check if the user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'client') {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user_role'];
$clientEmail = $_SESSION['email'];
$client_id = getClientId(); 

// Fetch client data
$sqlClient = "SELECT * FROM clients WHERE email = ?";
$stmtClient = $conn->prepare($sqlClient);
$stmtClient->bind_param("s", $clientEmail);
$stmtClient->execute();
$clientData = $stmtClient->get_result()->fetch_assoc();

//if (!$clientData) {
    // Handle case where no client data is found
    //echo "Client data not found.";
    //exit();
//}

// Fetch all loan numbers and their statuses for the logged-in client
$query = "SELECT loan_id, loan_status FROM loan_info WHERE client_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $client_id);
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

// Assuming you've already fetched the loan IDs and stored them in $activeLoanIds
$loanSummaries = [];

foreach ($activeLoanIds as $loan_id) {
    $loanBalance = calculateLoanBalance($loan_id);
    $loanInstallments = calculateInstallments($loan_id);
    $loanAccruedInterest = getAllAccruedInterest($loan_id);
    $loanNextPaymentDate = getNextPaymentDate($loan_id);
    $loanNextPaymentAmount = getNextPaymentAmount($loan_id);
    $loanPeriodsPassed = getPeriodsPassed($loan_id);
    $loanPeriodsRemaining = getPeriodsRemaining($loan_id);
    $loanClearAmountToday = calculateClearTodayAmount($loan_id);
    $loanAllPayments = getAllPayments($loan_id);
    
    $loanSummaries[] = [
        'loan_id' => $loan_id,
        'loan_balance' => $loanBalance,
        'loan_installments' => $loanInstallments,
        'loan_accrued_interest' => $loanAccruedInterest,
        'next_payment_date' => $loanNextPaymentDate,
        'next_payment_amount' => $loanNextPaymentAmount,
        'periods_passed' => $loanPeriodsPassed,
        'periods_remaining' => $loanPeriodsRemaining,
        'clear_today_amount' => $loanClearAmountToday,
        'total_payments' => $loanAllPayments,
    ];
}

$paymentsData = [];

foreach ($loanNumbers as $loan_id) {
    $sqlPayments = "SELECT * FROM payments WHERE loan_id = ? ORDER BY payment_date DESC LIMIT 5";
    $stmtPayments = $conn->prepare($sqlPayments);
    $stmtPayments->bind_param("s", $loan_id);
    $stmtPayments->execute();
    $result = $stmtPayments->get_result();
    
    while ($payment = $result->fetch_assoc()) {
        $paymentsData[] = $payment; // Collect all payments for this loan
    }
}

// After the code block
//$currentMemory = memory_get_usage();
//$currentPeakMemory = memory_get_peak_usage();

//echo "Memory used by this block: " . ($currentMemory - $startMemory) . " bytes\n";
//echo "Peak memory usage during this block: " . ($currentPeakMemory - $startPeakMemory) . " bytes\n";

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
                <h2 class="h3 mb-0">Client Dashboard</h2>
            </div>
            <div class="row clearfix">

            <div class="col-md-6 col-sm-12 mb-30">
							<div class="pd-20 card-box height-100-p">
								<h4 class="mb-15 text-blue h4">My Profile Management</h4>
								<p>
									Decide how you interact with us here.
								</p>
								<div class="btn-list">
                                <a href="my-profile.php">
									<button
										type="button"
										class="btn btn-success btn-lg btn-block"
									>
										My Profile
									</button>
                                </a>
                                <a href="edit-my-profile.php">
									<button
										type="button"
										class="btn btn-secondary btn-lg btn-block"
									>
										Edit My Profile
									</button>
                                </a>
								</div>
							</div>
						</div>


            <div class="col-md-6 col-sm-12 mb-30">
							<div class="pd-20 card-box height-100-p">
								<h4 class="mb-15 text-blue h4">Loan Management Module</h4>
								<p>
                                Access your loan summaries here.
								</p>
                                <div class="btn-list">
                                <a href="search-loan-id.php">
									<button
										type="button"
										class="btn btn-success btn-lg btn-block"
									>
                                    Search loan
									</button>
                                </a>
                                <a href="pay-now.php">
                                    <button
										type="button"
										class="btn btn-secondary btn-lg btn-block"
									>
										Make A Payment
									</button>
                                </a>
								</div>

							</div>
						</div>
    </div>

            <!-- Personal Information -->
             <!-- HTML Content -->
             <div class="bg-white pd-20 card-box mb-30">
<div class="clearfix">
							<div class="pull-left">
								<h4 class="text-blue h4">Loan Summaries: These are your active loans</h4>
							</div>
						</div></div>

                        <?php if (empty($loanSummaries)): ?>
    <!-- Display the message when no loans are available -->
    <div class="alert alert-success" role="alert" style="color: green;">
        No loan profiles available for this client.
    </div>
<?php else: ?>

<?php foreach ($loanSummaries as $summary): ?>
    <div class="row pb-20">
        <div class="col-md-4 mb-20">
            <div class="card-box height-100-p">
            <h4 class="text-center text-blue h5 mb-20">PAYMENT DETAILS</h4>
            <div class="profile-info text-center">
    <ul>

<li><span><strong>Amount:</strong></span></li>
<li>
    <h5 class="text-center"><?php echo number_format($summary['next_payment_amount'], 2); ?></h5> <!-- Default placeholder -->
</li>

<li><span><strong>MPESA NO:</strong></span></li>
<li>
    <h5 class="text-center" id="phoneNumberDisplay"><?php echo htmlspecialchars($clientData['phone_number'] ?? ''); ?></h5>
</li>

<!-- Send the selected data via AJAX -->
<li>
    <button 
        type="button" 
        class="btn btn-success btn-sm btn-block"
        onclick="triggerSTKPush('<?php echo $summary['loan_id']; ?>', '<?php echo htmlspecialchars($clientData['phone_number']); ?>')"
    >
        Make Payment via MPESA
    </button>
</li>
<li>
    <div id="mpesaPinPrompt-<?php echo htmlspecialchars($summary['loan_id']); ?>"></div> <!-- Unique ID for each loan summary -->
</li>

    </ul>
</div>
            </div>
        </div>

        <!-- Account Summary for each loan -->
        <div class="col-md-8 mb-20">
            <div class="card-box height-100-p">
                <h4 class="text-center text-blue h5 mb-20">ACCOUNT SUMMARY FOR LOAN NUMBER <?php echo htmlspecialchars($summary['loan_id']); ?></h4>
                <div class="profile-info-container" style="display: flex; justify-content: space-between; gap: 20px;">
                    <div class="profile-info" style="width: 50%">
                        <ul>
                            <li><span><strong>Loan Balance: </strong></span><?php echo number_format($summary['loan_balance'], 2); ?></li>
                            <li><span><strong>Loan Installments: </strong></span><?php echo number_format($summary['loan_installments'], 2); ?></li>
                            <li><span><strong>Accrued Interest: </strong></span><?php echo number_format($summary['loan_accrued_interest'], 2); ?></li>
                            <li><span><strong>Next Payment Date: </strong></span>
    <?php echo htmlspecialchars($summary['next_payment_date'] ?? 'N/A'); ?>
</li>
                            <li><span><strong>Next Payment Amount: </strong></span><?php echo number_format($summary['next_payment_amount'], 2); ?></li>
                        </ul>
                    </div>
                    <div class="profile-info" style="width: 50%">
                        <ul>
                            <li><span><strong>Loan Period Passed: </strong></span><?php echo htmlspecialchars($summary['periods_passed']); ?></li>
                            <li><span><strong>Loan Period Remaining: </strong></span><?php echo htmlspecialchars($summary['periods_remaining']); ?></li>
                            <li><span><strong>To Clear Loan Today, Pay: </strong></span><?php echo number_format($summary['clear_today_amount'], 2); ?></li>
                            <li><span><strong>Total Payments Made: </strong></span><?php echo number_format($summary['total_payments'], 2); ?></li>
                                </span>
                            </li>
                            <li><a href="printify.php?loan_id=<?php echo htmlspecialchars($summary['loan_id']); ?>">
									<button
										type="button"
										class="btn btn-success btn-sm btn-block"
									>
                                    View Statement
									</button>
                                </a></li>
                            </ul>
                    </div>
                    
                </div>
                
            </div>
        </div>
    </div>
<?php endforeach; ?>
<?php endif; ?>


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
            <!-- Recent Transactions -->
            <div class="row pb-20">
                <div class="col-xl-12 col-lg-12 col-md-8 col-sm-12 mb-30">
                    <div class="pd-20 card-box height-100-p">
                        <h4 class="text-blue h4">Recent Transactions</h4>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Reference</th>
                                    <th>Payment Mode</th>
                                </tr>
                            </thead>
                            <tbody>
    <?php foreach ($paymentsData as $payment): ?>
        <tr>
            <td><?php echo htmlspecialchars($payment['payment_date']); ?></td>
            <td><?php echo number_format($payment['amount'], 2); ?></td>
            <td><?php echo htmlspecialchars($payment['transaction_reference']); ?></td>
            <td><?php echo htmlspecialchars($payment['payment_mode']); ?></td>
        </tr>
    <?php endforeach; ?>
</tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Financial Tools -->
            <div class="row pb-20">
    <div class="col-xl-12 col-lg-12 col-md-8 col-sm-12 mb-30">
        <div class="pd-20 card-box height-100-p">
            <h4 class="text-blue h4">Loan Calculator</h4>
            <form id="loanCalculator">
                <div class="row">
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="principal">Principal Amount (Ksh)</label>
                            <input type="number" class="form-control" id="principal" required>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="interest">Simple Interest Rate (Monthly %)</label>
                            <input type="number" class="form-control" id="interest" step="0.01" required>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="duration">Loan Duration</label>
                            <input type="number" class="form-control" id="duration" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="period">Repayment Period</label>
                            <select class="custom-select" id="period">
                                <option value="weeks">Weeks</option>
                                <option value="months">Months</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="installments">Repayment Installments Are?: (Weekly/Monthly)</label>
                            <select class="custom-select" id="installments">
                                <option value="weeks">Weekly</option>
                                <option value="months">Monthly</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-sm-12 text-right">
                        <input class="btn btn-primary btn-lg btn-block" type="submit" value="Calculate">
                    </div>
                </div>
            </form>

            <div id="schedule" class="mt-4"></div>
        </div>
    </div>
</div>



        <?php require_once("footer.php"); ?>
        </div>
		</div>
        <!-- js -->
    <!-- JavaScript to handle dropdown change and update the amount -->


    <script>
let timeout;

function triggerSTKPush(loan_id, phone_number) {
    // Display loading message while waiting for the STK Push response
    const promptId = 'mpesaPinPrompt-' + loan_id; // Unique ID for the prompt
    document.getElementById(promptId).innerHTML = '<p>Please wait... Sending STK Push request.</p>';

    // Send the STK Push request via AJAX
    fetch('api/stk_push.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            loan_id: loan_id,
            phone_number: phone_number
        })
    })
    .then(response => response.text())
    .then(data => {
        console.log('Raw response:', data);
        try {
            const jsonData = JSON.parse(data);
            if (jsonData.status === 'success') {
                // If successful, prompt for MPESA PIN
                document.getElementById(promptId).innerHTML = '<p>Enter MPESA PIN on your phone to complete the payment.</p>';
                
                // Start timeout to check for inactivity
                startTimeout(promptId);
            } else {
                document.getElementById(promptId).innerHTML = '<p>Error: ' + jsonData.message + '</p>';
            }
        } catch (error) {
            console.error('Failed to parse JSON:', error);
            console.error('Response data:', data);
            document.getElementById(promptId).innerHTML = '<p>Failed to process payment. Please try again.</p>';
        }
    })
    .catch(error => {
        console.error('Error during STK push:', error);
        document.getElementById(promptId).innerHTML = '<p>There was an error processing your request.</p>';
    });
}

function startTimeout(promptId) {
    // Set a timeout for 30 seconds
    timeout = setTimeout(() => {
        // Notify user about the timeout
        document.getElementById(promptId).innerHTML = '<p>Payment timed out. Please try again.</p>';
    }, 30000); // 30000 milliseconds = 30 seconds
}

// Call this function if the user enters the PIN to clear the timeout
function clearCustomTimeout() {
    if (timeout) {
        clearTimeout(timeout);
    }
}
</script>


<!-- Loan Calculator -->
<script>
document.getElementById('loanCalculator').addEventListener('submit', function(e) {
    e.preventDefault();

    // Get user input
    const principal = parseFloat(document.getElementById('principal').value);
    const interestRate = parseFloat(document.getElementById('interest').value) / 100;
    const duration = parseInt(document.getElementById('duration').value);
    const period = document.getElementById('period').value;
    const installmentFrequency = document.getElementById('installments').value;

    // Calculate the number of installments
    const numInstallments = period === 'weeks' ? duration * (installmentFrequency === 'weeks' ? 1 : 4) : duration;

    // Calculate installment amount (simple interest formula)
    const totalInterest = principal * interestRate * duration;
    const totalAmount = principal + totalInterest;
    const installmentAmount = totalAmount / numInstallments;

    // Generate installment schedule
    let scheduleHtml = '<h5>Repayment Schedule</h5>';
    scheduleHtml += '<table class="table"><thead><tr><th>Installment</th><th>Date</th><th>Amount (Ksh)</th></tr></thead><tbody>';

    const startDate = new Date();
    for (let i = 1; i <= numInstallments; i++) {
        let installmentDate = new Date(startDate);
        if (installmentFrequency === 'weeks') {
            installmentDate.setDate(startDate.getDate() + 7 * i);
        } else {
            installmentDate.setMonth(startDate.getMonth() + i);
        }

        const dateFormatted = installmentDate.toISOString().split('T')[0]; // Format YYYY-MM-DD

        scheduleHtml += `<tr><td>${i}</td><td>${dateFormatted}</td><td>${installmentAmount.toFixed(2)}</td></tr>`;
    }

    scheduleHtml += '</tbody></table>';
    document.getElementById('schedule').innerHTML = scheduleHtml;
});
</script>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="src/plugins/apexcharts/apexcharts.min.js"></script>
		<script src="vendors/scripts/apexcharts-setting.js"></script>

		<script src="vendors/scripts/core.js"></script>
		<script src="vendors/scripts/script.min.js"></script>
		<script src="vendors/scripts/process.js"></script>
		<script src="vendors/scripts/layout-settings.js"></script>
		<script src="vendors/scripts/dashboard2.js"></script>
		<script src="src/plugins/datatables/js/jquery.dataTables.min.js"></script>
		<script src="src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
		<script src="src/plugins/datatables/js/dataTables.responsive.min.js"></script>
		<script src="src/plugins/datatables/js/responsive.bootstrap4.min.js"></script>
    </body>
</html>
