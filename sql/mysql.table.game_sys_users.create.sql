---------------------------------------------------------------------------------
-- GNU GENERAL PUBLIC LICENSE
-- origin of this project/file:
--   https://github.com/OPESomeRandomPB/board-game
-- thanks a lot for all the people, which spread their knowlegde on several pages
--
-- Script to create the users of the system
--
---------------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS game_sys_users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(30) NOT NULL,
    password VARCHAR(255) NOT NULL,
    little_salt CHAR(20) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_logged_in datetime
) 
;

ALTER TABLE game_sys_users
ADD
    nick_name VARCHAR(30) NOT NULL default 'not given'
AFTER
    user_name
;

alter table game_sys_users 
  add constraint gsu_user_name_uc unique 
  (user_name);
;

// ALTER TABLE table_name RENAME COLUMN old_column_name TO new_column_name;
