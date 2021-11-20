<?php
// GNU GENERAL PUBLIC LICENSE
// origin of this project/file:
//   https://github.com/OPESomeRandomPB/board-game
// thanks a lot for all the people, which spread their knowlegde on several pages
//

// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if ( isset($_SESSION["loggedin"]) ) {
	require_once ('incl/session.control.php');
	if ( check_session_timeout() ) {
	  $_SESSION['lo_reason'] = "timeout";
     header("location: logout.php");		
	}
}

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}

	// check DB-conn
  	require_once('incl/functions.php');
	checkDBConn();


if ( isset($_REQUEST["action"]) && $_REQUEST["action"] == "insertPlayedGame" ){
    	require_once('incl/functions.php');
		insertPlayed();
	// and now set "show Content"
		$_REQUEST["action"] = "showContent";
}

if ( ! isset($_REQUEST["action"]) ){
	// set default -> "show Content"
		$_REQUEST["action"] = "showContent";
}

?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php  require_once('incl/functions.php');
                  insertTitle("played Games"); ?></title>
	 <link rel="stylesheet" type="text/css" href="styles/styles.css" />
</head>
<body>
    <div class="userlogin">
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to our site.</h1>
 	   <p>
 	       <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
   	     <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a>
    	</p>
    	Timeout: <?php echo date('Y-m-d H:i:s', $_SESSION["timeout"]); ?><br>
    </div>
	now let's put some butter to the fishes!<br />
    <div class="insertTopRight">
    holla
    <?php
    	require_once('incl/functions.php');
    	showInsertMenu();
    ?>
    </div>

    <?php
    	require_once('incl/functions.php');
    	switch ($_REQUEST["action"]) {
		case 'showInsertGame': {
					showInsertGame();
					break;
				}
		case 'showGameOverview': {
					showGameOverview();
					break;
				}
		case 'showInsertPlayedGame': {
					showInsertPlayedGame();
					break;
				} 
		case 'showGamesPlayed':
		default:	{
					showPlayed();
					break;
				}
    	}
    	debug_log("am I here at the end");
    ?>
</body>
</html>
