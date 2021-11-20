<?php
// GNU GENERAL PUBLIC LICENSE
// origin of this project/file:
//   https://github.com/OPESomeRandomPB/board-game
// thanks a lot for all the people, which spread their knowledge on several pages
//


function insertTitle($TEXT) {
	echo "GameSystem - yoursite.123 - $TEXT";

}

function debug_log ($DEBUG_TEXT) {
	$file_name = 'debug/debug.' . date("Y-m-d", time()) . ".txt";
	//opens the file or implicitly creates the file
	$myfile = fopen($file_name, 'a') or die('Cannot open file: '.$file_name);
	// add a timestamp before	
	$LOG_LINE = date ("Y-m-d H-i-s", time()) . ": " . $DEBUG_TEXT . "\n";	
	// write name to the file
	fwrite($myfile, $LOG_LINE);
	// close the file
	fclose($myfile);
}

function	checkDBConn() {
	// check DB-conn
	// do not use "require_once" here... you won't be able to use any of the definitions from config
	include('incl/config.php');
	//to check again:
	//echo $obj_mySqliConn->host_info . "<br>\n";
	if ($obj_mySqliConn->connect_errno) {
   	printf("Connect failed: %s - please check connection-details\n", $obj_mySqliConn->connect_error);
    	$username_err="Connect failed: " . $obj_mySqliConn->connect_error;
    	$password_err="please check connection-details";
    	// Unset all of the session variables
		$_SESSION = array(); 
		// Destroy the session.
		session_destroy();
	}    
	if ($obj_mySqliConn->ping()) {
   	// printf ("Our connection is ok!\n");
	} else {
   	printf ("Error: %s\n", $obj_mySqliConn->error);
	}
}

function convertDateTime($unixTime) {
   return $unixTime;
   // $dt = new DateTime("@$unixTime");
   // return $dt->format('Y-M-d H:i:s');
}

function showInsertMenu() {

	$formPattern = ""; 
	$formPattern .= "<form action=\"game.php\" method=\"post\">";
	$formPattern .= "	<input type=\"hidden\" name=\"action\" value=\"%s\">"; // 1
	$formPattern .= "	<input type=\"submit\" value=\"%s\">";  // 2
	$formPattern .= "</form><br />";

	printf($formPattern, "showInsertGame"      , "create Game");
	printf($formPattern, "showInsertPlayedGame", "create Session");
	printf($formPattern, "showInsertGroup"     , "create group");
	printf($formPattern, "showGameOverview"    , "show games to edit");
	printf($formPattern, "showGamesPlayed"     , "show games played");

}

function showPlayed() {
	// Include config file
	include ('incl/config.php');
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

	// SQL to be executed
	// $sql = "SELECT link_group_name, lid, link_uri, link_text, group_name FROM $TABGAME WHERE user_id = ? order by group_name, link_text";
	$sql = "SELECT g.game_id, g.game_title, p.played_id, p.play_timestamp, p.PLAY_TIME, p.play_stars, p.play_difficulty, p.PLAY_GROUP_PLAYERS_TEXT, p.PLAY_WINNERS FROM $TABGAME g, $TABPLAYED p WHERE g.game_id = p.game_id and PLAY_GROUP_PLAYERS_TEXT like ? order by p.PLAY_TIMESTAMP desc" ;
	$sql = "SELECT g.game_id, g.game_title, p.played_id, p.play_timestamp, p.play_time, p.play_stars, p.play_difficulty, p.PLAY_GROUP_PLAYERS_TEXT, p.PLAY_WINNERS FROM $TABGAME g, $TABPLAYED p WHERE g.game_id = p.game_id order by p.PLAY_TIMESTAMP desc";

	/* create a prepared statement */
	$stmt = $obj_mySqliConn->prepare($sql);

   // Set parameters
	$param_username = $_SESSION['username'];
	// $param_username = 'Oli';

	/* bind parameters for markers (markers = the "?" in the sql-statement) */
	// $stmt->bind_param("s", $param_username);

	/* execute query */
	$stmt->execute();

	// get result
	$result = $stmt->get_result();

	// print all
	printf ("<table border=\"0\">");
	// print the header
	printf ("<tr>");
	//               1            2          3             4              5             6         7
	$HeaderArray = ["Play Date", "Players", "Game Title", "Play length", "difficulty", "rating", "Winner"];
	// https://www.codewall.co.uk/5-ways-to-loop-through-array-php/
	// for ($i = 0; $i < count($HeaderArray); $i++)  {
	foreach ($HeaderArray as $HEADER)  {
   	echo "<th>" . $HEADER ."</th>";
	}
	printf ("</tr>");
	// end of the headers
	
	// g.game_id, g.game_title, p.played_id, p.play_timestamp, p.play_stars, p.play_difficulty, p.PLAY_GROUP_PLAYERS_TEXT, p.PLAY_WINNERS
	$oldGroup = "";
	$even="uneven";
	while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
		$NewGroup = date($row["play_timestamp"]);
/*		printf ("old %s, new %s", $oldGroup, $NewGroup);
		if ( $oldGroup != $NewGroup ) {
			if ( $oldGroup != "") { 
			// close previous date-group
				print ("</table>");
			}
			$oldGroup = $NewGroup;
///////////
/*
	$sqlCountPlayTimestamp = "SELECT count(*) as numberOfGamesPerTimestamp from $TABPLAYED WHERE PLAY_GROUP_PLAYERS_TEXT like ? and PLAY_TIMESTAMP = ?";

	// create a prepared statement
	$stmtCountPlayTimestamp = $obj_mySqliConn->prepare($sqlCountPlayTimestamp);

   // Set parameters
	$param_username = 'Oli';
	$param_timestamp = $oldGroup;

	// bind parameters for markers (markers = the "?" in the sql-statement)
	$stmtCountPlayTimestamp->bind_param("ss", $param_username, $param_timestamp);

	// execute query
	$stmtCountPlayTimestamp->execute();

	// get result
	$resultCountPlay = $stmtCountPlayTimestamp->get_result();
///////////

			printf ("<tr><td rowspan=\"%s\">%s</td>", $resultCountPlay['numberOfGamesPerTimestamp'], date($row["play_timestamp"]) );
*/
	//	}


		// and now show the games, which were played 
		if ($even == "even") {$even = "uneven";} else {$even="even";}
	//                  1            2          3             4              5             6         7
	// $HeaderArray = ["Play Date", "Players", "Game Title", "Play length", "difficulty", "rating", "Winner"];
	    printf("<tr class=\"%s\">", $even);
	// without date-grouping
		$dateVarName = convertDateTime($row["play_timestamp"]);
		 printf ("<td>%s</td>", $dateVarName);
	    printf("<td>%s</td>", $row["PLAY_GROUP_PLAYERS_TEXT"]); // 
	    printf("<td>%s (%s)</td>", $row["game_title"], $row["game_id"]); // 
	    printf("<td>%s</td>", $row["play_time"]); // 
	    printf("<td>%s</td>", $row["play_difficulty"]); // 
	    printf("<td>%s</td>", $row["play_stars"]); // 
	    printf("<td>%s</td>", $row["PLAY_WINNERS"]); // 
	    printf("</tr>\n");
	}

	mysqli_stmt_free_result($stmt);

	mysqli_stmt_close($stmt);

}

function getGameDetails($gameId) {

	$returnArray = array("gi" => 0
		, "gt" => "no game found"
		, "gpl" => 0	// GamePlayerMin/Low
		, "gph" => 0	// GamePlayerMax/High
		, "gal" => 0	// GameAgeMin/Low
		, "gah" => 0	// GameAgeMax/High
		, "gtl" => 0	// GameTimeMin/Low
		, "gth" => 0	// GameTimeMax/High
		, "gef" => 0	// GameEstimatedFrom
		);
	
	return $returnArray;
/*
$wochentage = array(
"so" => "Sonntag",
"mo" => "Montag",
"di" => "Dienstag",
"mi" => "Mittwoch",
"do" => "Donnerstag",
"fr" => "Freitag",
"sa" => "Samstag");

echo $wochentage["mo"];
*/



}

function showInsertGame() {
	print("Let's insert a game");

/*
GAME_ID                    
GAME_TITLE                 
GAME_PLAYER_MIN            
GAME_PLAYER_MAX            
GAME_AGE_MIN               
GAME_AGE_MAX               
GAME_ESTIM_TIME_MIN        
GAME_ESTIM_TIME_MAX        
GAME_EXTENSION_FROM_GAME   
GAME_EXTENSION_FROM_GAME_ID
*/
	if ( isset($_REQUEST['gameId']) ) {
		$gameId = $_REQUEST['gameId'];
	} else {
		$gameId = 0;
	}
	$gameDetails = getGameDetails($gameId);	

	$formPattern = "";
	$formPattern .= "<form action=\"game.php\" method=\"post\">";
	$formPattern .= "<input type=\"hidden\" name=\"GameId\" value=\"%s\">"; // 1
	$formPattern .= "<table>";

	printf($formPattern, $gameDetails["gi"]);

	$oneRowPattern = "<tr><th><label for=\"%s\">%s</label></th>		<td><input type=\"%s\"		size=\"%d\"	name=\"%s\"		placeholder=\"%s\"	 value=\"%s\" %s></td></tr>";
	printf($oneRowPattern, "GameTitle", "Game title", "text", 30, "GameTitle", "Skat", $gameDetails["gt"], "");
	printf($oneRowPattern, "GamePlayMin", "player min", "number", 3, "GamePlayMin", "2", $gameDetails["gpl"], "min=\"1\"");
	

	$formPattern .= " ";
	$formPattern .= " ";
	$formPattern .= " ";
	$formPattern .= " ";
	
	
	?>
	
	
		<tr><th><label for="GamePlayMax"		>player max:	</label></th>		<td><input type="number"	size= "3"	name="GamePlayMax"	min="1"	placeholder="4"		 value="<?php $gameDetails["gph"] ?>"></td></tr>
		<tr><th><label for="GameAgeMin"		>age min:		</label></th>		<td><input type="number"	size= "3"	name="GameAgeMin"		min="1"	placeholder="6"		 value="<?php $gameDetails["gal"] ?>"></td></tr>
		<tr><th><label for="GameAgeMax"		>age max:		</label></th>		<td><input type="number"	size= "3"	name="GameAgeMax"		min="1"	placeholder="99"		 value="<?php $gameDetails["gah"] ?>"></td></tr>
		<tr><th><label for="GameTimeMin"		>estim. time min:		</label></th>		<td><input type="number"	size= "3"	name="GameTimeMin"		min="1"	placeholder="10"		 value="<?php $gameDetails["gtl"] ?>"></td></tr>
		<tr><th><label for="GameTimeMax"		>estim. time max:		</label></th>		<td><input type="number"	size= "3"	name="GameTimeMax"		min="1"	placeholder="270"		 value="<?php $gameDetails["gth"] ?>"></td></tr>

		<tr><th><label for="GameExtendFromGameId"    >extend from game:    </label></th>		<td>
        <select name="GameExtendFromGameId">
        		<option value="" selected> </option>
        </select>
		</td></tr>

		</table>
		<input type="hidden" name="action" value="insertUpdateGame">
		<input type="submit" value="create">
	</form>
	<?php
}

function showInsertPlayedGame() {
	
	print("Let's insert a played game");
	?>
	<form action="game.php" method="post">
		<table>
		<tr><th><label for="PlayTimestamp" >Date:      </label></th>		<td><input type="date" size="30" name="PlayTimestampD">
		                                                                      <input type="text" size="10" name="PlayTimestampT" placeholder="01:23" pattern="([0-1]{1}[0-9]{1}|20|21|22|23):[0-5]{1}[0-9]{1}"  ></td></tr>		
		<tr><th><label for="PlayPlayers"   >Players:   </label></th>		<td><input type="text" size="50" name="PlayPlayers"    placeholder="Friend1, Friend2, you"></td></tr>		
		<tr><th><label for="PlayGameTitle" >Game:      </label></th>		<td><input type="text" size="50" name="PlayGameTitle"  placeholder="Skat"                 ></td></tr>		
		<tr><th><label for="PlayLength"    >Length:    </label></th>		<td><input type="text" size="10" name="PlayTime"       placeholder="01:30" pattern="([0-1]{1}[0-9]{1}|20|21|22|23):[0-5]{1}[0-9]{1}"></td></tr>		

		<tr><th><label for="PlayDifficulty">Difficulty:</label></th>		<td>
			<input type="radio" name="PlayDifficulty" id="1" value="1"><label for="1">easy</label>
			<input type="radio" name="PlayDifficulty" id="3" value="3"><label for="3">mid</label>
			<input type="radio" name="PlayDifficulty" id="5" value="5"><label for="5">hard</label>
		</td></tr>

		<tr><th><label for="PlayRating"    >Rating:    </label></th>		<td>
        <input type="range" name="PlayRating" value="40" min="10" max="50"
            oninput="document.getElementById('pRating').innerHTML = '(' + this.value / 10 + ')'" />
        <label id="pRating">(4)</label>
		</td></tr>

		<tr><th><label for="PlayWinner"    >Winner:    </label></th>		<td><input type="text" size="50" name="PlayWinner"     placeholder="F1, F2, F3, theGame"></td></tr>		
		</table>
		<input type="hidden" name="action" value="insertPlayedGame">
		<input type="submit" value="create">
	</form>
	<?php
}

function checkGameName($GameNameToCheck, $anyIndicator) {
	// Include config file
	include ('incl/config.php');
   mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	
	// SQL to be executed
	if ( $anyIndicator ) {
		// true = get any entry to title/name
		$sqlCheckGame = "select game_id from $TABGAME where GAME_TITLE = ?";
	} else {	
		// printf ("false = get last inserted %s", $GameNameToCheck);
		$sqlCheckGame = "select game_id from $TABGAME where GAME_TITLE = ? ORDER BY game_id desc";
	}
	// printf("will check with %s to Game %s\n", $sqlCheckGame, $GameNameToCheck);
	// create a prepared statement
	$stmt = $obj_mySqliConn->prepare($sqlCheckGame) or die ("died prepare");
	// bind
	$stmt->bind_param("s", $BindParam) or die("died while bind");
	$BindParam = $GameNameToCheck;
	/* execute query */
	$stmt->execute() or die("died while execute");
	
	/* bind result variables */
	$stmt->bind_result($res_game_id);

	/* fetch value */
	$stmt->fetch();

	debug_log($GameNameToCheck . " has the ID " . $res_game_id);
	
//	$row=$stmt->get_result();
//	$row->fetch_row(MYSQLI_ASSOC) or die ("something happened in checkGameName while ". $GameNameToCheck . " but I tried " . $row["game_id"]);
//	printf("accessed game_id: %s\n\n", $row["game_id"]);
	return $res_game_id;
	
//            mysqli_stmt_bind_param($stmt, "s", $param_username);
// Set parameters
//      $param_username = $username;
// Attempt to execute the prepared statement
//      if(mysqli_stmt_execute($stmt)){
// Store result
//        mysqli_stmt_store_result($stmt);
// Check if username exists, if yes then verify password
//        if(mysqli_stmt_num_rows($stmt) == 1){                    
// Bind result variables
//           mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
//           if(mysqli_stmt_fetch($stmt)){
	
	
}

function createGameFromSession ($GameName) {
	// Include config file
	include ('incl/config.php');
	// SQL to be executed
	$sqlInsert = "INSERT INTO $TABGAME (GAME_TITLE) value (?)";
	// create a prepared statement
	$stmt = $obj_mySqliConn->prepare($sqlInsert);
	// bind
	$stmt->bind_param("s", $GameName);
	/* execute query */
	// use exec() because no results are returned
	$stmt->execute();
	$last_id = checkGameName($GameName, false);
	debug_log ("New record created to game " . $GameName . " successfully. Last inserted ID is: " . $last_id);

	return $last_id;
}

function insertPlayed() {
	// Include config file
	include ('incl/config.php');
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

	$param_gameid=isset($_REQUEST['PlayGameID']);
	if ( is_numeric($param_gameid) ) {
	// gameID given -> lets use it	
		debug_log("game-ID given " . $param_gameid . ", will use");
	} else {
		// gameId empty -> check name
		// set parameters
		$param_gamename  = $_REQUEST['PlayGameTitle'];
		$found_gameID = checkGameName($param_gamename, true);
		if ( $found_gameID > 0 ) {
			// gameID found -> lets use it
			$param_gameid = $found_gameID;
			debug_log("game-ID found " . $param_gameid . ", will use<br />");
		} else {
			// game not found -> create new game
			debug_log("game " . $param_gamename . " not found " . $param_gameid . ", will create");
			$param_gameid = createGameFromSession($param_gamename);
			debug_log("game-ID after createfromSession " . $param_gameid . ", will use");

		}
	}	// end of gameID-check

	//----------------
	$sqlInsert  = "INSERT INTO $TABPLAYED";
	//              1        2              3                        4
	$sqlInsert .= "(GAME_ID, PLAY_GROUP_ID, PLAY_GROUP_PLAYERS_TEXT, PLAY_TIMESTAMP, ";
	//             5          6           7                8
	$sqlInsert .= "PLAY_TIME, PLAY_STARS, PLAY_DIFFICULTY, PLAY_WINNERS";
	$sqlInsert .= ")";
	//                    1 2 3 4 5 6 7 8
	$sqlInsert .= "value (?,?,?,?,?,?,?,?)";
	// create a prepared statement
	$stmt = $obj_mySqliConn->prepare($sqlInsert);

	//----------------
   // Set parameters
   // auto set:
   //   PLAYED_ID
	// set already:
	//   $param_gameid		GAME_ID
	//
	// pending feature:
	//		PLAY_PLACE              
	//		PLAY_PLACE_ID           
	//
	// set values
	// 1 int = game_id
	// 2 int = play_group_id alternatively
	// 3 str = players_text
	$param_play_group_id = 1;
	$param_play_group_players_text = $_REQUEST['PlayPlayers'];
	// 4 str = played when
	$param_play_date = date("Y-m-d H:i:s", strtotime($_REQUEST['PlayTimestampD'] . ' ' . $_REQUEST['PlayTimestampT'] . ":00"));
	debug_log ("Timestamp: " . $param_play_date . " from input: . " . $_REQUEST['PlayTimestampD'] . " and " . $_REQUEST['PlayTimestampT'] );
	// 5 str = played how long ( time )
	$param_play_length = date("H:i:s", strtotime($_REQUEST['PlayTime'] . ':00'));
	debug_log ("and this length: " . $param_play_length . " from input " . $_REQUEST['PlayTime']);
	// 6 int = rating/stars
	$param_play_stars = $_REQUEST['PlayRating'];
	// 7 int = difficulty
	$param_play_diff = $_REQUEST['PlayDifficulty'];
	// 8 str = winners
	$param_play_win = $_REQUEST['PlayWinner'];

	//----------------
	// bind
	//                 12345678    1              2                     3                               4                 5                   6                  7                 8
	$stmt->bind_param("iisssiis", $param_gameid, $param_play_group_id, $param_play_group_players_text, $param_play_date, $param_play_length, $param_play_stars, $param_play_diff, $param_play_win);

	/* execute query */
	// use exec() because no results are returned
	$stmt->execute();
	

	mysqli_stmt_close($stmt);

}

function showGameOverview() {
	// Include config file
	include ('incl/config.php');
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

	// SQL to be executed
	$sql  = "SELECT GAME_ID        , GAME_TITLE";
	$sql .= ", GAME_PLAYER_MIN     , GAME_PLAYER_MAX";
	$sql .= ", GAME_AGE_MIN        , GAME_AGE_MAX";
	$sql .= ", GAME_ESTIM_TIME_MIN , GAME_ESTIM_TIME_MAX";
	$sql .= ", GAME_EXTENSION_FROM_GAME,GAME_EXTENSION_FROM_GAME_ID";
	$sql .= " from $TABGAME order by game_title";
	
	debug_log($sql);
	/* create a prepared statement */
	$stmt = $obj_mySqliConn->prepare($sql);

   // Set parameters

	/* bind parameters for markers (markers = the "?" in the sql-statement) */
	// $stmt->bind_param("s", $param_username);

	/* execute query */
	$stmt->execute();

	// get result
	$result = $stmt->get_result();

	// print all
	printf ("<table border=\"0\">");
	// print the header
	printf ("<tr>");
	include_once('incl/language.php');
	$HeaderArray = ["game_title,1"        // 1 
	               ,"game_player,2"       // 2
	               //,"game_player_max"   // 3
	               ,"game_age,2"          // 4
	               //,"game_age_max"      // 5
	               ,"game_estim_time,2"   // 6
	               //,"game_estim_time_max" // 7
	               ,"game_ext_from_game,2"   // 8
	               //,"game_ext_from_game_name" // 9
	               ];
	// https://www.codewall.co.uk/5-ways-to-loop-through-array-php/
	// for ($i = 0; $i < count($HeaderArray); $i++)  {
	foreach ($HeaderArray as $HEADER)  {
		$HEADERARR=explode(",", $HEADER);
		if ( $HEADERARR["1"] == "" ) { $HEADERARR["1"] = 1; };
   	echo "<th colspan=\"" . $HEADERARR["1"] . "\">" . getTextToLanguageKey($HEADERARR['0'], $_SESSION['username']) ."</th>";
	}
	printf ("</tr>");
	// end of the headers
	
	$oldGroup = "";
	$even="uneven";
	
	$columnsToShow = [
 "GAME_TITLE"                 
,"GAME_PLAYER_MIN"            
,"GAME_PLAYER_MAX"           
,"GAME_AGE_MIN"            
,"GAME_AGE_MAX"              
,"GAME_ESTIM_TIME_MIN"        
,"GAME_ESTIM_TIME_MAX"        
,"GAME_EXTENSION_FROM_GAME"   
,"GAME_EXTENSION_FROM_GAME_ID"
];
	
	while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

		if ($even == "even") {$even = "uneven";} else {$even="even";}
	    printf("<tr class=\"%s\">", $even);

		foreach ($columnsToShow as $toShow)  {
   		echo "<td>" . $row[$toShow] ."</td>";
		}
		
		$formPattern = "<td><form>";
		$formPattern .=	"  <form action=\"game.php\" method=\"post\">";
		$formPattern .=	"  <input type=\"hidden\" name=\"action\" value=\"%s\">";  // 1
		$formPattern .=	"  <input type=\"hidden\" name=\"gameId\" value=\"%s\">";  // 2
		$formPattern .=	"  <input type=\"submit\" value=\"%s\">";                  // 3
		$formPattern .=	"</form>";
		printf($formPattern, "showInsertGame", $row["GAME_ID"], getTextToLanguageKey("edit game", $_SESSION['username']));		

		printf ("</tr>");

	}

	mysqli_stmt_free_result($stmt);

	mysqli_stmt_close($stmt);

}


?>
