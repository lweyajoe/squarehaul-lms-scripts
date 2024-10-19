<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection file if not already included
if (!isset($conn)) {
    require_once("config.php");
}

// Fetch user data if not already set in session
if (!isset($_SESSION['user_name_initials'])) {
    $user_id = $_SESSION['user_id'];
    $user_query = $conn->prepare("SELECT first_name, last_name, role FROM users WHERE user_id = ?");
    if (!$user_query) {
        die("User query preparation failed: " . $conn->error);
    }
    $user_query->bind_param("i", $user_id);
    $user_query->execute();
    $user_result = $user_query->get_result();
    $user_data = $user_result->fetch_assoc();
    
    if ($user_data) {
        $_SESSION['user_name_initials'] = strtoupper($user_data['first_name'][0] . ". " . $user_data['last_name']);
        $_SESSION['user_role'] = $user_data['role'];
    }
}

// Define profile link based on user role
$profile_link = "#";
if ($_SESSION['user_role'] == 'client') {
    $profile_link = "my-profile.php";
} elseif ($_SESSION['user_role'] == 'manager') {
    $profile_link = "my-manager-profile.php";
}

// Get unread notifications for the logged-in user
$sql = "SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']); // Assuming user_id is stored in session
$stmt->execute();
$result = $stmt->get_result();
$userNotifications = $result->fetch_all(MYSQLI_ASSOC);

// Now use this data to display in the notifications section

?>

<div class="header">
    <div class="header-left">
        <div class="menu-icon bi bi-list"></div>
        <div class="search-toggle-icon bi bi-search" data-toggle="header_search"></div>
        <div class="header-search">
            <form>
                <div class="form-group mb-0">
                    <i class="dw dw-search2 search-icon"></i>
                    <input
                        type="text"
                        class="form-control search-input"
                        placeholder="Search Here"
                    />
                </div>
            </form>
        </div>
    </div>
    <div class="header-right">
        <div class="user-notification">
            <div class="dropdown">
                <a
                    class="dropdown-toggle no-arrow"
                    href="#"
                    role="button"
                    data-toggle="dropdown"
                >
                    <i class="icon-copy dw dw-notification"></i>
                    <span class="badge notification-active"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
    <div class="notification-list mx-h-350 customscroll">
        <ul id="notification-list">
            <!-- Notification items will be dynamically populated here -->
            <?php foreach ($userNotifications as $notification): ?>
                <li data-notification-id="<?php echo $notification['id']; ?>">
                    <a href="javascript:;">
                        <h3><?php echo $notification['heading']; ?></h3>
                        <p><?php echo $notification['message']; ?></p>
                        <small><?php echo $notification['created_at']; ?></small>
                        <button type="button" class="btn btn-success btn-sm btn-block" onclick="markNotificationAsRead(<?php echo $notification['id']; ?>, this)">Mark As Read</button>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
            </div>
        </div>
        <div class="user-info-dropdown">
            <div class="dropdown">
                <a
                    class="dropdown-toggle"
                    href="#"
                    role="button"
                    data-toggle="dropdown"
                >
                    <span class="user-icon">
                        <img src="vendors/images/photo1.jpg" alt="" />
                    </span>
                    <span class="user-name"><?php echo $_SESSION['user_name_initials']; ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                    <?php if ($_SESSION['user_role'] != 'admin'): ?>
                    <a class="dropdown-item" href="<?php echo $profile_link; ?>"><i class="dw dw-user1"></i> Profile</a>
                    <?php endif; ?>
                    <a class="dropdown-item" href="logout.php"><i class="dw dw-logout"></i> Log Out</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function markNotificationAsRead(notificationId, button) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "mark-notifications-read.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            // Hide the specific notification item
            let notificationItem = button.closest('li');
            notificationItem.style.display = 'none';  // Hides the clicked notification
        }
    };

    xhr.send("notification_id=" + notificationId);  // Send the notification ID to mark it as read
}
</script>
