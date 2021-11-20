---------------------------------------------------------------------------------
-- GNU GENERAL PUBLIC LICENSE
-- origin of this project/file:
--   https://github.com/OPESomeRandomPB/board-game
-- thanks a lot for all the people, which spread their knowlegde on several pages
--
-- Script to create the games
--
---------------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS game_sys_game (
	  GAME_ID	int NOT NULL AUTO_INCREMENT PRIMARY KEY
	, GAME_TITLE	VARCHAR(350) NOT NULL
	, GAME_PLAYER_MIN 	int
	, GAME_PLAYER_MAX 	int
	, GAME_AGE_MIN 	int
	, GAME_AGE_MAX 	int
	, GAME_ESTIM_TIME_MIN 	int
	, GAME_ESTIM_TIME_MAX 	int
	, GAME_EXTENSION_FROM_GAME	VARCHAR(350)
	, GAME_EXTENSION_FROM_GAME_ID	int
) 
