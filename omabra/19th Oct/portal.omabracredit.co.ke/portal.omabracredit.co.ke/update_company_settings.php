<?php
// Include database connection file
include 'config.php';  
// Ensure your connection file is correctly set

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $company_name = $_POST['company_name'] ?? null;
    $company_address = $_POST['company_address'] ?? null;
    $company_email = $_POST['company_email'] ?? null;
    $company_website = $_POST['company_website'] ?? null;
    $company_phone = $_POST['company_phone'] ?? null;
    $company_tax_rate = $_POST['tax_rate'] ?? null;

    // Get the new bank details
    $bank_name = $_POST['bank_name'] ?? null;
    $account_number = $_POST['account_number'] ?? null;
    $account_reference = $_POST['account_reference'] ?? null;
    $payee_name = $_POST['payee_name'] ?? null;

    // Get the new interest rate, billing period, processing fees, and insurance fees
    $interest_rate = $_POST['interest_rate'] ?? null;
    $interest_billing_period = $_POST['interest_billing_period'] ?? null;
    $processing_fees = $_POST['processing_fees'] ?? null;
    $insurance_fees = $_POST['insurance_fees'] ?? null;

    // SQL query to insert or update company settings
    $sql = "REPLACE INTO company_settings 
            (id, company_name, company_address, company_email, company_website, company_phone, tax_rate, interest_rate, interest_billing_period, processing_fees, insurance_fees, bank_name, account_number, account_reference, payee_name)
            VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters, changing 'tax_rate' and 'insurance_fees' to 'i' for integers
        $stmt->bind_param("sssssddsddssss", $company_name, $company_address, $company_email, $company_website, $company_phone, $company_tax_rate, $interest_rate, $interest_billing_period, $processing_fees, $insurance_fees, $bank_name, $account_number, $account_reference, $payee_name);

        // Execute the query
        if ($stmt->execute()) {
            echo "<script>alert('Company details updated successfully.'); window.location.href='settings.php';</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }

    // Close the connection
    $conn->close();
}
?>

