<?php
// Include database connection file
require_once("config.php");
include_once "functions-tena.php";

// Check if the user is logged in. Page can only be viewed when logged in.
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'manager')) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user_role'];
$onboardingOfficer = getOnboardingOfficer();

// Fetch all loan numbers and their statuses for the logged-in client
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

// Assuming you've already fetched the loan IDs and stored them in $activeLoanIds
$loanSummaries = [];

foreach ($activeLoanIds as $loan_id) {
    $phoneNumber = getPhoneNumber($loan_id);
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



// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $loanIdPost = htmlspecialchars($_POST["loan_id"]);
    $nationalId = htmlspecialchars($_POST["national_id"]);
    $paymentMode = htmlspecialchars($_POST["mode_of_payment"]);
    $paymentDate = date("Y-m-d");
    $amount = htmlspecialchars($_POST["amount"]);
    $transactionRef = htmlspecialchars($_POST["transaction_reference"]);

    // Validate that loan_id and national_id exist in loan_info table
    $stmt = $conn->prepare("SELECT * FROM loan_info WHERE loan_id = ? AND national_id = ?");
    $stmt->bind_param("ss", $loanIdPost, $nationalId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Insert payment record into the database
        $stmt = $conn->prepare("INSERT INTO payments (loan_id, national_id, transaction_reference, payment_mode, payment_date, amount) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssd", $loanIdPost, $nationalId, $transactionRef, $paymentMode, $paymentDate, $amount);

        if ($stmt->execute()) {
            $to = $email;
			$subject = "Transaction Status: Received";
			$message = "Dear Client,\n\n";
			$message .= "Thank you for paying. Below are the details:\n";
			$message .= "Loan Amount: " . $_POST['requested_amount'] . "\n";
            $message .= "Received Amount: " . $_POST['amount'] . "\n";
            $message .= "Reference: " . $_POST['transaction_reference'] . "\n";
            $message .= "Balance: $balance\n\n";
			$message .= "Best regards,\nLoan Administrator";
			
			sendEmail($to, $subject, $message);

            echo '<script>alert("Payment recorded successfully!");</script>';
        } else {
            echo '<script>alert("Error: ' . $stmt->error . '");</script>';
        }
    } else {
        echo '<script>alert("Error: Loan ID or National ID does not exist.");</script>';
    }

    $stmt->close();
    $conn->close();
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

			<div class="page-header">
						<div class="row">
							<div class="col-md-6 col-sm-12">
								<div class="title">
									<h4>Payments</h4>
								</div>
								<nav aria-label="breadcrumb" role="navigation">
									<ol class="breadcrumb">
										<li class="breadcrumb-item">
											<a href="">Home</a>
										</li>
										<li class="breadcrumb-item active" aria-current="page">
											Payments
										</li>
									</ol>
								</nav>
							</div>
						</div>
					</div>

					
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
            <h4 class="text-center text-blue h5 mb-20">PAYMENT DETAILS <?php echo htmlspecialchars($summary['loan_id']); ?></h4>
            <div class="profile-info text-center">
    <ul>

<li><span><strong>Amount:</strong></span></li>
<li>
    <h5 class="text-center"><?php echo number_format($summary['next_payment_amount'], 2); ?></h5> <!-- Default placeholder -->
</li>

<li><span><strong>MPESA NO:</strong></span></li>
<li>
    <h5 class="text-center" id="phoneNumberDisplay"><?php echo htmlspecialchars($phoneNumber ?? ''); ?></h5>
</li>

<!-- Send the selected data via AJAX -->
<li>
    <button 
        type="button" 
        class="btn btn-success btn-sm btn-block"
        onclick="triggerSTKPush('<?php echo $summary['loan_id']; ?>', '<?php echo htmlspecialchars($phoneNumber ?? ''); ?>')"
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

                    
                    
                    <!-- Form grid Start -->
					<div class="pd-20 card-box mb-30">
        <div class="clearfix">
            <div class="pull-left">
                <h4 class="text-blue h4">Payment Form</h4>
                <p class="mb-30">All payments recorded here</p>
            </div>
        </div>
        <!--<div id='mpesaButton'></div> -->
        <form method="POST" action="pay-now.php">
            <div class="row">
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Loan ID</label>
                        <input type="text" class="form-control" name="loan_id" required />
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>National ID</label>
                        <input type="text" class="form-control" name="national_id" required />
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Payment Mode</label>
                        <select class="custom-select col-12" name="mode_of_payment" required>
                            <option selected disabled>Choose...</option>
                            <option value="MPESA">MPESA</option>
                            <option value="Cash">Cash</option>
                            <option value="BANK">BANK</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Payment Date</label>
                        <input type="text" class="form-control date-picker" placeholder="Select Date" name="date_paid" required />
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Amount</label>
                        <input type="text" class="form-control" name="amount" required />
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Transaction Ref:</label>
                        <input type="text" class="form-control" name="transaction_reference" required />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-12 text-right">
                    <div class="input-group mb-0">
                        <input class="btn btn-primary btn-lg btn-block" type="submit" value="RECORD PAYMENT">
                    </div>
                </div>
            </div>
        </form>
    </div>
					<!-- Form grid End -->

        <?php require_once("footer.php"); ?>
        </div>
		</div>
        <!-- js -->
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
    }, 45000); // 45000 milliseconds = 45 seconds
}

// Call this function if the user enters the PIN to clear the timeout
function clearCustomTimeout() {
    if (timeout) {
        clearTimeout(timeout);
    }
}
</script>
		<script src="vendors/scripts/core.js"></script>
		<script src="vendors/scripts/script.min.js"></script>
		<script src="vendors/scripts/process.js"></script>
		<script src="vendors/scripts/layout-settings.js"></script>
		<script src="vendors/scripts/dashboard2.js"></script>
		<!-- Datatable Setting js -->
		<script src="vendors/scripts/datatable-setting.js"></script>
    </body>
</html>
