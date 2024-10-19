<?php
// Include database connection file
require_once("config.php");
include_once "functions-tena.php";

// Check if the user is logged in. Page can only be viewed when logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user_role'];
$user_email = $_SESSION['email'];

$clientId = getClientId();
$clientData = getClientData($clientId);


?>

<!DOCTYPE html>
<html>
<?php require_once("head.php"); ?>
    <body>
        <?php require_once("header.php"); ?>
        <?php require_once("right-sidebar.php"); ?>

		<?php
		// Include the appropriate sidebar based on the user role
		if ($user_role == 'admin') {
			include('left-sidebar-admin.php');
		} elseif ($user_role == 'manager') {
			include('left-sidebar-manager.php');
		} elseif ($user_role == 'client') {
			include('left-sidebar-client.php');
		} else {
			// If the user role is neither admin, manager, nor client, redirect or show an error
			header("Location: login.php");
			exit();
		}
		?>
        
        <!-- Your content goes here -->

		<div class="mobile-menu-overlay"></div>

		<div class="main-container">
    <div class="xs-pd-20-10 pd-ltr-20">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="title">
                            <h4>Profile</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="index.html">Home</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Profile
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-30">
                    <div class="pd-20 card-box height-100-p">
                        <div class="profile-photo">
                            <img src="vendors/images/photo1.jpg" alt="" class="avatar-photo" />
                        </div>
                        <h5 class="text-center h5 mb-0">
                            <?php echo htmlspecialchars($clientData['first_name']) . ' ' . htmlspecialchars($clientData['last_name']); ?>
                        </h5>
                        <p class="text-center text-muted font-14">
                            Are these your correct email and <strong>mpesa phone number</strong> details?
                        </p>
                        <div class="profile-info">
                            <h5 class="mb-20 h5 text-blue">Contact Information</h5>
                            <ul>
                                <li>
                                    <span>Email Address:</span> <?php echo htmlspecialchars($clientData['email']); ?>
                                </li>
                                <li>
                                    <span>Phone Number:</span> <?php echo htmlspecialchars($clientData['phone_number']); ?>
                                </li>
                                <li>
                                    <span>County:</span> <?php echo htmlspecialchars($clientData['county']); ?>
                                </li>
                                <li>
                                    <span>Place of Residence:</span> <?php echo nl2br(htmlspecialchars($clientData['residence_nearest_building'])); ?>
                                </li>
                                <li>
                                    <span>Next of Kin Name:</span> <?php echo htmlspecialchars($clientData['next_of_kin_name']); ?>
                                </li>
                                <li>
                                    <span>Next of Kin Phone Number:</span> <?php echo htmlspecialchars($clientData['next_of_kin_phone_number']); ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 mb-30">
                    <div class="card-box height-100-p overflow-hidden">
                        <div class="profile-tab height-100-p">
                            <div class="tab height-100-p">
                                <div class="profile-setting">
                                    <form method="POST" action="update-client-profile.php">
                                        <ul class="profile-edit-list row">
                                            <li class="weight-500 col-md-6">
                                                <h4 class="text-blue h5 mb-20">Update Your Personal Details</h4>
                                                <div class="form-group">
                                                    <label>First Name</label>
                                                    <input class="form-control form-control-lg" type="text" name="first_name" value="<?php echo htmlspecialchars($clientData['first_name']); ?>" placeholder="<?php echo htmlspecialchars($clientData['first_name']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Last Name</label>
                                                    <input class="form-control form-control-lg" type="text" name="last_name" value="<?php echo htmlspecialchars($clientData['last_name']); ?>" placeholder="<?php echo htmlspecialchars($clientData['last_name']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Email</label>
                                                    <input class="form-control form-control-lg" type="email" name="email" value="<?php echo htmlspecialchars($clientData['email']); ?>" placeholder="<?php echo htmlspecialchars($clientData['email']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Phone Number</label>
                                                    <input class="form-control form-control-lg" type="text" name="phone_number" value="<?php echo htmlspecialchars($clientData['phone_number']); ?>" placeholder="<?php echo htmlspecialchars($clientData['phone_number']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Next of Kin Name</label>
                                                    <input class="form-control form-control-lg" type="text" name="next_of_kin_name" value="<?php echo htmlspecialchars($clientData['next_of_kin_name']); ?>" placeholder="<?php echo htmlspecialchars($clientData['next_of_kin_name']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Next of Kin Phone Number</label>
                                                    <input class="form-control form-control-lg" type="text" name="next_of_kin_phone_number" value="<?php echo htmlspecialchars($clientData['next_of_kin_phone_number']); ?>" placeholder="<?php echo htmlspecialchars($clientData['next_of_kin_phone_number']); ?>">
                                                </div>
                                                <div class="form-group mb-0">
                                                    <input type="submit" class="btn btn-primary" value="Save & Update">
                                                </div>
                                            </li>
                                        </ul>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


        <?php require_once("footer.php"); ?>
        </div>
		</div>
        <!-- js -->
		<script src="vendors/scripts/core.js"></script>
		<script src="vendors/scripts/script.min.js"></script>
		<script src="vendors/scripts/process.js"></script>
		<script src="vendors/scripts/layout-settings.js"></script>
		<script src="src/plugins/jQuery-Knob-master/jquery.knob.min.js"></script>
    </body>
</html>
