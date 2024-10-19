<?php
require '../config.php';

$loan_id = $_GET['loan_id'] ?? '';

if ($loan_id) {
    // Query the database to get the latest created_at timestamp for the given loan_id
    $sql = "SELECT created_at FROM payments WHERE loan_id = ? ORDER BY created_at DESC LIMIT 1";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $loan_id);
        $stmt->execute();
        $stmt->bind_result($createdAt);
        $stmt->fetch();
        $stmt->close();

        if ($createdAt) {
            // Convert the created_at timestamp to a DateTime object
            $paymentDateTime = new DateTime($createdAt);
            $currentDateTime = new DateTime();

            // Calculate the difference in minutes between the current time and the created_at timestamp
            $interval = $currentDateTime->diff($paymentDateTime);
            $minutesDifference = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;

            // If the difference is less than or equal to 5 minutes, return 'paid'
            if ($minutesDifference <= 5) {
                echo json_encode(['status' => 'paid']);
            } else {
                echo json_encode(['status' => 'pending']);
            }
        } else {
            // No payment record found for this loan_id
            echo json_encode(['status' => 'pending']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database query failed']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid loan ID']);
}
?>
