<?php
include 'config.php'; // Database connection
include 'functions-tena.php';

// Get input data from the AJAX request
$data = json_decode(file_get_contents('php://input'), true);
$loanId = $data['loan_id'] ?? null;

// Get logged-in user info
$clientId = getClientId();  // Function to get client id
$managerEmail = getOnboardingOfficer();  // Function to get manager email

// Fetch loan data based on the provided loan_id or fetch the latest loan
$loanData = getLoanDataForChart($clientId, $managerEmail);

if ($loanData) {
    echo json_encode($loanData);
} else {
    echo json_encode(['error' => 'No data found']);
}
?>