---------------------------------------------------------------------------------
-- GNU GENERAL PUBLIC LICENSE
-- origin of this project/file:
--   https://github.com/OPESomeRandomPB/board-game
-- thanks a lot for all the people, which spread their knowledge on several pages
--
-- Script to create the game-sessions
--
---------------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS game_sys_game_played (
	  PLAYED_ID	int NOT NULL AUTO_INCREMENT PRIMARY KEY
	, GAME_ID	int NOT NULL 
	, PLAY_GROUP_ID	int NOT NULL
	, PLAY_GROUP_PLAYERS_TEXT	VARCHAR(350)
	, PLAY_PLACE	VARCHAR(50)
	, PLAY_PLACE_ID	int
	, PLAY_TIMESTAMP	DATETIME  -- yyyy-mm-dd HH:MM:SS ( from 1000-01-01 00:00:00 to 9999-12-31 23:59:59)
	, PLAY_TIME		TIME
	, PLAY_STARS		decimal  COMMENT '1 = bad // 5 = awesome'
	, PLAY_DIFFICULTY	int	   COMMENT '1 = easy // 3 = mid // 5 = hard'
	, PLAY_WINNERS	VARCHAR(350)
)
