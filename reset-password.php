<?php
// GNU GENERAL PUBLIC LICENSE
// origin of this project/file:
//   https://github.com/OPESomeRandomPB/board-game
// thanks a lot for all the people, which spread their knowledge on several pages
//

// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect to login page
if ( isset($_SESSION["loggedin"]) ) {
	require_once ('incl/session.control.php');
	if ( check_session_timeout() ) {
	  $_SESSION['lo_reson'] = "timeout";
     header("location: logout.php");		
	}
}
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    //header("location: login.php");
    header("location: index.php");
    exit;
}
 
// Include config file
require_once "incl/config.php";
 
// Define variables and initialize with empty values
$old_password = $new_password = $confirm_password = "";
$old_password_err = $new_password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
	// Validate old password
	if(empty(trim($_POST["old_password"]))){
        $old_password_err = "Please enter the old password.";     
    } else{
        $old_password = trim($_POST["old_password"]);
	}
	global $TABUSER, $mysqliConn;
	///###### check if old PW is given correctly
        $sql = "SELECT user_id, user_name, password FROM $TABUSER WHERE user_name = ?";
        
        if($stmt = mysqli_prepare($mysqliConn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $_SESSION["username"];
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $param_username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($old_password, $hashed_password)){
                            // Password is correct, so start a new session
        							$old_password_err = "";
        						} else {
 						       $old_password_err = "old password is not correct";             							
        						}
        					} else {
 						       $old_password_err = "should not happen. somehow user disappeared (fetch)";	
        					}
     					} else {
					       $old_password_err = "should not happen. somehow user disappeared (num)";	
     					}
  					} else {
 				       $old_password_err = "should not happen. somehow user disappeared (attemp)";
      			}
				} else {
			       $old_password_err = "should not happen. somehow user disappeared (bind)";
     			}        					
	///###### end check if old PW is given correctly

    // Validate new password
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Please enter the new password.";     
    } elseif(strlen(trim($_POST["new_password"])) < 6){
        $new_password_err = "Password must have at least 6 characters.";
    } else{
        $new_password = trim($_POST["new_password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm the password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
        
    // Check input errors before updating the database
    if(empty($old_password_err) && empty($new_password_err) && empty($confirm_password_err)){
        // Prepare an update statement
        $sql = "UPDATE $TABUSER SET password = ? WHERE user_id = ?";
        
        if($stmt = mysqli_prepare($mysqliConn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "si", $param_password, $param_id);
            // Set parameters
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];
            //echo $sql . "#" .  $param_password_prev . "#" . $param_password . "#" . $param_id. "#" . $old_password . "#" . $new_password;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Password updated successfully. Destroy the session, and redirect to login page
                session_destroy();
                //header("location: login.php");
                header("location: index.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($mysqliConn);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php  require_once('incl/functions.php');
                  insertTitle("Reset Password"); ?></title>
    <link rel="stylesheet" type="text/css" href="styles/styles.css" />
</head>
<body>
    <div class="userlogin">
        <h2>Reset Password</h2>
        <p>Please fill out this form to reset your password.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
            <div class="form-group_name <?php echo (!empty($old_password_err)) ? 'has-error' : ''; ?>">
                <label>current Password</label>
                <input type="password" name="old_password" class="form-control" value="<?php echo $old_password; ?>">
                <span class="help-block"><?php echo $old_password_err; ?></span>
            </div>
            <div class="form-group_name <?php echo (!empty($new_password_err)) ? 'has-error' : ''; ?>">
                <label>New Password</label>
                <input type="password" name="new_password" class="form-control" value="<?php echo $new_password; ?>">
                <span class="help-block"><?php echo $new_password_err; ?></span>
            </div>
            <div class="form-group_name <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group_name">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a class="btn btn-link" href="index.php">Cancel</a>
            </div>
        </form>
    </div>    
</body>
</html>
