<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
   // header("location: welcome.php");
    if ($_SESSION["permission"] === 3) {
        header("location: manager.php");
    }
    else if ($_SESSION["permission"]  === 2) {
        header("location: staff.php");
    }
    else {
        // Redirect user to welcome page
        header("location: items.php");   
    }
    exit;
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Check if username is empty
    if(empty(trim($_POST["user"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["user"]);
    }
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
	    $sql = "SELECT idno, UserID, Pass, Permission 
		    FROM Users
		    WHERE UserID = ? "; 
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            // Set parameters
            mysqli_stmt_bind_param($stmt, "s", $user);
            
            $user = $username;
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                
                    mysqli_stmt_bind_result($stmt, $param_id, $user, $pwd, $permission); // actual password and userId from database. 
                    if(mysqli_stmt_fetch($stmt)){
                        if ($password == $pwd) { // temporary, need to find a way to check
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            
                            $_SESSION["loggedin"] = true;
                            $_SESSION["user"] = $user;   
                            $_SESSION["permission"] = $permission;
                            $_SESSION["id"] = $param_id;
                    
                            if ($permission == 3) {
                                header("location: manager.php");
                            }
                            else if ($permission == 2) {
                                header("location: staff.php");
                            }
                            else {
                                // Redirect user to welcome page
                                header("location: items.php");   
                            }
                        } else{
                            // Display an error message if password is not valid
                            $password_err = "The password you entered was not valid.";
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $username_err = "No account found with that username.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        // Close statement
        mysqli_stmt_close($stmt);
    }
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="user" class="form-control" value="<?php echo $user; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
        </form>
    </div>    
</body>
</html>
