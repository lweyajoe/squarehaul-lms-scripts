<?php
require_once("config.php");
include_once("functions-tena.php");

// Check if the user is logged in as admin or manager
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'manager')) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user_role'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Personal Info
    $firstName = htmlspecialchars($_POST["first_name"]);
    $lastName = htmlspecialchars($_POST["last_name"]);
    $email = htmlspecialchars($_POST["email"]);
    $phone = htmlspecialchars($_POST["phone_number"]);
    $county = htmlspecialchars($_POST["county"]);
    $townCentre = htmlspecialchars($_POST["town_centre"]);

    // KYC Documents
    $nationalId = htmlspecialchars($_POST["national_id"]);
    $workActivity = htmlspecialchars($_POST["work_economic_activity"]);
    $residenceBuilding = htmlspecialchars($_POST["residence_nearest_building"]);
    $residenceRoad = htmlspecialchars($_POST["residence_nearest_road"]);
    $dateOfOnboarding = date("Y-m-d");
    $onboardingOfficer = getOnboardingOfficer($conn);
    $age = htmlspecialchars($_POST["age"]);
    $gender = htmlspecialchars($_POST["gender"]);
    $nextOfKinName = htmlspecialchars($_POST["next_of_kin_name"]);
    $nextOfKinResidence = htmlspecialchars($_POST["next_of_kin_residence"]);
    $nextOfKinPhone = htmlspecialchars($_POST["next_of_kin_phone_number"]);
    $nextOfKinRelation = htmlspecialchars($_POST["next_of_kin_relation"]);
    $guarantorNationalId = htmlspecialchars($_POST["guarantor_national_id"]);
    $guarantorResidenceBuilding = htmlspecialchars($_POST["guarantor_residence_nearest_building"]);
    $guarantorResidenceRoad = htmlspecialchars($_POST["guarantor_residence_nearest_road"]);

// File upload handling
$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Initialize variables
$idPhotoFront = "";
$idPhotoBack = "";
$guarantorIdPhotoFront = "";
$guarantorIdPhotoBack = "";
$guarantorPassportPhoto = "";
$clientPassportPhoto = "";

// Allow only image types
$validExt = ['jpg', 'jpeg', 'png'];

// Compress and upload files
if (isset($_FILES['id_photo_front']) && $_FILES['id_photo_front']['error'] === UPLOAD_ERR_OK) {
    $fileExt = pathinfo($_FILES['id_photo_front']['name'], PATHINFO_EXTENSION);
    if (in_array(strtolower($fileExt), $validExt)) {
        $idPhotoFront = $uploadDir . 'front_' . uniqid() . '.' . $fileExt;
        compressImage($_FILES['id_photo_front']['tmp_name'], $idPhotoFront, 75); // Compress image
    } else {
        $error = "Invalid file type for front ID photo.";
    }
}

if (isset($_FILES['id_photo_back']) && $_FILES['id_photo_back']['error'] === UPLOAD_ERR_OK) {
    $fileExt = pathinfo($_FILES['id_photo_back']['name'], PATHINFO_EXTENSION);
    if (in_array(strtolower($fileExt), $validExt)) {
        $idPhotoBack = $uploadDir . 'back_' . uniqid() . '.' . $fileExt;
        compressImage($_FILES['id_photo_back']['tmp_name'], $idPhotoBack, 75); // Compress image
    } else {
        $error = "Invalid file type for back ID photo.";
    }
}

if (isset($_FILES['guarantor_id_photo_front']) && $_FILES['guarantor_id_photo_front']['error'] === UPLOAD_ERR_OK) {
    $fileExt = pathinfo($_FILES['guarantor_id_photo_front']['name'], PATHINFO_EXTENSION);
    if (in_array(strtolower($fileExt), $validExt)) {
        $guarantorIdPhotoFront = $uploadDir . 'guarantor-front_' . uniqid() . '.' . $fileExt;
        compressImage($_FILES['guarantor_id_photo_front']['tmp_name'], $guarantorIdPhotoFront, 75); // Compress image
    } else {
        $error = "Invalid file type for guarantor front ID photo.";
    }
}

if (isset($_FILES['guarantor_id_photo_back']) && $_FILES['guarantor_id_photo_back']['error'] === UPLOAD_ERR_OK) {
    $fileExt = pathinfo($_FILES['guarantor_id_photo_back']['name'], PATHINFO_EXTENSION);
    if (in_array(strtolower($fileExt), $validExt)) {
        $guarantorIdPhotoBack = $uploadDir . 'guarantor-back_' . uniqid() . '.' . $fileExt;
        compressImage($_FILES['guarantor_id_photo_back']['tmp_name'], $guarantorIdPhotoBack, 75); // Compress image
    } else {
        $error = "Invalid file type for guarantor back ID photo.";
    }
}

if (isset($_FILES['guarantor_passport_photo']) && $_FILES['guarantor_passport_photo']['error'] === UPLOAD_ERR_OK) {
    $fileExt = pathinfo($_FILES['guarantor_passport_photo']['name'], PATHINFO_EXTENSION);
    if (in_array(strtolower($fileExt), $validExt)) {
        $guarantorPassportPhoto = $uploadDir . 'guarantor-passport_' . uniqid() . '.' . $fileExt;
        compressImage($_FILES['guarantor_passport_photo']['tmp_name'], $guarantorPassportPhoto, 75); // Compress image
    } else {
        $error = "Invalid file type for guarantor passport photo.";
    }
}

if (isset($_FILES['client_passport_photo']) && $_FILES['client_passport_photo']['error'] === UPLOAD_ERR_OK) {
    $fileExt = pathinfo($_FILES['client_passport_photo']['name'], PATHINFO_EXTENSION);
    if (in_array(strtolower($fileExt), $validExt)) {
        $clientPassportPhoto = $uploadDir . 'client-passport_' . uniqid() . '.' . $fileExt;
        compressImage($_FILES['client_passport_photo']['tmp_name'], $clientPassportPhoto, 75); // Compress image

        // Now you can save $clientPassportPhoto to the database, e.g.:
        // $sql = "UPDATE clients SET client_passport_photo = '$clientPassportPhoto' WHERE client_id = $clientId";
        // Execute your SQL query here
    } else {
        $error = "Invalid file type for client passport photo.";
    }
}
        

    if (!isset($error)) {
        // Check if client already exists
        if (clientExists($email, $phone, $nationalId)) {
            $error = "Client already exists.";
        } else {
            // Insert client data into database
            $stmt = $conn->prepare("INSERT INTO clients (first_name, last_name, email, phone_number, county, town_centre,
                                    national_id, id_photo_front, id_photo_back, work_economic_activity, residence_nearest_building,
                                    residence_nearest_road, date_of_onboarding, onboarding_officer, age, gender, next_of_kin_name, next_of_kin_residence,
                                    next_of_kin_phone_number, next_of_kin_relation, guarantor_national_id, guarantor_residence_nearest_building, guarantor_residence_nearest_road, guarantor_id_photo_front, guarantor_id_photo_back, guarantor_passport_photo, client_passport_photo)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssssssssssssssssssssss", $firstName, $lastName, $email, $phone, $county, $townCentre,
                              $nationalId, $idPhotoFront, $idPhotoBack, $workActivity, $residenceBuilding, $residenceRoad,
                              $dateOfOnboarding, $onboardingOfficer, $age, $gender, $nextOfKinName, $nextOfKinResidence, $nextOfKinPhone, $nextOfKinRelation, $guarantorNationalId, $guarantorResidenceBuilding, $guarantorResidenceRoad, $guarantorIdPhotoFront, $guarantorIdPhotoBack, $guarantorPassportPhoto, $clientPassportPhoto);

            if ($stmt->execute()) {
                // Retrieve the client_id generated by the trigger
                $stmt->close();
                $result = $conn->query("SELECT client_id FROM clients WHERE email = '$email'");
                if ($result && $result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $generatedClientId = $row['client_id'];

                    // Create user with role 'client' and random password
                    $password = generatePassword();
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, file_no, email, password, role) VALUES (?, ?, ?, ?, ?, 'client')");
                    if ($stmt) {
                        $stmt->bind_param("sssss", $firstName, $lastName, $generatedClientId, $email, $hashedPassword);
                        if ($stmt->execute()) {
                            // Insert notification only if user creation is successful
                            $stmt->close();

                            // Send email to client with details and temporary password
                            $to = $email;
                            $subject = "Welcome to Our Platform";
                            $message = "Dear $firstName,\n\n";
                            $message .= "Thank you for registering with us. Below are your details:\n\n";
                            $message .= "Client ID: $generatedClientId\n";
                            $message .= "Email: $email\n";
                            $message .= "Temporary Password: $password\n\n";
                            $message .= "You can log in to your portal at https://portal.omabracredit.co.ke as client using your email and this temporary password.\n\n";
                            $message .= "Best regards,\nAdministrator.";

                            sendEmail($to, $subject, $message);

                            // Insert notification for the new client
                            $clientQuery = "SELECT user_id FROM users WHERE file_no = '$generatedClientId'";
                            $clientResult = $conn->query($clientQuery);
                            if ($clientResult && $clientResult->num_rows > 0) {
                                $clientRow = $clientResult->fetch_assoc();
                                $clientUserId = $clientRow['user_id'];
                                $clientNotificationHeading = "Client Onboarding";
                                $clientNotificationMessage = "Welcome, $firstName $lastName! Your account has been created. You can now apply for a loan.";
                                addNotification($clientUserId, $clientNotificationHeading, $clientNotificationMessage);
                            }                                

                            // Insert notification for the current manager/admin who onboarded the client
                            $loggedInUserId = $_SESSION['user_id'];
                            $managerNotificationHeading = "Client Onboarding";
                            $managerNotificationMessage = "You have successfully onboarded $firstName $lastName. They can now apply for a loan.";
                            addNotification($loggedInUserId, $managerNotificationHeading, $managerNotificationMessage);

                            // Insert notifications for all admin users
                            $adminQuery = "SELECT user_id FROM users WHERE role = 'admin'";
                            $adminResult = $conn->query($adminQuery);
                            while ($admin = $adminResult->fetch_assoc()) {
                                $adminUserId = $admin['user_id'];
                                $adminNotificationHeading = "Client Onboarding";
                                $adminNotificationMessage = "$firstName $lastName has been onboarded and can now apply for a loan.";
                                addNotification($adminUserId, $adminNotificationHeading, $adminNotificationMessage);
                            }

                            $insertQuerySuccess = "Form submitted successfully! Client should receive email with their details and a temporary password.";

                            // Show success message
                            echo '<script>alert("Form submitted successfully! Client should receive email with their details and a temporary password.");</script>';
                        } else {
                            $error = "Error creating user: " . $stmt->error;
                        }
                    } else {
                        $error = "Error preparing user statement: " . $conn->error;
                    }
                } else {
                    $error = "Error fetching client_id: " . $conn->error;
                }
            } else {
                $error = "Error executing client statement: " . $stmt->error;
            }
        }
    }

    if (isset($error)) {
        echo '<script>alert("'.$error.'");</script>';
    }
}
?>


<!-- HTML Template -->
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
    <div class="main-container">
        <div class="xs-pd-20-10 pd-ltr-20">
            <!-- Your form goes here -->
            <div class="page-header">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <!-- Page title and breadcrumb -->
                        <div class="title">
                            <h4>Client On-Boarding</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Client On-Boarding</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-md-6 col-sm-12 text-right">
                    </div>
                </div>
            </div>

            <!-- Form for client onboarding -->
            <div class="pd-20 card-box mb-30">
                <div class="clearfix">
                    <h4 class="text-blue h4">Please register client</h4>
                    <p class="mb-30">All fields required: If not applicable enter "NA" in caps lock</p>
                </div>
                <div class="wizard-content">
                <form class="tab-wizard wizard-circle wizard" method="POST" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <?php 
                if (isset($error)) {
                    echo '<script>alert("' . $error . '");</script>';
                    }
                if (!empty($insertQuerySuccess)) {
                    echo '<p style="color:green;">' . htmlspecialchars($insertQuerySuccess) . '</p>';
                    }
                    ?>
    <!-- Personal Info Section -->
    <h5 class="text-blue h4">Personal Info</h5>
    <section>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>First Name :</label>
                    <input type="text" class="form-control" name="first_name" required />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Last Name :</label>
                    <input type="text" class="form-control" name="last_name" required />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Email Address :</label>
                    <input type="email" class="form-control" name="email" required />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Phone Number :</label>
                    <input type="text" class="form-control" name="phone_number" required />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Select County :</label>
                    <select class="custom-select form-control" name="county" required>
                        <option value="">Select County</option>
												<option value="Baringo">Baringo</option>
												<option value="Bomet">Bomet</option>
                                                <option value="Bungoma">Bungoma</option>
                                                <option value="Busia">Busia</option>
                                                <option value="Elgeyo-Marakwet">Elgeyo-Marakwet</option>
                                                <option value="Embu">Embu</option>
                                                <option value="Garissa">Garissa</option>
                                                <option value="Homa Bay">Homa Bay</option>
                                                <option value="Isiolo">Isiolo</option>
                                                <option value="Kajiado">Kajiado</option>
                                                <option value="Kakamega">Kakamega</option>
                                                <option value="Kericho">Kericho</option>
                                                <option value="Kiambu">Kiambu</option>
                                                <option value="Kilifi">Kilifi</option>
                                                <option value="Kirinyaga">Kirinyaga</option>
                                                <option value="Kisii">Kisii</option>
                                                <option value="Kisumu">Kisumu</option>
                                                <option value="Kitui">Kitui</option>
                                                <option value="Kwale">Kwale</option>
                                                <option value="Laikipia">Laikipia</option>
                                                <option value="Lamu">Lamu</option>
                                                <option value="Machakos">Machakos</option>
                                                <option value="Makueni">Makueni</option>
                                                <option value="Mandera">Mandera</option>
                                                <option value="Marsabit">Marsabit</option>
                                                <option value="Meru">Meru</option>
                                                <option value="Migori">Migori</option>
                                                <option value="Mombasa">Mombasa</option>
                                                <option value="Murang'a">Murang'a</option>
                                                <option value="Nairobi">Nairobi</option>
                                                <option value="Nakuru">Nakuru</option>
                                                <option value="Nandi">Nandi</option>
                                                <option value="Narok">Narok</option>
                                                <option value="Nyamira">Nyamira</option>
                                                <option value="Nyandarua">Nyandarua</option>
                                                <option value="Nyeri">Nyeri</option>
                                                <option value="Samburu">Samburu</option>
                                                <option value="Siaya">Siaya</option>
                                                <option value="Taita-Taveta">Taita-Taveta</option>
                                                <option value="Tana River">Tana River</option>
                                                <option value="Tharaka-Nithi">Tharaka-Nithi</option>
                                                <option value="Trans Nzoia">Trans Nzoia</option>
                                                <option value="Turkana">Turkana</option>
                                                <option value="Uasin Gishu">Uasin Gishu</option>
                                                <option value="Vihiga">Vihiga</option>
                                                <option value="Wajir">Wajir</option>
                                                <option value="West Pokot">West Pokot</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Town Centre :</label>
                    <input type="text" class="form-control" name="town_centre" required />
                </div>
            </div>
        </div>
    </section>

    <!-- KYC Documents Section -->
    <h5 class="text-blue h4">KYC Documents</h5>
    <section>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>ID/Passport Number :</label>
                    <input type="text" class="form-control" name="national_id" required />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Attach ID (FRONT SIDE) :</label>
                    <input type="file" class="form-control-file form-control height-auto" name="id_photo_front" accept="image/*" capture="camera" required />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Attach ID (BACK SIDE) :</label>
                    <input type="file" class="form-control-file form-control height-auto" name="id_photo_back" accept="image/*" capture="camera" required />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Place of Work/Economic Activity :</label>
                    <input type="text" class="form-control" name="work_economic_activity" required />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Place of Residence (Nearest Building) :</label>
                    <input type="text" class="form-control" name="residence_nearest_building" required />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Place of Residence (Nearest Road) :</label>
                    <input type="text" class="form-control" name="residence_nearest_road" required />
                </div>
            </div>
        </div>
    </section>

    <!-- Bio Data Section -->
    <h5 class="text-blue h4">Bio Data</h5>
    <section>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Age :</label>
                    <input type="text" class="form-control" name="age" required />
                </div>
            </div>                                    
            <div class="col-md-4">
										<div class="form-group">
											<label>Select Gender :</label>
											<select class="custom-select form-control" name="gender">
												<option value="">Select Gender</option>
												<option value="Baringo">Male</option>
												<option value="Bomet">Female</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                <div class="form-group">
                    <label>Next of Kin Name :</label>
                    <input type="text" class="form-control" name="next_of_kin_name" required />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Next of Kin Residence :</label>
                    <input type="text" class="form-control" name="next_of_kin_residence" required />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Next of Kin Phone Number :</label>
                    <input type="text" class="form-control" name="next_of_kin_phone_number" required />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Next of Kin Relation :</label>
                    <input type="text" class="form-control" name="next_of_kin_relation" required />
                </div>
            </div>    
        </div>
    </section>

        <!-- KYC Documents Section -->
        <h5 class="text-blue h4">Security Documents</h5>
    <section>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Guarantor Phone Number :</label>
                    <input type="text" class="form-control" name="guarantor_national_id" required />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Attach Guarantor ID (FRONT SIDE) :</label>
                    <input type="file" class="form-control-file form-control height-auto" name="guarantor_id_photo_front" accept="image/*" capture="camera" required />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Attach Guarantor ID (BACK SIDE) :</label>
                    <input type="file" class="form-control-file form-control height-auto" name="guarantor_id_photo_back" accept="image/*" capture="camera" required />
                </div>
            </div>
        </div>
        <div class="row">
        <div class="col-md-4">
                <div class="form-group">
                    <label>Attach Guarantor Passport Photo :</label>
                    <input type="file" class="form-control-file form-control height-auto" name="guarantor_passport_photo" accept="image/*" capture="camera" required />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Guarantor Place of Residence (Nearest Building) :</label>
                    <input type="text" class="form-control" name="guarantor_residence_nearest_building" required />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Guarantor Place of Residence (Nearest Road) :</label>
                    <input type="text" class="form-control" name="guarantor_residence_nearest_road" required />
                </div>
            </div>
        </div>
    </section>
    <h5 class="text-blue h4">Passport Photo: Client</h5>
    <section>
        <div class="row">
        <div class="col-md-4">
                <div class="form-group">
                    <label>Attach Client's Passport Photo :</label>
                    <input type="file" class="form-control-file form-control height-auto" name="client_passport_photo" accept="image/*" capture="camera" required />
                </div>
            </div>
        </div>
    </section>

<!-- Final Step -->
    <section>
        <div class="text-center mt-4 form-group mb-0">
        <button type="submit" class="btn btn-primary">Submit: Onboard Client</button>
        </div>
    </section>
</form>
                </div>
            </div>
              <!-- Include footer.php -->
              <?php require_once("footer.php"); ?>  
        </div>
    </div>

    <!-- Include JavaScript files -->
    <!-- Your JavaScript files go here -->
    <!-- js -->
		<script src="vendors/scripts/core.js"></script>
		<script src="vendors/scripts/script.min.js"></script>
		<script src="vendors/scripts/process.js"></script>
		<script src="vendors/scripts/layout-settings.js"></script>
		<script src="src/plugins/jquery-steps/jquery.steps.js"></script>
		<!-- <script src="vendors/scripts/steps-setting.js"></script> -->
</body>
</html>