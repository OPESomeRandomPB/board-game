<?php
// GNU GENERAL PUBLIC LICENSE
// origin of this project/file:
//   https://github.com/OPESomeRandomPB/board-game
// thanks a lot for all the people, which spread their knowlegde on several pages
//

// Include config file
require_once "incl/config.php";
 
// Define variables and initialize with empty values
$username = $password = $confirm_password = $nickname = "";
$username_err = $password_err = $confirm_password_err = $nickname_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
	 $username = trim($_POST["username"]);
    if( empty($username) ){
        $username_err = "Please enter a username.";
    } else{
        // Prepare a select statement
        $sql = "SELECT user_id FROM $TABUSER WHERE user_name = ?";

        if($stmt = mysqli_prepare($mysqliConn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
							// nothing to be done... $username already set
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        } else {
					  echo("Error description: " . mysqli_error($mysqliConn) . "<br>\n");
					  die ();
        }
    }

    // Validate nickname
    if(empty(trim($_POST["nickname"]))){
        $nickname_err = "Please enter a nickname.";     
    } else{
        $nickname = trim($_POST["nickname"]);
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have at least 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
	 // Mandatory:
	 // Username - which is used to login
	 // password - well.. yes
	 // confirm_password - needs to match
	 // nickname - there can be many Angelas, Boris', Emanuels, Joes, Vladimirs
// debug	          printf ("try1 $sql with $username and $nickname <br>\n");
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($nickname_err) ){
// debug	          printf ("try2 error? $username_err uor $password_err por $confirm_password_err cpor $nickname_err nor<br>\n");
        
        // Prepare an insert statement
        $sql = "INSERT INTO $TABUSER (user_name, password, nick_name, little_salt) VALUES (?, ?, ?, ?)";
         printf ("try3 $sql with $username and $nickname<br>\n");
        if($stmt = mysqli_prepare($mysqliConn, $sql)){
            // Bind variables to the prepared statement as parameters
         printf ("try4 $sql with $username and $nickname<br>\n");
         $some_salt = "wewrter3434";
            mysqli_stmt_bind_param($stmt, "ssss", $param_username, $param_password, $param_nickname, $some_salt);
            
            // Set parameters
            $param_username = $username;
            $param_nickname = $nickname;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
         printf ("try5 $sql with $username and $nickname<br>\n");
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: index.php");
            } else{
					  echo("Error description: " . mysqli_error($mysqliConn) . "<br>\n");
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
         printf ("try-end $sql with $username and $nickname<br>\n");
    
    // Close connection
    mysqli_close($mysqliConn);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
        <title><?php  require_once('incl/functions.php');
                  insertTitle("Sign up"); ?></title>
    <link rel="stylesheet" type="text/css" href="styles/styles.css" />
</head>
<body>
    <div class="userlogin">
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group_name <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group_name <?php echo (!empty($nickname_err)) ? 'has-error' : ''; ?>">
                <label>Spitzname</label>
                <input type="text" name="nickname" class="form-control" value="<?php echo $nickname; ?>">
                <span class="help-block"><?php echo $nickname_err; ?></span>
            </div>    
            <div class="form-group_name <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group_name <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group_name">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
            <p>Already have an account? <a href="index.php">Login here</a>.</p>
        </form>
    </div>    
</body>
</html>
