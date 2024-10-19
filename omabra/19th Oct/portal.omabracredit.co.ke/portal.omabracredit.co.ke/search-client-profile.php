<?php
// Include database connection and utility functions
require_once("config.php");
include_once "functions-tena.php";

// Start session and check user authentication

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['admin', 'manager'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user_role'];
$clientId;

$profile_found = false;
$client_data = [];
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $national_id = trim($_POST['national_id']);

    if (!empty($national_id)) {
        // Fetch data from the clients table
        $client_query = $conn->prepare("SELECT * FROM clients WHERE national_id = ?");
        $client_query->bind_param("s", $national_id);
        if ($client_query->execute()) {
            $client_result = $client_query->get_result();
            $client_data = $client_result->fetch_assoc();
            $client_query->close();
        } else {
            $error_message = 'Error executing client query: ' . htmlspecialchars($conn->error);
        }

        $clientId = $client_data['client_id'];
        $loan_data = getTotalClientLoanData($clientId);
    
    }
    
    if ($client_data && !empty($loan_data)) {
        $profile_found = true;
    } elseif (empty($client_data)) {
        $error_message = 'No client found with the provided National ID.';
    } else {
        $error_message = 'National ID cannot be empty.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once("head.php"); ?>
</head>
<body>
    <?php require_once("header.php"); ?>
    <?php require_once("right-sidebar.php"); ?>

    <?php
    // Include the appropriate sidebar based on the user role
    if ($user_role == 'admin') {
        include('left-sidebar-admin.php');
    } elseif ($user_role == 'manager') {
        include('left-sidebar-manager.php');
    } else {
        header("Location: login.php");
        exit();
    }
    ?>

    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="xs-pd-20-10 pd-ltr-20">
        <div class="page-header">
						<div class="row">
							<div class="col-md-12 col-sm-12">
								<div class="title">
									<h4>Profile</h4>
								</div>
								<nav aria-label="breadcrumb" role="navigation">
									<ol class="breadcrumb">
										<li class="breadcrumb-item">
											<a href="">Home</a>
										</li>
										<li class="breadcrumb-item active" aria-current="page">
											Client Profile
										</li>
									</ol>
								</nav>
							</div>
						</div>
					</div>
            <div class="search-icon-box card-box mb-30">
                <form method="post" action="">
                    <input
                        type="text"
                        class="border-radius-10"
                        name="national_id"
                        id="filter_input"
                        placeholder="Search Client Profile: Enter National ID..."
                        title="Type in a national ID"
                        required
                    />
                    <i class="search_icon dw dw-search" onclick="this.closest('form').submit();"></i>
                </form>
            </div>

            <?php if ($profile_found): ?>
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-8 col-sm-12 mb-30">
                        <div class="pd-20 card-box height-100-p">
                            <h5 class="text-center h5 mb-0"><?php echo htmlspecialchars($client_data['first_name']) . ' ' . htmlspecialchars($client_data['last_name']); ?></h5>
                            <p class="text-center text-muted font-14">
                                Here are your client's details. If any details change, please contact the administrator.
                            </p>
                            <div class="profile-info-container">
                            <div class="profile-info">
                                    <h5 class="mb-20 h5 text-blue">Loan Information</h5>
                                    <ul>
                                    <li>
    <span>Loanee Passport Photo:</span>
    <img src="<?php echo htmlspecialchars($client_data['client_passport_photo']); ?>" alt="Loanee Passport Photo" style="max-width: 100px; max-height: 100px;"/>
    <br>
    <a href="<?php echo htmlspecialchars($client_data['client_passport_photo']); ?>" target="_blank">
        View Photo
    </a>
</li>

<li>
    <span>Loanee ID:</span>
    <img src="<?php echo htmlspecialchars($client_data['id_photo_front']); ?>" alt="ID Photo" style="max-width: 100px; max-height: 100px;"/>
    <br>
    <a href="<?php echo htmlspecialchars($client_data['id_photo_front']); ?>" target="_blank">
        View Photo
    </a>
</li>

<li>
    <span>Loanee (other ref doc):</span>
    <img src="<?php echo htmlspecialchars($client_data['id_photo_back']); ?>" alt="ID Photo" style="max-width: 100px; max-height: 100px;"/>
    <br>
    <a href="<?php echo htmlspecialchars($client_data['id_photo_back']); ?>" target="_blank">
        View Photo
    </a>
</li>


<li>
    <span>Active Loan Principal:</span>
    <?php echo !empty($loan_data['total_principle']) ? number_format($loan_data['total_principle'], 2) : 'N/A'; ?>
</li>
<li>
    <span>Total Payments:</span>
    <?php echo !empty($loan_data['total_payments']) ? number_format($loan_data['total_payments'], 2) : 'N/A'; ?>
</li>
<li>
    <span>Loan Balance:</span>
    <?php echo !empty($loan_data['loan_balance']) ? number_format($loan_data['loan_balance'], 2) : 'N/A'; ?>
</li>

                                    </ul>
                                </div>
                                <div class="profile-info">
                                    <h5 class="mb-20 h5 text-blue">Contact Information</h5>
                                    <ul>
    <?php for ($i = 1; $i <= 5; $i++): ?>
        <li>
            <span>Next of Kin Name and Number (<?php echo $i; ?>):</span>
            <?php 
            $field = 'next_of_kin_name_number_' . $i; 
            echo !empty($client_data[$field]) ? htmlspecialchars($client_data[$field]) : 'N/A'; 
            ?>
        </li>
    <?php endfor; ?>
</ul>

                                </div>
                                <div class="profile-info">
                                    <h5 class="mb-20 h5 text-blue">KYC Information</h5>
                                    <ul>
                                        <li><span>On-Boarding Date:</span> <?php echo htmlspecialchars($client_data['date_of_onboarding']); ?></li>
                                        <li><span>Identification Number:</span> <?php echo htmlspecialchars($client_data['national_id']); ?></li>
                                        <li><span>Email Address:</span> <?php echo htmlspecialchars($client_data['email']); ?></li>
                                        <li><span>Phone Number:</span> <?php echo htmlspecialchars($client_data['phone_number']); ?></li>
                                        <li><span>County:</span> <?php echo htmlspecialchars($client_data['county']); ?></li>
                                        <li><span>Place of Residence:</span> <?php echo nl2br(htmlspecialchars($client_data['residence_nearest_road'])); ?></li>

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="alert alert-warning" role="alert">
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php require_once("footer.php"); ?>
        </div>
    </div>

    <!-- js -->
    <script src="vendors/scripts/core.js"></script>
    <script src="vendors/scripts/script.min.js"></script>
    <script src="vendors/scripts/process.js"></script>
    <script src="vendors/scripts/layout-settings.js"></script>
    <script src="vendors/scripts/dashboard2.js"></script>

    <!-- buttons for Export datatable -->
    <!-- Datatable Setting js -->
    <script src="vendors/scripts/datatable-setting.js"></script>
</body>
</html>

<?php
$conn->close();
?>
