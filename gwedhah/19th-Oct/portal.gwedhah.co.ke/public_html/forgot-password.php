<?php
require_once("config.php");
include_once "functions.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = htmlspecialchars($_POST["role"]);
    $email = htmlspecialchars($_POST["email"]);

    // Check if email exists in the database
    $query = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
    $query->bind_param("ss", $email, $role);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        // Generate a unique token for password reset
        $token = bin2hex(random_bytes(16));

        // Store token in the database
        $stmt = $conn->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
        $stmt->bind_param("ss", $token, $email);
        $stmt->execute();

        // Send email with reset link
        $resetLink = "https://portal.gwedhah.co.ke/reset-password.php?token=" . $token;
        $to = $email;
        $subject = "Password Reset";
        $message = "Hello,\n\n";
        $message .= "You have requested a password reset. Please click the link below to reset your password:\n";
        $message .= $resetLink . "\n";
        $message .= "If you did not request this, please ignore this email.\n\n";
		$message .= "Best regards,\nAdministrator.";

		sendEmail($to, $subject, $message);

		//remove in production
		//$successResetLink = "Reset link is $resetLink .";

        // Show success message
        echo '<script>alert("An email with password reset instructions has been sent to your email address.");</script>';
    } else {
        // Show error message if email not found
        echo '<script>alert("Email address not found.");</script>';
    }
}
?>

<!DOCTYPE html>
<html>
	<head>
		<!-- Basic Page Info -->
		<meta charset="utf-8" />
		<title>Gwedhah Investments -  Loan Management System</title>

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
					<a href="login.php">
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
								<h2 class="text-center text-primary">Forgot Password</h2>
							</div>
							<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
							<div class="select-role">
								<div class="btn-group btn-group-toggle" data-toggle="buttons">
									<label class="btn active">
										<input type="radio" name="role" id="manager" value="manager" required />
										<div class="icon">
											<img
												src="vendors/images/briefcase.svg"
												class="svg"
												alt=""
											/>
										</div>
										<span>I'm</span>
										Manager
									</label>
									<label class="btn">
										<input type="radio" name="role" id="client" value="client" required />
										<div class="icon">
											<img
												src="vendors/images/person.svg"
												class="svg"
												alt=""
											/>
										</div>
										<span>I'm</span>
										Client
									</label>
								</div>
							</div>
							<h6 class="mb-20">
								Enter your email address to reset your password
							</h6>
								<div class="input-group custom">
									<input
										type="text"
										class="form-control form-control-lg"
										placeholder="Email"
										name="email"
									/>
									<div class="input-group-append custom">
										<span class="input-group-text"
											><i class="fa fa-envelope-o" aria-hidden="true"></i
										></span>
									</div>
								</div>
								<div class="row align-items-center">
									<div class="col-5">
										<div class="input-group mb-0"><button class="btn btn-primary btn-lg btn-block" type="submit">Submit</button></div>
									</div>
									<div class="col-2">
										<div
											class="font-16 weight-600 text-center"
											data-color="#707373"
										>
											OR
										</div>
									</div>
									<div class="col-5">
										<div class="input-group mb-0">
											<a
												class="btn btn-outline-primary btn-lg btn-block"
												href="login.php"
												>Login</a
											>
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
