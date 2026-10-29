<?php
require_once "config.php";
 
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.php");
    exit;
}
 
$username = $email = $password = "";
$username_err = $email_err = $password_err = "";
$feedback_message = "";
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else {
        $sql = "SELECT id FROM users WHERE username = ?";
        if($stmt = $mysqli->prepare($sql)){
            $stmt->bind_param("s", $param_username);
            $param_username = trim($_POST["username"]);
            if($stmt->execute()){
                $stmt->store_result();
                if($stmt->num_rows == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            }
            $stmt->close();
        }
    }
    
    // Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter an email address.";
    } else {
        $sql = "SELECT id FROM users WHERE email = ?";
        if($stmt = $mysqli->prepare($sql)){
            $stmt->bind_param("s", $param_email);
            $param_email = trim($_POST["email"]);
            if($stmt->execute()){
                $stmt->store_result();
                if($stmt->num_rows == 1){
                    $email_err = "This email is already taken.";
                } else {
                    $email = trim($_POST["email"]);
                }
            }
            $stmt->close();
        }
    }

    // Validate password
    if(empty(trim($_POST["password"])) || strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have at least 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Check for errors before inserting into the database
    if(empty($username_err) && empty($email_err) && empty($password_err)){
        $sql = "INSERT INTO users (username, email, password, is_verified) VALUES (?, ?, ?, 1)";
        
        if ($stmt = $mysqli->prepare($sql)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            
            if ($stmt->execute()) {
                // Redirect to login page with a success message flag
                header("location: login.php?registration=success");
                exit();
            }
        }
    } else {
        $feedback_message = '<div class="bg-red-100 border-red-400 text-red-700 p-4 rounded-lg">Please fix the errors below.</div>';
    }
    
    $mysqli->close();
}
?>
 
<?php include 'header.php'; ?>
<div class="container mx-auto px-6 py-12">
    <div class="max-w-md mx-auto card p-8 animate-on-scroll">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Create an Account</h2>
        <?php if(!empty($feedback_message)) echo $feedback_message; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="space-y-4 mt-6">
            <div>
                <label for="username" class="block font-semibold text-gray-700">Username</label>
                <input type="text" name="username" class="mt-1 form-input <?php echo (!empty($username_err)) ? 'border-red-500' : ''; ?>" value="<?php echo htmlspecialchars($username); ?>" required>
                <span class="text-red-500 text-sm"><?php echo $username_err; ?></span>
            </div>
            <div>
                <label for="email" class="block font-semibold text-gray-700">Email</label>
                <input type="email" name="email" class="mt-1 form-input <?php echo (!empty($email_err)) ? 'border-red-500' : ''; ?>" value="<?php echo htmlspecialchars($email); ?>" required>
                <span class="text-red-500 text-sm"><?php echo $email_err; ?></span>
            </div>
            <div>
                <label for="password" class="block font-semibold text-gray-700">Password</label>
                <input type="password" name="password" class="mt-1 form-input <?php echo (!empty($password_err)) ? 'border-red-500' : ''; ?>" required>
                <span class="text-red-500 text-sm"><?php echo $password_err; ?></span>
            </div>
            <div><button type="submit" class="w-full btn btn-primary mt-2">Create Account</button></div>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>