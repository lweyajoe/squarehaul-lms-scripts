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
    $amount = $callbackJSON['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'];
    $mpesaReceiptNumber = $callbackJSON['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value'];
    $transactionDate = $callbackJSON['Body']['stkCallback']['CallbackMetadata']['Item'][3]['Value'];
    $phoneNumber = $callbackJSON['Body']['stkCallback']['CallbackMetadata']['Item'][4]['Value'];

    // Check if the transaction was successful (ResultCode 0 indicates success)
    if ($resultCode == 0) {
        // Try inserting the payment details into your payments table and log any errors
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
        // The transaction failed, log the failure result description
        file_put_contents('stk_callback_errors.txt', "Transaction Failed: " . $resultDesc . "\n", FILE_APPEND);
    }
} else {
    // Invalid callback received, log the event
    file_put_contents('stk_callback_errors.txt', 'Invalid callback received' . "\n", FILE_APPEND);
}
?>