<?php
require_once("config.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';


function generatePassword() {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return substr(str_shuffle($chars), 0, 10);
}

function managerExists($conn, $email, $phone, $nationalId) {
    $stmt = $conn->prepare("SELECT * FROM managers WHERE email = ? OR phone_number = ? OR national_id = ?");
    $stmt->bind_param("sss", $email, $phone, $nationalId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

function clientExists($conn, $email, $phone, $nationalId) {
    $stmt = $conn->prepare("SELECT * FROM clients WHERE email = ? OR phone_number = ? OR national_id = ?");
    $stmt->bind_param("sss", $email, $phone, $nationalId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
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

// Function to generate a summary report for business performance
function generateBusinessPerformanceReport($conn) {
    // Total loans disbursed
    $totalLoansSql = "
        SELECT COUNT(*) AS total_loans, SUM(requested_amount) AS total_amount_disbursed
        FROM loan_info WHERE loan_status IN ('Active', 'Cleared', 'Defaulted')
    ";
    $totalLoansResult = $conn->query($totalLoansSql);
    $totalLoansData = $totalLoansResult->fetch_assoc();

    // Total interest earned
    $totalInterestSql = "
        SELECT SUM(
            CASE
                WHEN interest_rate_period = 'Yearly' THEN requested_amount * (interest_rate / 100) * duration * 
                    CASE duration_period
                        WHEN 'Year' THEN 1
                        WHEN 'Month' THEN 1/12
                        WHEN 'Week' THEN 1/52
                    END
                WHEN interest_rate_period = 'Monthly' THEN requested_amount * (interest_rate / 100) * duration * 
                    CASE duration_period
                        WHEN 'Year' THEN 12
                        WHEN 'Month' THEN 1
                        WHEN 'Week' THEN 1/4
                    END
                WHEN interest_rate_period = 'Weekly' THEN requested_amount * (interest_rate / 100) * duration * 
                    CASE duration_period
                        WHEN 'Year' THEN 52
                        WHEN 'Month' THEN 4
                        WHEN 'Week' THEN 1
                    END
            END
        ) AS total_interest_earned
        FROM loan_info
        WHERE loan_status IN ('Active', 'Cleared', 'Defaulted')
    ";
    $totalInterestResult = $conn->query($totalInterestSql);
    $totalInterestData = $totalInterestResult->fetch_assoc();

    // Total payments received
    $totalPaymentsSql = "SELECT SUM(amount) AS total_payments_received FROM payments";
    $totalPaymentsResult = $conn->query($totalPaymentsSql);
    $totalPaymentsData = $totalPaymentsResult->fetch_assoc();

    // Return the report data
    return [
        'total_loans' => $totalLoansData['total_loans'],
        'total_amount_disbursed' => $totalLoansData['total_amount_disbursed'],
        'total_interest_earned' => $totalInterestData['total_interest_earned'],
        'total_payments_received' => $totalPaymentsData['total_payments_received']
    ];
}

//repetitive function
function calculateAccruedInterest($requested_amount, $interest_rate, $interest_rate_period, $created_at, $duration_period) {
    $current_date = new DateTime();
    $loan_start_date = new DateTime($created_at);
    $interval = $loan_start_date->diff($current_date);

    // Determine the number of periods that have elapsed
    switch ($interest_rate_period) {
        case 'Yearly':
            $elapsed_periods = $interval->y + $interval->m / 12 + $interval->d / 365;
            break;
        case 'Monthly':
            $elapsed_periods = $interval->y * 12 + $interval->m + $interval->d / 30;
            break;
        case 'Weekly':
            $elapsed_periods = $interval->days / 7;
            break;
        default:
            $elapsed_periods = 0;
            break;
    }

    // Calculate accrued interest
    $accrued_interest = $requested_amount * ($interest_rate / 100) * $elapsed_periods;

    return $accrued_interest;
}

function getAccruedInterestData($conn) {
    $sql = "SELECT loan_id, client_id, requested_amount, interest_rate, interest_rate_period, created_at, duration, duration_period FROM active_loans WHERE loan_status = 'Active'";
    $result = $conn->query($sql);
    $accruedInterestData = [];

    while ($row = $result->fetch_assoc()) {
        $accrued_interest = calculateAccruedInterest(
            $row['requested_amount'],
            $row['interest_rate'],
            $row['interest_rate_period'],
            $row['created_at'],
            $row['duration_period']
        );

        $accruedInterestData[] = [
            'loan_id' => $row['loan_id'],
            'client_id' => $row['client_id'],
            'requested_amount' => $row['requested_amount'],
            'interest_rate' => $row['interest_rate'],
            'interest_rate_period' => $row['interest_rate_period'],
            'created_at' => $row['created_at'],
            'accrued_interest' => $accrued_interest
        ];
    }

    return $accruedInterestData;
}

function generateTodayBusinessPerformanceReport($conn) {
    $today = date('Y-m-d');

    // Total loans disbursed today
    $totalLoansTodaySql = "
        SELECT COUNT(*) AS total_loans_today, SUM(requested_amount) AS total_amount_disbursed_today
        FROM active_loans
        WHERE DATE(created_at) = '$today'
    ";
    $totalLoansTodayResult = $conn->query($totalLoansTodaySql);
    $totalLoansTodayData = $totalLoansTodayResult->fetch_assoc();

    // Total interest earned today
    $totalInterestTodaySql = "
        SELECT SUM(
            CASE
                WHEN interest_rate_period = 'Yearly' THEN requested_amount * (interest_rate / 100) / 365
                WHEN interest_rate_period = 'Monthly' THEN requested_amount * (interest_rate / 100) / 30
                WHEN interest_rate_period = 'Weekly' THEN requested_amount * (interest_rate / 100) / 7
            END
        ) AS total_interest_earned_today
        FROM active_loans
        WHERE DATE(created_at) = '$today' AND loan_status IN ('Active', 'Cleared')
    ";
    $totalInterestTodayResult = $conn->query($totalInterestTodaySql);
    $totalInterestTodayData = $totalInterestTodayResult->fetch_assoc();

    // Total payments received today
    $totalPaymentsTodaySql = "SELECT SUM(amount) AS total_payments_received_today FROM payments WHERE DATE(payment_date) = '$today'";
    $totalPaymentsTodayResult = $conn->query($totalPaymentsTodaySql);
    $totalPaymentsTodayData = $totalPaymentsTodayResult->fetch_assoc();

    return [
        'total_loans_today' => $totalLoansTodayData['total_loans_today'],
        'total_amount_disbursed_today' => $totalLoansTodayData['total_amount_disbursed_today'],
        'total_interest_earned_today' => $totalInterestTodayData['total_interest_earned_today'],
        'total_payments_received_today' => $totalPaymentsTodayData['total_payments_received_today']
    ];
}

function generateManagerBusinessPerformanceReport($conn, $onboardingOfficer) {
    // Total loans disbursed by the specific manager
    $totalLoansSql = "
        SELECT COUNT(*) AS total_loans, SUM(requested_amount) AS total_amount_disbursed
        FROM active_loans
        WHERE onboarding_officer = '$onboardingOfficer'
    ";
    $totalLoansResult = $conn->query($totalLoansSql);
    $totalLoansData = $totalLoansResult->fetch_assoc();

    // Total interest earned by the specific manager
    $totalInterestSql = "
        SELECT SUM(
            CASE
                WHEN interest_rate_period = 'Yearly' THEN requested_amount * (interest_rate / 100) / 365 * DATEDIFF(CURRENT_DATE, created_at)
                WHEN interest_rate_period = 'Monthly' THEN requested_amount * (interest_rate / 100) / 30 * DATEDIFF(CURRENT_DATE, created_at)
                WHEN interest_rate_period = 'Weekly' THEN requested_amount * (interest_rate / 100) / 7 * DATEDIFF(CURRENT_DATE, created_at)
            END
        ) AS total_interest_earned
        FROM active_loans
        WHERE onboarding_officer = '$onboardingOfficer' AND loan_status IN ('Active', 'Cleared', 'Defaulted')
    ";
    $totalInterestResult = $conn->query($totalInterestSql);
    $totalInterestData = $totalInterestResult->fetch_assoc();

    // Total payments received
    $totalPaymentsSql = "
        SELECT SUM(amount) AS total_payments_received 
        FROM payments 
        WHERE loan_id IN (SELECT loan_id FROM active_loans WHERE onboarding_officer = '$onboardingOfficer')
    ";
    $totalPaymentsResult = $conn->query($totalPaymentsSql);
    $totalPaymentsData = $totalPaymentsResult->fetch_assoc();

    return [
        'total_loans' => $totalLoansData['total_loans'],
        'total_amount_disbursed' => $totalLoansData['total_amount_disbursed'],
        'total_interest_earned' => $totalInterestData['total_interest_earned'],
        'total_payments_received' => $totalPaymentsData['total_payments_received']
    ];
}

function generateTodayManagerBusinessPerformanceReport($conn, $onboardingOfficer) {
    $today = date('Y-m-d');

    // Total loans disbursed today by the specific manager
    $totalLoansTodaySql = "
        SELECT COUNT(*) AS total_loans_today, SUM(requested_amount) AS total_amount_disbursed_today
        FROM active_loans
        WHERE DATE(created_at) = '$today' AND onboarding_officer = '$onboardingOfficer'
    ";
    $totalLoansTodayResult = $conn->query($totalLoansTodaySql);
    $totalLoansTodayData = $totalLoansTodayResult->fetch_assoc();

    // Total interest earned today by the specific manager
    $totalInterestTodaySql = "
        SELECT SUM(
            CASE
                WHEN interest_rate_period = 'Yearly' THEN requested_amount * (interest_rate / 100) / 365
                WHEN interest_rate_period = 'Monthly' THEN requested_amount * (interest_rate / 100) / 30
                WHEN interest_rate_period = 'Weekly' THEN requested_amount * (interest_rate / 100) / 7
            END
        ) AS total_interest_earned_today
        FROM active_loans
        WHERE onboarding_officer = '$onboardingOfficer' AND loan_status IN ('Active')
    ";
    $totalInterestTodayResult = $conn->query($totalInterestTodaySql);
    $totalInterestTodayData = $totalInterestTodayResult->fetch_assoc();

    // Total payments received today
    $totalPaymentsTodaySql = "
        SELECT SUM(amount) AS total_payments_received_today 
        FROM payments 
        WHERE DATE(payment_date) = '$today' AND loan_id IN (SELECT loan_id FROM active_loans WHERE onboarding_officer = '$onboardingOfficer')
    ";
    $totalPaymentsTodayResult = $conn->query($totalPaymentsTodaySql);
    $totalPaymentsTodayData = $totalPaymentsTodayResult->fetch_assoc();

    return [
        'total_loans_today' => $totalLoansTodayData['total_loans_today'],
        'total_amount_disbursed_today' => $totalLoansTodayData['total_amount_disbursed_today'],
        'total_interest_earned_today' => $totalInterestTodayData['total_interest_earned_today'],
        'total_payments_received_today' => $totalPaymentsTodayData['total_payments_received_today']
    ];
}


// functions for data in tables

//transactions data table
function getTransactionsData($conn) {
    $sql = "
        SELECT 
            p.loan_id, 
            c.first_name, 
            p.amount AS received_payment, 
            p.payment_date AS transaction_date, 
            p.transaction_reference, 
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
function getManagerTransactionsData($conn, $onboardingOfficer) {
    $sql = "
        SELECT 
            p.loan_id, 
            c.first_name, 
            p.amount AS received_payment, 
            p.payment_date AS transaction_date, 
            p.transaction_reference, 
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

//repetitive function ... data already in database
function calculateEMI($principal, $interest_rate, $duration, $interest_rate_period, $duration_period) {
    $interest = 0; // Initialize interest to 0

    if ($duration === 0) {
        return 0; // Avoid division by zero and return 0 if duration is 0
    }

    if ($interest_rate_period === 'Yearly') {
        if ($duration_period === 'Year') {
            $interest = $principal * ($interest_rate / 100) * $duration;
        } elseif ($duration_period === 'Month') {
            $interest = $principal * ($interest_rate / 100 / 12) * $duration;
        } elseif ($duration_period === 'Week') {
            $interest = $principal * ($interest_rate / 100 / 52) * $duration;
        }
    } elseif ($interest_rate_period === 'Monthly') {
        if ($duration_period === 'Year') {
            $interest = $principal * ($interest_rate * 12 / 100) * $duration;
        } elseif ($duration_period === 'Month') {
            $interest = $principal * ($interest_rate / 100) * $duration;
        } elseif ($duration_period === 'Week') {
            $interest = $principal * ($interest_rate / 100 / 4) * $duration;
        }
    } elseif ($interest_rate_period === 'Weekly') {
        if ($duration_period === 'Year') {
            $interest = $principal * ($interest_rate * 52 / 100) * $duration;
        } elseif ($duration_period === 'Month') {
            $interest = $principal * ($interest_rate * 4 / 100) * $duration;
        } elseif ($duration_period === 'Week') {
            $interest = $principal * ($interest_rate / 100) * $duration;
        }
    }

    $ei = ($principal + $interest) / $duration;

    return $ei;
}



// Function to calculate loan status info
// Function to calculate loan status info
function getLoanStatusInfo($conn, $loan_info) {
    $loan_id = $loan_info['loan_id'];
    $loan_status_info = [];

    // Activation date
    $activation_date_query = $conn->prepare("SELECT created_at FROM loan_info WHERE loan_id = ? ");
    $activation_date_query->bind_param("s", $loan_id);
    $activation_date_query->execute();
    $activation_date_result = $activation_date_query->get_result();
    $activation_date_row = $activation_date_result->fetch_assoc();
    $activation_date = $activation_date_row ? $activation_date_row['created_at'] : null;

    // Calculate interest accrued
    $principal = $loan_info['requested_amount'];
    $interest_rate = $loan_info['interest_rate'];
    $interest_period = $loan_info['interest_rate_period'];
    $duration = $loan_info['duration'];
    $duration_period = $loan_info['duration_period'];

    $periods_passed = calculatePeriodsPassed($activation_date, $duration_period);
    $interest_accrued = $principal * ($interest_rate / 100) * $periods_passed;

    // Calculate equal installments
    $total_interest = $principal * ($interest_rate / 100) * $duration;
    $total_amount_due = $principal + $total_interest;
    $equal_installments = $total_amount_due / $duration;

    // Calculate total payments
    $total_payments_query = $conn->prepare("SELECT SUM(amount) AS total_payments FROM payments WHERE loan_id = ?");
    $total_payments_query->bind_param("s", $loan_id);
    $total_payments_query->execute();
    $total_payments_result = $total_payments_query->get_result();
    $total_payments_row = $total_payments_result->fetch_assoc();
    $total_payments = $total_payments_row ? $total_payments_row['total_payments'] : 0;

    // Calculate balance
    $balance = $total_amount_due - $total_payments;

    // Calculate if client wants to clear loan today
    $clear_today = $principal + $interest_accrued - $total_payments;

    // Calculate expected loan completion date
    $expected_completion_date = calculateCompletionDate($activation_date, $duration, $duration_period);

    $loan_status_info = [
        'Activation Date' => $activation_date,
        'Interest accrued' => number_format($interest_accrued, 2),
        'Equal Installments' => number_format($equal_installments, 2),
        'Total EIs' => number_format($total_amount_due, 2),
        'EIs due to date' => number_format($equal_installments * $periods_passed, 2),
        'Total Payments' => number_format($total_payments, 2),
        'Balance' => number_format($balance, 2),
        'If client wants to clear loan today' => number_format($clear_today, 2),
        'Expected Loan Completion Date' => $expected_completion_date->format('Y-m-d'),
    ];

    return $loan_status_info;
}

// repetitive function
function getBalance($conn, $loan_info) {
    $loan_id = $loan_info['loan_id'];

    // Activation date
    $activation_date_query = $conn->prepare("SELECT created_at FROM loan_info WHERE loan_id = ? AND loan_status = 'Active'");
    $activation_date_query->bind_param("s", $loan_id);
    $activation_date_query->execute();

    // Calculate interest accrued
    $principal = $loan_info['requested_amount'];
    $interest_rate = $loan_info['interest_rate'];
    $duration = $loan_info['duration'];


    // Calculate equal installments
    $total_interest = $principal * ($interest_rate / 100) * $duration;
    $total_amount_due = $principal + $total_interest;

    // Calculate total payments
    $total_payments_query = $conn->prepare("SELECT SUM(amount) AS total_payments FROM payments WHERE loan_id = ?");
    $total_payments_query->bind_param("s", $loan_id);
    $total_payments_query->execute();
    $total_payments_result = $total_payments_query->get_result();
    $total_payments_row = $total_payments_result->fetch_assoc();
    $total_payments = $total_payments_row ? $total_payments_row['total_payments'] : 0;

    // Calculate balance
    $balance = $total_amount_due - $total_payments;


    return $balance;
}

// repetitive function
// Function to calculate expected loan completion date
function calculateCompletionDate($activation_date, $duration, $duration_period) {
    $start_date = new DateTime($activation_date);

    switch ($duration_period) {
        case 'Week':
            $interval = new DateInterval('P' . $duration * 7 . 'D'); // duration in days
            break;
        case 'Month':
            $interval = new DateInterval('P' . $duration . 'M'); // duration in months
            break;
        case 'Year':
            $interval = new DateInterval('P' . $duration . 'Y'); // duration in years
            break;
        default:
            throw new Exception('Invalid duration period');
    }

    $start_date->add($interval); // Add interval to activation date
    return $start_date;
}

function getPaymentsArray($loan_id, $conn) {
    // Prepare an array to store the payments
    $payments = [];

    // SQL query to fetch payment records for the specific loan
    $sql = "SELECT amount, payment_date FROM payments WHERE loan_id = '$loan_id' ORDER BY payment_date ASC";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Loop through each row and add it to the payments array
        while ($row = $result->fetch_assoc()) {
            $payments[] = [
                'amount' => $row['amount'],
                'date' => $row['payment_date']
            ];
        }
    }

    return $payments;
}

// repetitive function
//function to get next installment payment date
function calculateNextDueDateAndAmount($activation_date, $duration_period, $num_periods, $payments) {
    // Strip the time part of the activation date (work only with the date)
    $activation_date_obj = new DateTime($activation_date);
    $activation_date_obj->setTime(0, 0, 0); // Set time to midnight (00:00:00)
    
    $now = new DateTime(); // Current date
    $now->setTime(0, 0, 0); // Set the current time to midnight for comparison

    $payment_dates = [];
    $emi = 5000; // Example EMI amount (this can be passed as a parameter if dynamic)
    $overpayment = 0; // Track overpayments
    $outstanding_balance = 0;

    // Determine the interval based on the duration period
    if ($duration_period === 'Year') {
        $interval = new DateInterval('P1Y'); // 1 year
    } elseif ($duration_period === 'Month') {
        $interval = new DateInterval('P1M'); // 1 month
    } elseif ($duration_period === 'Week') {
        $interval = new DateInterval('P1W'); // 1 week
    }

    // Generate an array of all payment due dates
    for ($i = 0; $i < $num_periods; $i++) {
        $payment_date = clone $activation_date_obj;
        $payment_dates[] = $payment_date->add($interval)->format('Y-m-d'); // Store only the date
    }

    // Sum up the payments received
    $total_paid = array_sum(array_column($payments, 'amount'));

    // Go through each due date to check payments
    foreach ($payment_dates as $index => $due_date) {
        if (new DateTime($due_date) > $now) {
            // Check the payments and balance
            $paid_towards_this_due = 0;

            foreach ($payments as $payment) {
                if (new DateTime($payment['date']) <= new DateTime($due_date)) {
                    $paid_towards_this_due += $payment['amount'];
                }
            }

            // Handle overpayments or underpayments
            if ($paid_towards_this_due >= ($emi + $outstanding_balance)) {
                $overpayment = $paid_towards_this_due - ($emi + $outstanding_balance);
                $outstanding_balance = 0; // Fully paid, reset balance
                continue; // Move to the next due date
            } else {
                // There is still some balance left to pay
                $outstanding_balance = ($emi + $outstanding_balance) - $paid_towards_this_due;
                return [
                    'next_due_date' => $due_date,
                    'next_due_amount' => $outstanding_balance
                ];
            }
        }
    }

    // If all payments are made and no future payments are due
    return null;
}

//repetitive function
// Function to calculate periods passed based on duration period
function calculatePeriodsPassed($activation_date, $duration_period) {
    $start_date = new DateTime($activation_date);
    $current_date = new DateTime();
    $interval = $current_date->diff($start_date);

    switch ($duration_period) {
        case 'Week':
            return floor($interval->days / 7);
        case 'Month':
            return $interval->m + ($interval->y * 12);
        case 'Year':
            return $interval->y;
        default:
            return 0;
    }
}

// Function to get client's national ID based on user email
function getClientNationalId($email, $conn) {
    $sql = "SELECT national_id FROM clients WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['national_id'];
    }
    return null;
}

function companySettings() {
    global $conn;  

    // Define global variables to store company information
    global $companyName, $companyAddress, $companyEmail, $companyWebsite, $companyPhone, $companyTaxRate;

    // SQL query to get company settings (assuming there's only one row with id = 1)
    $sql = "SELECT company_name, company_address, company_email, company_website, company_phone, tax_rate FROM company_settings WHERE id = 1";
    
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
    } else {
        // Set default values if no data is found
        $companyName = "Not Available";
        $companyAddress = "Not Available";
        $companyEmail = "Not Available";
        $companyWebsite = "Not Available";
        $companyPhone = "Not Available";
        $companyTaxRate = 30;
    }
}

function totalDepreciation(){
    $depreciation = 0;
    return $depreciation;
}

function totalBadDebt(){
    $badDebt = 0;
    return $badDebt;
}

function getTaxRate(){
    $taxRate = 30;
    return $taxRate;
};

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

// notifications module

function addNotification($userId, $heading, $message) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, heading, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $heading, $message);
    $stmt->execute();
    $stmt->close();
}

// Display Notifications
// When users log in, you'll want to display their unread notifications. 
// You can create a function to fetch the notifications from the notifications table.

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

function markNotificationAsRead($notificationId) {
    global $conn;
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
    $stmt->bind_param("i", $notificationId);
    $stmt->execute();
    $stmt->close();
}


?>