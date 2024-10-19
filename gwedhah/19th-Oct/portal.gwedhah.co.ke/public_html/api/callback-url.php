<?php
// Assuming you have a database connection file included
require '../config.php';

// Retrieve the JSON data from Safaricom
$callbackData = file_get_contents('php://input');
$callbackJSON = json_decode($callbackData, true);

if ($callbackJSON) {
    // Example of logging callback data
    file_put_contents('stk_callback_log.txt', print_r($callbackJSON, true), FILE_APPEND);

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
        // Insert the payment details into your payments table
        $sql = "INSERT INTO payments (loan_id, transaction_reference, payment_mode, payment_date, amount, phone_number)
                VALUES (?, ?, 'MPESA', ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssds", $loanId, $mpesaReceiptNumber, $transactionDate, $amount, $phoneNumber);

        if ($stmt->execute()) {
            // Transaction successfully saved in the database
            echo json_encode(['status' => 'success', 'message' => 'Payment recorded successfully']);
        } else {
            // Error inserting payment into the database
            echo json_encode(['status' => 'error', 'message' => 'Failed to record payment']);
        }
        $stmt->close();
    } else {
        // The transaction failed, log the failure
        file_put_contents('stk_callback_errors.txt', $resultDesc . "\n", FILE_APPEND);
    }
} else {
    // Invalid callback received, log the event
    file_put_contents('stk_callback_errors.txt', 'Invalid callback received' . "\n", FILE_APPEND);
}

?>
