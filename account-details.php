<?php
include 'header.php';

// Redirect if not logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['id'];
$username = $_SESSION['username'];
$email = '';
$feedback_message = '';

// Fetch current user's email
$sql_fetch = "SELECT email FROM users WHERE id = ?";
if ($stmt_fetch = $mysqli->prepare($sql_fetch)) {
    $stmt_fetch->bind_param("i", $user_id);
    $stmt_fetch->execute();
    $stmt_fetch->bind_result($email);
    $stmt_fetch->fetch();
    $stmt_fetch->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- Update Profile Information ---
    if (isset($_POST['action']) && $_POST['action'] == 'update_profile') {
        $new_username = trim($_POST['username']);
        $new_email = trim($_POST['email']);

        if (!empty($new_username) && !empty($new_email)) {
            $sql_update = "UPDATE users SET username = ?, email = ? WHERE id = ?";
            if ($stmt_update = $mysqli->prepare($sql_update)) {
                $stmt_update->bind_param("ssi", $new_username, $new_email, $user_id);
                if ($stmt_update->execute()) {
                    $_SESSION['username'] = $new_username;
                    $username = $new_username;
                    $email = $new_email;
                    $feedback_message = '<div class="bg-green-500/20 text-green-400 p-4 rounded-lg mb-6">Profile updated successfully.</div>';
                }
                $stmt_update->close();
            }
        }
    }
    // --- Change Password ---
    elseif (isset($_POST['action']) && $_POST['action'] == 'change_password') {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        $sql_pass = "SELECT password FROM users WHERE id = ?";
        if ($stmt_pass = $mysqli->prepare($sql_pass)) {
            $stmt_pass->bind_param("i", $user_id);
            $stmt_pass->execute();
            $stmt_pass->bind_result($hashed_password);
            $stmt_pass->fetch();
            $stmt_pass->close();

            if (password_verify($current_password, $hashed_password) && $new_password === $confirm_password && strlen($new_password) >= 6) {
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $sql_update_pass = "UPDATE users SET password = ? WHERE id = ?";
                if ($stmt_update_pass = $mysqli->prepare($sql_update_pass)) {
                    $stmt_update_pass->bind_param("si", $new_hashed_password, $user_id);
                    if ($stmt_update_pass->execute()) {
                        $feedback_message = '<div class="bg-green-500/20 text-green-400 p-4 rounded-lg mb-6">Password changed successfully.</div>';
                    }
                    $stmt_update_pass->close();
                }
            } else {
                $feedback_message = '<div class="bg-red-500/20 text-red-400 p-4 rounded-lg mb-6">Error changing password. Check details and try again.</div>';
            }
        }
    }
}
?>

<div class="container mx-auto px-6 py-12">
    <h1 class="text-4xl font-extrabold text-center text-white mb-10 animate-on-scroll">Account Details</h1>
    <?php echo $feedback_message; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
        <div class="card p-8 animate-on-scroll">
            <h2 class="text-2xl font-bold text-white mb-6">Update Your Profile</h2>
            <form action="account-details.php" method="POST" class="space-y-4">
                <input type="hidden" name="action" value="update_profile">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-300 mb-1">Username</label>
                    <input type="text" name="username" class="form-input" value="<?php echo htmlspecialchars($username); ?>" required>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email Address</label>
                    <input type="email" name="email" class="form-input" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <button type="submit" class="w-full btn btn-primary mt-4">Save Changes</button>
            </form>
        </div>

        <div class="card p-8 animate-on-scroll">
            <h2 class="text-2xl font-bold text-white mb-6">Change Password</h2>
            <form action="account-details.php" method="POST" class="space-y-4">
                <input type="hidden" name="action" value="change_password">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-300 mb-1">Current Password</label>
                    <input type="password" name="current_password" class="form-input" required>
                </div>
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-300 mb-1">New Password</label>
                    <input type="password" name="new_password" class="form-input" required>
                </div>
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-300 mb-1">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-input" required>
                </div>
                <button type="submit" class="w-full btn btn-primary mt-4">Change Password</button>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>