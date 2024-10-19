<?php
// Start the session to store messages
//session_start();

// Include database connection file
require_once("config.php");

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $mobile = $_POST["mobile"];
    $subject = $_POST["subject"];
    $message = $_POST["message"];

    // Prepare and bind SQL statement
    $stmt = $conn->prepare("INSERT INTO call_back_contacts (name, email, mobile, subject, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $mobile, $subject, $message);

    // Execute SQL statement and handle success/failure
    if ($stmt->execute()) {
        // Set success message in the session
        $_SESSION['success'] = "Your request has been submitted successfully!";
    } else {
        // Set error message in the session
        $_SESSION['error'] = "Error: " . $stmt->error;
    }

    // Close statement and database connection
    $stmt->close();
    $conn->close();

    // Redirect back to index.php
    header("Location: index.php#contact-form");
    exit(); // Terminate script execution after redirection
}
?>