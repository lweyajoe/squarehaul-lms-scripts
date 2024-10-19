<?php
// Assuming you have a database connection file included
require '../config.php';

// Log the headers to verify if Safaricom's request reached your server
$headers = apache_request_headers();
file_put_contents('stk_callback_log.txt', "Headers: " . print_r($headers, true) . "\n", FILE_APPEND);

// Log the raw input data from Safaricom to check the callback data
$callbackData = file_get_contents('php://input');
file_put_contents('stk_callback_log.txt', "Raw Callback Data: " . $callbackData . "\n", FILE_APPEND);

// Decode the JSON data
$callbackJSON = json_decode($callbackData, true);

// Check if JSON decoding was successful
if ($callbackJSON === null) {
    file_put_contents('stk_callback_errors.txt', "JSON Decode Error: " . json_last_error_msg() . "\n", FILE_APPEND);
    exit; // Stop further processing if JSON is invalid
}

if ($callbackJSON) {
    // Log the decoded JSON to confirm successful parsing
    file_put_contents('stk_callback_log.txt', "Decoded JSON: " . print_r($callbackJSON, true) . "\n", FILE_APPEND);

    $resultCode = $callbackJSON['Body']['stkCallback']['ResultCode'];
    $resultDesc = $callbackJSON['Body']['stkCallback']['ResultDesc'];
    $merchantRequestID = $callbackJSON['Body']['stkCallback']['MerchantRequestID'];
    $checkoutRequestID = $callbackJSON['Body']['stkCallback']['CheckoutRequestID'];
    
    // Safely access CallbackMetadata items
    if (isset($callbackJSON['Body']['stkCallback']['CallbackMetadata']['Item'])) {
        $callbackItems = $callbackJSON['Body']['stkCallback']['CallbackMetadata']['Item'];

        $amount = $callbackItems[0]['Value'] ?? null;
        $mpesaReceiptNumber = $callbackItems[1]['Value'] ?? null;
        $transactionDate = $callbackItems[3]['Value'] ?? null;
        $phoneNumber = $callbackItems[4]['Value'] ?? null;
    } else {
        file_put_contents('stk_callback_errors.txt', "CallbackMetadata is missing or malformed\n", FILE_APPEND);
        exit;
    }

    // Check if the transaction was successful (ResultCode 0 indicates success)
    if ($resultCode == 0) {
        // Select the loan_id based on MerchantRequestID and CheckoutRequestID
        $selectSql = "SELECT loan_id FROM stk_requests WHERE merchant_request_id = ? AND checkout_request_id = ?";
        $selectStmt = $conn->prepare($selectSql);

        if ($selectStmt) {
            $selectStmt->bind_param("ss", $merchantRequestID, $checkoutRequestID);

            if ($selectStmt->execute()) {
                $selectStmt->bind_result($loanId);
                $selectStmt->fetch();  // Fetch the loan_id result

                // Ensure to close the statement before executing another query
                $selectStmt->close();

                if ($loanId) {
                    // Now try inserting the payment details into your payments table
                    $sql = "INSERT INTO payments (loan_id, transaction_reference, payment_mode, payment_date, amount, national_id)
                            VALUES (?, ?, 'MPESA', ?, ?, ?)";
                    $stmt = $conn->prepare($sql);

                    if ($stmt) {
                        $stmt->bind_param("sssds", $loanId, $mpesaReceiptNumber, $transactionDate, $amount, $phoneNumber);

                        if ($stmt->execute()) {
                            // Transaction successfully saved in the database
                            file_put_contents('stk_callback_log.txt', "Payment recorded successfully\n", FILE_APPEND);
                            echo json_encode(['status' => 'success', 'message' => 'Payment recorded successfully']);
                        } else {
                            // Error inserting payment into the database, log error message
                            file_put_contents('stk_callback_errors.txt', "Database Insertion Error: " . $stmt->error . "\n", FILE_APPEND);
                            echo json_encode(['status' => 'error', 'message' => 'Failed to record payment']);
                        }
                        $stmt->close();
                    } else {
                        // Log the preparation error
                        file_put_contents('stk_callback_errors.txt', "Database Prepare Error: " . $conn->error . "\n", FILE_APPEND);
                    }
                } else {
                    // No matching loan_id found, log this
                    file_put_contents('stk_callback_errors.txt', "No matching loan_id found for the given MerchantRequestID and CheckoutRequestID\n", FILE_APPEND);
                }
            } else {
                // Error executing the select query, log this
                file_put_contents('stk_callback_errors.txt', "Database Select Error: " . $selectStmt->error . "\n", FILE_APPEND);
            }
        } else {
            // Log the preparation error for the select query
            file_put_contents('stk_callback_errors.txt', "Database Prepare Error for Select: " . $conn->error . "\n", FILE_APPEND);
        }
    } else {
        // The transaction failed, log the failure result description
        file_put_contents('stk_callback_errors.txt', "Transaction Failed: " . $resultDesc . "\n", FILE_APPEND);
    }
} else {
    // Invalid callback received, log the event
    file_put_contents('stk_callback_errors.txt', 'Invalid callback received' . "\n", FILE_APPEND);
}
?>
