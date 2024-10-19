<?php
// Include the MpesaAPI class
require 'MpesaAPI.php'; // Ensure this path points to where MpesaAPI.php is located

// Create an instance of MpesaAPI
$mpesa = new MpesaAPI();

// Set your confirmation URL (ensure this matches the correct domain)
$confirmationURL = "https://portal.omabracredit.co.ke/api/confirmation.php"; // Path to your confirmation.php

// Register the confirmation URL (validation URL can be skipped or set to null if not used)
$response = $mpesa->registerConfirmationURL($confirmationURL);

// Print the response from M-Pesa
print_r($response);

?>
