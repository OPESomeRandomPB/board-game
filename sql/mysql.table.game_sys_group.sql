---------------------------------------------------------------------------------
-- GNU GENERAL PUBLIC LICENSE
-- origin of this project/file:
--   https://github.com/OPESomeRandomPB/board-game
-- thanks a lot for all the people, which spread their knowledge on several pages
--
-- Script to create the game-groups
--
---------------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS game_sys_group_name (
	  GROUP_ID	int NOT NULL AUTO_INCREMENT  PRIMARY KEY
	, GROUP_FOUNDER_ID int NOT NULL
	, FOUNDED_AT 	DATETIME DEFAULT CURRENT_TIMESTAMP
) 
