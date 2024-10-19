<?php

include 'config.php';
include 'functions-tena.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $fileNo = $_POST['file_no'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the email already exists
    $checkEmailQuery = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email already exists
        $register = "Error: The email address is already registered.";
    } else {
        // Insert the new user into the database
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, file_no, email, password, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $firstName, $lastName, $fileNo, $email, $hashed_password, $role);

        if ($stmt->execute()) {
            // Send email to manager with details and temporary password
            $to = $email;
            $subject = "Welcome to Our Platform";
            $message = "Dear $firstName,\n\n";
            $message .= "Thank you for joining us as $role . Below are your details:\n\n";
            $message .= "Email: $email\n";
            $message .= "Temporary Password: $password\n\n";
            $message .= "You can log in to your dashboard at https://portal.omabracredit.co.ke as $role using your email and this temporary password.\n\n";
            $message .= "Best regards,\nAdministrator.";

            sendEmail($to, $subject, $message);

            $register = "New user registered successfully.";
        } else {
            $register = "Error: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();
}

?>


<!DOCTYPE html>
<html>
<head>
    <!-- Basic Page Info -->
    <meta charset="utf-8" />
    <title>Omabra Credit -  Loan Management System</title>

    <!-- Site favicon -->
    <link
            rel="apple-touch-icon"
            sizes="180x180"
            href="vendors/images/apple-touch-icon.png"
    />
    <link
            rel="icon"
            type="image/png"
            sizes="32x32"
            href="vendors/images/favicon-32x32.png"
    />
    <link
            rel="icon"
            type="image/png"
            sizes="16x16"
            href="vendors/images/favicon-16x16.png"
    />

    <!-- Mobile Specific Metas -->
    <meta
            name="viewport"
            content="width=device-width, initial-scale=1, maximum-scale=1"
    />

    <!-- Google Font -->
    <link
            href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
            rel="stylesheet"
    />
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="vendors/styles/core.css" />
    <link
            rel="stylesheet"
            type="text/css"
            href="vendors/styles/icon-font.min.css"
    />
    <link rel="stylesheet" type="text/css" href="vendors/styles/style.css" />

</head>
<body class="login-page">
<div class="login-header box-shadow">
    <div
            class="container-fluid d-flex justify-content-between align-items-center"
    >
        <div class="brand-logo">
            <a href="">
                <img src="vendors/images/omabra_logo.png" alt="" />
            </a>
        </div>
        <div class="login-menu">
            <ul>
                <li><a href="login.php">Login</a></li>
            </ul>
        </div>
    </div>
</div>
<div
        class="login-wrap d-flex align-items-center flex-wrap justify-content-center"
>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 col-lg-7">
                <img src="vendors/images/login-page-img.png" alt="" />
            </div>
            <div class="col-md-6 col-lg-5">
                <div class="login-box bg-white box-shadow border-radius-10">
                    <div class="login-title">
                        <h2 class="text-center text-primary">Register for Omabra Credit - LMS</h2>
                    </div>

					<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <?php 
                    if (!empty($register)) {
                        echo '<p style="color:green;">' . htmlspecialchars($register) . '</p>';
                    }
                    ?>
    <div class="select-role">
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <label class="btn active d-none">
                <input type="radio" name="role" id="admin" value="admin" required /> <!-- Role selection -->
                <div class="icon">
                    <img src="vendors/images/briefcase.svg" class="svg" alt="Admin Icon" />
                </div>
                <span>Register</span>
                Admin
            </label>

            <label class="btn d-none">
                <input type="radio" name="role" id="manager" value="manager" required />
                <div class="icon">
                    <img src="vendors/images/briefcase.svg" class="svg" alt="Manager Icon" />
                </div>
                <span>Register</span>
                Manager
            </label>

            <label class="btn active text-center">
                <input type="radio" name="role" id="admin" value="admin" required />
                <div class="icon">
                    <img src="vendors/images/briefcase.svg" class="svg" alt="Admin Icon" />
                </div>
                <span>Register</span>
                Admin
            </label>
        </div>
    </div>
    <div class="input-group custom">
        <input
            type="text"
            class="form-control form-control-lg"
            placeholder="First Name"
            name="first_name"
            required
        />
        <div class="input-group-append custom">
            <span class="input-group-text">
                <i class="icon-copy dw dw-user1"></i>
            </span>
        </div>
    </div>
    <div class="input-group custom">
        <input
            type="text"
            class="form-control form-control-lg"
            placeholder="Last Name"
            name="last_name"
            required
        />
        <div class="input-group-append custom">
            <span class="input-group-text">
                <i class="icon-copy dw dw-user1"></i>
            </span>
        </div>
    </div>
    <div class="input-group custom">
        <input
            type="text"
            class="form-control form-control-lg"
            placeholder="File Number format: ad003"
            name="file_no"
            required
        />
        <div class="input-group-append custom">
            <span class="input-group-text">
                <i class="icon-copy dw dw-user1"></i>
            </span>
        </div>
    </div>
    <div class="input-group custom">
        <input
            type="email"
            class="form-control form-control-lg"
            placeholder="Email"
            name="email"
            required
        />
        <div class="input-group-append custom">
            <span class="input-group-text">
                <i class="icon-copy dw dw-user1"></i>
            </span>
        </div>
    </div>
    <div class="input-group custom">
        <input
            type="password"
            class="form-control form-control-lg"
            placeholder="**********"
            name="password"
            required
        />
        <div class="input-group-append custom">
            <span class="input-group-text">
                <i class="dw dw-padlock1"></i>
            </span>
        </div>
    </div>
        <p class="text-danger"><?php $register ?></p>
    <div class="row">
        <div class="col-sm-12">
            <div class="input-group mb-0">
                <button class="btn btn-primary btn-lg btn-block" type="submit">Register</button>
            </div>
        </div>
    </div>
</form>

</div>
            </div>
        </div>
    </div>
</div>
<!-- js -->
<script src="vendors/scripts/core.js"></script>
<script src="vendors/scripts/script.min.js"></script>
<script src="vendors/scripts/process.js"></script>
<script src="vendors/scripts/layout-settings.js"></script>
</body>
</html>
