<?php
require_once("config.php");

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'manager')) {
    header("Location: login.php");
    exit();
}

// Get the loan ID from the POST request
if (isset($_POST['loan_id'])) {
    $loan_id = $_POST['loan_id'];

    // Prepare the SQL query to update the loan status
    $stmt = $conn->prepare("UPDATE loan_info SET loan_status = 'Defaulted' WHERE loan_id = ?");
    $stmt->bind_param("s", $loan_id);

    if ($stmt->execute()) {
        // If successful, return a success message or redirect
        $loanStatusUpdateSuccess = 'Loan status updated to Defaulted.';
        //echo json_encode(['status' => 'success', 'message' => 'Loan status updated to Defaulted.']);
        header("Location: loan-defaults.php");
    } else {
        $loanStatusUpdateFail = 'Failed to update loan status.';
        //echo json_encode(['status' => 'error', 'message' => 'Failed to update loan status.']);
    }

    $stmt->close();
}
?>
