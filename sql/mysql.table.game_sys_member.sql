---------------------------------------------------------------------------------
-- GNU GENERAL PUBLIC LICENSE
-- origin of this project/file:
--   https://github.com/OPESomeRandomPB/board-game
-- thanks a lot for all the people, which spread their knowledge on several pages
--
-- Script to create the game-group
--
---------------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS game_sys_group_name_member (
	  GROUP_ID	int NOT NULL
	, PLAYER_ID	int NOT NULL
) 
