<?php
require_once("config.php");
include "functions-tena.php";

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'manager')) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user_role'];
$actionMessage = '';
$actionError = '';
companySettings();


if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST['approve_loan_id']) || isset($_POST['reject_loan_id']))) {
    $loanId = isset($_POST['approve_loan_id']) ? $_POST['approve_loan_id'] : $_POST['reject_loan_id'];
    $loanStatus = isset($_POST['approve_loan_id']) ? 'Active' : 'Rejected';

    if ($loanId) {
        $conn->begin_transaction();
        try {
            // Retrieve loan data
            $loanQuery = "SELECT * FROM loan_applications WHERE loan_id = ?";
            $stmtLoan = $conn->prepare($loanQuery);
            if ($stmtLoan === false) {
                throw new Exception($conn->error);
            }
            $stmtLoan->bind_param("s", $loanId);
            $stmtLoan->execute();
            $loanResult = $stmtLoan->get_result();
            $loanData = $loanResult->fetch_assoc();
            $stmtLoan->close();

            if ($loanData) {
                $insertQuery = "
                    INSERT INTO loan_info (
                        loan_id, client_id, national_id, requested_amount, loan_purpose, duration, 
                        duration_period, date_applied, collateral_name, 
                        collateral_value, collateral_pic1, collateral_pic2, signed_application_form, loan_status, onboarding_officer
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ";
                $stmtInsert = $conn->prepare($insertQuery);
                if ($stmtInsert === false) {
                    throw new Exception($conn->error);
                }
                $stmtInsert->bind_param(
                    "sssssssssssssss",
                    $loanData['loan_id'],
                    $loanData['client_id'],
                    $loanData['national_id'],
                    $loanData['requested_amount'],
                    $loanData['loan_purpose'],
                    $loanData['duration'],
                    $loanData['duration_period'],
                    $loanData['date_applied'],
                    $loanData['collateral_name'],
                    $loanData['collateral_value'],
                    $loanData['collateral_pic1'],
                    $loanData['collateral_pic2'],
                    $loanData['signed_application_form'],
                    $loanStatus,
                    $loanData['onboarding_officer']
                );
                if (!$stmtInsert->execute()) {
                    throw new Exception($stmtInsert->error);
                }
                $stmtInsert->close();

                // Delete loan application after moving to the target table
                $deleteLoanQuery = "DELETE FROM loan_applications WHERE loan_id = ?";
                $stmtDeleteLoan = $conn->prepare($deleteLoanQuery);
                if ($stmtDeleteLoan === false) {
                    throw new Exception($stmtDeleteLoan->error);
                }
                $stmtDeleteLoan->bind_param("s", $loanId);
                if (!$stmtDeleteLoan->execute()) {
                    throw new Exception($stmtDeleteLoan->error);
                }
                $stmtDeleteLoan->close();

                if ($loanStatus == 'Active') {
                    // Send email to client with detail of loan

                    $clientName = getClientName($loanId);
                    $clientId = $loanData['client_id'];
                    $clientData = getClientData($clientId);
                    $email = $clientData['email'] ?? null;


                    $to = $email;
                    $subject = "Loan Approved!";
                    $message = "Dear $clientName,\n\n";
                    $message .= "Your loan request has been approved.\n";
                    $message .= "You can log in to your portal at https://portal.omabracredit.co.ke to track your balances.\n\n";
                    $message .= "Best regards,\nAdministrator.";

                    sendEmail($to, $subject, $message);

                    // Insert notification for the new client
                    $clientQuery = "SELECT user_id FROM users WHERE file_no = '$clientId'";
                    $clientResult = $conn->query($clientQuery);
                    if ($clientResult && $clientResult->num_rows > 0) {
                        $clientRow = $clientResult->fetch_assoc();
                        $clientUserId = $clientRow['user_id'];
                        $clientNotificationHeading = "Loan Approved!";
                        $clientNotificationMessage = "Welcome, $clientName! Your loan account has been credited.";
                        addNotification($clientUserId, $clientNotificationHeading, $clientNotificationMessage);
                    }                       


// Loan is approved, now add the entry in oc24entries table

// Extract only the numeric part of the loan ID
$loanNumber = preg_replace('/\D/', '', $loanData['loan_id']);
$todayDate = date('Y-m-d');
$dr_total = $loanData['requested_amount'];
$cr_total = $loanData['requested_amount'];
$narration = "Disbursement to client $clientName for loan number " . $loanData['loan_id'];

// Insert into oc24entries table
$oc24entriesInsertQuery = "
    INSERT INTO oc24entries (entrytype_id, number, date, dr_total, cr_total, narration) 
    VALUES (?, ?, ?, ?, ?, ?)
";
$stmtOc24entriesInsert = $conn->prepare($oc24entriesInsertQuery);
if ($stmtOc24entriesInsert === false) {
    throw new Exception($stmtOc24entriesInsert->error);
}
$entrytype_id = 2; // Entry type is 2 for approved loans
$stmtOc24entriesInsert->bind_param("issdds", $entrytype_id, $loanNumber, $todayDate, $dr_total, $cr_total, $narration);

if (!$stmtOc24entriesInsert->execute()) {
    throw new Exception($stmtOc24entriesInsert->error);
}

// Get the last inserted ID (this will be used as entry_id in oc24entryitems)
$oc24entriesId = $conn->insert_id;
$stmtOc24entriesInsert->close(); // Close the statement after execution

// Insert into oc24entryitems table: 2 entries
$oc24entryitemsInsertQuery = "
    INSERT INTO oc24entryitems (entry_id, ledger_id, amount, dc) 
    VALUES (?, ?, ?, ?), (?, ?, ?, ?)
";
$stmtOc24entryitemsInsert = $conn->prepare($oc24entryitemsInsertQuery);
if ($stmtOc24entryitemsInsert === false) {
    throw new Exception($stmtOc24entryitemsInsert->error);
}

// Bind parameters for both entries
$ledgerId1 = 11;
$ledgerId2 = 12;
$dc1 = 'C'; // Credit
$dc2 = 'D'; // Debit
$stmtOc24entryitemsInsert->bind_param(
    "iidsiids",
    $oc24entriesId, $ledgerId1, $dr_total, $dc1,
    $oc24entriesId, $ledgerId2, $dr_total, $dc2
);

if (!$stmtOc24entryitemsInsert->execute()) {
    throw new Exception($stmtOc24entryitemsInsert->error);
}

$stmtOc24entryitemsInsert->close(); // Close the statement after execution
                

                // Insert each installment into expected_payments
                $loanAmount = (float) $loanData['requested_amount'];
                $duration = (int) $loanData['duration'];
                $durationPeriod = strtolower($loanData['duration_period']);

                // Check the duration period and adjust the interest rate accordingly
                switch (strtolower($durationPeriod)) {
                    case 'week':
                        // Divide the monthly interest rate by 4 if duration is in weeks
                        $interestRate = ($interestRate / 4) / 100;
                        break;
                    case 'month':
                        // Use the monthly interest rate as is (already a percentage)
                        $interestRate = $interestRate / 100;
                        break;
                    case 'year':
                        // Optionally, handle for years if needed
                        $interestRate = ($interestRate * 12) / 100;
                        break;
                    default:
                    // Default to monthly rate if durationPeriod is invalid
                        $interestRate = $interestRate / 100;
                        break;
                }
                
                $totalInterest = $loanAmount * $interestRate * $duration;
                $totalLoanWithInterest = $loanAmount + $totalInterest;
                $installmentAmount = ceil($totalLoanWithInterest / $duration);
                $principalPortion = ceil($loanAmount / $duration);
                $interestPortion = ceil($totalInterest / $duration);

                $startDate = new DateTime(); // Assuming start date is now

                for ($i = 1; $i <= $duration; $i++) {
                    $paymentDate = clone $startDate;
                
                    // Set $intervalSpec based on the $durationPeriod
                    switch (strtolower($durationPeriod)) {
                        case 'week':
                            $intervalSpec = "P1W";  // Always add 1 week per iteration
                            break;
                        case 'month':
                            $intervalSpec = "P1M";  // Always add 1 month per iteration
                            break;
                        case 'year':
                            $intervalSpec = "P1Y";  // Always add 1 year per iteration
                            break;
                        default:
                            // Default interval is 1 week if invalid durationPeriod
                            $intervalSpec = "P1W";  // Default to weekly
                            break;
                    }
                
                    // Cumulatively add the interval to the start date
                    $startDate->add(new DateInterval($intervalSpec)); 
                    $formattedPaymentDate = $startDate->format('Y-m-d');
                    
                    // Format the amounts before binding them
                    $formattedInstallmentAmount = number_format($installmentAmount, 2, '.', '');
                    $formattedInterestPortion = number_format($interestPortion, 2, '.', '');
                    $formattedPrincipalPortion = number_format($principalPortion, 2, '.', '');
                    
                    $paymentInsertQuery = "
                        INSERT INTO expected_payments (
                            loan_id, installment_amount, payment_date, payment_status, interest_income, principal
                        ) VALUES (?, ?, ?, 'not paid', ?, ?)
                    ";
                    $stmtPaymentInsert = $conn->prepare($paymentInsertQuery);
                    if ($stmtPaymentInsert === false) {
                        throw new Exception($stmtPaymentInsert->error);
                    }
                    $stmtPaymentInsert->bind_param(
                        "sssss",
                        $loanId, $formattedInstallmentAmount, $formattedPaymentDate, $formattedInterestPortion, $formattedPrincipalPortion
                    );
                
                    if (!$stmtPaymentInsert->execute()) {
                        throw new Exception($stmtPaymentInsert->error);
                    }
                    $stmtPaymentInsert->close();
                }
                
                }

                $conn->commit();
            }
        } catch (Exception $e) {
            $conn->rollback();
            $actionError = "Transaction failed: " . $e->getMessage();
        }
    }
}

$loansQuery = "
    SELECT l.*, c.first_name, c.last_name, c.email, c.phone_number AS client_phone, c.county, c.guarantor_national_id, c.guarantor_id_photo_front, c.guarantor_id_photo_back, c.guarantor_passport_photo
    FROM loan_applications l
    JOIN clients c ON l.national_id = c.national_id
";
$loansResult = $conn->query($loansQuery);
?>

<!DOCTYPE html>
<html>
<?php require_once("head.php"); ?>
<body>
<?php require_once("header.php"); ?>
<?php require_once("right-sidebar.php"); ?>

<?php
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
                            <h4>Profile</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Applied Loans Profile</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <?php if ($actionMessage): ?>
                <p style="color: green;"><?php echo htmlspecialchars($actionMessage); ?></p>
            <?php endif; ?>

            <?php if ($actionError): ?>
                <p style="color: red;"><?php echo htmlspecialchars($actionError); ?></p>
            <?php endif; ?>

            <div class="row">
                <?php while ($loan = $loansResult->fetch_assoc()): ?>
                    <div class="col-xl-12 col-lg-12 col-md-8 col-sm-12 mb-30">
                        <div class="pd-20 card-box height-100-p">
                            <h5 class="text-center h5 mb-0">Loan ID: <?php echo htmlspecialchars($loan['loan_id']); ?></h5>
                            <p class="text-center text-muted font-14">Here are the loan application details. Please approve, but if any details are not satisfactory, reject the loan and proceed to the next one.</p>
                            <div class="profile-info-container">
    <div class="profile-info">
        <h5 class="mb-20 h5 text-blue">Contact Information</h5>
        <ul>
            <li><span>Name:</span> <?php echo htmlspecialchars(($loan['first_name'] ?? '') . ' ' . ($loan['last_name'] ?? '')); ?></li>
            <li><span>Email Address:</span> <?php echo htmlspecialchars($loan['email'] ?? 'N/A'); ?></li>
            <li><span>Phone Number:</span> <?php echo htmlspecialchars($loan['client_phone'] ?? 'N/A'); ?></li>
            <li><span>County:</span> <?php echo htmlspecialchars($loan['county'] ?? 'N/A'); ?></li>
        </ul>
    </div>
    <div class="profile-info">
        <h5 class="mb-20 h5 text-blue">Loan In Numbers</h5>
        <ul>
            <li><span>National ID:</span> <?php echo htmlspecialchars($loan['national_id'] ?? 'N/A'); ?></li>
            <li><span>Requested Amount:</span> <?php echo htmlspecialchars($loan['requested_amount'] ?? 'N/A'); ?></li>
            <li><span>Duration:</span> <?php echo htmlspecialchars(($loan['duration'] ?? '') . ' ' . ($loan['duration_period'] ?? '')); ?></li>
        </ul>
    </div>
    <div class="profile-info">
        <h5 class="mb-20 h5 text-blue">Loan Profile Info</h5>
        <ul>
            <li><span>Date of Applying:</span> <?php echo htmlspecialchars($loan['date_applied'] ?? 'N/A'); ?></li>
            <li><span>Loan Onboarding Officer:</span> <?php echo htmlspecialchars($loan['onboarding_officer'] ?? 'N/A'); ?></li>
            <li><span>Purpose:</span> <?php echo htmlspecialchars($loan['loan_purpose'] ?? 'N/A'); ?></li>
        </ul>
    </div>
    <div class="profile-info">
        <h5 class="mb-20 h5 text-blue">Security Information</h5>
        <ul>
            <li><span>Collateral Name:</span> <?php echo htmlspecialchars($loan['collateral_name'] ?? 'N/A'); ?></li>
            <li><span>Collateral Value:</span> <?php echo htmlspecialchars($loan['collateral_value'] ?? 'N/A'); ?></li>
            <li>
                <span>Collateral Pic 1:</span> 
                <a href="<?php echo htmlspecialchars($loan['collateral_pic1'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" target="_blank">
                    View Collateral Pic 1
                </a>
            </li>
            <li>
                <span>Collateral Pic 2:</span> 
                <a href="<?php echo htmlspecialchars($loan['collateral_pic2'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" target="_blank">
                    View Collateral Pic 2
                </a>
            </li>
        </ul>
    </div>
    <div class="profile-info">
        <h5 class="mb-20 h5 text-blue">Guarantor Information</h5>
        <ul>
            <li><span>Guarantor Phone Number:</span> <?php echo htmlspecialchars($loan['guarantor_national_id'] ?? 'N/A'); ?></li>
            <li>
                <span>Guarantor ID Front:</span> 
                <a href="<?php echo htmlspecialchars($loan['guarantor_id_photo_front'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" target="_blank">
                    View Image
                </a>
            </li>
            <li>
                <span>Guarantor ID Back:</span> 
                <a href="<?php echo htmlspecialchars($loan['guarantor_id_photo_back'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" target="_blank">
                    View Image
                </a>
            </li>
            <li>
                <span>Guarantor Photo:</span> 
                <a href="<?php echo htmlspecialchars($loan['guarantor_passport_photo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" target="_blank">
                    View Image
                </a>
            </li>
        </ul>
    </div>
</div>

                            <div>
<h5 class="text-center h5 mb-0">Signed Application Form: <a href="<?php echo htmlspecialchars($loan['signed_application_form'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" target="_blank">
        View Form
    </a></h5> 
     
</div>
<br>
<form method="post" class="row" style="display: flex; justify-content: space-between;">
    <input type="hidden" name="loan_id" value="<?php echo htmlspecialchars($loan['loan_id']); ?>">

    <!-- Reject Loan Button -->
    <div class="form-group mb-0">
        <button type="submit" name="reject_loan_id" value="<?php echo htmlspecialchars($loan['loan_id']); ?>" class="btn btn-primary">Reject</button>
    </div>

    <!-- Edit Application Button: This posts loan_id to edit-loan-application.php -->
    <div class="form-group mb-0">
        <button type="submit" formaction="edit-loan-application.php" name="edit_loan_id" value="<?php echo htmlspecialchars($loan['loan_id']); ?>" class="btn btn-primary">Edit Application Before Approval</button>
    </div>

    <!-- Approve Loan Button -->
    <div class="form-group mb-0">
        <button type="submit" name="approve_loan_id" value="<?php echo htmlspecialchars($loan['loan_id']); ?>" class="btn btn-primary">Approve</button>
    </div>
</form>

                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php require_once("footer.php"); ?>
    </div>
</div>

<script src="vendors/scripts/core.js"></script>
<script src="vendors/scripts/script.min.js"></script>
<script src="vendors/scripts/process.js"></script>
<script src="vendors/scripts/layout-settings.js"></script>
</body>
</html>
