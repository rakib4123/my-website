<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (isset($_SESSION["admin_loggedin"]) && $_SESSION["admin_loggedin"] === true) { header("location: index.php"); exit; }

// IMPORTANT: Change to your own secure credentials
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'Hossain@#*&6767');
$login_err = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (trim($_POST["username"]) === ADMIN_USERNAME && trim($_POST["password"]) === ADMIN_PASSWORD) {
        $_SESSION["admin_loggedin"] = true;
        header("location: index.php");
    } else { $login_err = "Invalid username or password."; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="card p-8 w-full max-w-sm">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Admin Panel</h2>
            <?php if (!empty($login_err)) echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">' . $login_err . '</div>'; ?>
            <form action="login.php" method="post" class="space-y-6">
                <div><label for="username" class="block font-semibold text-gray-700">Username</label><input type="text" name="username" class="mt-1 form-input" required></div>
                <div><label for="password" class="block font-semibold text-gray-700">Password</label><input type="password" name="password" class="mt-1 form-input" required></div>
                <div><button type="submit" class="w-full btn btn-primary">Login</button></div>
            </form>
        </div>
    </div>
</body>
</html>