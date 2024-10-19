<?php
require_once("config.php");

// Check if token is provided in the URL
if (!empty($_GET['token'])) {
    $token = $_GET['token'];

    // Check if token exists in the database
    $query = $conn->prepare("SELECT * FROM users WHERE reset_token = ?");
    $query->bind_param("s", $token);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        // Token found in database
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $newPassword = htmlspecialchars($_POST["new-password"]);
            $confirmPassword = htmlspecialchars($_POST["confirm-password"]);

            // Ensure the passwords match
            if ($newPassword === $confirmPassword) {
                // Hash the new password
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Update user's password and clear the reset token
                $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?");
                $stmt->bind_param("ss", $hashedPassword, $token);
                $stmt->execute();

                // Redirect to login page or show a success message
                echo '<script>alert("Your password has been reset successfully. Please login with your new password.");</script>';
                echo '<meta http-equiv="refresh" content="0;url=login.php">';
            } else {
                echo '<script>alert("Passwords do not match. Please try again.");</script>';
            }
        }
    } else {
        // Token not found in the database
        echo '<script>alert("Invalid token.");</script>';
    }
} else {
    // No token provided in the URL
    echo '<script>alert("Token not provided.");</script>';
}
?>

<!-- HTML form for password reset -->
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
	<body>
		<div class="login-header box-shadow">
			<div
				class="container-fluid d-flex justify-content-between align-items-center"
			>
				<div class="brand-logo">
					<a href="login.html">
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
					<div class="col-md-6">
						<img src="vendors/images/forgot-password.png" alt="" />
					</div>
					<div class="col-md-6">
						<div class="login-box bg-white box-shadow border-radius-10">
							<div class="login-title">
								<h2 class="text-center text-primary">Reset Password</h2>
							</div>
							<h6 class="mb-20">Enter your new password, confirm and submit</h6>
							<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?token=' . $token; ?>">
    <div class="input-group custom">
        <input
            type="password"
            class="form-control form-control-lg"
            name="new-password"
            placeholder="New Password"
            required
        />
        <div class="input-group-append custom">
            <span class="input-group-text"><i class="dw dw-padlock1"></i></span>
        </div>
    </div>
    <div class="input-group custom">
        <input
            type="password"
            class="form-control form-control-lg"
            name="confirm-password"
            placeholder="Confirm New Password"
            required
        />
        <div class="input-group-append custom">
            <span class="input-group-text"><i class="dw dw-padlock1"></i></span>
        </div>
    </div>
    <div class="row align-items-center">
        <div class="col-5">
            <div class="input-group mb-0">
                <input class="btn btn-primary btn-lg btn-block" type="submit" value="Submit">
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
