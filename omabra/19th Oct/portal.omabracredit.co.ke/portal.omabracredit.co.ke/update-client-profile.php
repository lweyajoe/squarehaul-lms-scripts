<?php

// Include database connection file
require_once("config.php");
include_once "functions-tena.php";

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the client ID of the logged-in user
$clientId = getClientId();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form input data
    $first_name = mysqli_real_escape_string($conn, trim($_POST['first_name']));
    $last_name = mysqli_real_escape_string($conn, trim($_POST['last_name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone_number = mysqli_real_escape_string($conn, trim($_POST['phone_number']));
    $next_of_kin_name = mysqli_real_escape_string($conn, trim($_POST['next_of_kin_name']));
    $next_of_kin_phone_number = mysqli_real_escape_string($conn, trim($_POST['next_of_kin_phone_number']));

    // Fetch the current email of the client to check if it has changed
    $query = "SELECT email FROM clients WHERE client_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $clientId);
    $stmt->execute();
    $stmt->bind_result($current_email);
    $stmt->fetch();
    $stmt->close();

    // Update clients table
    $updateClientQuery = "
        UPDATE clients 
        SET first_name = ?, last_name = ?, email = ?, phone_number = ?, next_of_kin_name = ?, next_of_kin_phone_number = ?
        WHERE client_id = ?";
    $stmt = $conn->prepare($updateClientQuery);
    $stmt->bind_param('sssssss', $first_name, $last_name, $email, $phone_number, $next_of_kin_name, $next_of_kin_phone_number, $clientId);

    if ($stmt->execute()) {
        // Check if the email has changed
        if ($current_email !== $email) {
            // Update email in the users table
            $updateUserEmailQuery = "UPDATE users SET email = ? WHERE file_no = ?";
            $stmt = $conn->prepare($updateUserEmailQuery);
            $stmt->bind_param('ss', $email, $clientId);
            $stmt->execute();
        }

        // Redirect to the profile page with a success message
        header("Location: edit-my-profile.php?success=1");
        exit();
    } else {
        // Redirect back with an error message
        header("Location: edit-my-profile.php?error=1");
        exit();
    }
}

?>