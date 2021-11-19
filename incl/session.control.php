<?php
// GNU GENERAL PUBLIC LICENSE
// origin of this project/file:
//   https://github.com/OPESomeRandomPB/board-game
// thanks a lot for all the people, which spread their knowlegde on several pages
//

function check_session_timeout () {
	
	include_once ('config.php');	
	
	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
		return true;
	}

	if ( !isset ($_SESSION['timeout']) || $_SESSION['timeout'] < time() ) {
		return true;	
	}
	// if no timeout, then set new timer
	// 10 * 60 = 10 Minutes
	$_SESSION['timeout'] = time() + $timeoutminutes;
	return false;	
}

?>
