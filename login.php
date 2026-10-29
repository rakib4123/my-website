<?php
require_once "config.php";
 
// Check if the user is already logged in, if yes then redirect them to the home page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.php");
    exit;
}
 
$email = $password = "";
$login_err = "";
 
// Process form data when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (empty(trim($_POST["email"]))) {
        $login_err = "Please enter email.";
    } else {
        $email = trim($_POST["email"]);
    }
    
    if (empty(trim($_POST["password"]))) {
        $login_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    if (empty($login_err)) {
        $sql = "SELECT id, username, password FROM users WHERE email = ?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $email);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $username, $hashed_password);
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, start the session
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            // Redirect user to home page
                            header("location: index.php");
                            exit();
                        } else {
                            $login_err = "Invalid email or password.";
                        }
                    }
                } else {
                    $login_err = "Invalid email or password.";
                }
            }
            $stmt->close();
        }
    }
    $mysqli->close();
}
?>
 
<?php include 'header.php'; ?>
<div class="container mx-auto px-6 py-12">
    <div class="max-w-md mx-auto card p-8">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Login</h2>
        <?php if(isset($_GET['registration']) && $_GET['registration'] == 'success') echo '<div class="bg-green-100 text-green-700 p-3 rounded mb-4">Registration successful! You can now log in.</div>'; ?>
        <?php if(!empty($login_err)) echo '<div class="bg-red-100 text-red-700 p-3 rounded mb-4">' . $login_err . '</div>'; ?>
        <form action="login.php" method="post" class="space-y-6">
            <div>
                <label for="email" class="block font-semibold text-gray-700">Email</label>
                <input type="email" name="email" class="mt-1 form-input" required>
            </div>
            <div>
                <label for="password" class="block font-semibold text-gray-700">Password</label>
                <input type="password" name="password" class="mt-1 form-input" required>
            </div>
            <div><button type="submit" class="w-full btn btn-primary">Login</button></div>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>