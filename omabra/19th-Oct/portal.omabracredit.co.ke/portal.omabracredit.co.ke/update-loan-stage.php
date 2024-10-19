<?php
// Include database connection file
require_once("config.php");

// Logging function
function logMessage($message) {
    $logFile = 'logfile.log'; // Ensure this path is writable
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - " . $message . PHP_EOL, FILE_APPEND);
}

// SQL logic to classify loans based on payments

$sql = "
  UPDATE loan_stage ls
  JOIN (
    SELECT
      e.loan_id,
      SUM(e.installment_amount) AS total_expected,
      COALESCE(SUM(p.amount), 0) AS total_paid,
      COUNT(e.id) AS total_installments,
      LEAST(GREATEST(ROUND((SUM(e.installment_amount) - COALESCE(SUM(p.amount), 0)) / (SUM(e.installment_amount) / COUNT(e.id))), 0), 4) AS missed_payments
    FROM expected_payments e
    LEFT JOIN payments p
      ON e.loan_id = p.loan_id
      AND p.payment_date <= CURDATE()
    WHERE e.payment_date <= CURDATE()
    GROUP BY e.loan_id
  ) calc ON ls.loan_id = calc.loan_id
  SET ls.stage = CASE
    WHEN ls.stage IN ('Loan Loss Provision Recognised', 'Default Recognised') THEN ls.stage
    WHEN calc.missed_payments > 1 AND calc.missed_payments <= 2 THEN 'Early Delinquency'
    WHEN calc.missed_payments = 3 THEN 'Loan Loss Provision Unrecognised'
    ELSE 'Default Unrecognised'
  END
";

if ($conn->query($sql) === TRUE) {
    logMessage("Loan stages updated successfully!");
} else {
    logMessage("Error: " . $conn->error);
}

$conn->close();
?>