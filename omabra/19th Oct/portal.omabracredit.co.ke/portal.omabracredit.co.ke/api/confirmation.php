<?php
// Include database connection and functions (assuming config.php has the connection details)
require '../config.php';
include '../functions-tena.php';

companySettings();

// Log the raw input data from Safaricom to verify the callback
$callbackData = file_get_contents('php://input');
file_put_contents('confirmation_callback_log.txt', "Raw Callback Data: " . $callbackData . "\n", FILE_APPEND);
if (empty($callbackData)) {
    file_put_contents('confirmation_errors.txt', "Empty callback received\n", FILE_APPEND);
    exit;
}


// Decode the JSON data
$callbackJSON = json_decode($callbackData, true);

// Check if JSON decoding was successful
if ($callbackJSON === null) {
    file_put_contents('confirmation_errors.txt', "JSON Decode Error: " . json_last_error_msg() . "\n", FILE_APPEND);
    exit; // Stop further processing if JSON is invalid
}

// Log the decoded JSON for debugging
file_put_contents('confirmation_callback_log.txt', "Decoded JSON: " . print_r($callbackJSON, true) . "\n", FILE_APPEND);

// Safely access required fields from the JSON data
$mpesaReceiptNumber = $callbackJSON['TransID'] ?? null;
$amount = $callbackJSON['TransAmount'] ?? null;
$transactionDate = $callbackJSON['TransTime'] ?? null;
$phoneNumber = $callbackJSON['MSISDN'] ?? null;
$loanId = $callbackJSON['BillRefNumber'] ?? null; // Assuming this is provided as part of the reference

// Validate required data
if (!$mpesaReceiptNumber || !$amount || !$transactionDate || !$phoneNumber || !$loanId) {
    file_put_contents('confirmation_errors.txt', "Missing required data in the callback\n", FILE_APPEND);
    exit; // Stop further processing if any required data is missing
}

// Convert phone number to string (ensure consistency)
$phoneNumber = (string)$phoneNumber;

// Assuming you have a function to get the `loan_id` using the `BillRefNumber`
// $loanId = getLoanIdByReference($nationalId);

// Check if the loan_id exists
if (!$loanId) {
    file_put_contents('confirmation_errors.txt', "No matching loan_id for BillRefNumber: $loanId\n", FILE_APPEND);
    exit; // Stop further processing if no loan_id found
}

// Check if this transaction already exists in the payments table (to avoid duplicate entries)
$checkSql = "SELECT COUNT(*) FROM payments WHERE transaction_reference = ?";
$checkStmt = $conn->prepare($checkSql);

if ($checkStmt) {
    $checkStmt->bind_param("s", $mpesaReceiptNumber);
    $checkStmt->execute();
    $checkStmt->bind_result($transactionExists);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($transactionExists > 0) {
        // Log duplicate transaction and stop further execution
        file_put_contents('confirmation_errors.txt', "Duplicate transaction: $mpesaReceiptNumber\n", FILE_APPEND);
        echo json_encode(['status' => 'error', 'message' => 'Duplicate transaction']);
        exit();
    } else {
        // Insert the payment details into the payments table
        $sql = "INSERT INTO payments (loan_id, transaction_reference, payment_mode, payment_date, amount, national_id)
                VALUES (?, ?, 'MPESA', ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sssds", $loanId, $mpesaReceiptNumber, $transactionDate, $amount, $phoneNumber);

            if ($stmt->execute()) {
                // Log successful insertion
                file_put_contents('confirmation_callback_log.txt', "Payment recorded successfully: LoanID: $loanId, TransactionRef: $mpesaReceiptNumber\n", FILE_APPEND);
                echo json_encode(['status' => 'success', 'message' => 'Payment recorded successfully']);

                                                    // Send email to client with details of the transaction
                                                    $requestedAmount = getRequestedAmountByLoanId($loanId);
                                                    $balance = calculateLoanBalance($loanId);
                                                    $clientName = getClientName($loanId);
                                                    $clientId = getClientIdByPhoneNumber($phoneNumber);
                                                    $email = getClientData($clientId)['email'];
                                                    $to = $email;
                                                    $subject = "Transaction Status: Received";
                                                    $message = "Dear $clientName,\n\n";
                                                    $message .= "Thank you for paying. Below are the details:\n";
                                                    $message .= "Loan Amount:  $requestedAmount\n";
                                                    $message .= "Received Amount: $amount\n";
                                                    $message .= "Reference: $mpesaReceiptNumber\n";
                                                    $message .= "Pending Balance: $balance\n\n";
                                                    $message .= "Payment Procedure:\n";
                                                    $message .= "Option 1: Make automatic payments via MPESA by logging in to your client's portal at https://portal.omabracredit.co.ke/ and follow the instructions on your dashboard.\n";
                                                    $message .= "Option 2: Make payments to $bankName, No. $accountNumber, Account: $loanId, and this should read $payeeName as the payee.\n";
                                                    $message .= "Option 3: Make payments to Equity Bank at: Company: Omabra Limited Account Number: 0260272825664\n\n";
                                                    $message .= "Best regards,\nLoan Administrator";
                            
                                                    sendEmail($to, $subject, $message);

                                                    file_put_contents('confirmation_callback_log.txt', "Before formatting phone number\n", FILE_APPEND);
                
                                                    $atPhoneNumber = formatPhoneNumber($phoneNumber); // Client's phone number
                                                    $smsMessage = "Dear $clientName, your payment for loan $loanId has been received. Your pending balance is Kshs $balance .Thank you for choosing us!";
                                                    sendSMS($atPhoneNumber, $smsMessage);
                                                            
                                                    // Insert notification for the client
                                                    $clientQuery = "SELECT user_id FROM users WHERE file_no = '$clientId'";
                                                    $clientResult = $conn->query($clientQuery);
                            
                                                    if ($clientResult && $clientResult->num_rows > 0) {
                                                        $clientRow = $clientResult->fetch_assoc();
                                                        $clientUserId = $clientRow['user_id'];
                                                        $clientNotificationHeading = "Payment Received for $clientName";
                                                        $clientNotificationMessage = "Hi, $clientName! Your payment of $amount has been received.";
                                                        addNotification($clientUserId, $clientNotificationHeading, $clientNotificationMessage);
                                                    }
                        
                                                    // Insert notification for the manager/admin who is handling the client
                                                    $onboardingOfficer = getOnboardingOfficerByLoanId($loanId);
                                                    if ($onboardingOfficer != 'admin') {
                                                        $managerId = getManagerIdByEmail($onboardingOfficer);
                                                        $managerQuery = "SELECT user_id FROM users WHERE file_no = '$managerId'";
                                                        $managerResult = $conn->query($managerQuery);
                                
                                                        if ($managerResult && $managerResult->num_rows > 0) {
                                                            $managerRow = $managerResult->fetch_assoc();
                                                            $managerUserId = $managerRow['user_id'];
                                                            $managerNotificationHeading = "Payment Received for $clientName";
                                                            $managerNotificationMessage = "$clientName's payment of $amount has been recorded.";
                                                            addNotification($managerUserId, $managerNotificationHeading, $managerNotificationMessage);
                                                        }
                                                    }
                        
                                                    // Insert notifications for all admin users
                                                    $adminQuery = "SELECT user_id FROM users WHERE role = 'admin'";
                                                    $adminResult = $conn->query($adminQuery);
                            
                                                    while ($admin = $adminResult->fetch_assoc()) {
                                                        $adminUserId = $admin['user_id'];
                                                        $adminNotificationHeading = "Payment Received for $clientName";
                                                        $adminNotificationMessage = "$clientName has made a payment of $amount . Please review the transaction.";
                                                        addNotification($adminUserId, $adminNotificationHeading, $adminNotificationMessage);
                                                    }
                

            } else {
                // Log database insertion error
                file_put_contents('confirmation_errors.txt', "Database Insertion Error: " . $stmt->error . "\n", FILE_APPEND);
                echo json_encode(['status' => 'error', 'message' => 'Failed to record payment']);
            }
            $stmt->close();
        } else {
            // Log preparation error for the insert statement
            file_put_contents('confirmation_errors.txt', "Database Prepare Error: " . $conn->error . "\n", FILE_APPEND);
        }
    }
} else {
    // Log preparation error for the select statement
    file_put_contents('confirmation_errors.txt', "Database Prepare Error for Select: " . $conn->error . "\n", FILE_APPEND);
}

?>
