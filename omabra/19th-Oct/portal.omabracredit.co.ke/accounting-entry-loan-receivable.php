<?php
require_once("config.php"); // Include your configuration file

// Logging function
function logMessage($message) {
    $logFile = 'logfile.log'; // Ensure this path is writable
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - " . $message . PHP_EOL, FILE_APPEND);
}

// Get today's date
$today = date('Y-m-d');

// Fetch loans in 'Green Zone' or 'Early Delinquency'
$sql = "SELECT ls.loan_id, ls.stage 
        FROM loan_stage ls 
        WHERE ls.stage IN ('Green Zone', 'Early Delinquency')";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $loanId = $row['loan_id'];

        // Check for expected payments for today
        $sql = "SELECT id, installment_amount 
                FROM expected_payments 
                WHERE loan_id = ? 
                AND payment_date = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $loanId, $today);
        $stmt->execute();
        $paymentResult = $stmt->get_result();

        while ($paymentRow = $paymentResult->fetch_assoc()) {
            $expectedPaymentId = $paymentRow['id'];
            $installmentAmount = $paymentRow['installment_amount'];

            // Insert into oc24entries table
            $entrySql = "INSERT INTO oc24entries (entrytype_id, number, date, dr_total, cr_total, narration) 
                         VALUES (4, ?, ?, ?, ?, ?)";
            $narration = "Recognition of accrued interest on loan $loanId";
            
            $entryStmt = $conn->prepare($entrySql);
            $loanNumber = substr($loanId, 2); // Extract number from loan_id, e.g., '000009'
            $entryStmt->bind_param('ssdds', $loanNumber, $today, $installmentAmount, $installmentAmount, $narration);
            $entryStmt->execute();
            $entryId = $entryStmt->insert_id;

            // Insert into oc24entryitems table
            $itemSql = "INSERT INTO oc24entryitems (entry_id, ledger_id, amount, dc) 
                        VALUES (?, 13, ?, 'D'), (?, 1, ?, 'C')";

            $itemStmt = $conn->prepare($itemSql);
            $itemStmt->bind_param('ididd', $entryId, $installmentAmount, $entryId, $installmentAmount);
            $itemStmt->execute();
        }
    }
}

$conn->close();
?>
