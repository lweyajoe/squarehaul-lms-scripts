<?php
include 'config.php'; // This includes session_start()

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL statement
    $stmt = $conn->prepare("SELECT user_id, password FROM users WHERE email = ? AND role = ?");
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            session_start();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_role'] = $role; // Use user_role for consistency
            $_SESSION['email'] = $email;

            // Redirect based on role
            if ($role == 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($role == 'manager') {
                header("Location: manager_dashboard.php");
            } else {
                header("Location: client_dashboard.php");
            }
            exit();
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "No user found with that email and role.";
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
                <li><a href="">Login</a></li>
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
                        <h2 class="text-center text-primary">Login To Gwedhah Investments -  LMS</h2>
                    </div>
					<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div class="select-role">
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <label class="btn active">
                <input type="radio" name="role" id="admin" value="admin" required /> <!-- Role selection -->
                <div class="icon">
                    <img src="vendors/images/briefcase.svg" class="svg" alt="Admin Icon" />
                </div>
                <span>I'm</span>
                Admin
            </label>

            <label class="btn">
                <input type="radio" name="role" id="manager" value="manager" required />
                <div class="icon">
                    <img src="vendors/images/briefcase.svg" class="svg" alt="Manager Icon" />
                </div>
                <span>I'm</span>
                Manager
            </label>

            <label class="btn">
                <input type="radio" name="role" id="client" value="client" required />
                <div class="icon">
                    <img src="vendors/images/person.svg" class="svg" alt="Client Icon" />
                </div>
                <span>I'm</span>
                Client
            </label>
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
    <?php if (isset($error_message)) { ?>
        <p class="text-danger"><?php echo htmlspecialchars($error_message); ?></p>
    <?php } ?>
    <div class="row pb-30">
        <div class="col-4">
            <div class="custom-control custom-checkbox">
                <input
                    type="checkbox"
                    class="custom-control-input"
                    id="customCheck1"
                />
                <label class="custom-control-label" for="customCheck1">Remember</label>
            </div>
        </div>
        <div class="col-4 d-none">
            <div class="forgot-password">
                <a href="register.php">Register Here</a>
            </div>
        </div>
        <div class="col-4">
            <div class="forgot-password">
                <a href="forgot-password.php">Forgot Password</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="input-group mb-0">
                <button class="btn btn-primary btn-lg btn-block" type="submit">Sign In</button>
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
