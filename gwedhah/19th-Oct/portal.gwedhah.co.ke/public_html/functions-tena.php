<?php

// Before the code block
//$startMemory = memory_get_usage();
//$startPeakMemory = memory_get_peak_usage();


require_once("config.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';


function sendEmail($to, $subject, $messageBody) {
    $mail = new PHPMailer(true);
    
    try {
        // Enable verbose debug output
        $mail->SMTPDebug = 3;  // Set to 2 for less verbose output, 0 to disable
        $mail->Debugoutput = 'error_log';  // Send debug output to PHP's error log

        // Server settings
        $mail->isSMTP();                                          // Send using SMTP
        $mail->Host       = 'smtp.gwedhah.co.ke';                 // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                 // Enable SMTP authentication
        $mail->Username   = 'admin@gwedhah.co.ke';                // SMTP username
        $mail->Password   = 'Invest@Growth2024!';                       // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;       // Enable TLS encryption
        $mail->Port       = 587;                                  // TCP port to connect to

        // Recipients
        $mail->setFrom('admin@gwedhah.co.ke', 'Gwedhah Investments');
        $mail->addAddress($to);                                   // Add recipient

        // Content
        $mail->isHTML(false);                                     // Set email format to plain text
        $mail->Subject = $subject;
        $mail->Body    = $messageBody;

        // Send email
        if ($mail->send()) {
            return true;
        } else {
            error_log('Mail Error: ' . $mail->ErrorInfo);  // Log PHPMailer errors
            return false;
        }
    } catch (Exception $e) {
        error_log('PHPMailer Exception: ' . $e->getMessage());  // Log exceptions
        return false;
    }
}


//OPERATIONS FUNCTIONS

//image-size reduction
function compressImage($source, $destination, $quality) {
    // Get image info
    $imageInfo = getimagesize($source);
    $mime = $imageInfo['mime'];

    // Create a new image from the file based on its MIME type
    switch($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source);
            break;
        default:
            return false; // Return false if the file type is unsupported
    }

    // Save the image with the given quality
    if ($mime == 'image/jpeg') {
        imagejpeg($image, $destination, $quality);
    } elseif ($mime == 'image/png') {
        $quality = 9 - (int)($quality / 10); // PNG compression level (0-9)
        imagepng($image, $destination, $quality);
    }

    // Free up memory
    imagedestroy($image);

    return true;
}

function generatePassword() {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return substr(str_shuffle($chars), 0, 10);
}


function companySettings() {
    global $conn;

    // Define global variables to store company information
    global $companyName, $companyAddress, $companyEmail, $companyWebsite, $companyPhone, $companyTaxRate;
    global $bankName, $accountNumber, $accountReference, $payeeName;  // Bank-related variables
    global $interestRate, $interestBillingPeriod;  // New interest-related variables

    // SQL query to get company settings (assuming there's only one row with id = 1)
    $sql = "SELECT company_name, company_address, company_email, company_website, company_phone, tax_rate, bank_name, account_number, account_reference, payee_name, interest_rate, interest_billing_period 
            FROM company_settings WHERE id = 1";
    
    // Execute query
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        // Fetch result and store in global variables
        $row = $result->fetch_assoc();
        $companyName = $row['company_name'];
        $companyAddress = $row['company_address'];
        $companyEmail = $row['company_email'];
        $companyWebsite = $row['company_website'];
        $companyPhone = $row['company_phone'];
        $companyTaxRate = $row['tax_rate'];

        // Set the bank-related variables
        $bankName = $row['bank_name'];
        $accountNumber = $row['account_number'];
        $accountReference = $row['account_reference'];
        $payeeName = $row['payee_name'];

        // Set the new interest-related variables
        $interestRate = $row['interest_rate'];
        $interestBillingPeriod = $row['interest_billing_period'];
    } else {
        // Set default values if no data is found
        $companyName = "Not Available";
        $companyAddress = "Not Available";
        $companyEmail = "Not Available";
        $companyWebsite = "Not Available";
        $companyPhone = "Not Available";
        $companyTaxRate = 30;

        // Default values for bank-related fields
        $bankName = "Not Available";
        $accountNumber = "Not Available";
        $accountReference = "Not Available";
        $payeeName = "Not Available";

        // Default values for interest-related fields
        $interestRate = 0;  // Default interest rate
        $interestBillingPeriod = "Not Available";  // Default billing period
    }
}


function getOnboardingOfficer() {
    
    if (isset($_SESSION['user_role'])) {
        if ($_SESSION['user_role'] === 'admin') {
            return 'admin';
        } elseif ($_SESSION['user_role'] === 'manager' && isset($_SESSION['email'])) {
            return $_SESSION['email'];
        }
    }
    return null;
}

function getOnboardingOfficerByLoanId($loan_id) {
    global $conn;
    // SQL query to fetch onboarding_officer based on loan_id
    $sql = "SELECT onboarding_officer FROM loan_info WHERE loan_id = ?";
    
    // Prepare the SQL statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind loan_id to the SQL statement
        $stmt->bind_param("s", $loan_id);
        
        // Execute the statement
        $stmt->execute();
        
        // Get the result
        $result = $stmt->get_result();
        
        // Fetch and store the onboarding_officer if found
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['onboarding_officer'];
        } else {
            return null; // No matching loan_id found
        }
    } else {
        // Handle error
        error_log("Error preparing statement: " . $conn->error);
        return null;
    }
}


function getManagerIdByEmail($email) {
    global $conn;
    // SQL query to fetch manager_id based on email
    $sql = "SELECT manager_id FROM managers WHERE email = ?";
    
    // Prepare the SQL statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind email to the SQL statement
        $stmt->bind_param("s", $email);
        
        // Execute the statement
        $stmt->execute();
        
        // Get the result
        $result = $stmt->get_result();
        
        // Fetch and store the manager_id if found
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['manager_id'];
        } else {
            return null; // No matching email found
        }
    } else {
        // Handle error
        error_log("Error preparing statement: " . $conn->error);
        return null;
    }
}


function getClientIdByNationalId($national_id) {
    global $conn;
    // Prepare SQL query to get client_id based on national_id
    $sql = "SELECT client_id FROM clients WHERE national_id = ?";
    
    // Prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind the national_id to the query
        $stmt->bind_param("s", $national_id);
        
        // Execute the statement
        $stmt->execute();
        
        // Get the result
        $result = $stmt->get_result();
        
        // Check if a row was found
        if ($result->num_rows > 0) {
            // Fetch the client_id
            $row = $result->fetch_assoc();
            return $row['client_id'];
        } else {
            return null; // No client found for the provided national_id
        }
    } else {
        // Return error if SQL fails
        error_log("Error preparing statement: " . $conn->error);
        return null;
    }
}

function getClientId() {
    global $conn;

    // Ensure the user is logged in
    if (!isset($_SESSION['user_id'])) {
        return null; // User is not logged in
    }

    // Fetch the logged-in user's file_no (which is the client_id) from the users table
    $user_id = $_SESSION['user_id'];
    $query = "SELECT file_no FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->bind_result($clientId); // Directly use file_no as client_id
    $stmt->fetch();
    $stmt->close();

    return $clientId ? $clientId : null; // Return the client_id or null if not found
}


function managerExists($email, $phone, $nationalId) {
    global $conn; // Use the existing database connection from config.php
    $stmt = $conn->prepare("SELECT * FROM managers WHERE email = ? OR phone_number = ? OR national_id = ?");
    $stmt->bind_param("sss", $email, $phone, $nationalId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

function clientExists($email, $phone, $nationalId) {
    global $conn; // Use the existing database connection from config.php
    $stmt = $conn->prepare("SELECT * FROM clients WHERE email = ? OR phone_number = ? OR national_id = ?");
    $stmt->bind_param("sss", $email, $phone, $nationalId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}


//CORE LOAN CALCULATION FUNCTIONS

// Function to calculate the loan balance
// Function to calculate the loan balance with fallback for missing loan ID

function getRequestedAmountByLoanId($loanId) {
    global $conn; // Ensure you have access to your database connection

    // Prepare SQL query to get the requested_amount by loan_id
    $sql = "SELECT requested_amount FROM loan_info WHERE loan_id = ?";
    $stmt = $conn->prepare($sql);

    // Bind the loan_id to the prepared statement
    $stmt->bind_param("s", $loanId);

    // Execute the query
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Check if the result contains a row
    if ($result->num_rows > 0) {
        // Fetch the requested_amount
        $row = $result->fetch_assoc();
        return $row['requested_amount']; // Return the requested amount
    } else {
        return null; // Return null if no matching loan_id is found
    }
}



function calculateLoanBalance($loan_id) {
    global $conn;

    // Get the total expected payments for the loan
    $sql_expected = "SELECT SUM(installment_amount) AS total_expected FROM expected_payments WHERE loan_id = ?";
    $stmt_expected = $conn->prepare($sql_expected);
    $stmt_expected->bind_param("s", $loan_id);
    $stmt_expected->execute();
    $result_expected = $stmt_expected->get_result();
    
    $total_expected = ($result_expected && $result_expected->num_rows > 0) ? $result_expected->fetch_assoc()['total_expected'] : 0;

    // Get the total payments made for the loan
    $sql_payments = "SELECT SUM(amount) AS total_payments FROM payments WHERE loan_id = ?";
    $stmt_payments = $conn->prepare($sql_payments);
    $stmt_payments->bind_param("s", $loan_id);
    $stmt_payments->execute();
    $result_payments = $stmt_payments->get_result();
    
    $total_payments = ($result_payments && $result_payments->num_rows > 0) ? $result_payments->fetch_assoc()['total_payments'] : 0;

    // Calculate the remaining balance
    return $total_expected - $total_payments;
}

// Function to calculate equal installments
// Function to calculate equal installments
function calculateInstallments($loan_id) {
    global $conn;
    
    // Get the total expected payments and count the number of periods
    $sql = "SELECT SUM(installment_amount) AS total_expected, COUNT(*) AS periods FROM expected_payments WHERE loan_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $loan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Handle cases where there are no periods or no expected payments
    $total_expected = $row['total_expected'] ?? 0;
    $periods = $row['periods'] ?? 0;

    // Prevent division by zero by checking if periods is greater than 0
    if ($periods > 0) {
        return $total_expected / $periods;
    } else {
        // Return 0 if there are no periods to avoid division by zero
        return 0;
    }
}

// Function to calculate accrued interest
function calculateAccruedInterest($loan_id) {
    global $conn;
    // Sum the interest income from expected payments where the status is 'not paid' or based on date logic
    $sql = "SELECT SUM(interest_income) AS accrued_interest FROM expected_payments WHERE loan_id = ? AND payment_status = 'not paid'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $loan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['accrued_interest'] ?? 0;
}

// Function to get the next payment date
function getNextPaymentDate($loan_id) {
    global $conn;

    // SQL query to fetch the next unpaid payment date that is either today or the nearest future date
    $sql = "SELECT payment_date 
            FROM expected_payments 
            WHERE loan_id = ? 
            AND payment_status = 'not paid' 
            AND payment_date >= CURDATE()
            ORDER BY payment_date ASC
            LIMIT 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $loan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    // Return the next payment date or null if no such date exists
    return $row['payment_date'] ?? null;
}

// Function to get the next payment amount
function getNextPaymentAmount($loan_id) {
    global $conn;

    // First, get the next payment date using the updated getNextPaymentDate function
    $nextPaymentDate = getNextPaymentDate($loan_id);

    // If no next payment date exists, return 0
    if (!$nextPaymentDate) {
        return 0;
    }

    // SQL query to sum all installment amounts due on or before the next payment date, with status 'not paid'
    $sql = "SELECT SUM(installment_amount) AS total_due
            FROM expected_payments 
            WHERE loan_id = ? 
            AND payment_status = 'not paid' 
            AND payment_date <= ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $loan_id, $nextPaymentDate);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    // Return the total due or 0 if no such amounts exist
    return $row['total_due'] ?? 0;
}


// Function to calculate periods passed
function getPeriodsPassed($loan_id) {
    global $conn;

    // Count the number of periods where payment date has passed
    $sql = "SELECT COUNT(*) AS periods_passed FROM expected_payments WHERE loan_id = ? AND payment_date < CURDATE()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $loan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $row['periods_passed'] ?? 0;
}

// Function to calculate periods remaining
function getPeriodsRemaining($loan_id) {
    global $conn;

    // Count the number of periods remaining
    $sql_total = "SELECT COUNT(*) AS total_periods FROM expected_payments WHERE loan_id = ?";
    $stmt_total = $conn->prepare($sql_total);
    $stmt_total->bind_param("s", $loan_id);
    $stmt_total->execute();
    $result_total = $stmt_total->get_result();
    $row_total = $result_total->fetch_assoc();
    $total_periods = $row_total['total_periods'] ?? 0;

    $periods_passed = getPeriodsPassed($loan_id);

    return $total_periods - $periods_passed;
}

// Function to calculate the amount to clear today
function calculateClearTodayAmount($loan_id) {
    global $conn;

    // Fetch the sum of unpaid principal from expected payments for future installments
    $sql = "SELECT SUM(installment_amount) AS clear_today FROM expected_payments WHERE loan_id = ? AND payment_status = 'not paid'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $loan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $row['clear_today'] ?? 0;
}

// Function to check if the loan is cleared
function isLoanCleared($loan_id) {
    global $conn;

    // Get the total expected payments
    $sql_expected = "SELECT SUM(installment_amount) AS total_expected FROM expected_payments WHERE loan_id = ?";
    $stmt_expected = $conn->prepare($sql_expected);
    $stmt_expected->bind_param("s", $loan_id);
    $stmt_expected->execute();
    $result_expected = $stmt_expected->get_result();
    $row_expected = $result_expected->fetch_assoc();
    $total_expected = $row_expected['total_expected'] ?? 0;

    // Get the total actual payments
    $sql_payments = "SELECT SUM(amount) AS total_payments FROM payments WHERE loan_id = ?";
    $stmt_payments = $conn->prepare($sql_payments);
    $stmt_payments->bind_param("s", $loan_id);
    $stmt_payments->execute();
    $result_payments = $stmt_payments->get_result();
    $row_payments = $result_payments->fetch_assoc();
    $total_payments = $row_payments['total_payments'] ?? 0;

    // Return true if payments are equal to or greater than expected payments
    return $total_payments >= $total_expected;
}


// Business Reports Functions

// Get total accrued interest for a loan
function getAllExpectedPaymentsDue($loan_id){
    global $conn;
    $sql = "SELECT SUM(installment_amount) AS all_expected_payments_due
            FROM expected_payments
            WHERE loan_id = ? AND payment_date <= CURDATE()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $loan_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['all_expected_payments_due'] ?: 0;
}

function getAllAccruedInterest($loan_id) {
    global $conn;
    $sql = "SELECT SUM(interest_income) AS all_accrued_interest
            FROM expected_payments
            WHERE loan_id = ? AND payment_date <= CURDATE() AND payment_status = 'not paid'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $loan_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['all_accrued_interest'] ?: 0;
}

// Get total accrued interest in the business
function getTotalAccruedInterest() {
    global $conn;
    $sql = "SELECT SUM(interest_income) AS total_accrued_interest
            FROM expected_payments
            WHERE payment_date <= CURDATE() AND payment_status = 'not paid'";
    $result = $conn->query($sql)->fetch_assoc();
    return $result['total_accrued_interest'] ?: 0;
}

// Get total payments made for a loan
function getAllPayments($loan_id) {
    global $conn;
    $sql = "SELECT SUM(amount) AS total_payments
            FROM payments
            WHERE loan_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $loan_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['total_payments'] ?: 0;
}

// Get total payments made across all loans
// Get total payments made across all loans
function getTotalPayments() {
    global $conn;

    // Query to sum all payments across all loans
    $sql = "SELECT SUM(amount) AS total_payments FROM payments";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch the total payments or default to 0 if not found
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['total_payments'] ?? 0; // Return total payments or 0 if null
    } else {
        return 0; // Default to 0 if no payments found
    }
}


// Get total amount disbursed for a loan
function getLoansDisbursed($loan_id) {
    global $conn;
    $sql = "SELECT requested_amount AS disbursed_amount
            FROM loan_info
            WHERE loan_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $loan_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['disbursed_amount'] ?: 0;
}

// Get total amount disbursed across all loans
function getTotalLoansDisbursed() {
    global $conn;
    $sql = "SELECT SUM(requested_amount) AS total_disbursed_amount FROM loan_info";
    $result = $conn->query($sql)->fetch_assoc();
    return $result['total_disbursed_amount'] ?: 0;
}


// Count total number of loans
function countLoans() {
    global $conn;
    $sql = "SELECT COUNT(*) AS total_loans FROM loan_info";
    $result = $conn->query($sql)->fetch_assoc();
    return $result['total_loans'] ?: 0;
}

// Count total number of clients
function countClients() {
    global $conn;
    $sql = "SELECT COUNT(DISTINCT client_id) AS total_clients FROM clients";
    $result = $conn->query($sql)->fetch_assoc();
    return $result['total_clients'] ?: 0;
}

// Get the current stage status of a loan
// Get the current stage status of a loan
function getStageStatus($loan_id) {
    global $conn;
    $sql = "SELECT stage FROM loan_stage WHERE loan_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $loan_id);
    $stmt->execute();
    
    $result = $stmt->get_result()->fetch_assoc();
    
    // Check if result is null or does not contain 'stage'
    if ($result && isset($result['stage'])) {
        return $result['stage'];
    }
    
    return 'unknown'; // Default value if no stage is found
}

// Get overall loan book status (e.g., total outstanding balance)
function getLoanBookStatus() {
    global $conn;
    $sql = "SELECT SUM(requested_amount - (SELECT SUM(amount) 
                                         FROM payments 
                                         WHERE payments.loan_id = loan_info.loan_id)) AS outstanding_balance
            FROM loan_info";
    $result = $conn->query($sql)->fetch_assoc();
    return $result['outstanding_balance'] ?: 0;
}

//today-specific business performance functions

function getTotalLoansDisbursedToday() {
    global $conn;
    $today = date('Y-m-d');
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_loans FROM loan_info WHERE DATE(created_at) = ?");
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['total_loans'] ?? 0;
}

function getTotalAmountDisbursedToday() {
    global $conn;
    $today = date('Y-m-d');
    $stmt = $conn->prepare("SELECT SUM(requested_amount) AS total_amount FROM loan_info WHERE DATE(created_at) = ?");
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['total_amount'] ?? 0;
}

function getTotalInterestEarnedToday() {
    global $conn;
    $today = date('Y-m-d');
    $stmt = $conn->prepare("
        SELECT SUM(interest_income) AS total_interest
        FROM expected_payments
        WHERE DATE(payment_date) = ? ");
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['total_interest'] ?? 0;
}

function getTotalPaymentsReceivedToday() {
    global $conn;
    $today = date('Y-m-d');
    $stmt = $conn->prepare("SELECT SUM(amount) AS total_payments FROM payments WHERE DATE(payment_date) = ?");
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['total_payments'] ?? 0;
}

//chart data
function getLoanDataForChart($clientId, $managerEmail) {
    global $conn;

    // Fetch the latest loan for the client or manager based on the created_at timestamp
    $sql = "SELECT li.loan_id, li.requested_amount, li.created_at
            FROM loan_info li
            WHERE (li.client_id = ? OR li.onboarding_officer = ?)
            ORDER BY li.created_at DESC LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $clientId, $managerEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    $loan = $result->fetch_assoc();

    if ($loan) {
        $loanId = $loan['loan_id'];
        $requestedAmount = $loan['requested_amount'];
        $createdAt = $loan['created_at'];

        // Fetch cumulative payments for this loan
        $sqlPayments = "SELECT SUM(p.amount) as total_payments, p.payment_date 
                        FROM payments p
                        WHERE p.loan_id = ?
                        GROUP BY p.payment_date
                        ORDER BY p.payment_date";

        // Fetch expected payments data for this loan
        $sqlExpectedPayments = "SELECT SUM(ep.installment_amount) as total_expected_payments, 
                                       SUM(ep.principal) as total_principal, 
                                       SUM(ep.interest_income) as total_interest, 
                                       ep.payment_date 
                                FROM expected_payments ep
                                WHERE ep.loan_id = ?
                                GROUP BY ep.payment_date
                                ORDER BY ep.payment_date";

        // Execute payments query
        $stmtPayments = $conn->prepare($sqlPayments);
        $stmtPayments->bind_param("s", $loanId);
        $stmtPayments->execute();
        $resultPayments = $stmtPayments->get_result();
        $paymentsData = [];
        while ($row = $resultPayments->fetch_assoc()) {
            $paymentsData[] = $row;
        }

        // Execute expected payments query
        $stmtExpectedPayments = $conn->prepare($sqlExpectedPayments);
        $stmtExpectedPayments->bind_param("s", $loanId);
        $stmtExpectedPayments->execute();
        $resultExpectedPayments = $stmtExpectedPayments->get_result();
        $expectedPaymentsData = [];

        // Variables to track cumulative sum of installment_amount and interest
        $cumulativeInstallmentAmount = 0;
        $cumulativeInterest = 0;

        while ($row = $resultExpectedPayments->fetch_assoc()) {
            // Calculate the cumulative sum of installment_amount and interest
            $cumulativeInstallmentAmount += $row['total_expected_payments'];
            $cumulativeInterest += $row['total_interest'];

            // Update principal to reflect requested amount + cumulative interest
            $row['cumulative_principal'] = $requestedAmount + $cumulativeInterest;

            // Add the cumulative sum of installment_amount
            $row['cumulative_installment_amount'] = $cumulativeInstallmentAmount;
            
            $expectedPaymentsData[] = $row;
        }

        // Return all variables, including the new cumulative principal and installment amount
        return [
            'loan_id' => $loanId,
            'requested_amount' => $requestedAmount,
            'created_at' => $createdAt,
            'payments_data' => $paymentsData,
            'expected_payments_data' => $expectedPaymentsData,  // Includes 'cumulative_installment_amount' and 'cumulative_principal'
            'cumulative_installment_amount' => $cumulativeInstallmentAmount  // Total cumulative installment amount
        ];
    }

    return null;
}


//manager-specific business report data
// Fetch the logged-in manager's email from the session

function getManagerTotalLoanData() {
    // Fetch the required data
    $totalLoans = countLoansByManager() ?? 0;
    $totalClients = countClientsByManager() ?? 0;
    $totalPayments = getTotalPaymentsByManager() ?? 0;
    $totalAccruedInterest = getTotalAccruedInterestByManager() ?? 0;
    $totalAmountDisbursed = getTotalAmountDisbursedByManager() ?? 0;
    $totalEarnedInterest = getTotalEarnedInterestByManager() ?? 0;

    // Store the data in an associative array
    $managerTotalLoanData = [
        'total_loans' => $totalLoans,
        'total_clients' => $totalClients,
        'total_payments' => $totalPayments,
        'total_accrued_interest' => $totalAccruedInterest,
        'total_amount_disbursed' => $totalAmountDisbursed,
        'total_earned_interest' => $totalEarnedInterest
    ];

    // Return the array
    return $managerTotalLoanData;
}


function getManagerData($email) {
    global $conn;

    // Prepare the SQL query to retrieve manager data using the email
    $manager_query = $conn->prepare("SELECT * FROM managers WHERE email = ?");
    $manager_query->bind_param("s", $email);
    
    // Execute the query and fetch the manager's data
    if ($manager_query->execute()) {
        $manager_result = $manager_query->get_result();
        $manager_data = $manager_result->fetch_assoc();
        return $manager_data; // Return the manager data
    } else {
        return null; // Return null if the query fails
    }
}

// Count loans relevant to the logged-in manager
function countLoansByManager() {
    global $conn;
    $officer = getOnboardingOfficer();
    if ($officer === 'admin') {
        $sql = "SELECT COUNT(*) AS total_loans FROM loan_info";
    } else {
        $sql = "SELECT COUNT(*) AS total_loans FROM loan_info WHERE onboarding_officer = ?";
    }
    $stmt = $conn->prepare($sql);
    if ($officer !== 'admin') {
        $stmt->bind_param("s", $officer);
    }
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['total_loans'] ?: 0;
}

// Count clients relevant to the logged-in manager
function countClientsByManager() {
    global $conn;
    $officer = getOnboardingOfficer();
    if ($officer === 'admin') {
        $sql = "SELECT COUNT(DISTINCT client_id) AS total_clients FROM clients";
    } else {
        $sql = "SELECT COUNT(DISTINCT client_id) AS total_clients FROM clients WHERE client_id IN 
               (SELECT client_id FROM loan_info WHERE onboarding_officer = ?)";
    }
    $stmt = $conn->prepare($sql);
    if ($officer !== 'admin') {
        $stmt->bind_param("s", $officer);
    }
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['total_clients'] ?: 0;
}

// Get total payments made relevant to the logged-in manager
function getTotalPaymentsByManager() {
    global $conn;
    $officer = getOnboardingOfficer();
    if ($officer === 'admin') {
        $sql = "SELECT SUM(amount) AS total_payments FROM payments";
    } else {
        $sql = "SELECT SUM(amount) AS total_payments FROM payments 
                WHERE loan_id IN (SELECT loan_id FROM loan_info WHERE onboarding_officer = ?)";
    }
    $stmt = $conn->prepare($sql);
    if ($officer !== 'admin') {
        $stmt->bind_param("s", $officer);
    }
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['total_payments'] ?: 0;
}

// Get total accrued interest relevant to the logged-in manager
function getTotalAccruedInterestByManager() {
    global $conn;
    $officer = getOnboardingOfficer();
    if ($officer === 'admin') {
        $sql = "SELECT SUM(interest_income) AS total_accrued_interest 
                FROM expected_payments 
                WHERE payment_date <= CURDATE() AND payment_status = 'not paid'";
    } else {
        $sql = "SELECT SUM(interest_income) AS total_accrued_interest 
                FROM expected_payments 
                WHERE loan_id IN (SELECT loan_id FROM loan_info WHERE onboarding_officer = ?) 
                AND payment_date <= CURDATE() AND payment_status = 'not paid'";
    }
    $stmt = $conn->prepare($sql);
    if ($officer !== 'admin') {
        $stmt->bind_param("s", $officer);
    }
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['total_accrued_interest'] ?: 0;
}

// Get total earned interest relevant to the logged-in manager
function getTotalEarnedInterestByManager() {
    global $conn;

    // Get the current onboarding officer
    $officer = getOnboardingOfficer();

    // Check if the logged-in user is admin
    if ($officer === 'admin') {
        // Admin sees total earned interest across all loans
        $sql = "SELECT SUM(interest_income) AS total_earned_interest 
                FROM expected_payments";
        $stmt = $conn->prepare($sql);
    } else {
        // Non-admin users see interest only for their loans
        $sql = "SELECT SUM(interest_income) AS total_earned_interest 
                FROM expected_payments 
                WHERE loan_id IN (SELECT loan_id FROM loan_info WHERE onboarding_officer = ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $officer);
    }

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    // Return total earned interest or 0 if null
    return $result['total_earned_interest'] ?: 0;
}


function getTotalLoansDisbursedTodayForManager() {
    global $conn;
    $officer = getOnboardingOfficer();
    $today = date('Y-m-d');

    if ($officer === 'admin') {
        // Admin query only requires one parameter ($today)
        $stmt = $conn->prepare("SELECT COUNT(*) AS total_loans FROM loan_info WHERE DATE(created_at) = ?");
        $stmt->bind_param("s", $today);
    } else {
        // Manager query requires both $today and $officer parameters
        $stmt = $conn->prepare("SELECT COUNT(*) AS total_loans FROM loan_info WHERE DATE(created_at) = ? AND onboarding_officer = ?");
        $stmt->bind_param("ss", $today, $officer);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['total_loans'] ?? 0;
}

function getTotalAmountDisbursedTodayForManager() {
    global $conn;
    $officer = getOnboardingOfficer();
    $today = date('Y-m-d');

    if ($officer === 'admin') {
        // Admin query only requires one parameter ($today)
        $stmt = $conn->prepare("SELECT SUM(requested_amount) AS total_amount FROM loan_info WHERE DATE(created_at) = ?");
        $stmt->bind_param("s", $today);
    } else {
        // Manager query requires both $today and $officer parameters
        $stmt = $conn->prepare("SELECT SUM(requested_amount) AS total_amount FROM loan_info WHERE DATE(created_at) = ? AND onboarding_officer = ?");
        $stmt->bind_param("ss", $today, $officer);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['total_amount'] ?? 0;
}

function getTotalInterestEarnedTodayForManager() {
    global $conn;
    $officer = getOnboardingOfficer();
    $today = date('Y-m-d');

    if ($officer === 'admin') {
        // Admin query only requires the $today parameter
        $stmt = $conn->prepare("SELECT SUM(interest_income) AS total_interest FROM expected_payments ep JOIN loan_info li ON ep.loan_id = li.loan_id WHERE DATE(ep.payment_date) = ?");
        $stmt->bind_param("s", $today);
    } else {
        // Manager query requires both $today and $officer parameters
        $stmt = $conn->prepare("SELECT SUM(interest_income) AS total_interest FROM expected_payments ep JOIN loan_info li ON ep.loan_id = li.loan_id WHERE DATE(ep.payment_date) = ? AND li.onboarding_officer = ?");
        $stmt->bind_param("ss", $today, $officer);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['total_interest'] ?? 0;
}

function getTotalPaymentsReceivedTodayForManager() {
    global $conn;
    $officer = getOnboardingOfficer();
    $today = date('Y-m-d');

    if ($officer === 'admin') {
        // Query for admin, only requires the $today parameter
        $stmt = $conn->prepare("SELECT SUM(p.amount) AS total_payments FROM payments p JOIN loan_info li ON p.loan_id = li.loan_id WHERE DATE(p.payment_date) = ?");
        $stmt->bind_param("s", $today);
    } else {
        // Query for manager, requires both $today and $officer parameters
        $stmt = $conn->prepare("SELECT SUM(p.amount) AS total_payments FROM payments p JOIN loan_info li ON p.loan_id = li.loan_id WHERE DATE(p.payment_date) = ? AND li.onboarding_officer = ?");
        $stmt->bind_param("ss", $today, $officer);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['total_payments'] ?? 0;
}

function getTotalAmountDisbursedByManager() {
    global $conn;
    $officer = getOnboardingOfficer();

    if ($officer === 'admin') {
        // For admin, fetch total amount disbursed across all loans
        $stmt = $conn->prepare("SELECT SUM(requested_amount) AS total_amount_disbursed FROM loan_info");
    } else {
        // For manager, fetch total amount disbursed for loans they onboarded
        $stmt = $conn->prepare("SELECT SUM(requested_amount) AS total_amount_disbursed FROM loan_info WHERE onboarding_officer = ?");
        $stmt->bind_param("s", $officer);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['total_amount_disbursed'] ?? 0;
}


//functions for data tables used

//all transactions data table
function getTransactionsData() {
    global $conn;
    $sql = "
        SELECT 
            p.loan_id, 
            c.first_name, 
            p.amount AS received_payment, 
            p.payment_date AS transaction_date, 
            p.transaction_reference
        FROM 
            payments p
        JOIN 
            loan_info l ON p.loan_id = l.loan_id
        JOIN 
            clients c ON l.client_id = c.client_id
        ORDER BY 
            p.payment_date DESC
    ";
    $result = $conn->query($sql);
    $transactions = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
        }
    }
    return $transactions;
}

//manager-specific transactions data table
function getManagerTransactionsData($onboardingOfficer) {
    global $conn;
    $sql = "
        SELECT 
            p.loan_id, 
            c.first_name, 
            p.amount AS received_payment, 
            p.payment_date AS transaction_date, 
            p.transaction_reference 
        FROM 
            payments p
        JOIN 
            loan_info l ON p.loan_id = l.loan_id
        JOIN 
            clients c ON l.client_id = c.client_id
        WHERE 
            l.onboarding_officer = ?
        ORDER BY 
            p.payment_date DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $onboardingOfficer);
    $stmt->execute();
    $result = $stmt->get_result();
    $transactions = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
        }
    }
    $stmt->close();
    return $transactions;
}

//Loan Identification Functions

function getClientDataFromLoanID($loanId) {
    global $conn; // Use the global $conn for database connection

    // SQL query to get client data by loan_id using national_id as the common field
    $query = "
        SELECT clients.* 
        FROM clients 
        INNER JOIN loan_info ON loan_info.national_id = clients.national_id 
        WHERE loan_info.loan_id = ?
    ";

    // Prepare the SQL statement
    if ($stmt = $conn->prepare($query)) {
        // Bind the loan_id parameter
        $stmt->bind_param("s", $loanId);
        
        // Execute the query
        if ($stmt->execute()) {
            // Fetch the result
            $result = $stmt->get_result();
            
            // If client data is found, return it as an associative array
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            } else {
                return null; // No client data found for this loan_id
            }
        } else {
            // Handle execution errors
            return null;
        }
    } else {
        // Handle statement preparation errors
        return null;
    }
}


function getClientName($loan_id) {
    global $conn;

    // Step 1: Retrieve the client_id from the loan_info table using loan_id
    $stmt = $conn->prepare("SELECT client_id FROM loan_info WHERE loan_id = ?");
    $stmt->bind_param("s", $loan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $client_id = $result->fetch_assoc()['client_id'] ?? null;

    if ($client_id) {
        // Step 2: Retrieve the first and last names from the clients table using client_id
        $stmt = $conn->prepare("SELECT first_name, last_name FROM clients WHERE client_id = ?");
        $stmt->bind_param("s", $client_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $client = $result->fetch_assoc();

        if ($client) {
            // Step 3: Concatenate first_name and last_name to form the full client name
            $clientName = $client['first_name'] . ' ' . $client['last_name'];
            return $clientName;
        }
    }

    // Return null if client information is not found
    return null;
}

function getClientAllNames($clientId) {
    global $conn;

    // Trim and sanitize the input
    $clientId = trim($clientId);

    // Prepare the statement
    $stmt = $conn->prepare("SELECT first_name, last_name FROM clients WHERE client_id = ?");
    if ($stmt === false) {
        // Return null if the statement could not be prepared
        return null;
    }

    // Bind parameters and execute
    $stmt->bind_param("s", $clientId);
    if (!$stmt->execute()) {
        // Handle statement execution failure
        $stmt->close();
        return null;
    }

    // Fetch result
    $result = $stmt->get_result();
    $client = $result->fetch_assoc();
    
    // Close the statement
    $stmt->close();

    // Check if client data was found and concatenate names
    if ($client) {
        $firstName = $client['first_name'] ?? '';
        $lastName = $client['last_name'] ?? '';
        
        // Form the full client name
        $clientName = trim($firstName . ' ' . $lastName);

        return $clientName;
    }

    // Return null if no client found
    return null;
}

function getClientData($clientId) {
    global $conn;

    // Prepare the SQL query to retrieve all columns except 'client_id'
    $stmt = $conn->prepare("
        SELECT first_name, last_name, email, phone_number, county, town_centre, national_id, 
        id_photo_front, id_photo_back, work_economic_activity, residence_nearest_building, 
        residence_nearest_road, date_of_onboarding, onboarding_officer, age, gender, 
        next_of_kin_name, next_of_kin_phone_number, next_of_kin_relation, created_at 
        FROM clients WHERE client_id = ?
    ");
    
    // Bind the clientId as a parameter
    $stmt->bind_param("s", $clientId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the result as an associative array
    $clientData = $result->fetch_assoc();
    
    // Return the fetched data
    return $clientData;
}

function getTotalClientLoanData($clientId) {
    global $conn;

    // Initialize variables
    $total_principle = 0;
    $total_payments = 0;
    $total_expected_payments = 0;
    $loan_balance = 0;

    // 1. Retrieve total loan principal (requested amount) for the client
    $sql = "SELECT SUM(requested_amount) AS total_principle FROM loan_info WHERE client_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $clientId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $total_principle = $result['total_principle'] ?: 0;  // Handle null values
    $stmt->close();

    // 2. Retrieve the national_id of the client (assuming 1 client has 1 national_id)
    $sql = "SELECT national_id FROM loan_info WHERE client_id = ? LIMIT 1";  // Limit to 1 result
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $clientId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $nationalId = $result['national_id'] ?? null;  // Handle case where no national_id exists
    $stmt->close();

    // 3. Retrieve total payments made by client (if national_id exists)
    if ($nationalId) {
        $sql = "SELECT SUM(amount) AS total_payments FROM payments WHERE national_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nationalId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $total_payments = $result['total_payments'] ?: 0;  // Handle null values
        $stmt->close();
    }

    // 4. Retrieve total expected payments for all loans of the client
    $sql = "SELECT SUM(ep.installment_amount) AS expected_total_payments 
            FROM expected_payments ep 
            JOIN loan_info li ON ep.loan_id = li.loan_id 
            WHERE li.client_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $clientId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $total_expected_payments = $result['expected_total_payments'] ?: 0;  // Handle null values
    $stmt->close();

    // 5. Calculate loan balance
    $loan_balance = $total_expected_payments - $total_payments;

    // Return an array with all data
    return [
        'total_principle' => $total_principle,
        'total_payments' => $total_payments,
        'total_expected_payments' => $total_expected_payments,
        'loan_balance' => $loan_balance,
    ];
}


function getPhoneNumber($loan_id) {
    global $conn;

    // First, get the client_id using the loan_id
    $stmt = $conn->prepare("SELECT client_id FROM loan_info WHERE loan_id = ?");
    $stmt->bind_param("s", $loan_id); // assuming loan_id is a string
    $stmt->execute();
    $result = $stmt->get_result();
    $clientId = $result->fetch_assoc()['client_id'] ?? null;

    // If client_id is not found, return null
    if (!$clientId) {
        return null;
    }

    // Now, get the phone number using the client_id
    $stmt = $conn->prepare("SELECT phone_number FROM clients WHERE client_id = ?");
    $stmt->bind_param("i", $clientId); // assuming client_id is an integer
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Return the phone number or null if not found
    return $result->fetch_assoc()['phone_number'] ?? null;
}

function getLoanPrincipal($loan_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT requested_amount FROM loan_info WHERE loan_id = ?");
    $stmt->bind_param("s", $loan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['requested_amount'] ?? null;
}

function getLoanInterestRate() {
    companySettings();
    return $interestRate ?? null;
}

function getLoanStartDate($loan_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT created_at FROM loan_info WHERE loan_id = ?");
    $stmt->bind_param("s", $loan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['created_at'] ?? null;
}

function getLoanEndDate($loan_id) {
    global $conn;

    // Query to get duration and duration_period
    $stmt = $conn->prepare("SELECT created_at, duration, duration_period FROM loan_info WHERE loan_id = ?");
    $stmt->bind_param("s", $loan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $loanData = $result->fetch_assoc();

    if (!$loanData) {
        return null; // Return null if no loan data found
    }

    // Calculate the loan end date based on duration and duration_period
    $duration = (int) $loanData['duration'];
    $durationPeriod = strtolower($loanData['duration_period']);
    $startDate = $loanData['created_at']; // The start date (created_at)

    // Determine the appropriate SQL interval based on the duration_period
    switch ($durationPeriod) {
        case 'week':
            $interval = "INTERVAL $duration WEEK";
            break;
        case 'month':
            $interval = "INTERVAL $duration MONTH";
            break;
        case 'year':
            $interval = "INTERVAL $duration YEAR";
            break;
        default:
            // If the period is invalid, assume months as a fallback
            $interval = "INTERVAL $duration MONTH";
            break;
    }

    // Prepare a query to calculate the end date
    $endDateQuery = "SELECT DATE_ADD(?, $interval) AS loan_end_date";
    $stmt = $conn->prepare($endDateQuery);
    $stmt->bind_param("s", $startDate);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc()['loan_end_date'] ?? null;
}


function getLoanSecurityInfo($loan_id) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT 
            collateral_name,
            FORMAT(collateral_value, 2) AS collateral_value,
            collateral_pic1,
            collateral_pic2,
            guarantor1_name,
            guarantor1_phone,
            guarantor2_name,
            guarantor2_phone,
            onboarding_officer
        FROM loan_info
        WHERE loan_id = ?
    ");
    
    $stmt->bind_param("s", $loan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc() ?? null;
}

function getLoanPaymentsInfo($loan_id) {
    global $conn; // Assuming $conn is your database connection

    // Prepare the SQL query to fetch payments for the given loan_id
    $sql = "SELECT payment_date, transaction_reference, payment_mode, amount
            FROM payments
            WHERE loan_id = ?
            ORDER BY payment_date ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $loan_id); // "s" for string, since loan_id is varchar
    $stmt->execute();
    $result = $stmt->get_result();

    $payments_info = [];
    $total_payments = 0;

    // Fetch results and group them by payment_date
    while ($row = $result->fetch_assoc()) {
        $payment_date = $row['payment_date'];
        $payment_details = [
            'transaction_reference' => $row['transaction_reference'],
            'payment_mode' => $row['payment_mode'],
            'amount' => $row['amount']
        ];

        // Add the amount to the total_payments
        $total_payments += $row['amount'];

        // Group payments by payment_date
        if (!isset($payments_info[$payment_date])) {
            $payments_info[$payment_date] = [];
        }
        $payments_info[$payment_date][] = $payment_details;
    }

    // Add total_payments to the array
    $payments_info['total_payments'] = $total_payments;

    return $payments_info;
}


function getLoanExpectedPaymentsInfo($loan_id) {
    // Access the global database connection
    global $conn;

    // Prepare the SQL query to fetch expected payments for the given loan ID
    $sql = "SELECT payment_date, installment_amount, payment_status, interest_income, principal 
            FROM expected_payments 
            WHERE loan_id = ?
            ORDER BY payment_date ASC";

    // Prepare and execute the query
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $loan_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Initialize an array to store the grouped payment info
    $expected_payments_info = [];
    $total_expected_payments = 0;

    // Loop through the results and group them by payment_date
    while ($row = $result->fetch_assoc()) {
        $payment_date = $row['payment_date'];
        $installment_amount = $row['installment_amount'];

        // Add the payment info to the corresponding date group
        $expected_payments_info[$payment_date][] = [
            'installment_amount' => $installment_amount,
            'payment_status' => $row['payment_status'],
            'interest_income' => $row['interest_income'],
            'principal' => $row['principal']
        ];

        // Add to the total expected payments
        $total_expected_payments += $installment_amount;
    }

    // Add the total expected payments to the result array
    $expected_payments_info['total_expected_payments'] = $total_expected_payments;

    // Return the grouped expected payments info
    return $expected_payments_info;
}



//all loan details
function getLoanStatusInfo($loan_info) {

    global $conn;

    $loan_id = $loan_info['loan_id'];
    $loan_status_info = [];

        // Activation date
        $activation_date_query = $conn->prepare("SELECT created_at FROM loan_info WHERE loan_id = ? ");
        $activation_date_query->bind_param("s", $loan_id);
        $activation_date_query->execute();
        $activation_date_result = $activation_date_query->get_result();
        $activation_date_row = $activation_date_result->fetch_assoc();
        $activation_date = $activation_date_row ? $activation_date_row['created_at'] : null;
    

    $loanBalance = calculateLoanBalance($loan_id);
    $loanInstallments = calculateInstallments($loan_id);
    $loanAccruedInterest = getAllAccruedInterest($loan_id);
    $loanNextPaymentDate = getNextPaymentDate($loan_id);
    $loanNextPaymentAmount = getNextPaymentAmount($loan_id);
    $loanPeriodsPassed = getPeriodsPassed($loan_id);
    $loanPeriodsRemaining = getPeriodsRemaining($loan_id);
    $loanClearAmountToday = calculateClearTodayAmount($loan_id);
    $loanAllPayments = getAllPayments($loan_id);

    $loan_status_info = [
        'Activation Date' => $activation_date,
        'Loan balance' => number_format($loanBalance, 2),
        'Equal Installments' => number_format($loanInstallments, 2),
        'Accrued Interest' => number_format($loanAccruedInterest, 2),
        'Next Payment Date' => $loanNextPaymentDate,
        'Next Payment Amount' => number_format($loanNextPaymentAmount, 2),
        'Periods past since activation' => $loanPeriodsPassed,
        'Periods remaining to completion date' => $loanPeriodsRemaining,
        'If client wants to clear loan today' => number_format($loanClearAmountToday, 2),
        'Total Payments' => number_format($loanAllPayments, 2)
    ];

    return $loan_status_info;

}



function getLoanDetails($loan_id) {
    // Core Calculations
    $loanBalance = calculateLoanBalance($loan_id); // implement logic
    $equalInstallments = calculateInstallments($loan_id); // implement logic
    $accruedInterest = calculateAccruedInterest($loan_id); // implement logic
    $nextPaymentDate = getNextPaymentDate($loan_id); // implement logic
    $nextPaymentAmount = getNextPaymentAmount($loan_id); // implement logic
    $periodsPassed = getPeriodsPassed($loan_id); // implement logic
    $periodsRemaining = getPeriodsRemaining($loan_id); // implement logic
    $clearToday = calculateClearTodayAmount($loan_id); // implement logic
    $clearedStatus = isLoanCleared($loan_id); // implement logic

    // Business Reports
    $allAccruedInterest = getAllAccruedInterest($loan_id); // implement logic
    $payments = getTotalPayments($loan_id); // implement logic
    $loansDisbursed = getLoansDisbursed($loan_id); // implement logic
    $countLoans = countLoans(); // implement logic
    $countClients = countClients(); // implement logic
    $stageStatus = getStageStatus($loan_id); // implement logic
    $loanBookStatus = getLoanBookStatus($loan_id); // implement logic

    // Identification
    $clientName = getClientName($loan_id); // implement logic
    $phoneNumber = getPhoneNumber($loan_id); // implement logic
    $loanPrincipal = getLoanPrincipal($loan_id); // implement logic
    $loanInterestRate = getLoanInterestRate($loan_id); // implement logic
    $loanStartDate = getLoanStartDate($loan_id); // implement logic
    $loanEndDate = getLoanEndDate($loan_id); // implement logic

    // Loan Info
    //$national_id = getNationalID($loan_id); // implement logic
    //$loan_purpose = getLoanPurpose($loan_id); // implement logic
    //$duration = getLoanDuration($loan_id); // implement logic
    //$date_applied = getDateApplied($loan_id); // implement logic
    //$interest_rate_period = getInterestRatePeriod($loan_id); // implement logic
    //$duration_period = getDurationPeriod($loan_id); // implement logic

    // Loan Security Info
    $loanSecurityInfo = getLoanSecurityInfo($loan_id);

    return compact(
        'loanBalance', 'equalInstallments', 'accruedInterest', 'nextPaymentDate', 
        'nextPaymentAmount', 'periodsPassed', 'periodsRemaining', 'clearToday', 
        'clearedStatus', 'allAccruedInterest', 'payments', 'loansDisbursed', 'countLoans', 
        'countClients', 'stageStatus', 'loanBookStatus', 'clientName', 'phoneNumber', 
        'loanPrincipal', 'loanInterestRate', 'loanStartDate', 'loanEndDate', 'loanSecurityInfo'
    );
}

//FUNCTIONS FOR NOTIFICATIONS MODULE

function addNotification($userId, $heading, $message) {
    global $conn;
    // Modify the SQL query to include heading
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, heading, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $heading, $message); // Bind user_id, heading, and message
    $stmt->execute();
    $stmt->close();
}

function getUserNotifications($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    return $notifications;
}



// After the code block
//$currentMemory = memory_get_usage();
//$currentPeakMemory = memory_get_peak_usage();

//echo "Memory used by this block: " . ($currentMemory - $startMemory) . " bytes\n";
//echo "Peak memory usage during this block: " . ($currentPeakMemory - $startPeakMemory) . " bytes\n";
?>
