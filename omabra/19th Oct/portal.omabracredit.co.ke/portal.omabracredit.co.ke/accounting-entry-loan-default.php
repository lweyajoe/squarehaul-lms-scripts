<?php

// Include the configuration file
require_once 'config.php';

// Logging function
function logMessage($message) {
    $logFile = 'logfile.log'; // Ensure this path is writable
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - " . $message . PHP_EOL, FILE_APPEND);
}

// Function to get the numeric part of the loan ID
function getLoanNumber($loanId) {
    return substr($loanId, 2);
}

// Get today's date
$today = date('Y-m-d');

// Prepare SQL queries
// Query to get loans in 'Default Unrecognised' stage
$sqlLoans = "
    SELECT ls.loan_id
    FROM loan_stage ls
    WHERE ls.stage = 'Default Unrecognised'
";

$resultLoans = $conn->query($sqlLoans);

if ($resultLoans->num_rows > 0) {
    // Process each loan
    while ($row = $resultLoans->fetch_assoc()) {
        $loanId = $row['loan_id'];
        $loanNumber = getLoanNumber($loanId);

        // Calculate the total payments and expected payments
        $sqlPayments = "
            SELECT COALESCE(SUM(amount), 0) AS total_payments
            FROM payments
            WHERE loan_id = '$loanId'
        ";

        $resultPayments = $conn->query($sqlPayments);
        $totalPayments = ($resultPayments->num_rows > 0) ? $resultPayments->fetch_assoc()['total_payments'] : 0;

        $sqlExpectedPayments = "
            SELECT COALESCE(SUM(installment_amount), 0) AS total_expected
            FROM expected_payments
            WHERE loan_id = '$loanId'
        ";

        $resultExpectedPayments = $conn->query($sqlExpectedPayments);
        $totalExpectedPayments = ($resultExpectedPayments->num_rows > 0) ? $resultExpectedPayments->fetch_assoc()['total_expected'] : 0;

        $amount = $totalExpectedPayments - $totalPayments;

        if ($amount > 0) {
            // Insert into oc24entries table
            $sqlInsertOc24entries = "
                INSERT INTO oc24entries (entrytype_id, number, date, cr_total, dr_total, narration)
                VALUES (4, '$loanNumber', '$today', $amount, $amount, 'Write-off for loan $loanId')
            ";

            if ($conn->query($sqlInsertOc24entries) === TRUE) {
                $entryId = $conn->insert_id;

                // Insert into oc24entryitems table
                $sqlInsertOc24entryitems1 = "
                    INSERT INTO oc24entryitems (entry_id, ledger_id, amount, dc)
                    VALUES ($entryId, 30, $amount, 'D')
                ";

                $sqlInsertOc24entryitems2 = "
                    INSERT INTO oc24entryitems (entry_id, ledger_id, amount, dc)
                    VALUES ($entryId, 12, $amount, 'C')
                ";

                $conn->query($sqlInsertOc24entryitems1);
                $conn->query($sqlInsertOc24entryitems2);

                // Update loan_stage table
                $sqlUpdateLoanStage = "
                    UPDATE loan_stage
                    SET stage = 'Default Recognised'
                    WHERE loan_id = '$loanId'
                ";

                $conn->query($sqlUpdateLoanStage);
            } else {
                logMessage("Error inserting into oc24entries: " . $conn->error);
            }
        }
    }
} else {
    logMessage("No loans in 'Default Unrecognised' stage.");
}

// Close the database connection
$conn->close();

?>
