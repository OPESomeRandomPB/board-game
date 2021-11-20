<?php
// GNU GENERAL PUBLIC LICENSE
// origin of this project/file:
//   https://github.com/OPESomeRandomPB/board-game
// thanks a lot for all the people, which spread their knowlegde on several pages
//

	$mySqli_site="server.localhost";
	$mySqli_username="username";
	$mySqli_password="password";
	$mySqli_database="database";

	// procedural
	$mysqliConn = mysqli_connect($mySqli_site, $mySqli_username, $mySqli_password, $mySqli_database);
	// object oriented	
	$obj_mySqliConn = new mysqli($mySqli_site, $mySqli_username, $mySqli_password, $mySqli_database);

	//to check temporarily:
	//echo $obj_mySqliConn->host_info . "<br>\n";

$basedir = dirname(__FILE__)."/..";

	$BACKGRNDCOLOR="purple";
	$BACKGRNDCOLOR="white";
	$BACKGRNDCOLOR="grey";

// define table names
	$TABUSER="game_sys_users";
	$TABGAME="game_sys_game";
	$TABPLAYED="game_sys_game_played";
	$TABACCESS="game_sys_access";
	
	// ------- constants for MySQL-Bind
   // i 	the related variable has type integer
   // d 	the related variable has type double
   // s 	the related variable has type string
   // b 	the related variable is a BLOB and will be send paket-whise 
$globMySQLBindInteger = 'i';
$globMySQLBindDouble  = 'd';
$globMySQLBindString  = 's';
$globMySQLBindBlob    = 'b';

	// ------- constants for php-file
   // w 	open file in write mode
   // a 	open file in append mode
   // r 	open file in write mode
$globPhpFileWriteNew    = 'w';
$globPhpFileWriteAppend = 'a';
$globPhpFileRead        = 'r';


$timeoutminutes = 10 * 60;

?>
